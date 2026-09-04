<?php

namespace App\Livewire\FilPages;

use App\Models\User;
use App\Models\UserInfo;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use WireUi\Traits\Actions;
use App\Models\AgentQrCode;
use App\Models\CommissionRequest;
use App\Models\Notification as SystemNotification;
use App\Models\PurchaseAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use App\Mail\CommissionPaidMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class AgentManagement extends Component
{

    use Actions, WithFileUploads, WithFilePond;

    public $agent;

    public $name, $email, $phone, $password, $is_active = 1;

    public $commissionPercentage;
    public $professionalAgentId;
    public $realEstateLicenseNumber;

    public $selectedAgentMember, $editName, $editEmail, $editPhone, $editPassword, $edit_is_active;

    public $editCommissionPercentage;
    public $editProfessionalAgentId;
    public $editRealEstateLicenseNumber;

    public ?int $commissionAgentId = null;
    public ?int $commissionAccountId = null;

    public bool $showCommissionPaymentModal = false;
    public bool $showCommissionAgentModal = false;
    public bool $showCommissionClientDetails = false;
    public string $commissionClientTab = 'to_pay';

    public ?int $payingCommissionRequestId = null;
    public $commissionReceipt;
    public $commissionPaymentReference;
    public $commissionPaymentNotes;

    public function mount()
    {
        // $this->loadAgents();
    }

    // private function loadAgents(): void
    // {
    //     $this->agent = User::query()
    //         ->with('info')
    //         ->withCount([
    //             'commissionRequests as commission_requests_count',
    //         ])
    //         ->withCount([
    //             'commissionRequests as pending_commission_requests_count' =>
    //                 fn ($query) => $query->where('status', 'pending'),
    //         ])
    //         ->where('role', 'agent')
    //         ->latest()
    //         ->get();
    // }

    public function setCommissionClientTab(string $tab): void
    {
        if (! in_array($tab, [
            'not_requested',
            'to_pay',
            'paid',
        ], true)) {
            return;
        }

        $this->commissionClientTab = $tab;
    }


    public function getFilteredCommissionClientsProperty()
    {
        $clients = $this->commissionClients;

        return match ($this->commissionClientTab) {

            'to_pay' => $clients
                ->filter(
                    fn ($account) =>
                        ($account->pending_commission_request_count ?? 0) > 0
                )
                ->values(),

            'paid' => $clients
                ->filter(function ($account) {
                    $paidPeriods =
                        (int) ($account->paid_period_count ?? 0);

                    $totalPeriods =
                        (int) ($account->total_commission_periods ?? 0);

                    return $totalPeriods > 0
                        && $paidPeriods >= $totalPeriods;
                })
                ->values(),

            'not_requested' => $clients
                ->filter(
                    fn ($account) =>
                        ($account->pending_commission_request_count ?? 0) === 0
                        &&
                        ($account->paid_period_count ?? 0) === 0
                )
                ->values(),

            default => collect(),
        };
    }

    public function getCommissionNotRequestedCountProperty(): int
    {
        return $this->commissionClients
            ->filter(
                fn ($account) =>
                    ($account->pending_commission_request_count ?? 0) === 0
                    &&
                    ($account->paid_period_count ?? 0) === 0
            )
            ->count();
    }


    public function getCommissionToPayCountProperty(): int
    {
        return $this->commissionClients
            ->filter(
                fn ($account) =>
                    ($account->pending_commission_request_count ?? 0) > 0
            )
            ->count();
    }


    public function getCommissionPaidCountProperty(): int
    {
        return $this->commissionClients
            ->filter(function ($account) {
                $paidPeriods =
                    (int) ($account->paid_period_count ?? 0);

                $totalPeriods =
                    (int) ($account->total_commission_periods ?? 0);

                return $totalPeriods > 0
                    && $paidPeriods >= $totalPeriods;
            })
            ->count();
    }

    public function getAgentsProperty()
    {
        return User::query()
            ->with('info')
            ->withCount([
                'commissionRequests as commission_requests_count',
            ])
            ->withCount([
                'commissionRequests as pending_commission_requests_count' =>
                    fn ($query) => $query->where('status', 'pending'),
            ])
            ->where('role', 'agent')
            ->latest()
            ->get();
    }

    public function getCommissionAgentProperty(): ?User
    {
        if (! $this->commissionAgentId) {
            return null;
        }

        return User::query()
            ->with([
                'info',
                'qrCodes' => fn ($query) => $query
                    ->orderByDesc('is_primary')
                    ->latest(),
            ])
            ->where('role', 'agent')
            ->find($this->commissionAgentId);
    }

    private function commissionAccountsQuery(): Builder
    {
        return PurchaseAccount::query()
            ->whereHas(
                'reservation',
                fn ($query) => $query->where(
                    'agent_id',
                    $this->commissionAgentId
                )
            );
    }

    public function getCommissionClientsProperty()
    {
        if (! $this->commissionAgentId) {
            return collect();
        }

        return $this->commissionAccountsQuery()
            ->with([
                'user',
                'lot',
                'houseModel',
                'reservation.agent.info',

                'billings' => fn ($query) => $query
                    ->orderBy('due_date')
                    ->orderBy('id'),

                'commissionRequests' => fn ($query) => $query
                    ->orderBy('period_number'),
            ])
            ->latest()
            ->get()
            ->map(
                fn (PurchaseAccount $account) =>
                    $this->prepareCommissionAccount($account)
            );
    }

    public function getCommissionAccountProperty(): ?PurchaseAccount
    {
        if (
            ! $this->commissionAgentId
            || ! $this->commissionAccountId
        ) {
            return null;
        }

        $account = $this->commissionAccountsQuery()
            ->with([
                'user',
                'lot',
                'houseModel',
                'reservation.agent.info',

                'billings' => fn ($query) => $query
                    ->orderBy('due_date')
                    ->orderBy('id'),

                'commissionRequests' => fn ($query) => $query
                    ->orderBy('period_number'),
            ])
            ->find($this->commissionAccountId);

        return $account
            ? $this->prepareCommissionAccount($account)
            : null;
    }

    public function openAgentCommissionModal(int $agentId): void
    {
        $agent = User::query()
            ->where('role', 'agent')
            ->findOrFail($agentId);

        $this->commissionAgentId = $agent->id;
        $this->commissionAccountId = null;

        $this->showCommissionClientDetails = false;
        $this->showCommissionAgentModal = true;
        $this->commissionClientTab = 'to_pay';

        $this->resetCommissionPaymentForm();
    }

    public function closeAgentCommissionModal(): void
    {
        $this->showCommissionAgentModal = false;
        $this->showCommissionClientDetails = false;

        $this->commissionAgentId = null;
        $this->commissionAccountId = null;
        $this->reloadWeb();
        $this->resetCommissionPaymentForm();
    }

    public function selectCommissionClient(int $accountId): void
    {
        $exists = $this->commissionAccountsQuery()
            ->whereKey($accountId)
            ->exists();

        abort_unless($exists, 403);

        $this->commissionAccountId = $accountId;
        $this->showCommissionClientDetails = true;

        $this->resetCommissionPaymentForm();
    }

    public function backToCommissionClients(): void
    {
        $this->commissionAccountId = null;
        $this->showCommissionClientDetails = false;

        $this->resetCommissionPaymentForm();
    }

    private function prepareCommissionAccount(
    PurchaseAccount $account
    ): PurchaseAccount {
        $commissionRate = (float) (
            $account->reservation?->agent?->info
                ?->commission_percentage ?? 0
        );

        $totalContractPrice = (float) (
            $account->total_contract_price ?? 0
        );

        $totalCommission =
            $totalContractPrice * ($commissionRate / 100);

        $billings = $account->billings
            ->sortBy([
                ['due_date', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        if ($account->payment_scheme === 'cash') {
            $totalPeriods = $billings->isEmpty() ? 0 : 1;
        } else {
            $totalPeriods = (int) ceil(
                $billings->count() / 3
            );
        }

        $commissionPerPeriod = $totalPeriods > 0
            ? $totalCommission / $totalPeriods
            : 0;

        $periods = $this->buildAdminCommissionPeriods(
            $account,
            $commissionPerPeriod,
            $totalPeriods
        );

        $paidCommission = (float) $account
            ->commissionRequests
            ->where('status', 'paid')
            ->sum('requested_amount');

        $paidPeriodCount = $account
            ->commissionRequests
            ->where('status', 'paid')
            ->count();

        $pendingRequestCount = $account
            ->commissionRequests
            ->where('status', 'pending')
            ->count();

        $account->setAttribute(
            'agent_commission_percentage',
            $commissionRate
        );

        $account->setAttribute(
            'total_agent_commission',
            $totalCommission
        );

        $account->setAttribute(
            'commission_per_period',
            $commissionPerPeriod
        );

        $account->setAttribute(
            'total_commission_periods',
            $totalPeriods
        );

        $account->setAttribute(
            'paid_commission',
            $paidCommission
        );

        $account->setAttribute(
            'paid_period_count',
            $paidPeriodCount
        );

        $account->setAttribute(
            'pending_commission_request_count',
            $pendingRequestCount
        );

        $account->setAttribute(
            'has_pending_commission_request',
            $pendingRequestCount > 0
        );

        $account->setAttribute(
            'commission_periods',
            $periods
        );

        return $account;
    }

    private function buildAdminCommissionPeriods(
        PurchaseAccount $account,
        float $commissionPerPeriod,
        int $totalPeriods
    ): array {
        $billings = $account->billings
            ->sortBy([
                ['due_date', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        $periods = [];

        for (
            $periodNumber = 1;
            $periodNumber <= $totalPeriods;
            $periodNumber++
        ) {
            if ($account->payment_scheme === 'cash') {
                $periodBillings = $billings
                    ->take(1)
                    ->values();

                $requiredCount = 1;
            } else {
                $startIndex = ($periodNumber - 1) * 3;

                $periodBillings = $billings
                    ->slice($startIndex, 3)
                    ->values();

                $requiredCount = 3;
            }

            $allBillingsExist =
                $periodBillings->count() === $requiredCount;

            $allPaid = $allBillingsExist
                && $periodBillings->every(
                    fn ($billing) =>
                        $this->billingIsPaid($billing)
                );

            $request = $account
                ->commissionRequests
                ->firstWhere(
                    'period_number',
                    $periodNumber
                );

            $monthsLabel = $periodBillings->isNotEmpty()
                ? $periodBillings
                    ->map(
                        fn ($billing) =>
                            $billing->due_date->format('M Y')
                    )
                    ->implode(' – ')
                : 'Not generated';

            $periods[] = [
                'period_number' => $periodNumber,

                'label' =>
                    $account->payment_scheme === 'cash'
                        ? 'Cash Commission'
                        : "Period {$periodNumber}",

                'months_label' => $monthsLabel,

                'billings' => $periodBillings,

                'all_billings_exist' => $allBillingsExist,

                'all_paid' => $allPaid,

                'request' => $request,

                'amount' => $request
                    ? (float) $request->requested_amount
                    : $commissionPerPeriod,
            ];
        }

        return $periods;
    }

    private function billingIsPaid($billing): bool
    {
        return $billing->status === 'paid'
            || (
                (float) $billing->amount_paid
                >= (float) $billing->amount_due
            );
    }

    public function openCommissionPayment(
        int $commissionRequestId
    ): void {
        $request = CommissionRequest::query()
            ->where('agent_id', $this->commissionAgentId)
            ->where(
                'purchase_account_id',
                $this->commissionAccountId
            )
            ->whereIn('status', [
                'pending',
                'approved',
            ])
            ->findOrFail($commissionRequestId);

        $this->payingCommissionRequestId =
            $request->id;

        $this->commissionReceipt = null;
        $this->commissionPaymentReference = null;
        $this->commissionPaymentNotes = null;

        $this->resetValidation();

        $this->showCommissionPaymentModal = true;
    }

    public function getPayingCommissionRequestProperty():
    ?CommissionRequest
    {
        if (! $this->payingCommissionRequestId) {
            return null;
        }

        return CommissionRequest::query()
            ->with([
                'agent.qrCodes',
                'purchaseAccount.user',
                'purchaseAccount.lot',
            ])
            ->where('agent_id', $this->commissionAgentId)
            ->where(
                'purchase_account_id',
                $this->commissionAccountId
            )
            ->find(
                $this->payingCommissionRequestId
            );
    }

    public function closeCommissionPaymentModal(): void
    {
        $this->showCommissionPaymentModal = false;

        $this->resetCommissionPaymentForm();
    }

    public function resetCommissionPaymentForm(): void
    {
        $this->reset([
            'payingCommissionRequestId',
            'commissionReceipt',
            'commissionPaymentReference',
            'commissionPaymentNotes',
        ]);

        $this->resetValidation();
    }

    public function payCommissionRequest(): void
    {
        if (! $this->payingCommissionRequestId) {
            return;
        }

        $validated = $this->validate([
            'commissionReceipt' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            'commissionPaymentReference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'commissionPaymentNotes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Keep the paid request ID
        |--------------------------------------------------------------------------
        |
        | We'll use this after the transaction to send the email.
        |
        */

        $paidCommissionRequestId = null;

        DB::transaction(function () use (
            $validated,
            &$paidCommissionRequestId
        ) {

            $request = CommissionRequest::query()
                ->where(
                    'agent_id',
                    $this->commissionAgentId
                )
                ->where(
                    'purchase_account_id',
                    $this->commissionAccountId
                )
                ->whereIn('status', [
                    'pending',
                    'approved',
                ])
                ->lockForUpdate()
                ->findOrFail(
                    $this->payingCommissionRequestId
                );

            /*
            |--------------------------------------------------------------------------
            | Store Receipt
            |--------------------------------------------------------------------------
            */

            $path = $validated['commissionReceipt']
                ->store(
                    "commission-receipts/{$request->agent_id}",
                    'public'
                );

            /*
            |--------------------------------------------------------------------------
            | Delete Previous Receipt
            |--------------------------------------------------------------------------
            */

            if (
                $request->receipt_path
                && Storage::disk('public')->exists(
                    $request->receipt_path
                )
            ) {
                Storage::disk('public')->delete(
                    $request->receipt_path
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Payment Notes
            |--------------------------------------------------------------------------
            */

            $notes = collect([
                $validated['commissionPaymentReference']
                    ? 'Reference: '
                        . $validated['commissionPaymentReference']
                    : null,

                $validated['commissionPaymentNotes']
                    ?? null,
            ])
                ->filter()
                ->implode("\n");

            /*
            |--------------------------------------------------------------------------
            | Mark Commission As Paid
            |--------------------------------------------------------------------------
            */

            $request->update([
                'status' =>
                    'paid',

                'receipt_path' =>
                    $path,

                'remarks' =>
                    $notes
                        ?: $request->remarks,

                'reviewed_by' =>
                    auth()->id(),

                'reviewed_at' =>
                    now(),

                'paid_at' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | In-App Notification
            |--------------------------------------------------------------------------
            */

            $this->notifyAgentCommissionPaid(
                $request
            );

            /*
            |--------------------------------------------------------------------------
            | Remember Request
            |--------------------------------------------------------------------------
            */

            $paidCommissionRequestId =
                $request->id;
        });


        /*
        |--------------------------------------------------------------------------
        | EMAIL AGENT
        |--------------------------------------------------------------------------
        |
        | Send only AFTER the payment transaction has succeeded.
        |
        */

        if ($paidCommissionRequestId) {

            $paidRequest = CommissionRequest::query()
                ->with([
                    'agent',
                    'purchaseAccount.user',
                    'purchaseAccount.lot',
                ])
                ->find($paidCommissionRequestId);

            if (
                $paidRequest
                && $paidRequest->agent?->email
            ) {
                Mail::to(
                    $paidRequest->agent->email
                )->send(
                    new CommissionPaidMail(
                        $paidRequest
                    )
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Close / Reset
        |--------------------------------------------------------------------------
        */

        $this->showCommissionPaymentModal = false;

        $this->resetCommissionPaymentForm();


        /*
        |--------------------------------------------------------------------------
        | Admin Success Toast
        |--------------------------------------------------------------------------
        */

        Notification::make()
            ->title('Commission Paid')
            ->body(
                'The commission payment and receipt were saved successfully.'
            )
            ->success()
            ->send();

        // $this->reloadWeb();
        /*
        |--------------------------------------------------------------------------
        | Close small payment modal only
        |--------------------------------------------------------------------------
        */

        $this->dispatch('commission-paid');
    }

    private function notifyAgentCommissionPaid(
        CommissionRequest $request
    ): void {
        $request->loadMissing([
            'agent',
            'purchaseAccount.user',
        ]);

        $clientName =
            $request->purchaseAccount?->user?->name
            ?? 'Unknown Client';

        $notification = SystemNotification::create([
            'title' => 'Commission Payment Released',

            'message' =>
                'Your commission of ₱'
                . number_format(
                    $request->requested_amount,
                    2
                )
                . " for {$clientName}, "
                . "{$request->period_label}, has been paid.",

            'type' => 'commission_request_paid',

            'url' => route('agent.commission'),

            'data' => [
                'commission_request_id' => $request->id,
                'purchase_account_id' =>
                    $request->purchase_account_id,

                'period_number' =>
                    $request->period_number,

                'period_label' =>
                    $request->period_label,

                'requested_amount' =>
                    $request->requested_amount,

                'status' => $request->status,
            ],

            'created_by' => auth()->id(),
        ]);

        $notification->users()->attach([
            $request->agent_id,
        ]);
    }


    public function createAgentMemberAccount()
    {
        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:50',
            ],

            'email' => [
                'required',
                'email',
                'max:50',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'commissionPercentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'professionalAgentId' => [
                'nullable',
                'string',
                'max:100',
                'unique:user_infos,professional_agent_id',
            ],

            'realEstateLicenseNumber' => [
                'nullable',
                'string',
                'max:50',
                'unique:user_infos,real_estate_license_number',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'agent',
            'is_active' => $validated['is_active'],
            'is_verified' => 0,
        ]);

        UserInfo::create([
            'user_id' => $user->id,
            'phone' => $this->normalizePhilippinePhone(
                $validated['phone']
            ),

            'commission_percentage' =>
                $validated['commissionPercentage'] ?? null,

            'professional_agent_id' =>
                $validated['professionalAgentId'] ?: null,

            'real_estate_license_number' =>
                $validated['realEstateLicenseNumber'] ?: null,
        ]);

        $this->resetCreateForm();

        Notification::make()
            ->title('Success!')
            ->body('Agent account created successfully.')
            ->success()
            ->send();

        // $this->loadAgents();

        $this->reloadWeb();
    }

    private function resetCreateForm(): void
    {
        $this->reset([
            'name',
            'email',
            'phone',
            'password',
            'commissionPercentage',
            'professionalAgentId',
            'realEstateLicenseNumber',
        ]);

        $this->is_active = 1;

        $this->resetValidation();
    }

    public function getSelectedAgentMember($id)
    {
        $agent = User::query()
            ->with('info')
            ->where('role', 'agent')
            ->findOrFail($id);

        $this->selectedAgentMember = $agent->id;

        $this->editName = $agent->name;
        $this->editEmail = $agent->email;
        $this->editPhone = $agent->info?->phone;
        $this->editPassword = null;
        $this->edit_is_active = $agent->is_active;

        $this->editCommissionPercentage =
            $agent->info?->commission_percentage;

        $this->editProfessionalAgentId =
            $agent->info?->professional_agent_id;

        $this->editRealEstateLicenseNumber =
            $agent->info?->real_estate_license_number;
    }

    public function editAgentMemberAccount()
    {
        if (! $this->selectedAgentMember) {
            return;
        }

        $agentInfoId = UserInfo::query()
            ->where('user_id', $this->selectedAgentMember)
            ->value('id');

        $validated = $this->validate([
            'editName' => [
                'required',
                'string',
                'max:50',
            ],

            'editEmail' => [
                'required',
                'email',
                'max:50',
                Rule::unique('users', 'email')
                    ->ignore($this->selectedAgentMember),
            ],

            'editPhone' => [
                'required',
                'string',
                'max:20',
            ],

            'edit_is_active' => [
                'required',
                'boolean',
            ],

            'editPassword' => [
                'nullable',
                'string',
                'min:8',
                'max:20',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],

            'editCommissionPercentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'editProfessionalAgentId' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique(
                    'user_infos',
                    'professional_agent_id'
                )->ignore($agentInfoId),
            ],

            'editRealEstateLicenseNumber' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(
                    'user_infos',
                    'real_estate_license_number'
                )->ignore($agentInfoId),
            ],
        ]);

        $user = User::query()
            ->with('info')
            ->where('role', 'agent')
            ->findOrFail($this->selectedAgentMember);

        /*
        |--------------------------------------------------------------------------
        | STORE OLD VALUES
        |--------------------------------------------------------------------------
        */

        $oldName = $user->name;
        $oldEmail = $user->email;
        $oldStatus = (bool) $user->is_active;

        $oldPhone = $user->info?->phone;
        $oldCommission = $user->info?->commission_percentage;
        $oldProfessionalId = $user->info?->professional_agent_id;
        $oldLicenseNumber = $user->info?->real_estate_license_number;

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE NEW VALUES
        |--------------------------------------------------------------------------
        */

        $newPhone = $this->normalizePhilippinePhone(
            $validated['editPhone']
        );

        $newCommission =
            $validated['editCommissionPercentage'] ?? null;

        $newProfessionalId =
            $validated['editProfessionalAgentId'] ?: null;

        $newLicenseNumber =
            $validated['editRealEstateLicenseNumber'] ?: null;

        /*
        |--------------------------------------------------------------------------
        | BUILD CHANGES
        |--------------------------------------------------------------------------
        */

        $changes = [];

        if ($oldName !== $validated['editName']) {
            $changes['name'] = [
                'label' => 'Full Name',
                'old' => $oldName,
                'new' => $validated['editName'],
            ];
        }

        if ($oldEmail !== $validated['editEmail']) {
            $changes['email'] = [
                'label' => 'Email Address',
                'old' => $oldEmail,
                'new' => $validated['editEmail'],
            ];
        }

        if ($oldPhone !== $newPhone) {
            $changes['phone'] = [
                'label' => 'Phone Number',
                'old' => $oldPhone,
                'new' => $newPhone,
            ];
        }

        if (
            (string) $oldCommission
            !== (string) $newCommission
        ) {
            $changes['commission_percentage'] = [
                'label' => 'Commission Rate',
                'old' => filled($oldCommission)
                    ? $oldCommission . '%'
                    : null,
                'new' => filled($newCommission)
                    ? $newCommission . '%'
                    : null,
            ];
        }

        if ($oldProfessionalId !== $newProfessionalId) {
            $changes['professional_agent_id'] = [
                'label' => 'Professional Agent ID',
                'old' => $oldProfessionalId,
                'new' => $newProfessionalId,
            ];
        }

        if ($oldLicenseNumber !== $newLicenseNumber) {
            $changes['real_estate_license_number'] = [
                'label' => 'Real Estate License Number',
                'old' => $oldLicenseNumber,
                'new' => $newLicenseNumber,
            ];
        }

        if (
            $oldStatus
            !== (bool) $validated['edit_is_active']
        ) {
            $changes['is_active'] = [
                'label' => 'Account Status',
                'old' => $oldStatus
                    ? 'Active'
                    : 'Inactive',
                'new' => $validated['edit_is_active']
                    ? 'Active'
                    : 'Inactive',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PASSWORD CHECK
        |--------------------------------------------------------------------------
        */

        if (
            ! empty($validated['editPassword']) &&
            Hash::check($validated['editPassword'], $user->password)
        ) {
            $this->addError(
                'editPassword',
                'The new password must be different from the current password.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | PASSWORD CHANGE
        |--------------------------------------------------------------------------
        |
        | Never store actual password values inside the notification.
        |
        */

        $passwordChanged = false;

        if (! empty($validated['editPassword'])) {
            $passwordChanged = true;

            $changes['password'] = [
                'label' => 'Password',
                'old' => null,
                'new' => Crypt::encryptString($validated['editPassword']),
                'is_encrypted' => true,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DETECT DEACTIVATION
        |--------------------------------------------------------------------------
        */

        $wasDeactivated =
            $oldStatus === true
            && (bool) $validated['edit_is_active'] === false;

        /*
        |--------------------------------------------------------------------------
        | UPDATE USER
        |--------------------------------------------------------------------------
        */

        $userData = [
            'name' => $validated['editName'],
            'email' => $validated['editEmail'],
            'is_active' => $validated['edit_is_active'],
        ];

        if ($passwordChanged) {
            $userData['password'] = Hash::make(
                $validated['editPassword']
            );

            $userData['remember_token'] = null;
        }

        $user->update($userData);

        /*
        |--------------------------------------------------------------------------
        | FORCE LOGOUT
        |--------------------------------------------------------------------------
        */

        if ($passwordChanged || $wasDeactivated) {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE USER INFO
        |--------------------------------------------------------------------------
        */

        $user->info()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'phone' => $newPhone,

                'commission_percentage' =>
                    $newCommission,

                'professional_agent_id' =>
                    $newProfessionalId,

                'real_estate_license_number' =>
                    $newLicenseNumber,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | NOTIFY THE AGENT
        |--------------------------------------------------------------------------
        */

        if (! empty($changes)) {

            $changedFields = collect($changes)
                ->pluck('label')
                ->map(fn ($label) => strtolower($label))
                ->values();

            if ($changedFields->count() === 1) {

                $changesText = $changedFields->first();

            } elseif ($changedFields->count() === 2) {

                $changesText = $changedFields->implode(' and ');

            } else {

                $lastField = $changedFields->pop();

                $changesText =
                    $changedFields->implode(', ')
                    . ', and '
                    . $lastField;
            }

            $actorName = auth()->user()->name;

            $notification = SystemNotification::create([
                'title' => 'Account Information Updated',

                'message' =>
                    "{$actorName} has made changes to your {$changesText}.",

                'type' => 'agent_account_updated_by_staff',

                'url' => route('client.account'),

                'data' => [
                    'agent_id' => $user->id,
                    'agent_name' => $user->name,
                    'changes' => $changes,
                    'updated_by' => auth()->id(),
                    'updated_by_name' => $actorName,
                ],

                'created_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | SEND ONLY TO THE INTENDED AGENT
            |--------------------------------------------------------------------------
            */

            $notification->users()->attach([
                $user->id,
            ]);
        }

        Notification::make()
            ->title('Updated!')
            ->body('Agent member updated successfully.')
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function editAgentMemberConfirmation($name){
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => "Do you want to edit this agent info with name . ".  html_entity_decode('<span class="text-red-600 underline">' . $name . '</span>') . " ?",
            'acceptLabel' => 'Yes, update it',
            'method'      => 'editAgentMemberAccount',
            'icon'        => 'error',
            'params'      => $name
        ]);
    }

    public function deleteAgentMember($id)
    {
        $agent = User::findOrFail($id);

        $agent->info()->delete();
        
        $agent->delete();

        Notification::make()
            ->title('Deleted!')
            ->body('Agent Member removed successfully.')
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function deleteAgentMemberConfirmation($id, $agentName){
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => "Do you want to remove this agent Name: ".  html_entity_decode('<span class="text-red-600 underline">' . $agentName . '</span>') . " ?",
            'acceptLabel' => 'Yes, delete it',
            'method'      => 'deleteAgentMember',
            'icon'        => 'error',
            'params'      => $id
        ]);
    }

    private function normalizePhilippinePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '63')) {
            $digits = substr($digits, 2);
        }

        $digits = ltrim($digits, '0');

        return '+63' . $digits;
    }

    public function reloadWeb(){

        $this->dispatch('reload');
        // return redirect()->back();

    }

    public function render()
    {
        return view('livewire.fil-pages.agent-management');
    }
}
