<div class="flex-1 min-w-0">
    <div class="w-full mx-auto ">

        <div class="flex justify-between items-center mb-5">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                Client Ledgers
            </h1>

            <x-button
                label="Create Ledger"
                icon="plus"
                x-on:click="$openModal('createLedger')"
                class="bg-[#101727] text-white"
            />
        </div>

        <div class="mb-5 rounded-2xl border bg-white p-4 shadow-sm">

            <div class="grid gap-3 md:grid-cols-4">

                <div class="md:col-span-2">
                    <x-input
                        icon="magnifying-glass"
                        placeholder="Search client, email, lot, or reservation type..."
                        wire:model.live.debounce.500ms="search"
                    />
                </div>

                <x-select
                    placeholder="Account Status"
                    wire:model.live="statusFilter"
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

                <x-select
                    placeholder="Payment Scheme"
                    wire:model.live="paymentSchemeFilter"
                    :options="[
                        
                        ['id' => 'cash', 'name' => 'Cash'],
                        ['id' => 'bank_loan', 'name' => 'Bank Loan'],
                        ['id' => 'deferred_payment', 'name' => 'Deferred Payment'],
                    ]"
                    option-label="name"
                    option-value="id"
                />

            </div>

            <div class="mt-3 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                <div class="flex flex-wrap gap-2">

                    {{-- <button
                        type="button"
                        wire:click="setPaymentReviewFilter('')"
                        class="rounded-full px-4 py-2 text-xs font-medium transition
                            {{ $paymentReviewFilter === '' ? 'bg-[#101727] text-white' : 'bg-gray-100 text-gray-600' }}"
                    >
                        All Payments
                    </button>

                    <button
                        type="button"
                        wire:click="setPaymentReviewFilter('pending_review')"
                        class="rounded-full px-4 py-2 text-xs font-medium transition
                            {{ $paymentReviewFilter === 'pending_review' ? 'bg-violet-600 text-white' : 'bg-violet-50 text-violet-700' }}"
                    >
                        Pending Review
                    </button>

                    <button
                        type="button"
                        wire:click="setPaymentReviewFilter('has_verified')"
                        class="rounded-full px-4 py-2 text-xs font-medium transition
                            {{ $paymentReviewFilter === 'has_verified' ? 'bg-green-600 text-white' : 'bg-green-50 text-green-700' }}"
                    >
                        Verified Payments
                    </button> --}}

                </div>

                <x-button
                    flat
                    xs
                    icon="x-mark"
                    label="Clear Filters"
                    wire:click="resetFilters"
                />

            </div>

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
                            <div class="text-xs text-gray-500">
                                {{ $account->payment_scheme === 'bank_loan' ? 'Monthly Downpayment' : 'Monthly Amortization' }}
                            </div>
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

                    {{-- <div class="flex gap-2 mt-4">

                        <x-button
                            label="Record Office Payment"
                            icon="banknotes"
                            x-on:click="
                                $wire.accountId = {{ $account->id }};
                                $openModal('recordPayment')
                            "
                            class="bg-[#101727] text-white"
                        />

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

                    </div> --}}

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
                                            ->whereIn(
                                                'status',
                                                ['unpaid', 'partial']
                                            )
                                            ->sortBy('due_date')
                                            ->first();

                                        $isPayable =
                                            $firstPayableBilling
                                            && $firstPayableBilling->id === $billing->id;

                                        $remainingBalance =
                                            (float) $billing->remaining_balance;

                                        $discount =
                                            (float) $billing->calculated_discount;

                                        $penalty =
                                            (float) $billing->calculated_penalty;

                                        $payableAmount =
                                            (float) $billing->payable_amount;

                                        $monthsOverdue =
                                            (int) $billing->months_overdue;

                                        $isOverdue =
                                            $monthsOverdue > 0
                                            && $billing->status !== 'paid';

                                        $isEarly =
                                            $discount > 0
                                            && ! $isOverdue
                                            && $billing->status !== 'paid';

                                        $pendingPayment =
                                            $billing->payments
                                                ->where('status', 'pending')
                                                ->first();
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
                                                <span class="text-gray-500">
                                                    Payable Now
                                                </span>

                                                <span
                                                    class="font-semibold
                                                    {{ $isOverdue
                                                        ? 'text-red-700'
                                                        : (
                                                            $isEarly
                                                                ? 'text-green-700'
                                                                : 'text-gray-900'
                                                        ) }}"
                                                >
                                                    ₱{{ number_format(
                                                        $payableAmount,
                                                        2
                                                    ) }}
                                                </span>
                                            </div>

                                            @if($discount > 0)

                                                <div class="rounded-lg bg-green-50 px-3 py-2 text-xs">

                                                    <div class="flex justify-between text-green-700">

                                                        <span>
                                                            Early Payment Discount
                                                        </span>

                                                        <span class="font-semibold">
                                                            -₱{{ number_format(
                                                                $discount,
                                                                2
                                                            ) }}
                                                        </span>

                                                    </div>

                                                    <p class="mt-1 text-[10px] text-green-600">
                                                        Client saves ₱{{ number_format(
                                                            $discount,
                                                            2
                                                        ) }}
                                                        when payment is recorded before the due date.
                                                    </p>

                                                </div>

                                            @endif

                                            @if($penalty > 0)

                                                <div class="rounded-lg bg-red-50 px-3 py-2 text-xs">

                                                    <div class="flex justify-between text-red-700">

                                                        <span>
                                                            Late Penalty
                                                            ({{ $monthsOverdue }} ×
                                                            {{ number_format(
                                                                $billing->monthly_penalty_rate,
                                                                0
                                                            ) }}%)
                                                        </span>

                                                        <span class="font-semibold">
                                                            +₱{{ number_format(
                                                                $penalty,
                                                                2
                                                            ) }}
                                                        </span>

                                                    </div>

                                                </div>

                                            @endif
                                        </div>

                                        @php
                                            $pendingPayment = $billing->payments
                                                ->where('status', 'pending')
                                                ->first();
                                        @endphp

                                        @if($pendingPayment)
                                            <div class="mt-4 rounded-2xl border border-violet-100 bg-violet-50/60 p-4">

                                                <div class="flex items-start justify-between gap-3">

                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="h-2 w-2 rounded-full bg-violet-500"></span>

                                                            <h4 class="text-sm font-semibold text-gray-900">
                                                                Payment Waiting for Review
                                                            </h4>
                                                        </div>

                                                        <p class="mt-1 text-xs text-gray-500">
                                                            Submitted proof requires admin verification.
                                                        </p>
                                                    </div>

                                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-medium text-violet-700 shadow-sm">
                                                        Pending
                                                    </span>

                                                </div>

                                                <div class="mt-4 bg-white rounded-xl p-4 space-y-3 text-sm">

                                                    <div class="grid grid-cols-2 gap-3">

                                                        <div>
                                                            <div class="text-gray-400 text-xs">
                                                                Amount
                                                            </div>

                                                            <div class="font-semibold text-gray-900">
                                                                ₱{{ number_format($pendingPayment->amount, 2) }}
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <div class="text-gray-400 text-xs">
                                                                Method
                                                            </div>

                                                            <div class="font-semibold text-gray-900">
                                                                {{ Str::headline($pendingPayment->payment_method) }}
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="border-t pt-3">
                                                        <div class="text-gray-400 text-xs">
                                                            Reference Number
                                                        </div>

                                                        <div class="font-semibold text-gray-900 break-all mt-1">
                                                            {{ $pendingPayment->reference_no ?? 'N/A' }}
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="mt-4 space-y-3">

                                                    @if($pendingPayment->proof_of_payment)
                                                        <a
                                                            href="{{ asset('storage/' . $pendingPayment->proof_of_payment) }}"
                                                            target="_blank"
                                                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-3 py-2 text-sm font-medium text-violet-700 hover:text-violet-900"
                                                        >
                                                            <x-icon name="arrow-top-right-on-square" class="h-4 w-4" />
                                                            View Proof
                                                        </a>
                                                    @else
                                                        <div class="text-center text-sm text-gray-400">
                                                            No proof uploaded
                                                        </div>
                                                    @endif

                                                    <div class="grid grid-cols-2 gap-2">

                                                        <x-button
                                                            label="Reject"
                                                            icon="x-mark"
                                                            wire:click="rejectBillingPayment({{ $pendingPayment->id }})"
                                                            class="w-full border border-gray-200 bg-white text-gray-700 hover:border-red-200 hover:text-red-700"
                                                        />

                                                        <x-button
                                                            label="Approve"
                                                            icon="check"
                                                            wire:click="approveBillingPayment({{ $pendingPayment->id }})"
                                                            class="w-full bg-[#101727] text-white"
                                                        />

                                                    </div>

                                                </div>

                                            </div>
                                        @endif

                                        <div class="mt-4">
                                            <x-button
                                                label="{{ $billing->status === 'paid'
                                                    ? 'Paid'
                                                    : 'Pay ₱' . number_format(
                                                        $payableAmount,
                                                        2
                                                    ) }}"
                                                icon="banknotes"
                                                x-on:click="
                                                    $wire.accountId =
                                                        {{ $account->id }};

                                                    $wire.billingId =
                                                        {{ $billing->id }};

                                                    $wire.paymentAmount =
                                                        {{ $payableAmount }};

                                                    $wire.paymentDescription =
                                                        'Payment for {{ $billing->title }}';

                                                    $openModal('recordPayment');
                                                "
                                                class="w-full bg-[#101727] text-white"
                                                :disabled="
                                                    ! $isPayable
                                                    || $billing->status === 'paid'
                                                    || $pendingPayment
                                                "
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

            @if(in_array($selectedPayment, ['bank_loan', 'Loanable', 'Bank Loan']) && $selectedReservation)
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="text-xs text-blue-700">Downpayment Setup</div>

                    <div class="font-semibold text-blue-900">
                        {{ $selectedReservation->downpayment_percentage ?? 20 }}%
                        downpayment payable in
                        {{ $selectedReservation->downpayment_term_months ?? 12 }} months
                    </div>
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
                readonly
            />

            <div class="mt-4">
                <x-select
                    label="Payment Method"
                    wire:model="officePaymentMethod"
                    :options="[
                        ['id' => 'cash', 'name' => 'Cash'],
                        ['id' => 'bank_transfer', 'name' => 'Bank Transfer'],
                        ['id' => 'gcash', 'name' => 'GCash'],
                        ['id' => 'maya', 'name' => 'Maya'],
                    ]"
                    option-label="name"
                    option-value="id"
                />
            </div>

            <div class="mt-4">
                <x-input
                    label="Reference Number"
                    placeholder="Optional for cash"
                    wire:model="officeReferenceNo"
                />
            </div>

            <div class="mt-4">
                <label class="text-sm font-medium">
                    Proof / Receipt Image
                </label>

                <x-filepond::upload
                    wire:model="officeProofOfPayment"
                />
            </div>

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