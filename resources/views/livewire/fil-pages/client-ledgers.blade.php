<div class="w-full h-auto">
    <div class="w-full max-w-6xl mx-auto p-4">

        <div class="flex justify-between items-center mb-5">
            <h1 class="text-xl font-bold text-gray-900">
                Client Ledgers
            </h1>

            <x-button
                label="Create Ledger"
                icon="plus"
                x-on:click="$openModal('createLedger')"
                class="bg-[#101727] text-white"
            />
        </div>

        <div class="grid gap-4">

            @forelse($accounts as $account)

                <div class="bg-white border rounded-xl shadow-sm p-5">

                    <div class="flex justify-between gap-4 mb-4">
                        <div>
                            <div class="font-bold text-lg">
                                {{ $account->user->name }}
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $account->lot->name ?? 'N/A' }}
                                —
                                {{ Str::headline($account->payment_scheme) }}
                            </div>
                        </div>

                        <span class="px-3 py-1 flex justify-center items-center rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            {{ Str::headline($account->status) }}
                        </span>
                    </div>

                    <div class="grid md:grid-cols-5 gap-4 bg-gray-50 p-4 rounded-lg text-sm">

                        <div>
                            <div class="text-xs text-gray-500">TCP</div>
                            <div class="font-semibold">
                                ₱{{ number_format($account->total_contract_price, 2) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Reservation Fee</div>
                            <div class="font-semibold text-green-700">
                                ₱{{ number_format($account->reservation_fee_credit, 2) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Remaining DP</div>
                            <div class="font-semibold">
                                ₱{{ number_format($account->remaining_downpayment, 2) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Monthly Amortization</div>
                            <div class="font-semibold">
                                ₱{{ number_format($account->monthly_amortization, 2) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Remaining Balance</div>
                            <div class="font-semibold text-red-700">
                                ₱{{ number_format($account->remaining_balance, 2) }}
                            </div>
                        </div>

                    </div>

                    <div class="flex gap-2 mt-4">

                        {{-- <x-button
                            label="Record Office Payment"
                            icon="banknotes"
                            x-on:click="
                                $wire.accountId = {{ $account->id }};
                                $openModal('recordPayment')
                            "
                            class="bg-[#101727] text-white"
                        /> --}}

                        <x-button
                            label="Change Status"
                            icon="pencil-square"
                            x-on:click="
                                $wire.accountId = {{ $account->id }};
                                $wire.accountStatus = '{{ $account->status }}';
                                $openModal('changeStatus')
                            "
                            class="border border-gray-300 text-gray-700"
                        />

                    </div>

                    {{-- PAYMENT SCHEDULE --}}
                    <div
                        x-data="{ open: false }"
                        class="mt-5 border rounded-2xl overflow-hidden bg-white"
                    >
                        <button
                            type="button"
                            x-on:click="open = !open"
                            class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition"
                        >
                            <div>
                                <div class="font-semibold text-gray-900">
                                    Billing Schedule
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $account->billings->count() }} bill/s ·
                                    {{ $account->billings->where('status', 'unpaid')->count() }} unpaid ·
                                    {{ $account->billings->where('status', 'paid')->count() }} paid
                                </div>
                            </div>

                            <x-icon
                                name="chevron-down"
                                class="w-5 h-5 text-gray-500 transition-transform"
                                x-bind:class="open ? 'rotate-180' : ''"
                            />
                        </button>

                        <div x-show="open" x-collapse class="bg-[#F5F5F5] p-4">

                            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4 max-h-[600px] overflow-y-auto pr-2">

                                @forelse($account->billings->sortBy('due_date') as $billing)
                                    @php
                                        $firstPayableBilling = $account->billings
                                            ->whereIn('status', ['unpaid', 'partial'])
                                            ->sortBy('due_date')
                                            ->first();

                                        $isPayable = $firstPayableBilling
                                            && $firstPayableBilling->id === $billing->id;
                                    @endphp
                                    <div class="bg-white rounded-xl shadow-sm border p-4">

                                        <div class="flex items-start justify-between gap-3 mb-3">
                                            <div>
                                                <div class="text-lg font-semibold text-gray-900">
                                                    {{ $billing->due_date->format('M Y') }}
                                                </div>

                                                <div class="text-xs text-gray-500">
                                                    {{ $billing->title }}
                                                </div>
                                            </div>

                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                                {{ $billing->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $billing->status === 'partial' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $billing->status === 'unpaid' ? 'bg-orange-100 text-orange-700' : '' }}
                                                {{ $billing->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                            ">
                                                {{ Str::headline($billing->status) }}
                                            </span>
                                        </div>

                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Due Date</span>
                                                <span class="font-medium">
                                                    {{ $billing->due_date->format('M d, Y') }}
                                                </span>
                                            </div>

                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Amount Due</span>
                                                <span class="font-semibold">
                                                    ₱{{ number_format($billing->amount_due, 2) }}
                                                </span>
                                            </div>

                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Paid</span>
                                                <span class="font-semibold text-green-700">
                                                    ₱{{ number_format($billing->amount_paid, 2) }}
                                                </span>
                                            </div>

                                            <div class="flex justify-between border-t pt-2">
                                                <span class="text-gray-500">Balance</span>
                                                <span class="font-semibold text-red-700">
                                                    ₱{{ number_format($billing->amount_due - $billing->amount_paid, 2) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <x-button
                                                label="{{ $billing->status === 'paid' ? 'Paid' : 'Pay' }}"
                                                icon="banknotes"
                                                x-on:click="
                                                    $wire.accountId = {{ $account->id }};
                                                    $wire.billingId = {{ $billing->id }};
                                                    $wire.paymentAmount = {{ $billing->amount_due - $billing->amount_paid }};
                                                    $wire.paymentDescription = 'Payment for {{ $billing->title }}';
                                                    $openModal('recordPayment')
                                                "
                                                class="w-full bg-[#101727] text-white"
                                                :disabled="! $isPayable || $billing->status === 'paid'"
                                            />
                                        </div>

                                    </div>

                                @empty

                                    <div class="col-span-full text-center py-10 text-gray-400">
                                        No billing schedule found.
                                    </div>

                                @endforelse

                            </div>

                        </div>
                    </div>

                </div>

            @empty

                <div class="text-center py-10 border-2 border-dashed rounded-xl text-gray-400">
                    No client ledgers yet.
                </div>

            @endforelse

        </div>

    </div>

    <x-modal blur name="createLedger" align="center" max-width="2xl">

        <x-card title="Create Client Ledger">

            <x-select
                label="Approved Reservation"
                placeholder="Select approved reservation"
                wire:model.live="reservationId"
                :options="$this->approvedReservations->map(fn ($reservation) => [
                    'id' => $reservation->id,
                    'name' => $reservation->user->name . ' - ' . ($reservation->lot->name ?? 'Lot') . ' - ' . $reservation->type,
                ])->toArray()"
                option-label="name"
                option-value="id"
            />

            {{-- <x-select
                label="Payment Scheme"
                wire:model.live="paymentScheme"
                :options="[
                    ['id' => 'cash', 'name' => 'Cash'],
                    ['id' => 'bank_loan', 'name' => 'Bank Loan'],
                    ['id' => 'deferred_payment', 'name' => 'Deferred Payment - 36 Months'],
                ]"
                option-label="name"
                option-value="id"
            /> --}}

           @php
                $selectedReservation = $this->approvedReservations->firstWhere('id', $reservationId);
                $selectedPayment = $selectedReservation?->preferredPayment?->payment_type;
            @endphp

            @if($selectedReservation)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <div class="text-xs text-gray-500">Client Preferred Payment</div>
                    <div class="font-semibold">
                        {{ Str::headline($selectedPayment) }}
                    </div>
                </div>
            @endif

            @if(in_array($selectedPayment, ['bank_loan', 'Loanable', 'Bank Loan']))
                <div class="mt-4">
                    <x-input
                        label="Loan Term Years"
                        type="number"
                        wire:model.live="loanTermYears"
                    />
                </div>
            @endif

            <div class="mt-4">
                <x-input
                    label="First Due Date"
                    type="date"
                    wire:model="dueDate"
                />
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-button flat label="Cancel" x-on:click="close" />

                    <x-button
                        primary
                        label="Create Ledger"
                        wire:click="createLedger"
                    />
                </div>
            </x-slot>

        </x-card>

    </x-modal>

    <x-modal blur name="recordPayment" align="center">

        <x-card title="Record Office Payment">

            <x-input
                label="Payment Amount"
                type="number"
                wire:model="paymentAmount"
            />

            <div class="mt-4">
                <x-input
                    label="Description"
                    placeholder="Example: Office cash payment"
                    wire:model="paymentDescription"
                />
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-button flat label="Cancel" x-on:click="close" />

                    <x-button
                        primary
                        label="Save Payment"
                        wire:click="recordOfficePayment"
                    />
                </div>
            </x-slot>

        </x-card>

    </x-modal>

    <x-modal blur name="changeStatus" align="center">

        <x-card title="Change Account Status">

            <x-select
                label="Status"
                wire:model="accountStatus"
                :options="[
                    ['id' => 'active', 'name' => 'Active'],
                    ['id' => 'downpayment_pending', 'name' => 'Downpayment Pending'],
                    ['id' => 'bank_processing', 'name' => 'Bank Processing'],
                    ['id' => 'fully_paid', 'name' => 'Fully Paid'],
                    ['id' => 'cancelled', 'name' => 'Cancelled'],
                ]"
                option-label="name"
                option-value="id"
            />

            <x-slot name="footer">
                <div class="flex justify-end gap-2">
                    <x-button flat label="Cancel" x-on:click="close" />

                    <x-button
                        primary
                        label="Update Status"
                        wire:click="updateAccountStatus"
                    />
                </div>
            </x-slot>

        </x-card>

    </x-modal>
</div>