<main>
    <div class="min-h-screen bg-[#F5F5F5] pb-8">

        {{-- HEADER --}}
        {{-- <div class="bg-white pt-8 pb-6 px-6 sticky top-0 z-10">

            <div class="text-center mb-8">
                <h1 class="text-4xl lg:text-5xl font-light text-gray-900">
                    My Bills
                </h1>
            </div>

            @if($this->accounts->first())

                @php
                    $mainAccount =
                        $this->accounts->first();
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">

                    <div class="flex items-center justify-between gap-4">

                        <div>
                            <h2 class="text-lg font-light text-gray-900 mb-1">
                                {{ $mainAccount->reservation?->type ?? 'Property Account' }}
                            </h2>

                            <p class="text-sm font-light text-gray-600">
                                {{ Str::headline(
                                    $mainAccount->payment_scheme
                                ) }}
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="text-2xl font-light text-gray-900">
                                ₱{{ number_format(
                                    $mainAccount->remaining_balance,
                                    2
                                ) }}
                            </div>

                            <p class="text-sm font-light text-gray-600 mt-1">
                                Balance
                            </p>
                        </div>

                    </div>

                </div>

            @endif

        </div> --}}

        {{-- ACCOUNTS --}}
        <div class="px-6 pt-6 max-w-5xl mx-auto mt-28">

            @forelse($this->accounts as $account)

                @php
                    $billings = $account
                        ->billings
                        ->sortBy('due_date');

                    $firstPayableBilling =
                        $billings
                            ->whereIn(
                                'status',
                                ['unpaid', 'partial']
                            )
                            ->first();

                    $unpaidBillings =
                        $billings
                            ->whereIn(
                                'status',
                                ['unpaid', 'partial']
                            );

                    $paidBillings =
                        $billings
                            ->where(
                                'status',
                                'paid'
                            )
                            ->sortByDesc('due_date');

                    $currentBills =
                        $firstPayableBilling
                            ? collect([
                                $firstPayableBilling
                            ])
                            : collect();

                    $upcomingBills =
                        $unpaidBillings
                            ->filter(
                                fn ($billing) =>
                                    ! $firstPayableBilling
                                    || $billing->id
                                        !== $firstPayableBilling->id
                            );
                @endphp

                <div
                    x-data="{
                        tab: @js(
                            (int) $highlightAccount === (int) $account->id
                                ? $activeBillingTab
                                : 'unpaid'
                        )
                    }"
                    class="mb-8"
                >

                    {{-- ACCOUNT SUMMARY --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">

                        <div class="flex items-center justify-between gap-4">

                            <div>
                                <h2 class="text-lg font-light text-gray-900 mb-1">
                                    {{ $account->reservation?->type ?? 'Property Account' }}
                                </h2>

                                <p class="text-sm font-light text-gray-600">

                                    @if(
                                        $account->payment_scheme
                                            === 'bank_loan'
                                    )

                                        {{ number_format(
                                            $account
                                                ->reservation
                                                ?->downpayment_percentage
                                                ?? 20,
                                            0
                                        ) }}%
                                        Equity Payables

                                    @else

                                        {{ Str::headline(
                                            $account->payment_scheme
                                        ) }}

                                    @endif

                                </p>
                            </div>

                            <div class="text-right">

                                <div class="text-2xl font-light text-gray-900">
                                    ₱{{ number_format(
                                        $account->remaining_balance,
                                        2
                                    ) }}
                                </div>

                                <p class="text-sm font-light text-gray-600 mt-1">
                                    Balance
                                </p>

                            </div>

                        </div>

                    </div>

                    {{-- TABS --}}
                    <div class="bg-gray-100 rounded-xl p-1 flex gap-1 mb-6">

                        <button
                            type="button"
                            x-on:click="tab = 'unpaid'"
                            class="flex-1 py-3 rounded-lg font-light transition-all"
                            :class="tab === 'unpaid'
                                ? 'bg-white text-gray-900 shadow-sm'
                                : 'text-gray-600'"
                        >
                            Unpaid
                        </button>

                        <button
                            type="button"
                            x-on:click="tab = 'paid'"
                            class="flex-1 py-3 rounded-lg font-light transition-all"
                            :class="tab === 'paid'
                                ? 'bg-white text-gray-900 shadow-sm'
                                : 'text-gray-600'"
                        >
                            Paid
                        </button>

                    </div>

                    {{-- UNPAID --}}
                    <div
                        x-show="tab === 'unpaid'"
                        x-cloak
                    >

                        {{-- CURRENT --}}
                        <div class="mb-8">

                            <h3 class="text-lg font-light text-gray-900 mb-4">
                                Current Bills
                            </h3>

                            <div class="space-y-4">

                                @forelse(
                                    $currentBills
                                    as $billing
                                )

                                    @include(
                                        'livewire.client.partials.bill-card',
                                        [
                                            'billing' =>
                                                $billing,

                                            'isPayable' =>
                                                true,
                                        ]
                                    )

                                @empty

                                    <div class="text-sm text-gray-400 italic">
                                        No current bills.
                                    </div>

                                @endforelse

                            </div>

                        </div>

                        {{-- UPCOMING --}}
                        <div>

                            <h3 class="text-lg font-light text-gray-900 mb-4">
                                Upcoming Bills
                            </h3>

                            <div class="space-y-4">

                                @forelse(
                                    $upcomingBills
                                    as $billing
                                )

                                    @include(
                                        'livewire.client.partials.bill-card',
                                        [
                                            'billing' =>
                                                $billing,

                                            'isPayable' =>
                                                false,
                                        ]
                                    )

                                @empty

                                    <div class="text-sm text-gray-400 italic">
                                        No upcoming bills.
                                    </div>

                                @endforelse

                            </div>

                        </div>

                    </div>

                    {{-- PAID --}}
                    <div
                        x-show="tab === 'paid'"
                        x-cloak
                    >

                        <h3 class="text-lg font-light text-gray-900 mb-4">
                            Paid Bills
                        </h3>

                        <div class="space-y-4">

                            @forelse(
                                $paidBillings
                                as $billing
                            )

                                @include(
                                    'livewire.client.partials.bill-card',
                                    [
                                        'billing' =>
                                            $billing,

                                        'isPayable' =>
                                            false,
                                    ]
                                )

                            @empty

                                <div class="text-sm text-gray-400 italic">
                                    No paid bills yet.
                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            @empty

                <div class="text-center py-10 border-2 border-dashed rounded-lg text-gray-400">
                    No billing account found.
                </div>

            @endforelse

        </div>

        {{-- PAYMENT MODAL --}}
        <x-modal
            blur
            name="payBill"
            align="center"
            max-width="2xl"
        >

            <x-card title="Upload Payment Proof">

                {{-- BILLING SUMMARY --}}
                @if($this->selectedBilling)

                    @php
                        $selectedBilling = $this->selectedBilling;
                    @endphp

                    <div class="mb-6 rounded-2xl border border-gray-100 bg-gray-50 p-5">

                        <div class="flex items-start justify-between gap-4">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Current Bill
                                </p>

                                <p class="mt-1 text-base font-semibold text-gray-900">
                                    {{ $selectedBilling->title }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    Due {{ $selectedBilling->due_date->format('F d, Y') }}
                                </p>
                            </div>

                            <div class="text-right">

                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Pay Now
                                </p>

                                <p
                                    class="mt-1 text-2xl font-semibold
                                    {{ $selectedBilling->calculated_penalty > 0
                                        ? 'text-red-700'
                                        : (
                                            $selectedBilling->calculated_discount > 0
                                                ? 'text-green-700'
                                                : 'text-gray-900'
                                        ) }}"
                                >
                                    ₱{{ number_format(
                                        $selectedBilling->payable_amount,
                                        2
                                    ) }}
                                </p>

                            </div>

                        </div>

                        <div class="mt-4 space-y-2 border-t border-gray-200 pt-4 text-xs">

                            <div class="flex justify-between gap-4 text-gray-500">
                                <span>Remaining Balance</span>

                                <span>
                                    ₱{{ number_format(
                                        $selectedBilling->remaining_balance,
                                        2
                                    ) }}
                                </span>
                            </div>

                            @if($selectedBilling->calculated_discount > 0)

                                <div class="flex justify-between gap-4 text-green-600">
                                    <span>
                                        Early Payment Discount
                                    </span>

                                    <span>
                                        -₱{{ number_format(
                                            $selectedBilling->calculated_discount,
                                            2
                                        ) }}
                                    </span>
                                </div>

                            @endif

                            @if($selectedBilling->calculated_penalty > 0)

                                <div class="flex justify-between gap-4 text-red-600">

                                    <span>
                                        Late Penalty
                                        (
                                        {{ $selectedBilling->months_overdue }}
                                        ×
                                        {{ number_format(
                                            $selectedBilling->monthly_penalty_rate,
                                            0
                                        ) }}%
                                        )
                                    </span>

                                    <span>
                                        +₱{{ number_format(
                                            $selectedBilling->calculated_penalty,
                                            2
                                        ) }}
                                    </span>

                                </div>

                            @endif

                            <div class="flex justify-between gap-4 border-t border-gray-200 pt-2 text-sm font-semibold text-gray-900">

                                <span>
                                    Amount to Pay
                                </span>

                                <span>
                                    ₱{{ number_format(
                                        $selectedBilling->payable_amount,
                                        2
                                    ) }}
                                </span>

                            </div>

                        </div>

                    </div>

                @endif

                {{-- PAYMENT METHOD --}}
                <x-select
                    label="Payment Method"
                    wire:model.live="paymentMethod"
                    :options="[
                        [
                            'id' => 'bank_transfer',
                            'name' => 'Bank Transfer'
                        ],
                        [
                            'id' => 'gcash',
                            'name' => 'GCash'
                        ],
                        [
                            'id' => 'maya',
                            'name' => 'Maya'
                        ],
                        [
                            'id' => 'cash',
                            'name' => 'Cash'
                        ],
                    ]"
                    option-label="name"
                    option-value="id"
                />

                {{-- @error('paymentMethod')
                    <p class="mt-1 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror --}}

                {{-- QR --}}
                @if($this->selectedQrCode)

                    <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4 text-center">

                        <div class="text-sm font-medium text-gray-900 mb-2">
                            {{ Str::headline(
                                $this
                                    ->selectedQrCode
                                    ->payment_method
                            ) }}
                            QR Code
                        </div>

                        <img
                            src="{{ asset(
                                'storage/'
                                . $this
                                    ->selectedQrCode
                                    ->qr_image
                            ) }}"
                            class="mx-auto h-64 w-full object-contain rounded-lg border bg-white"
                        >

                        <div class="mt-3 text-sm font-medium text-gray-700">
                            {{ $this
                                ->selectedQrCode
                                ->account_name }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $this
                                ->selectedQrCode
                                ->account_number }}
                        </div>

                    </div>

                @elseif(
                    $paymentMethod
                    && $paymentMethod !== 'cash'
                )

                    <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">
                        No active QR code available for
                        {{ Str::headline(
                            $paymentMethod
                        ) }}.
                    </div>

                @endif

                {{-- REFERENCE --}}
                <div class="mt-4">

                    <x-input
                        label="Reference Number"
                        placeholder="Enter transaction reference"
                        wire:model.defer="referenceNo"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        required
                    />

                </div>

                {{-- FILEPOND --}}
                <div class="mt-4">

                    <label class="block mb-2 text-sm font-medium
                        {{ $errors->has('proofOfPayment') ? 'text-red-500' : 'text-gray-700' }}">
                        Proof of Payment
                    </label>

                    <x-filepond::upload
                        wire:model="proofOfPayment"
                        multiple="false"
                        max-files="1"
                    />

                    @error('proofOfPayment')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- NOTICE --}}
                @if($this->selectedBilling)

                    <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4">

                        <div class="flex items-start gap-2">

                            <x-icon
                                name="information-circle"
                                class="h-5 w-5 shrink-0 text-blue-600"
                            />

                            <p class="text-xs leading-relaxed text-blue-700">
                                Your payment will be submitted for administrator verification.

                                @if(
                                    $this
                                        ->selectedBilling
                                        ->calculated_discount
                                        > 0
                                )
                                    The ₱{{ number_format(
                                        $this
                                            ->selectedBilling
                                            ->calculated_discount,
                                        2
                                    ) }}
                                    early-payment discount will be recorded with this payment.
                                @endif

                                @if(
                                    $this
                                        ->selectedBilling
                                        ->calculated_penalty
                                        > 0
                                )
                                    The payable amount includes a ₱{{ number_format(
                                        $this
                                            ->selectedBilling
                                            ->calculated_penalty,
                                        2
                                    ) }}
                                    late-payment penalty.
                                @endif
                            </p>

                        </div>

                    </div>

                @endif

                <x-slot name="footer">

                    <div class="flex justify-end gap-2">

                        <x-button
                            flat
                            label="Cancel"
                            x-on:click="close"
                            wire:click="resetPaymentForm"
                        />

                        <x-button
                            primary
                            icon="banknotes"
                            label="{{ $this->selectedBilling
                                ? 'Submit ₱'
                                    . number_format(
                                        $this
                                            ->selectedBilling
                                            ->payable_amount,
                                        2
                                    )
                                : 'Submit Payment' }}"
                            wire:click="submitPayment"
                            wire:loading.attr="disabled"
                            wire:target="submitPayment,proofOfPayment"
                        />

                    </div>

                </x-slot>

            </x-card>

        </x-modal>

    </div>
</main>