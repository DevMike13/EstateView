<?php

namespace App\Livewire\FilPages;

use App\Models\PHCities;
use App\Models\PHProvinces;
use App\Models\PHRegions;
use App\Models\User;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;
use App\Models\PHBarangays;
use App\Models\Notification as SystemNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ClientRecords extends Component
{
    use WithPagination, Actions;

    public string $search = '';
    public string $status = '';

    public $selectedClient = null;

    public $regionCode = null;
    public $provinceCode = null;
    public $municipalityCode = null;

    public $regions = [];
    public $provinces = [];
    public $municipalities = [];
    public $barangays = [];

    public array $editForm = [
        'first_name' => '',
        'middle_name' => '',
        'last_name' => '',
        'suffix' => '',
        'email' => '',
        'phone' => '',
        'region' => '',
        'province' => '',
        'municipality' => '',
        'barangay' => '',
        'state' => 'Philippines',

        'property' => '',
        'lot_number' => '',
        'house_model' => '',
        'reservation_status' => '',
        'account_status' => '',
        'total_price' => 0,
        'total_paid' => 0,
        'payment_plan' => '',
        'accounts' => [],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatedEditForm($value, $key)
    {
        if ($key === 'region') {
            $this->editForm['province'] = '';
            $this->editForm['municipality'] = '';
            $this->editForm['barangay'] = '';

            $this->provinceCode = null;
            $this->municipalityCode = null;
            $this->municipalities = [];
            $this->barangays = [];

            $this->loadProvinces();
        }

        if ($key === 'province') {
            $this->editForm['municipality'] = '';
            $this->editForm['barangay'] = '';

            $this->municipalityCode = null;
            $this->barangays = [];

            $this->loadMunicipalities();
        }

        if ($key === 'municipality') {
            $this->editForm['barangay'] = '';

            $this->loadBarangays();
        }
    }

    public function getRegionCode()
    {
        $this->regionCode = PHRegions::where(
            'region_description',
            $this->editForm['region']
        )->value('region_code');
    }

    public function getProvinceCode()
    {
        $this->provinceCode = PHProvinces::where(
            'province_description',
            $this->editForm['province']
        )->value('province_code');
    }

    public function getMunicipalityCode()
    {
        $this->municipalityCode = PHCities::where(
            'city_municipality_description',
            $this->editForm['municipality']
        )->value('city_municipality_code');
    }

    public function viewClient($clientId)
    {
        $this->selectedClient = $this->clientQuery()->findOrFail($clientId);

        $this->dispatch('openModal', name: 'viewClientModal');
    }

    public function editClient($clientId)
    {
        $this->selectedClient = User::with([
            'info',
            'purchaseAccounts.lot',
            'purchaseAccounts.houseModel',
            'purchaseAccounts.reservation.lot',
            'purchaseAccounts.reservation.houseModel',
        ])
            ->where('role', 'user')
            ->findOrFail($clientId);

        $accounts = $this->selectedClient->purchaseAccounts
            ->values()
            ->map(function ($account) {
                $reservation = $account?->reservation;
                $lot = $account?->lot ?? $reservation?->lot;
                $houseModel = $account?->houseModel ?? $reservation?->houseModel;

                return [
                    'id' => $account->id,
                    'has_reservation' => (bool) $reservation,

                    'property' => $account || $reservation || $lot || $houseModel
                        ? trim(($houseModel?->model_name ?? 'Lot Only') . ' - ' . ($lot?->name ?? ''))
                        : '',

                    'lot_number' => $lot?->name ?? '',
                    'house_model' => $houseModel?->model_name ?? '',
                    'reservation_type' => $reservation?->type ?? '',
                    'reservation_status' => $reservation?->status ?? '',
                    'account_status' => $account?->status ?? '',
                    'lot_status' => $lot?->status ?? '',
                    'reserved_at' => $reservation?->reserved_at
                        ? Carbon::parse($reservation->reserved_at)->format('Y-m-d')
                        : '',

                    'payment_scheme' => $account?->payment_scheme ?? '',
                    'payment_plan' => $account
                        ? ucfirst($account->payment_scheme) . ' - ' . ($account->loan_term_years ?? 0) . ' years'
                        : '',

                    'lot_price' => $account?->lot_price ?? 0,
                    'house_price' => $account?->house_price ?? 0,
                    'total_price' => $account?->total_contract_price ?? 0,
                    'net_contract_price' => $account?->net_contract_price ?? 0,
                    'downpayment_amount' => $account?->downpayment_amount ?? 0,
                    'remaining_downpayment' => $account?->remaining_downpayment ?? 0,
                    'loanable_amount' => $account?->loanable_amount ?? 0,
                    'monthly_amortization' => $account?->monthly_amortization ?? 0,
                    'total_paid' => $account?->total_paid ?? 0,
                    'remaining_balance' => $account?->remaining_balance ?? 0,
                ];
            })
            ->toArray();

        $firstAccount = $accounts[0] ?? null;

        $this->editForm = [
            'first_name' => $this->selectedClient->info?->first_name,
            'middle_name' => $this->selectedClient->info?->middle_name,
            'last_name' => $this->selectedClient->info?->last_name,
            'suffix' => $this->selectedClient->info?->suffix,
            'email' => $this->selectedClient->email,
            'phone' => $this->selectedClient->info?->phone ?? '',

            'region' => trim($this->selectedClient->info?->region ?? ''),
            'province' => trim($this->selectedClient->info?->province ?? ''),
            'municipality' => trim($this->selectedClient->info?->municipality ?? ''),
            'barangay' => trim($this->selectedClient->info?->barangay ?? ''),
            'state' => $this->selectedClient->info?->state ?? 'Philippines',

            /*
            |--------------------------------------------------------------------------
            | Keep the existing top-level fields for compatibility.
            | The edit UI now uses "accounts" so every property record is shown.
            |--------------------------------------------------------------------------
            */

            'property' => $firstAccount['property'] ?? '',
            'lot_number' => $firstAccount['lot_number'] ?? '',
            'house_model' => $firstAccount['house_model'] ?? '',
            'reservation_status' => $firstAccount['reservation_status'] ?? '',
            'account_status' => $firstAccount['account_status'] ?? '',
            'total_price' => $firstAccount['total_price'] ?? 0,
            'total_paid' => $firstAccount['total_paid'] ?? 0,
            'payment_plan' => $firstAccount['payment_plan'] ?? '',

            'accounts' => $accounts,
        ];

        $this->loadRegions();
        $this->loadProvinces();
        $this->loadMunicipalities();
        $this->loadBarangays();

        $this->dispatch('openModal', name: 'editClientModal');
    }

    public function confirmUpdateClient()
    {
        $fullName = trim(
            ($this->editForm['first_name'] ?? '') . ' ' .
            ($this->editForm['middle_name'] ?? '') . ' ' .
            ($this->editForm['last_name'] ?? '')
        );

        $this->dialog()->confirm([
            'title'       => 'Are you sure?',
            'description' => "Do you want to update client info for <span class='text-blue-600 font-semibold underline'>{$fullName}</span>?",
            'acceptLabel' => 'Yes, update it',
            'rejectLabel' => 'Cancel',
            'method'      => 'updateClient',
            'icon'        => 'question',
        ]);
    }

    public function updateClient()
    {
        $this->validate([
            'editForm.first_name' => 'required|string|max:255',
            'editForm.middle_name' => 'nullable|string|max:255',
            'editForm.last_name' => 'required|string|max:255',
            'editForm.suffix' => 'nullable|string|max:255',
            'editForm.email' => 'required|email|max:255|unique:users,email,' . $this->selectedClient->id,
            'editForm.phone' => 'nullable|string|max:255',
            'editForm.region' => 'nullable|string|max:255',
            'editForm.province' => 'nullable|string|max:255',
            'editForm.municipality' => 'nullable|string|max:255',
            'editForm.barangay' => 'nullable|string|max:255',
            'editForm.state' => 'nullable|string|max:255',
            'editForm.accounts' => 'nullable|array',
            'editForm.accounts.*.id' => 'required|integer|exists:purchase_accounts,id',
            'editForm.accounts.*.total_paid' => 'nullable|numeric|min:0',
            'editForm.accounts.*.account_status' => 'nullable|string|max:255',
            'editForm.accounts.*.reservation_status' => 'nullable|string|max:255',
        ]);

        $actor = auth()->user();

        $client = $this->selectedClient;

        $info = $client->info;

        /*
        |--------------------------------------------------------------------------
        | CHECK IF EMAIL CHANGED
        |--------------------------------------------------------------------------
        */

        $emailChanged =
            strtolower(trim($client->email))
            !==
            strtolower(trim($this->editForm['email']));

        /*
        |--------------------------------------------------------------------------
        | COLLECT ALL PROFILE CHANGES INTO ONE DIFF
        |--------------------------------------------------------------------------
        */

        $diff = [];

        $fields = [
            'email' => [
                'label' => 'Email Address',
                'old' => $client->email,
                'new' => $this->editForm['email'],
            ],

            'first_name' => [
                'label' => 'First Name',
                'old' => $info?->first_name,
                'new' => $this->editForm['first_name'],
            ],

            'middle_name' => [
                'label' => 'Middle Name',
                'old' => $info?->middle_name,
                'new' => $this->editForm['middle_name'],
            ],

            'last_name' => [
                'label' => 'Last Name',
                'old' => $info?->last_name,
                'new' => $this->editForm['last_name'],
            ],

            'suffix' => [
                'label' => 'Suffix',
                'old' => $info?->suffix,
                'new' => $this->editForm['suffix'],
            ],

            'phone' => [
                'label' => 'Phone Number',
                'old' => $info?->phone,
                'new' => $this->editForm['phone'],
            ],

            'region' => [
                'label' => 'Region',
                'old' => $info?->region,
                'new' => $this->editForm['region'],
            ],

            'province' => [
                'label' => 'Province',
                'old' => $info?->province,
                'new' => $this->editForm['province'],
            ],

            'municipality' => [
                'label' => 'Municipality',
                'old' => $info?->municipality,
                'new' => $this->editForm['municipality'],
            ],

            'barangay' => [
                'label' => 'Barangay',
                'old' => $info?->barangay,
                'new' => $this->editForm['barangay'],
            ],
        ];

        foreach ($fields as $key => $field) {

            $old = $field['old'];
            $new = $field['new'];

            /*
            * Normalize null / empty string so they don't create
            * unnecessary changes.
            */
            $oldNormalized = filled($old) ? trim((string) $old) : '';
            $newNormalized = filled($new) ? trim((string) $new) : '';

            if ($oldNormalized === $newNormalized) {
                continue;
            }

            $diff[$key] = [
                'label' => $field['label'],
                'old' => $old,
                'new' => $new,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE USERS TABLE WITHOUT FIRING USER OBSERVER
        |--------------------------------------------------------------------------
        */

        $client->updateQuietly([
            'name' =>
                $this->editForm['last_name']
                . ', '
                . $this->editForm['first_name'],

            'email' => $this->editForm['email'],

            /*
            * Also invalidate any remember-me login.
            */
            'remember_token' => $emailChanged
                ? null
                : $client->remember_token,
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE USER INFO WITHOUT FIRING USERINFO OBSERVER
        |--------------------------------------------------------------------------
        */

        $userInfo = $client->info()->firstOrNew([
            'user_id' => $client->id,
        ]);

        $userInfo->fill([
            'first_name' => $this->editForm['first_name'],
            'middle_name' => $this->editForm['middle_name'],
            'last_name' => $this->editForm['last_name'],
            'suffix' => $this->editForm['suffix'],
            'phone' => $this->editForm['phone'],
            'region' => $this->editForm['region'],
            'province' => $this->editForm['province'],
            'municipality' => $this->editForm['municipality'],
            'barangay' => $this->editForm['barangay'],
            'state' => $this->editForm['state'],
        ]);

        $userInfo->saveQuietly();

        /*
        |--------------------------------------------------------------------------
        | FORCE CLIENT LOGOUT IF EMAIL CHANGED
        |--------------------------------------------------------------------------
        |
        | Remove every active login session belonging to this client.
        |
        */

        if ($emailChanged) {
            DB::table('sessions')
                ->where('user_id', $client->id)
                ->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE RESERVATIONS / PURCHASE ACCOUNTS
        |--------------------------------------------------------------------------
        |
        | The edit form now contains every purchase account instead of only
        | the first purchase account.
        |
        */

        foreach (($this->editForm['accounts'] ?? []) as $accountForm) {

            $account = $client
                ->purchaseAccounts()
                ->with('reservation')
                ->findOrFail($accountForm['id']);

            $reservation = $account?->reservation;

            if ($reservation) {
                $reservation->update([
                    'status' => $accountForm['reservation_status'] ?? $reservation->status,
                ]);
            }

            $account->update([
                'total_paid' => $accountForm['total_paid'] ?? $account->total_paid,
                'status' => $accountForm['account_status'] ?? $account->status,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE ONE COMBINED CLIENT NOTIFICATION
        |--------------------------------------------------------------------------
        */

        if (! empty($diff)) {

            $changedFields = collect($diff)
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

            $notification = SystemNotification::create([
                'title' => 'Account Information Updated',

                'message' =>
                    "{$actor->name} has made changes to your {$changesText}.",

                'type' => 'client_account_updated_by_staff',

                'url' => route('client.account'),

                'data' => [
                    'user_id' => $client->id,
                    'user_name' => $client->name,
                    'user_email' => $client->email,
                    'role' => $client->role,

                    'changes' => $diff,

                    'updated_by' => $actor->id,
                    'updated_by_name' => $actor->name,
                ],

                'created_by' => $actor->id,
            ]);

            /*
            * Only notify the affected client.
            */
            $notification->users()->attach([
                $client->id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN / STAFF SUCCESS TOAST
        |--------------------------------------------------------------------------
        */

        Notification::make()
            ->title('Client Updated Successfully')
            ->success()
            ->send();

        $this->dispatch(
            'closeModal',
            name: 'editClientModal'
        );
    }

    public function confirmDeleteClient($clientId)
    {
        $client = User::where('role', 'user')->findOrFail($clientId);

        $this->dialog()->confirm([
            'title' => 'Delete Client?',
            'description' => "Are you sure you want to delete <span class='text-red-600 font-semibold underline'>{$client->name}</span>?",
            'acceptLabel' => 'Yes, delete it',
            'rejectLabel' => 'Cancel',
            'method' => 'deleteClient',
            'params' => $clientId,
            'icon' => 'error',
        ]);
    }

    public function deleteClient($clientId)
    {
        User::where('role', 'user')->findOrFail($clientId)->delete();

        Notification::make()
            ->title('Client Deleted Successfully')
            ->danger()
            ->send();
    }

    private function clientQuery()
    {
        return User::query()
            ->where('role', 'user')
            ->with([
                'info',
                'purchaseAccounts.lot',
                'purchaseAccounts.houseModel',
                'purchaseAccounts.billings',
                'purchaseAccounts.billings.payments',
                'purchaseAccounts.ledgers',
                'purchaseAccounts.reservation.requiredDocuments',
                'purchaseAccounts.reservation.reservationPayments',
                'purchaseAccounts.reservation.lot',
                'purchaseAccounts.reservation.houseModel',
            ]);
    }

    public function loadRegions()
    {
        $this->regions = PHRegions::orderBy('region_description')
            ->pluck('region_description')
            ->toArray();

        if (
            !empty($this->editForm['region']) &&
            !in_array($this->editForm['region'], $this->regions)
        ) {
            array_unshift($this->regions, $this->editForm['region']);
        }
    }

    public function loadProvinces()
    {
        $this->getRegionCode();

        $this->provinces = $this->regionCode
            ? PHProvinces::where('region_code', $this->regionCode)
                ->orderBy('province_description')
                ->pluck('province_description')
                ->toArray()
            : [];

        if (
            !empty($this->editForm['province']) &&
            !in_array($this->editForm['province'], $this->provinces)
        ) {
            array_unshift($this->provinces, $this->editForm['province']);
        }
    }

    public function loadMunicipalities()
    {
        $this->getProvinceCode();

        $this->municipalities = $this->provinceCode
            ? PHCities::where('province_code', $this->provinceCode)
                ->orderBy('city_municipality_description')
                ->pluck('city_municipality_description')
                ->toArray()
            : [];

        if (
            !empty($this->editForm['municipality']) &&
            !in_array($this->editForm['municipality'], $this->municipalities)
        ) {
            array_unshift($this->municipalities, $this->editForm['municipality']);
        }
    }

    public function loadBarangays()
    {
        $this->getMunicipalityCode();

        $this->barangays = $this->municipalityCode
            ? PHBarangays::where('city_municipality_code', $this->municipalityCode)
                ->orderBy('barangay_description')
                ->pluck('barangay_description')
                ->toArray()
            : [];

        if (
            !empty($this->editForm['barangay']) &&
            !in_array($this->editForm['barangay'], $this->barangays)
        ) {
            array_unshift($this->barangays, $this->editForm['barangay']);
        }
    }

    public function render()
    {
        $clients = $this->clientQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhereHas('info', function ($info) {
                            $info->where('phone', 'like', "%{$this->search}%")
                                ->orWhere('first_name', 'like', "%{$this->search}%")
                                ->orWhere('last_name', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('purchaseAccounts.lot', function ($lot) {
                            $lot->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, function ($query) {
                $query->whereHas('purchaseAccounts', function ($account) {
                    if ($this->status === 'fully_paid') {
                        $account->whereIn('status', [
                            'fully_paid',
                            'bank_processing',
                        ]);

                        return;
                    }

                    $account->where('status', $this->status);
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.fil-pages.client-records', [
            'clients' => $clients,
            'totalClients' => User::where('role', 'user')->count(),
            'activeClients' => User::where('role', 'user')
                ->whereHas('purchaseAccounts', fn ($q) => $q->where('status', 'active'))
                ->count(),
            'pendingClients' => User::where('role', 'user')
                ->whereHas('purchaseAccounts', fn ($q) => $q->where('status', 'downpayment_pending'))
                ->count(),
        ]);
    }
}