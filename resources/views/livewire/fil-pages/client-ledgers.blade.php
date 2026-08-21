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
                        x-data="{
                            open: false,
                            billingTab: 'unpaid'
                        }"
                        class="mt-5 border rounded-2xl overflow-hidden bg-white"
                    >

                        {{-- MAIN BILLING HEADER --}}
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

                                    {{ $account->billings
                                        ->whereIn('status', ['unpaid', 'partial'])
                                        ->count()
                                    }} unpaid ·

                                    {{ $account->billings
                                        ->where('status', 'paid')
                                        ->count()
                                    }} paid
                                </div>
                            </div>

                            <x-icon
                                name="chevron-down"
                                class="w-5 h-5 text-gray-500 transition-transform"
                                x-bind:class="open ? 'rotate-180' : ''"
                            />
                        </button>


                        {{-- BILLING CONTENT --}}
                        <div
                            x-show="open"
                            x-collapse
                            class="bg-[#F5F5F5] p-4"
                        >

                            {{-- UNPAID / PAID TABS --}}
                            <div class="mb-4 flex items-center gap-2 rounded-xl border bg-white p-1.5">

                                {{-- UNPAID TAB --}}
                                <button
                                    type="button"
                                    x-on:click="billingTab = 'unpaid'"
                                    class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition"
                                    x-bind:class="
                                        billingTab === 'unpaid'
                                            ? 'bg-[#101727] text-white'
                                            : 'text-gray-500 hover:bg-gray-100'
                                    "
                                >
                                    Unpaid Bills

                                    <span
                                        class="ml-1 rounded-full px-2 py-0.5 text-[10px]"
                                        x-bind:class="
                                            billingTab === 'unpaid'
                                                ? 'bg-white/20 text-white'
                                                : 'bg-orange-100 text-orange-700'
                                        "
                                    >
                                        {{ $account->billings
                                            ->whereIn('status', ['unpaid', 'partial'])
                                            ->count()
                                        }}
                                    </span>
                                </button>


                                {{-- PAID TAB --}}
                                <button
                                    type="button"
                                    x-on:click="billingTab = 'paid'"
                                    class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition"
                                    x-bind:class="
                                        billingTab === 'paid'
                                            ? 'bg-green-600 text-white'
                                            : 'text-gray-500 hover:bg-gray-100'
                                    "
                                >
                                    Paid Bills

                                    <span
                                        class="ml-1 rounded-full px-2 py-0.5 text-[10px]"
                                        x-bind:class="
                                            billingTab === 'paid'
                                                ? 'bg-white/20 text-white'
                                                : 'bg-green-100 text-green-700'
                                        "
                                    >
                                        {{ $account->billings
                                            ->where('status', 'paid')
                                            ->count()
                                        }}
                                    </span>
                                </button>

                            </div>


                            {{-- ===================================================== --}}
                            {{-- UNPAID BILLS --}}
                            {{-- ===================================================== --}}

                            <div
                                x-show="billingTab === 'unpaid'"
                                class="grid md:grid-cols-2 xl:grid-cols-3 gap-4 max-h-[600px] overflow-y-auto pr-2"
                            >

                                @forelse(
                                    $account->billings
                                        ->whereIn('status', ['unpaid', 'partial'])
                                        ->sortBy('due_date')
                                    as $billing
                                )

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

                                        {{-- BILLING HEADER --}}
                                        <div class="flex items-start justify-between gap-3 mb-3">

                                            <div>
                                                <div class="text-lg font-semibold text-gray-900">
                                                    {{ $billing->due_date->format('M Y') }}
                                                </div>

                                                <div class="text-xs text-gray-500">
                                                    {{ $billing->title }}
                                                </div>
                                            </div>


                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-semibold
                                                    {{ $billing->status === 'paid'
                                                        ? 'bg-green-100 text-green-700'
                                                        : '' }}

                                                    {{ $billing->status === 'partial'
                                                        ? 'bg-blue-100 text-blue-700'
                                                        : '' }}

                                                    {{ $billing->status === 'unpaid'
                                                        ? 'bg-orange-100 text-orange-700'
                                                        : '' }}

                                                    {{ $billing->status === 'cancelled'
                                                        ? 'bg-red-100 text-red-700'
                                                        : '' }}
                                                "
                                            >
                                                {{ Str::headline($billing->status) }}
                                            </span>

                                        </div>


                                        {{-- BILLING AMOUNTS --}}
                                        <div class="space-y-2 text-sm">

                                            <div class="flex justify-between">
                                                <span class="text-gray-500">
                                                    Due Date
                                                </span>

                                                <span class="font-medium">
                                                    {{ $billing->due_date->format('M d, Y') }}
                                                </span>
                                            </div>


                                            <div class="flex justify-between">
                                                <span class="text-gray-500">
                                                    Amount Due
                                                </span>

                                                <span class="font-semibold">
                                                    ₱{{ number_format(
                                                        $billing->amount_due,
                                                        2
                                                    ) }}
                                                </span>
                                            </div>


                                            <div class="flex justify-between">
                                                <span class="text-gray-500">
                                                    Paid
                                                </span>

                                                <span class="font-semibold text-green-700">
                                                    ₱{{ number_format(
                                                        $billing->amount_paid,
                                                        2
                                                    ) }}
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
                                                        )
                                                    }}"
                                                >
                                                    ₱{{ number_format(
                                                        $payableAmount,
                                                        2
                                                    ) }}
                                                </span>

                                            </div>


                                            {{-- EARLY PAYMENT DISCOUNT --}}
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
                                                        Client saves
                                                        ₱{{ number_format(
                                                            $discount,
                                                            2
                                                        ) }}
                                                        when payment is recorded before the due date.
                                                    </p>

                                                </div>

                                            @endif


                                            {{-- PENALTY --}}
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


                                        {{-- PENDING PAYMENT --}}
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
                                                                ₱{{ number_format(
                                                                    $pendingPayment->amount,
                                                                    2
                                                                ) }}
                                                            </div>
                                                        </div>


                                                        <div>
                                                            <div class="text-gray-400 text-xs">
                                                                Method
                                                            </div>

                                                            <div class="font-semibold text-gray-900">
                                                                {{ Str::headline(
                                                                    $pendingPayment->payment_method
                                                                ) }}
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
                                                            href="{{ asset(
                                                                'storage/' .
                                                                $pendingPayment->proof_of_payment
                                                            ) }}"
                                                            target="_blank"
                                                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-3 py-2 text-sm font-medium text-violet-700 hover:text-violet-900"
                                                        >
                                                            <x-icon
                                                                name="arrow-top-right-on-square"
                                                                class="h-4 w-4"
                                                            />

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


                                        {{-- PAY BUTTON --}}
                                        <div class="mt-4">

                                            <x-button
                                                label="{{ $billing->status === 'paid'
                                                    ? 'Paid'
                                                    : 'Pay ₱' . number_format(
                                                        $payableAmount,
                                                        2
                                                    )
                                                }}"
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
                                                class="w-full bg-[#101727] text-white hover:!bg-[#3b4a68] hover:!text-white transition-colors"
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

                                        <x-icon
                                            name="check-circle"
                                            class="mx-auto mb-2 h-8 w-8 text-green-500"
                                        />

                                        <div class="font-medium text-gray-700">
                                            No Unpaid Bills
                                        </div>

                                        <div class="mt-1 text-xs text-gray-400">
                                            All currently generated billings have been paid.
                                        </div>

                                    </div>

                                @endforelse

                            </div>


                            {{-- ===================================================== --}}
                            {{-- PAID BILLS / PAYMENT HISTORY --}}
                            {{-- ===================================================== --}}

                            <div
                                x-show="billingTab === 'paid'"
                                x-cloak
                                class="space-y-3"
                            >

                                @forelse(
                                    $account->billings
                                        ->where('status', 'paid')
                                        ->sortByDesc('due_date')
                                    as $billing
                                )

                                    @php
                                        $paymentHistory = $billing->payments
                                            ->sortByDesc('paid_at');

                                        $verifiedPayments = $billing->payments
                                            ->where('status', 'verified');

                                        $totalVerifiedPayment =
                                            $verifiedPayments->sum('amount');
                                    @endphp


                                    <div
                                        x-data="{ historyOpen: false }"
                                        class="overflow-hidden rounded-xl border border-green-100 bg-white"
                                    >

                                        {{-- PAID BILL HEADER --}}
                                        <button
                                            type="button"
                                            x-on:click="historyOpen = !historyOpen"
                                            class="flex w-full items-center justify-between gap-4 p-4 text-left transition hover:bg-green-50/40"
                                        >

                                            <div class="flex min-w-0 items-center gap-3">

                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100">

                                                    <x-icon
                                                        name="check-circle"
                                                        class="h-5 w-5 text-green-700"
                                                    />

                                                </div>


                                                <div class="min-w-0">

                                                    <div class="flex flex-wrap items-center gap-2">

                                                        <p class="font-semibold text-gray-900">
                                                            {{ $billing->title }}
                                                        </p>

                                                        <span class="rounded-full bg-green-100 px-2 py-1 text-[10px] font-semibold text-green-700">
                                                            Paid
                                                        </span>

                                                    </div>


                                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-400">

                                                        <span>
                                                            {{ $billing->due_date->format('M Y') }}
                                                        </span>

                                                        <span>
                                                            •
                                                        </span>

                                                        <span>
                                                            Due {{ $billing->due_date->format('M d, Y') }}
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>


                                            <div class="flex shrink-0 items-center gap-4">

                                                <div class="hidden text-right sm:block">

                                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                        Paid
                                                    </div>

                                                    <div class="text-sm font-semibold text-green-700">
                                                        ₱{{ number_format(
                                                            $billing->amount_paid,
                                                            2
                                                        ) }}
                                                    </div>

                                                </div>


                                                <x-icon
                                                    name="chevron-down"
                                                    class="h-5 w-5 text-gray-400 transition-transform"
                                                    x-bind:class="
                                                        historyOpen
                                                            ? 'rotate-180'
                                                            : ''
                                                    "
                                                />

                                            </div>

                                        </button>


                                        {{-- PAYMENT HISTORY DETAILS --}}
                                        <div
                                            x-show="historyOpen"
                                            x-collapse
                                            class="border-t border-gray-100 bg-gray-50/60 p-4"
                                        >

                                            {{-- BILLING SUMMARY --}}
                                            <div class="mb-4 grid grid-cols-2 gap-3 md:grid-cols-4">

                                                <div class="rounded-lg border bg-white p-3">

                                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                        Amount Due
                                                    </div>

                                                    <div class="mt-1 text-sm font-semibold text-gray-900">
                                                        ₱{{ number_format(
                                                            $billing->amount_due,
                                                            2
                                                        ) }}
                                                    </div>

                                                </div>


                                                <div class="rounded-lg border bg-white p-3">

                                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                        Amount Paid
                                                    </div>

                                                    <div class="mt-1 text-sm font-semibold text-green-700">
                                                        ₱{{ number_format(
                                                            $billing->amount_paid,
                                                            2
                                                        ) }}
                                                    </div>

                                                </div>


                                                <div class="rounded-lg border bg-white p-3">

                                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                        Payment Records
                                                    </div>

                                                    <div class="mt-1 text-sm font-semibold text-gray-900">
                                                        {{ $paymentHistory->count() }}
                                                    </div>

                                                </div>


                                                <div class="rounded-lg border bg-white p-3">

                                                    <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                        Status
                                                    </div>

                                                    <div class="mt-1 text-sm font-semibold text-green-700">
                                                        Paid
                                                    </div>

                                                </div>

                                            </div>


                                            {{-- HISTORY TITLE --}}
                                            <div class="mb-3">

                                                <h5 class="text-sm font-semibold text-gray-900">
                                                    Payment History
                                                </h5>

                                                <p class="mt-0.5 text-xs text-gray-400">
                                                    Payment records and attached receipts for this billing.
                                                </p>

                                            </div>


                                            {{-- PAYMENT RECORDS --}}
                                            @if($paymentHistory->count())

                                                <div class="space-y-2">

                                                    @foreach($paymentHistory as $payment)

                                                        @php
                                                            $paymentColor = match($payment->status) {
                                                                'verified'
                                                                    => 'bg-green-100 text-green-700',

                                                                'pending'
                                                                    => 'bg-yellow-100 text-yellow-700',

                                                                'rejected'
                                                                    => 'bg-red-100 text-red-700',

                                                                default
                                                                    => 'bg-gray-100 text-gray-700',
                                                            };

                                                            $paymentSource = match($payment->source) {
                                                                'office_payment'
                                                                    => 'Office Payment',

                                                                'client_upload'
                                                                    => 'Client Upload',

                                                                default
                                                                    => $payment->source
                                                                        ? Str::headline(
                                                                            $payment->source
                                                                        )
                                                                        : 'N/A',
                                                            };
                                                        @endphp


                                                        <div class="rounded-xl border border-gray-200 bg-white p-4">

                                                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                                                {{-- PAYMENT INFO --}}
                                                                <div class="min-w-0 flex-1">

                                                                    <div class="flex flex-wrap items-center gap-2">

                                                                        <div class="text-base font-semibold text-gray-900">
                                                                            ₱{{ number_format(
                                                                                $payment->amount,
                                                                                2
                                                                            ) }}
                                                                        </div>


                                                                        <span
                                                                            class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $paymentColor }}"
                                                                        >
                                                                            {{ Str::headline(
                                                                                $payment->status
                                                                            ) }}
                                                                        </span>

                                                                    </div>


                                                                    <div class="mt-3 grid gap-x-6 gap-y-2 text-xs sm:grid-cols-2">

                                                                        {{-- METHOD --}}
                                                                        <div>

                                                                            <div class="text-gray-400">
                                                                                Payment Method
                                                                            </div>

                                                                            <div class="mt-0.5 font-medium text-gray-700">
                                                                                {{ $payment->payment_method
                                                                                    ? Str::headline(
                                                                                        $payment->payment_method
                                                                                    )
                                                                                    : 'N/A'
                                                                                }}
                                                                            </div>

                                                                        </div>


                                                                        {{-- SOURCE --}}
                                                                        <div>

                                                                            <div class="text-gray-400">
                                                                                Source
                                                                            </div>

                                                                            <div class="mt-0.5 font-medium text-gray-700">
                                                                                {{ $paymentSource }}
                                                                            </div>

                                                                        </div>


                                                                        {{-- REFERENCE --}}
                                                                        <div>

                                                                            <div class="text-gray-400">
                                                                                Reference Number
                                                                            </div>

                                                                            <div class="mt-0.5 break-all font-medium text-gray-700">
                                                                                {{ $payment->reference_no ?? 'N/A' }}
                                                                            </div>

                                                                        </div>


                                                                        {{-- PAID AT --}}
                                                                        <div>

                                                                            <div class="text-gray-400">
                                                                                Paid At
                                                                            </div>

                                                                            <div class="mt-0.5 font-medium text-gray-700">
                                                                                {{ $payment->paid_at
                                                                                    ? $payment->paid_at->format(
                                                                                        'M d, Y h:i A'
                                                                                    )
                                                                                    : 'N/A'
                                                                                }}
                                                                            </div>

                                                                        </div>


                                                                        {{-- VERIFIED AT --}}
                                                                        @if($payment->verified_at)

                                                                            <div>

                                                                                <div class="text-gray-400">
                                                                                    Verified At
                                                                                </div>

                                                                                <div class="mt-0.5 font-medium text-gray-700">
                                                                                    {{ $payment->verified_at->format(
                                                                                        'M d, Y h:i A'
                                                                                    ) }}
                                                                                </div>

                                                                            </div>

                                                                        @endif

                                                                    </div>


                                                                    {{-- REMARKS --}}
                                                                    @if($payment->remarks)

                                                                        <div class="mt-3 rounded-lg bg-gray-50 p-3">

                                                                            <div class="text-[10px] uppercase tracking-wide text-gray-400">
                                                                                Remarks
                                                                            </div>

                                                                            <div class="mt-1 text-xs text-gray-700">
                                                                                {{ $payment->remarks }}
                                                                            </div>

                                                                        </div>

                                                                    @endif

                                                                </div>


                                                                {{-- RECEIPT --}}
                                                                <div class="shrink-0">

                                                                    @if($payment->proof_of_payment)

                                                                        <a
                                                                            href="{{ asset(
                                                                                'storage/' .
                                                                                $payment->proof_of_payment
                                                                            ) }}"
                                                                            target="_blank"
                                                                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 sm:w-auto"
                                                                        >
                                                                            <x-icon
                                                                                name="document-magnifying-glass"
                                                                                class="h-4 w-4"
                                                                            />

                                                                            View Receipt
                                                                        </a>

                                                                    @else

                                                                        <span class="text-xs text-gray-400">
                                                                            No receipt
                                                                        </span>

                                                                    @endif

                                                                </div>

                                                            </div>

                                                        </div>

                                                    @endforeach

                                                </div>

                                            @else

                                                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-5 text-center">

                                                    <x-icon
                                                        name="document"
                                                        class="mx-auto mb-2 h-7 w-7 text-gray-300"
                                                    />

                                                    <div class="text-sm font-medium text-gray-600">
                                                        No Payment History
                                                    </div>

                                                    <div class="mt-1 text-xs text-gray-400">
                                                        No payment records were found for this billing.
                                                    </div>

                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                @empty

                                    <div class="py-10 text-center text-gray-400">

                                        <x-icon
                                            name="banknotes"
                                            class="mx-auto mb-2 h-8 w-8 text-gray-300"
                                        />

                                        <div class="font-medium text-gray-600">
                                            No Paid Bills Yet
                                        </div>

                                        <div class="mt-1 text-xs text-gray-400">
                                            Paid billing records will automatically appear here.
                                        </div>

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
                    x-on:keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
                    x-on:paste.prevent="
                        let paste = ($event.clipboardData || window.clipboardData).getData('text');
                        let cleaned = paste.replace(/[^0-9]/g, '');
                        $event.target.value = cleaned;
                        $wire.set('officeReferenceNo', cleaned);
                    "
                />
            </div>

            <div class="mt-4">
                <label class="text-sm font-medium">
                    Proof / Receipt Image
                </label>

                <x-filepond::upload
                    wire:model="officeProofOfPayment"
                />

                @error('officeProofOfPayment')
                    <span class="text-sm text-red-500">
                        {{ $message }}
                    </span>
                @enderror
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