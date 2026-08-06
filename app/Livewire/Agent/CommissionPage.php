<?php

namespace App\Livewire\Agent;

use App\Models\CommissionRequest;
use App\Models\Notification;
use App\Models\PurchaseAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;
use App\Models\AgentQrCode;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

#[Title('My Commissions')]
class CommissionPage extends Component
{
    use Actions, WithFileUploads;

    public string $search = '';

    public ?int $selectedAccountId = null;

    public $qrImage;
    public $qrLabel;
    public $qrProviderName;
    public $qrAccountName;
    public $qrAccountNumber;
    public bool $qrIsPrimary = false;

    public ?int $editingQrCodeId = null;

    public $editQrImage;
    public $editQrLabel;
    public $editQrProviderName;
    public $editQrAccountName;
    public $editQrAccountNumber;
    public bool $editQrIsPrimary = false;

    public bool $showCreateQrModal = false;
    public bool $showEditQrModal = false;

    public function mount(): void
    {
        $this->selectedAccountId = $this
            ->baseAccountsQuery()
            ->value('id');
    }

    public function updatedSearch(): void
    {
        $matchingIds = $this
            ->baseAccountsQuery()
            ->pluck('id');

        if (
            $this->selectedAccountId
            && $matchingIds->contains(
                $this->selectedAccountId
            )
        ) {
            return;
        }

        $this->selectedAccountId =
            $matchingIds->first();
    }

    public function selectAccount(
        int $accountId
    ): void {
        $exists = $this->baseAccountsQuery()
            ->whereKey($accountId)
            ->exists();

        abort_unless($exists, 403);

        $this->selectedAccountId = $accountId;
    }

    public function getAccountsProperty(): Collection
    {
        return $this->baseAccountsQuery()
            ->with([
                'user',
                'lot',
                'houseModel',
                'reservation.agent.info',

                'billings' => fn ($query) =>
                    $query
                        ->orderBy('due_date')
                        ->orderBy('id'),

                'commissionRequests',
            ])
            ->latest()
            ->get()
            ->map(
                fn (PurchaseAccount $account) =>
                    $this->prepareAccountSummary(
                        $account
                    )
            );
    }

    public function getSelectedAccountProperty():
        ?PurchaseAccount
    {
        if (! $this->selectedAccountId) {
            return null;
        }

        $account = $this->baseAccountsQuery()
            ->with([
                'user',
                'lot',
                'houseModel',
                'reservation.agent.info',

                'billings' => fn ($query) =>
                    $query
                        ->orderBy('due_date')
                        ->orderBy('id'),

                'commissionRequests' =>
                    fn ($query) =>
                        $query->orderBy(
                            'period_number'
                        ),
            ])
            ->find($this->selectedAccountId);

        if (! $account) {
            return null;
        }

        return $this->prepareAccountSummary(
            $account
        );
    }

    private function baseAccountsQuery(): Builder
    {
        $agentId = auth()->id();

        return PurchaseAccount::query()
            ->whereHas(
                'reservation',
                function ($query) use ($agentId) {
                    $query->where(
                        'agent_id',
                        $agentId
                    );
                }
            )
            ->when(
                trim($this->search) !== '',
                function ($query) {
                    $search =
                        '%'
                        . trim($this->search)
                        . '%';

                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->whereHas(
                                    'user',
                                    function ($query)
                                    use ($search) {
                                        $query
                                            ->where(
                                                'name',
                                                'like',
                                                $search
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                $search
                                            );
                                    }
                                )
                                ->orWhereHas(
                                    'lot',
                                    function ($query)
                                    use ($search) {
                                        $query->where(
                                            'name',
                                            'like',
                                            $search
                                        );
                                    }
                                );
                        }
                    );
                }
            );
    }

    private function prepareAccountSummary(
        PurchaseAccount $account
    ): PurchaseAccount {
        $commissionPercentage = (float) (
            $account
                ->reservation
                ?->agent
                ?->info
                ?->commission_percentage
            ?? 0
        );

        $totalContractPrice = (float) (
            $account->total_contract_price ?? 0
        );

        $totalCommission =
            $totalContractPrice
            * ($commissionPercentage / 100);

        $billings = $account->billings
            ->sortBy([
                ['due_date', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        /*
         * Cash generally has one billing.
         * Every other scheme groups billings by three.
         */
        if ($account->payment_scheme === 'cash') {
            $totalPeriods = $billings->isEmpty()
                ? 0
                : 1;
        } else {
            $totalPeriods = (int) ceil(
                $billings->count() / 3
            );
        }

        $commissionPerPeriod =
            $totalPeriods > 0
                ? $totalCommission / $totalPeriods
                : 0;

        /*
         * Only paid commission requests should count
         * as actually received.
         */
        $receivedCommission = (float) $account
            ->commissionRequests
            ->where('status', 'paid')
            ->sum('requested_amount');

        $approvedCommission = (float) $account
            ->commissionRequests
            ->where('status', 'approved')
            ->sum('requested_amount');

        $periods = $this
            ->buildCommissionPeriods(
                account: $account,
                commissionPerPeriod:
                    $commissionPerPeriod,
                totalPeriods: $totalPeriods
            );

        $account->setAttribute(
            'agent_commission_percentage',
            $commissionPercentage
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
            'received_commission',
            $receivedCommission
        );

        $account->setAttribute(
            'approved_commission',
            $approvedCommission
        );

        $account->setAttribute(
            'is_current_month_paid',
            $this->isCurrentMonthPaid(
                $account
            )
        );

        $account->setAttribute(
            'commission_periods',
            $periods
        );

        return $account;
    }

    private function buildCommissionPeriods(
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
            /*
             * Cash uses its single billing.
             * Monthly schemes use groups of three.
             */
            if (
                $account->payment_scheme === 'cash'
            ) {
                $periodBillings = $billings
                    ->take(1)
                    ->values();

                $requiredBillingCount = 1;
            } else {
                $startIndex =
                    ($periodNumber - 1) * 3;

                $periodBillings = $billings
                    ->slice($startIndex, 3)
                    ->values();

                $requiredBillingCount = 3;
            }

            $requiredBillingsExist =
                $periodBillings->count()
                === $requiredBillingCount;

            $allPaid =
                $requiredBillingsExist
                && $periodBillings->every(
                    fn ($billing) =>
                        $this->billingIsPaid(
                            $billing
                        )
                );

            $request = $account
                ->commissionRequests
                ->firstWhere(
                    'period_number',
                    $periodNumber
                );

            $monthsLabel =
                $periodBillings->isNotEmpty()
                    ? $periodBillings
                        ->map(
                            fn ($billing) =>
                                $billing
                                    ->due_date
                                    ->format('M Y')
                        )
                        ->implode(' – ')
                    : 'Not generated';

            $periods[] = [
                'period_number' =>
                    $periodNumber,

                'label' =>
                    $account->payment_scheme === 'cash'
                        ? 'Cash Commission'
                        : "Period {$periodNumber}",

                'months_label' =>
                    $monthsLabel,

                'billings' =>
                    $periodBillings,

                'required_billing_count' =>
                    $requiredBillingCount,

                'required_billings_exist' =>
                    $requiredBillingsExist,

                'all_paid' =>
                    $allPaid,

                'eligible' =>
                    $allPaid && ! $request,

                'request' =>
                    $request,

                'amount' =>
                    $request
                        ? (float)
                            $request->requested_amount
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

    private function isCurrentMonthPaid(
        PurchaseAccount $account
    ): bool {
        $currentBilling = $account
            ->billings
            ->first(
                fn ($billing) =>
                    $billing
                        ->due_date
                        ->isSameMonth(now())
            );

        if (! $currentBilling) {
            return false;
        }

        return $this->billingIsPaid(
            $currentBilling
        );
    }

    public function requestCommission(
        int $accountId,
        int $periodNumber
    ): void {
        if ($periodNumber < 1) {
            abort(
                422,
                'Invalid commission period.'
            );
        }

        $created = false;

        DB::transaction(
            function () use (
                $accountId,
                $periodNumber,
                &$created
            ) {
                /*
                 * lockForUpdate prevents duplicate requests
                 * from fast repeated clicks.
                 */
                $account = $this
                    ->baseAccountsQuery()
                    ->with([
                        'user',
                        'lot',
                        'reservation.agent.info',

                        'billings' =>
                            fn ($query) =>
                                $query
                                    ->orderBy(
                                        'due_date'
                                    )
                                    ->orderBy('id'),

                        'commissionRequests',
                    ])
                    ->lockForUpdate()
                    ->findOrFail($accountId);

                $account =
                    $this->prepareAccountSummary(
                        $account
                    );

                if (
                    $periodNumber
                    > $account
                        ->total_commission_periods
                ) {
                    abort(
                        422,
                        'Invalid commission period.'
                    );
                }

                $period = collect(
                    $account->commission_periods
                )->firstWhere(
                    'period_number',
                    $periodNumber
                );

                if (! $period) {
                    abort(
                        422,
                        'Commission period not found.'
                    );
                }

                if (! $period['all_paid']) {
                    return;
                }

                if ($period['request']) {
                    return;
                }

                /*
                 * Final duplicate protection.
                 */
                $existing = CommissionRequest::query()
                    ->where(
                        'purchase_account_id',
                        $account->id
                    )
                    ->where(
                        'period_number',
                        $periodNumber
                    )
                    ->exists();

                if ($existing) {
                    return;
                }

                $request =
                    CommissionRequest::create([
                        'agent_id' =>
                            auth()->id(),

                        'purchase_account_id' =>
                            $account->id,

                        'lot_reservation_id' =>
                            $account
                                ->lot_reservation_id,

                        'period_number' =>
                            $periodNumber,

                        'period_label' =>
                            $period['label'],

                        'commission_percentage' =>
                            $account
                                ->agent_commission_percentage,

                        'total_contract_price' =>
                            $account
                                ->total_contract_price,

                        'total_commission_amount' =>
                            $account
                                ->total_agent_commission,

                        'requested_amount' =>
                            $account
                                ->commission_per_period,

                        'covered_billing_ids' =>
                            collect(
                                $period['billings']
                            )
                                ->pluck('id')
                                ->values()
                                ->all(),

                        'status' =>
                            'pending',

                        'requested_at' =>
                            now(),
                    ]);

                $this->notifyAdminAndStaff(
                    $request,
                    $account
                );

                $created = true;
            }
        );

        if (! $created) {
            $this->notification()->error(
                'Commission Not Available',
                'Complete all required billing payments first, or check whether this period was already requested.'
            );

            return;
        }

        $this->notification()->success(
            'Commission Requested',
            'Your request was sent to admin and staff for review.'
        );
    }

    private function notifyAdminAndStaff(
        CommissionRequest $request,
        PurchaseAccount $account
    ): void {
        $agent = auth()->user();

        $notification =
            Notification::create([
                'title' =>
                    'New Commission Request',

                'message' =>
                    "{$agent->name} requested ₱"
                    . number_format(
                        $request
                            ->requested_amount,
                        2
                    )
                    . " for "
                    . ($account->user?->name
                        ?? 'Unknown Client')
                    . ", "
                    . $request->period_label
                    . ".",

                'type' =>
                    'commission_request_submitted',

                /*
                 * Replace this with the future admin
                 * commission-management route.
                 */
                'url' => route(
                    'filament.ev-admin.pages.agent-management'
                ),

                'data' => [
                    'commission_request_id' =>
                        $request->id,

                    'agent_id' =>
                        $agent->id,

                    'agent_name' =>
                        $agent->name,

                    'client_id' =>
                        $account->user_id,

                    'client_name' =>
                        $account->user?->name,

                    'purchase_account_id' =>
                        $account->id,

                    'lot_reservation_id' =>
                        $account
                            ->lot_reservation_id,

                    'period_number' =>
                        $request->period_number,

                    'period_label' =>
                        $request->period_label,

                    'requested_amount' =>
                        $request->requested_amount,

                    'status' =>
                        $request->status,
                ],

                'created_by' =>
                    $agent->id,
            ]);

        $recipientIds = User::query()
            ->whereIn(
                'role',
                ['admin']
            )
            ->pluck('id')
            ->toArray();

        $notification
            ->users()
            ->attach($recipientIds);
    }

    public function openCreateQrModal(): void
    {
        $this->resetCreateQrForm();

        $this->showCreateQrModal = true;
    }

    public function closeCreateQrModal(): void
    {
        $this->showCreateQrModal = false;

        $this->resetCreateQrForm();
    }

    public function closeEditQrModal(): void
    {
        $this->showEditQrModal = false;

        $this->resetEditQrForm();
    }

    public function getQrCodesProperty()
    {
        return AgentQrCode::query()
            ->where('agent_id', auth()->id())
            ->orderByDesc('is_primary')
            ->latest()
            ->get();
    }

    public function createQrCode(): void
    {
        $validated = $this->validate([
            'qrLabel' => [
                'nullable',
                'string',
                'max:100',
            ],

            'qrProviderName' => [
                'required',
                'string',
                'max:100',
            ],

            'qrAccountName' => [
                'required',
                'string',
                'max:150',
            ],

            'qrAccountNumber' => [
                'nullable',
                'string',
                'max:100',
            ],

            'qrImage' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'qrIsPrimary' => [
                'boolean',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $agentId = auth()->id();

            $hasExistingQr = AgentQrCode::query()
                ->where('agent_id', $agentId)
                ->exists();

            /*
            * Automatically make the first uploaded QR primary.
            */
            $isPrimary = ! $hasExistingQr
                || (bool) $validated['qrIsPrimary'];

            if ($isPrimary) {
                AgentQrCode::query()
                    ->where('agent_id', $agentId)
                    ->update([
                        'is_primary' => false,
                    ]);
            }

            $path = $validated['qrImage']->store(
                "agent-qr-codes/{$agentId}",
                'public'
            );

            AgentQrCode::create([
                'agent_id' => $agentId,

                'label' =>
                    $validated['qrLabel'] ?: null,

                'provider_name' =>
                    $validated['qrProviderName'],

                'account_name' =>
                    $validated['qrAccountName'],

                'account_number' =>
                    $validated['qrAccountNumber'] ?: null,

                'qr_image_path' => $path,

                'is_primary' => $isPrimary,
            ]);
        });

        $this->resetCreateQrForm();

        $this->showCreateQrModal = false;

        $this->notification()->success(
            'QR Code Added',
            'Your payment QR code was saved successfully.'
        );
    }

    public function openEditQrCode(
        int $qrCodeId
    ): void {
        $qrCode = AgentQrCode::query()
            ->where('agent_id', auth()->id())
            ->findOrFail($qrCodeId);

        $this->editingQrCodeId = $qrCode->id;

        $this->editQrLabel =
            $qrCode->label;

        $this->editQrProviderName =
            $qrCode->provider_name;

        $this->editQrAccountName =
            $qrCode->account_name;

        $this->editQrAccountNumber =
            $qrCode->account_number;

        $this->editQrIsPrimary =
            (bool) $qrCode->is_primary;

        $this->editQrImage = null;

        $this->resetValidation();

        $this->showEditQrModal = true;
    }

    public function updateQrCode(): void
    {
        if (! $this->editingQrCodeId) {
            return;
        }

        $validated = $this->validate([
            'editQrLabel' => [
                'nullable',
                'string',
                'max:100',
            ],

            'editQrProviderName' => [
                'required',
                'string',
                'max:100',
            ],

            'editQrAccountName' => [
                'required',
                'string',
                'max:150',
            ],

            'editQrAccountNumber' => [
                'nullable',
                'string',
                'max:100',
            ],

            'editQrImage' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'editQrIsPrimary' => [
                'boolean',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $agentId = auth()->id();

            $qrCode = AgentQrCode::query()
                ->where('agent_id', $agentId)
                ->lockForUpdate()
                ->findOrFail($this->editingQrCodeId);

            if ((bool) $validated['editQrIsPrimary']) {
                AgentQrCode::query()
                    ->where('agent_id', $agentId)
                    ->whereKeyNot($qrCode->id)
                    ->update([
                        'is_primary' => false,
                    ]);
            }

            $newImagePath = $qrCode->qr_image_path;

            if ($validated['editQrImage']) {
                $newImagePath =
                    $validated['editQrImage']->store(
                        "agent-qr-codes/{$agentId}",
                        'public'
                    );

                if (
                    $qrCode->qr_image_path
                    && Storage::disk('public')->exists(
                        $qrCode->qr_image_path
                    )
                ) {
                    Storage::disk('public')->delete(
                        $qrCode->qr_image_path
                    );
                }
            }

            $qrCode->update([
                'label' =>
                    $validated['editQrLabel'] ?: null,

                'provider_name' =>
                    $validated['editQrProviderName'],

                'account_name' =>
                    $validated['editQrAccountName'],

                'account_number' =>
                    $validated['editQrAccountNumber'] ?: null,

                'qr_image_path' =>
                    $newImagePath,

                /*
                * Do not allow removing the primary flag if this
                * is the only QR code.
                */
                'is_primary' =>
                    $this->canRemovePrimaryStatus($qrCode)
                        ? (bool) $validated['editQrIsPrimary']
                        : true,
            ]);
        });

        $this->resetEditQrForm();

        $this->showEditQrModal = false;

        $this->notification()->success(
            'QR Code Updated',
            'Your payment QR code was updated successfully.'
        );
    }

    private function canRemovePrimaryStatus(
        AgentQrCode $qrCode
    ): bool {
        if (! $qrCode->is_primary) {
            return true;
        }

        return AgentQrCode::query()
            ->where('agent_id', auth()->id())
            ->whereKeyNot($qrCode->id)
            ->exists();
    }

    public function setPrimaryQrCode(
        int $qrCodeId
    ): void {
        DB::transaction(function () use ($qrCodeId) {
            $agentId = auth()->id();

            $qrCode = AgentQrCode::query()
                ->where('agent_id', $agentId)
                ->findOrFail($qrCodeId);

            AgentQrCode::query()
                ->where('agent_id', $agentId)
                ->update([
                    'is_primary' => false,
                ]);

            $qrCode->update([
                'is_primary' => true,
            ]);
        });

        $this->notification()->success(
            'Primary QR Updated',
            'This QR code will be shown first to the administrator.'
        );
    }

    public function deleteQrCode(
        int $qrCodeId
    ): void {
        DB::transaction(function () use ($qrCodeId) {
            $agentId = auth()->id();

            $qrCode = AgentQrCode::query()
                ->where('agent_id', $agentId)
                ->lockForUpdate()
                ->findOrFail($qrCodeId);

            $wasPrimary = $qrCode->is_primary;
            $imagePath = $qrCode->qr_image_path;

            $qrCode->delete();

            if (
                $imagePath
                && Storage::disk('public')->exists(
                    $imagePath
                )
            ) {
                Storage::disk('public')->delete(
                    $imagePath
                );
            }

            /*
            * If the deleted code was primary, make the newest
            * remaining QR code primary.
            */
            if ($wasPrimary) {
                $nextQrCode = AgentQrCode::query()
                    ->where('agent_id', $agentId)
                    ->latest()
                    ->first();

                $nextQrCode?->update([
                    'is_primary' => true,
                ]);
            }
        });

        $this->notification()->success(
            'QR Code Deleted',
            'The payment QR code was removed.'
        );
    }

    public function confirmDeleteQrCode(
        int $qrCodeId
    ): void {
        $qrCode = AgentQrCode::query()
            ->where('agent_id', auth()->id())
            ->findOrFail($qrCodeId);

        $this->dialog()->confirm([
            'title' => 'Delete QR Code?',
            'description' =>
                "Remove {$qrCode->provider_name} QR code?",

            'acceptLabel' => 'Yes, delete it',
            'method' => 'deleteQrCode',
            'params' => $qrCode->id,
            'icon' => 'error',
        ]);
    }

    private function resetCreateQrForm(): void
    {
        $this->reset([
            'qrImage',
            'qrLabel',
            'qrProviderName',
            'qrAccountName',
            'qrAccountNumber',
            'qrIsPrimary',
        ]);

        $this->resetValidation();
    }

    private function resetEditQrForm(): void
    {
        $this->reset([
            'editingQrCodeId',
            'editQrImage',
            'editQrLabel',
            'editQrProviderName',
            'editQrAccountName',
            'editQrAccountNumber',
            'editQrIsPrimary',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.agent.commission-page');
    }
}
