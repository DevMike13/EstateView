<main class="flex-1 min-w-0">
    <div class="w-full mx-auto space-y-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100">
                Client Records
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                View client information, reservations, documents, payments, and account balances.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4">
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search clients by name, email, phone, or lot..."
                class="w-full rounded-lg border-gray-300 bg-white text-gray-900
                    placeholder:text-gray-400
                    dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100
                    dark:placeholder:text-gray-500"
            >

            <select wire:model.live="status" class="rounded-lg border-gray-300 bg-white text-gray-900
                dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">All Status</option>
                <option value="downpayment_pending">Downpayment Pending</option>
                <option value="fully_paid">Fully Paid</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <div class="grid sm:grid-cols-3 gap-6">

            {{-- Total Clients --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor"
                            class="h-5 w-5 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>

                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $totalClients }}
                    </div>
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Total Clients
                </div>
            </div>

            {{-- Active Clients --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor"
                            class="h-5 w-5 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>

                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $activeClients }}
                    </div>
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Active Clients
                </div>
            </div>

            {{-- Pending Clients --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor"
                            class="h-5 w-5 text-orange-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>

                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $pendingClients }}
                    </div>
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Pending Clients
                </div>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Client</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Property</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Total Paid</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Balance</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Joined</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Details</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($clients as $client)
                            @php
                                $account = $client->purchaseAccounts->first();
                                $reservation = $account?->reservation;
                                $lot = $account?->lot ?? $reservation?->lot;
                                $houseModel = $account?->houseModel ?? $reservation?->houseModel;

                                $fullName = trim(
                                    ($client->info?->first_name ?? '') . ' ' .
                                    ($client->info?->middle_name ?? '') . ' ' .
                                    ($client->info?->last_name ?? '')
                                );

                                $displayName = $fullName ?: $client->name;

                                $displayStatus = $account?->status ?? $reservation?->status ?? 'no_record';

                                $statusColor = match($displayStatus) {
                                    'active' => 'bg-green-100 text-green-700',
                                    'fully_paid' => 'bg-blue-100 text-blue-700',
                                    'downpayment_pending', 'bank_processing' => 'bg-yellow-100 text-yellow-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 align-top">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $displayName }}
                                    </div>

                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $client->email }}
                                    </div>

                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $client->info?->phone ?? 'No phone' }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ collect([
                                            $client->info?->barangay,
                                            $client->info?->municipality,
                                            $client->info?->province,
                                        ])->filter()->join(', ') ?: 'No address' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($account || $reservation || $lot || $houseModel)
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $houseModel?->model_name ?? 'Lot Only' }}

                                            @if($lot)
                                                - {{ $lot->name }}
                                            @endif
                                        </div>

                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $reservation?->type ?? 'No reservation type' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            Lot Status: {{ ucfirst($lot?->status ?? 'N/A') }}
                                        </div>
                                    @else
                                        <div class="border border-dashed border-gray-300 rounded-lg p-3 text-center">
                                            <div class="text-sm font-medium text-gray-500">
                                                No Property Record Yet
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                This client has no reservation or purchase account.
                                            </div>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 font-semibold">
                                    ₱{{ number_format($account?->total_paid ?? 0, 2) }}
                                </td>

                                <td class="px-6 py-4 font-semibold text-orange-600">
                                    ₱{{ number_format($account?->remaining_balance ?? 0, 2) }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                                        {{ $displayStatus === 'no_record' ? 'No Account' : ucfirst(str_replace('_', ' ', $displayStatus)) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $client->created_at->format('M d, Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm space-y-1">
                                        <div>Reservation: {{ ucfirst($reservation?->status ?? 'None') }}</div>
                                        <div>Docs: {{ $reservation?->requiredDocuments?->count() ?? 0 }}</div>
                                        <div>Reservation Payments: {{ $reservation?->reservationPayments?->count() ?? 0 }}</div>
                                        <div>Billings: {{ $account?->billings?->count() ?? 0 }}</div>
                                        <div>Ledgers: {{ $account?->ledgers?->count() ?? 0 }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2 w-28">

                                        <x-button
                                            sm
                                            blue
                                            icon="eye"
                                            label="View"
                                            wire:click="viewClient({{ $client->id }})"
                                            x-on:click="$openModal('viewClientModal')"
                                            rounded
                                            class="w-full"
                                        />

                                        <x-button
                                            sm
                                            gray
                                            icon="pencil"
                                            label="Edit"
                                            wire:click="editClient({{ $client->id }})"
                                            x-on:click="$openModal('editClientModal')"
                                            rounded
                                            class="w-full"
                                        />

                                        <x-button
                                            sm
                                            red
                                            icon="trash"
                                            label="Delete"
                                            wire:click="confirmDeleteClient({{ $client->id }})"
                                            rounded
                                            class="w-full"
                                        />

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    No client records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $clients->links() }}
            </div>
        </div>
    </div>

    <x-modal blur name="viewClientModal" max-width="5xl">
        <x-card title="Client Details">
            @if($selectedClient)
                @php
                    $accounts = $selectedClient->purchaseAccounts;
                @endphp

                <div class="space-y-6">

                    {{-- Personal Information --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Personal Information
                        </h4>

                        <div class="grid sm:grid-cols-2 gap-4 text-sm">
                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Full Name</div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $selectedClient->name }}
                                </div>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Email</div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $selectedClient->email ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Phone</div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $selectedClient->info?->phone ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">State</div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $selectedClient->info?->state ?? 'Philippines' }}
                                </div>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Region</div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $selectedClient->info?->region ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Province</div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $selectedClient->info?->province ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Municipality</div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $selectedClient->info?->municipality ?? 'N/A' }}
                                </div>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Barangay</div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $selectedClient->info?->barangay ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Property Records --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Property Records
                        </h4>

                        @if($accounts->count())
                            <div class="space-y-3">
                                @foreach($accounts as $account)
                                    @php
                                        $reservation = $account?->reservation;
                                        $lot = $account?->lot ?? $reservation?->lot;
                                        $houseModel = $account?->houseModel ?? $reservation?->houseModel;

                                        $hasPropertyRecord = $account || $reservation || $lot || $houseModel;

                                        $reservationStatusColor = match($reservation?->status) {
                                            'approved' => 'bg-green-100 text-green-700',
                                            'pending', 'awaiting_reservation_fee', 'reservation_fee_submitted' => 'bg-yellow-100 text-yellow-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };

                                        $accountStatusColor = match($account?->status) {
                                            'active', 'downpayment_paid' => 'bg-green-100 text-green-700',
                                            'completed' => 'bg-blue-100 text-blue-700',
                                            'downpayment_pending', 'downpayment_ongoing', 'loan_processing' => 'bg-yellow-100 text-yellow-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp

                                    <div
                                        x-data="{ openProperty: false }"
                                        class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
                                    >
                                        <button
                                            type="button"
                                            x-on:click="openProperty = !openProperty"
                                            class="flex w-full items-center justify-between gap-4 p-4 text-left transition hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                        >
                                            <div class="min-w-0">
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $lot?->name ?? 'Unknown Lot' }}
                                                </div>

                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $houseModel?->model_name ?? 'Lot Only' }}
                                                </div>
                                            </div>

                                            <x-heroicon-o-chevron-down
                                                class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200"
                                                x-bind:class="openProperty ? 'rotate-180' : ''"
                                            />
                                        </button>

                                        <div
                                            x-show="openProperty"
                                            x-collapse
                                            class="border-t border-gray-200 dark:border-gray-700"
                                        >
                                            <div class="space-y-6 p-4">

                    {{-- Property Information --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Property Information
                        </h4>

                        @if($hasPropertyRecord)
                            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Property</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $houseModel?->model_name ?? 'Lot Only' }}
                                        @if($lot)
                                            - {{ $lot->name }}
                                        @endif
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Lot Number</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $lot?->name ?? 'N/A' }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">House Model</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $houseModel?->model_name ?? 'None (Lot Only)' }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Reservation Type</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $reservation?->type ?? 'N/A' }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Reservation Status</div>
                                    <span class="inline-flex mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $reservationStatusColor }}">
                                        {{ $reservation?->status ? ucfirst(str_replace('_', ' ', $reservation->status)) : 'N/A' }}
                                    </span>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Account Status</div>
                                    <span class="inline-flex mt-1 px-3 py-1 rounded-full text-xs font-medium {{ $accountStatusColor }}">
                                        {{ $account?->status ? ucfirst(str_replace('_', ' ', $account->status)) : 'N/A' }}
                                    </span>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Lot Status</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $lot?->status ? ucfirst(str_replace('_', ' ', $lot->status)) : 'N/A' }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Reserved At</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $reservation?->reserved_at ? \Carbon\Carbon::parse($reservation->reserved_at)->format('M d, Y') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="border border-dashed border-gray-300 rounded-xl p-6 text-center">
                                <div class="text-sm font-semibold text-gray-600">
                                    No Property Record Yet
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    This client has no reservation, lot, house model, or purchase account yet.
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Payment Information --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Payment Information
                        </h4>

                        @if($account)
                            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Payment Scheme</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $account->payment_scheme ? ucfirst(str_replace('_', ' ', $account->payment_scheme)) : 'N/A' }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Payment Plan</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $account->loan_term_years ? 'Monthly - ' . $account->loan_term_years . ' years' : 'N/A' }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Lot Price</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format($account->lot_price ?? 0, 2) }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">House Price</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format($account->house_price ?? 0, 2) }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Total Contract Price</div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format($account->total_contract_price ?? 0, 2) }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Net Contract Price</div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format($account->net_contract_price ?? 0, 2) }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Downpayment Amount</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format($account->downpayment_amount ?? 0, 2) }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Remaining Downpayment</div>
                                    <div class="font-medium text-orange-600">
                                        ₱{{ number_format($account->remaining_downpayment ?? 0, 2) }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Loanable Amount</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format($account->loanable_amount ?? 0, 2) }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Monthly Amortization</div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format($account->monthly_amortization ?? 0, 2) }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Total Paid</div>
                                    <div class="font-semibold text-green-700">
                                        ₱{{ number_format($account->total_paid ?? 0, 2) }}
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <div class="text-xs text-gray-500">Remaining Balance</div>
                                    <div class="font-semibold text-orange-600">
                                        ₱{{ number_format($account->remaining_balance ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="border border-dashed border-gray-300 rounded-xl p-6 text-center">
                                <div class="text-sm font-semibold text-gray-600">
                                    No Payment Record Yet
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    This client has no purchase account or payment breakdown yet.
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Documents / Payments / Ledger Summary --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Records Summary
                        </h4>

                        @if($reservation || $account)
                            <div class="grid sm:grid-cols-4 gap-4 text-sm">
                                <div class="rounded-lg border border-gray-200 p-3 text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $reservation?->requiredDocuments?->count() ?? 0 }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Documents
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3 text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $reservation?->reservationPayments?->count() ?? 0 }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Reservation Payments
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3 text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $account?->billings?->count() ?? 0 }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Billings
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3 text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $account?->ledgers?->count() ?? 0 }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Ledgers
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="border border-dashed border-gray-300 rounded-xl p-6 text-center">
                                <div class="text-sm font-semibold text-gray-600">
                                    No Records Yet
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    Documents, reservation payments, billings, and ledger records will appear here once available.
                                </div>
                            </div>
                        @endif
                        {{-- Billing Payment Receipts --}}
                        @if($account)

                            @php
                                $paymentReceipts = $account->billings
                                    ->flatMap(fn ($billing) => $billing->payments)
                                    ->sortByDesc('paid_at');
                            @endphp

                            <div
                                x-data="{ openReceipts: false }"
                                class="mt-4 overflow-hidden rounded-xl border border-gray-200 bg-white"
                            >
                                {{-- SECTION HEADER --}}
                                <button
                                    type="button"
                                    x-on:click="openReceipts = !openReceipts"
                                    class="flex w-full items-center justify-between gap-4 p-4 text-left transition hover:bg-gray-50"
                                >
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            Billing Payment Receipts
                                        </h4>

                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{ $paymentReceipts->count() }}
                                            payment record{{ $paymentReceipts->count() !== 1 ? 's' : '' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="hidden text-xs text-gray-400 sm:inline">
                                            View payment history
                                        </span>

                                        <x-heroicon-o-chevron-down
                                            class="h-5 w-5 text-gray-400 transition-transform duration-200"
                                            x-bind:class="openReceipts ? 'rotate-180' : ''"
                                        />
                                    </div>
                                </button>

                                {{-- RECEIPTS --}}
                                <div
                                    x-show="openReceipts"
                                    x-collapse
                                    class="border-t border-gray-100"
                                >
                                    @if($paymentReceipts->count())
                                        <div class="space-y-2 bg-gray-50/50 p-4">
                                            @foreach($paymentReceipts as $payment)
                                                @php
                                                    $paymentStatusColor = match($payment->status) {
                                                        'verified', 'paid', 'approved'
                                                            => 'bg-green-100 text-green-700',

                                                        'pending'
                                                            => 'bg-yellow-100 text-yellow-700',

                                                        'rejected', 'declined'
                                                            => 'bg-red-100 text-red-700',

                                                        default
                                                            => 'bg-gray-100 text-gray-700',
                                                    };

                                                    $sourceLabel = match($payment->source) {
                                                        'office_payment' => 'Office Payment',
                                                        'client_upload' => 'Client Upload',

                                                        default => $payment->source
                                                            ? ucfirst(
                                                                str_replace(
                                                                    '_',
                                                                    ' ',
                                                                    $payment->source
                                                                )
                                                            )
                                                            : 'N/A',
                                                    };
                                                @endphp

                                                {{-- INDIVIDUAL PAYMENT --}}
                                                <div
                                                    x-data="{ open: false }"
                                                    class="overflow-hidden rounded-xl border border-gray-200 bg-white"
                                                >
                                                    {{-- COMPACT PAYMENT ROW --}}
                                                    <button
                                                        type="button"
                                                        x-on:click="open = !open"
                                                        class="flex w-full items-center justify-between gap-4 p-4 text-left transition hover:bg-gray-50"
                                                    >
                                                        <div class="flex min-w-0 flex-1 items-center gap-3">
                                                            {{-- PAYMENT ICON --}}
                                                            <div class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 sm:flex">
                                                                <x-heroicon-o-banknotes
                                                                    class="h-5 w-5 text-gray-500"
                                                                />
                                                            </div>

                                                            <div class="min-w-0">
                                                                <div class="flex flex-wrap items-center gap-2">
                                                                    <p class="truncate font-semibold text-gray-900 dark:text-gray-100">
                                                                        {{ $payment->billing?->title ?? 'Billing Payment' }}
                                                                    </p>

                                                                    <span
                                                                        class="shrink-0 rounded-full px-2 py-1 text-[10px] font-medium {{ $paymentStatusColor }}"
                                                                    >
                                                                        {{ ucfirst(
                                                                            str_replace(
                                                                                '_',
                                                                                ' ',
                                                                                $payment->status ?? 'unknown'
                                                                            )
                                                                        ) }}
                                                                    </span>
                                                                </div>

                                                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-400">
                                                                    <span>
                                                                        {{ $payment->paid_at
                                                                            ? $payment->paid_at->format('M d, Y')
                                                                            : 'No payment date'
                                                                        }}
                                                                    </span>

                                                                    <span class="hidden sm:inline">
                                                                        •
                                                                    </span>

                                                                    <span>
                                                                        {{ $payment->payment_method
                                                                            ? Str::headline($payment->payment_method)
                                                                            : 'N/A'
                                                                        }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="flex shrink-0 items-center gap-4">
                                                            {{-- AMOUNT --}}
                                                            <div class="hidden text-right sm:block">
                                                                <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                                    Amount
                                                                </div>

                                                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                                    ₱{{ number_format(
                                                                        $payment->amount ?? 0,
                                                                        2
                                                                    ) }}
                                                                </div>
                                                            </div>

                                                            {{-- ARROW --}}
                                                            <x-heroicon-o-chevron-down
                                                                class="h-5 w-5 text-gray-400 transition-transform duration-200"
                                                                x-bind:class="open ? 'rotate-180' : ''"
                                                            />
                                                        </div>
                                                    </button>

                                                    {{-- EXPANDED PAYMENT DETAILS --}}
                                                    <div
                                                        x-show="open"
                                                        x-collapse
                                                        class="border-t border-gray-100 bg-gray-50/60 p-4"
                                                    >
                                                        {{-- MOBILE AMOUNT --}}
                                                        <div class="mb-4 sm:hidden">
                                                            <div class="text-xs text-gray-400">
                                                                Amount
                                                            </div>

                                                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                                ₱{{ number_format(
                                                                    $payment->amount ?? 0,
                                                                    2
                                                                ) }}
                                                            </div>
                                                        </div>

                                                        <div class="grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                                                            <div>
                                                                <div class="text-xs text-gray-400">
                                                                    Payment Method
                                                                </div>

                                                                <div class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                                                                    {{ $payment->payment_method
                                                                        ? Str::headline($payment->payment_method)
                                                                        : 'N/A'
                                                                    }}
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <div class="text-xs text-gray-400">
                                                                    Source
                                                                </div>

                                                                <div class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                                                                    {{ $sourceLabel }}
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <div class="text-xs text-gray-400">
                                                                    Reference Number
                                                                </div>

                                                                <div class="mt-1 break-all font-medium text-gray-900 dark:text-gray-100">
                                                                    {{ $payment->reference_no ?? 'N/A' }}
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <div class="text-xs text-gray-400">
                                                                    Paid At
                                                                </div>

                                                                <div class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                                                                    {{ $payment->paid_at
                                                                        ? $payment->paid_at->format(
                                                                            'M d, Y h:i A'
                                                                        )
                                                                        : 'N/A'
                                                                    }}
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <div class="text-xs text-gray-400">
                                                                    Verified At
                                                                </div>

                                                                <div class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                                                                    {{ $payment->verified_at
                                                                        ? $payment->verified_at->format(
                                                                            'M d, Y h:i A'
                                                                        )
                                                                        : 'N/A'
                                                                    }}
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <div class="text-xs text-gray-400">
                                                                    Status
                                                                </div>

                                                                <div class="mt-1">
                                                                    <span
                                                                        class="inline-flex rounded-full px-2 py-1 text-[10px] font-medium {{ $paymentStatusColor }}"
                                                                    >
                                                                        {{ Str::headline(
                                                                            $payment->status ?? 'unknown'
                                                                        ) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- REMARKS --}}
                                                        @if($payment->remarks)
                                                            <div class="mt-4 rounded-lg border border-gray-100 bg-white p-3">
                                                                <div class="text-xs text-gray-400">
                                                                    Remarks
                                                                </div>

                                                                <div class="mt-1 text-sm text-gray-700">
                                                                    {{ $payment->remarks }}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- RECEIPT ACTION --}}
                                                        <div class="mt-4 flex justify-end">
                                                            @if($payment->proof_of_payment)
                                                                <a
                                                                    href="{{ asset(
                                                                        'storage/' .
                                                                        $payment->proof_of_payment
                                                                    ) }}"
                                                                    target="_blank"
                                                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-100"
                                                                >
                                                                    <x-heroicon-o-document-magnifying-glass
                                                                        class="h-4 w-4"
                                                                    />

                                                                    View Receipt
                                                                </a>
                                                            @else
                                                                <span class="text-xs text-gray-400">
                                                                    No receipt available
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="p-6 text-center">
                                            <x-heroicon-o-receipt-percent
                                                class="mx-auto mb-2 h-8 w-8 text-gray-300"
                                            />

                                            <div class="text-sm font-semibold text-gray-600">
                                                No Billing Payment Receipts Yet
                                            </div>

                                            <div class="mt-1 text-xs text-gray-400">
                                                Billing payment receipts will appear here once submitted.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        {{-- Required Documents --}}
                        @if($reservation)

                            @php
                                $requiredDocuments = $reservation->requiredDocuments
                                    ->sortBy('document_type');
                            @endphp

                            <div
                                x-data="{ openDocuments: false }"
                                class="mt-4 overflow-hidden rounded-xl border border-gray-200 bg-white"
                            >
                                {{-- SECTION HEADER --}}
                                <button
                                    type="button"
                                    x-on:click="openDocuments = !openDocuments"
                                    class="flex w-full items-center justify-between gap-4 p-4 text-left transition hover:bg-gray-50"
                                >
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            Required Documents
                                        </h4>

                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{ $requiredDocuments->count() }}
                                            document{{ $requiredDocuments->count() !== 1 ? 's' : '' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="hidden text-xs text-gray-400 sm:inline">
                                            View submitted documents
                                        </span>

                                        <x-heroicon-o-chevron-down
                                            class="h-5 w-5 text-gray-400 transition-transform duration-200"
                                            x-bind:class="openDocuments ? 'rotate-180' : ''"
                                        />
                                    </div>
                                </button>

                                {{-- DOCUMENT LIST --}}
                                <div
                                    x-show="openDocuments"
                                    x-collapse
                                    class="border-t border-gray-100"
                                >
                                    @if($requiredDocuments->count())
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-gray-50/50 p-4">
                                            @foreach($requiredDocuments as $document)
                                                <div
                                                    class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-white p-3"
                                                >
                                                    <div class="flex min-w-0 items-center gap-2">
                                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-gray-100">
                                                            <x-heroicon-o-document
                                                                class="h-4 w-4 text-gray-500"
                                                            />
                                                        </div>

                                                        <div class="min-w-0">
                                                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                                {{ Str::headline($document->document_type) }}
                                                            </p>

                                                            <p
                                                                class="mt-0.5 max-w-[150px] truncate text-[10px] text-gray-400"
                                                                title="{{ $document->original_name }}"
                                                            >
                                                                {{ $document->original_name ?? 'Uploaded document' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    @if($document->file_path)
                                                        <a
                                                            href="{{ asset('storage/' . $document->file_path) }}"
                                                            target="_blank"
                                                            class="inline-flex shrink-0 items-center justify-center gap-1 rounded-md border border-gray-200 px-2 py-1.5 text-[10px] font-medium text-gray-700 transition hover:bg-gray-50"
                                                        >
                                                            <x-heroicon-o-eye class="h-3.5 w-3.5" />

                                                            View
                                                        </a>
                                                    @else
                                                        <span class="shrink-0 text-[10px] text-gray-400">
                                                            No file
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="p-6 text-center">
                                            <x-heroicon-o-document
                                                class="mx-auto mb-2 h-8 w-8 text-gray-300"
                                            />

                                            <div class="text-sm font-semibold text-gray-600">
                                                No Required Documents Yet
                                            </div>

                                            <div class="mt-1 text-xs text-gray-400">
                                                Submitted reservation documents will appear here.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="border border-dashed border-gray-300 rounded-xl p-6 text-center">
                                <div class="text-sm font-semibold text-gray-600">
                                    No Property Record Yet
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    This client has no reservation, lot, house model, or purchase account yet.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-button flat label="Close" x-on:click="close" />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <x-modal blur name="editClientModal" max-width="2xl">
        <x-card>
            <div class="flex items-center justify-between mb-4 md:mb-6">
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit Client
                </h3>

                <button
                    type="button"
                    x-on:click="close"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    ✕
                </button>
            </div>

            <form wire:submit.prevent="confirmUpdateClient" class="space-y-6">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Personal Information
                    </h4>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <x-input
                            label="First Name"
                            wire:model.defer="editForm.first_name"
                        />

                        <x-input
                            label="Middle Name"
                            wire:model.defer="editForm.middle_name"
                        />

                        <x-input
                            label="Last Name"
                            wire:model.defer="editForm.last_name"
                        />

                        <x-input
                            label="Suffix"
                            wire:model.defer="editForm.suffix"
                        />
                        <x-input label="Email" type="email" wire:model.defer="editForm.email" />
                        <x-input label="Phone" wire:model.defer="editForm.phone" />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Select Region
                            </label>
                            <select wire:model.live="editForm.region" class="w-full h-10 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region }}" @selected($editForm['region'] === $region)>
                                        {{ $region }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Select Province
                            </label>
                            <select wire:model.live="editForm.province" class="w-full h-10 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-100 disabled:text-gray-500" @disabled(!$editForm['region'])>
                                <option value="">Select Province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province }}" @selected($editForm['province'] === $province)>
                                        {{ $province }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Select Municipality
                            </label>
                            <select wire:model.live="editForm.municipality" class="w-full h-10 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-100 disabled:text-gray-500" @disabled(!$editForm['province'])>
                                <option value="">Select Municipality</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality }}" @selected($editForm['municipality'] === $municipality)>
                                        {{ $municipality }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Select Barangay
                            </label>
                            <select wire:model.live="editForm.barangay" class="w-full h-10 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-100 disabled:text-gray-500" @disabled(!$editForm['municipality'])>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" @selected($editForm['barangay'] === $barangay)>
                                        {{ $barangay }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <x-input label="State" wire:model.defer="editForm.state" readonly />
                    </div>
                </div>
                @if(!empty($editForm['accounts']))
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Property Records
                        </h4>

                        <div class="space-y-3">
                            @foreach($editForm['accounts'] as $accountIndex => $accountForm)
                                <div
                                    wire:key="edit-client-account-{{ $accountForm['id'] }}"
                                    x-data="{ openProperty: true }"
                                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
                                >
                                    <button
                                        type="button"
                                        x-on:click="openProperty = !openProperty"
                                        class="flex w-full items-center justify-between gap-4 p-4 text-left transition hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                    >
                                        <div class="min-w-0">
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $accountForm['lot_number'] ?: 'Unknown Lot' }}
                                            </div>

                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ $accountForm['house_model'] ?: 'Lot Only' }}
                                            </div>
                                        </div>

                                        <x-heroicon-o-chevron-down
                                            class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200"
                                            x-bind:class="openProperty ? 'rotate-180' : ''"
                                        />
                                    </button>

                                    <div
                                        x-show="openProperty"
                                        x-collapse
                                        class="border-t border-gray-200 dark:border-gray-700"
                                    >
                                        <div class="space-y-6 p-4">

                                            {{-- Property Information --}}
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                                    Property Information
                                                </h4>

                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <x-input
                                                        label="Property"
                                                        value="{{ $accountForm['property'] }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Lot Number"
                                                        value="{{ $accountForm['lot_number'] ?: 'N/A' }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="House Model"
                                                        value="{{ $accountForm['house_model'] ?: 'None (Lot Only)' }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Reservation Type"
                                                        value="{{ $accountForm['reservation_type'] ?: 'N/A' }}"
                                                        readonly
                                                    />

                                                    @if($accountForm['has_reservation'])
                                                        <x-select
                                                            label="Reservation Status"
                                                            wire:model.defer="editForm.accounts.{{ $accountIndex }}.reservation_status"
                                                            :options="[
                                                                ['name' => 'Pending', 'id' => 'pending'],
                                                                ['name' => 'Awaiting Reservation Fee', 'id' => 'awaiting_reservation_fee'],
                                                                ['name' => 'Reservation Fee Submitted', 'id' => 'reservation_fee_submitted'],
                                                                ['name' => 'Approved', 'id' => 'approved'],
                                                                ['name' => 'Rejected', 'id' => 'rejected'],
                                                            ]"
                                                            option-label="name"
                                                            option-value="id"
                                                        />
                                                    @else
                                                        <x-input
                                                            label="Reservation Status"
                                                            value="N/A"
                                                            readonly
                                                        />
                                                    @endif

                                                    <x-select
                                                        label="Account Status"
                                                        wire:model.defer="editForm.accounts.{{ $accountIndex }}.account_status"
                                                        :options="[
                                                            ['name' => 'Downpayment Pending', 'id' => 'downpayment_pending'],
                                                            // ['name' => 'Downpayment Ongoing', 'id' => 'downpayment_ongoing'],
                                                            // ['name' => 'Downpayment Paid', 'id' => 'downpayment_paid'],
                                                            // ['name' => 'Loan Processing', 'id' => 'loan_processing'],
                                                            // ['name' => 'Bank Processing', 'id' => 'bank_processing'],
                                                            // ['name' => 'Active', 'id' => 'active'],
                                                            ['name' => 'Fully Paid', 'id' => 'fully_paid'],
                                                            // ['name' => 'Completed', 'id' => 'completed'],
                                                            ['name' => 'Cancelled', 'id' => 'cancelled'],
                                                        ]"
                                                        option-label="name"
                                                        option-value="id"
                                                    />

                                                    <x-input
                                                        label="Lot Status"
                                                        value="{{ $accountForm['lot_status'] ? \Illuminate\Support\Str::headline($accountForm['lot_status']) : 'N/A' }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Reserved At"
                                                        value="{{ $accountForm['reserved_at'] ? \Carbon\Carbon::parse($accountForm['reserved_at'])->format('M d, Y') : 'N/A' }}"
                                                        readonly
                                                    />
                                                </div>
                                            </div>

                                            {{-- Payment Information --}}
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                                    Payment Information
                                                </h4>

                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <x-input
                                                        label="Payment Scheme"
                                                        value="{{ $accountForm['payment_scheme'] ? \Illuminate\Support\Str::headline($accountForm['payment_scheme']) : 'N/A' }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Payment Plan"
                                                        value="{{ $accountForm['payment_plan'] ?: 'N/A' }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Lot Price (₱)"
                                                        value="{{ number_format($accountForm['lot_price'] ?? 0, 2, '.', '') }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="House Price (₱)"
                                                        value="{{ number_format($accountForm['house_price'] ?? 0, 2, '.', '') }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Total Contract Price (₱)"
                                                        value="{{ number_format($accountForm['total_price'] ?? 0, 2, '.', '') }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Net Contract Price (₱)"
                                                        value="{{ number_format($accountForm['net_contract_price'] ?? 0, 2, '.', '') }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Downpayment Amount (₱)"
                                                        value="{{ number_format($accountForm['downpayment_amount'] ?? 0, 2, '.', '') }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Remaining Downpayment (₱)"
                                                        value="{{ number_format($accountForm['remaining_downpayment'] ?? 0, 2, '.', '') }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Loanable Amount (₱)"
                                                        value="{{ number_format($accountForm['loanable_amount'] ?? 0, 2, '.', '') }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Monthly Amortization (₱)"
                                                        value="{{ number_format($accountForm['monthly_amortization'] ?? 0, 2, '.', '') }}"
                                                        readonly
                                                    />

                                                    <x-input
                                                        label="Total Paid (₱)"
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        wire:model.defer="editForm.accounts.{{ $accountIndex }}.total_paid"
                                                    />

                                                    <x-input
                                                        label="Remaining Balance (₱)"
                                                        value="{{ number_format($accountForm['remaining_balance'] ?? 0, 2, '.', '') }}"
                                                        readonly
                                                    />
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="border border-dashed border-gray-300 rounded-xl p-6 text-center">
                        <div class="text-sm font-semibold text-gray-600">
                            No Records Yet
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            This client has no reservation, property, or payment account yet.
                        </div>
                    </div>
                @endif
                <div class="flex gap-3 pt-4">
                    <x-button
                        flat
                        label="Cancel"
                        class="flex-1"
                        x-on:click="close"
                    />

                    <x-button
                        blue
                        label="Save Changes"
                        type="submit"
                        class="flex-1"
                        spinner="confirmUpdateClient"
                    />
                </div>
            </form>
        </x-card>
    </x-modal>
</main>