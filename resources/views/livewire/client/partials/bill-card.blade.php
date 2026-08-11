@php
    $latestPayment = $billing->latestPayment;

    $hasPendingPayment = $latestPayment
        && $latestPayment->status === 'pending';

    $verifiedPayment = $billing->payments
        ->where('status', 'verified')
        ->sortByDesc('verified_at')
        ->first();

    $rejectedPayment = $billing->payments
        ->where('status', 'rejected')
        ->sortByDesc('updated_at')
        ->first();

    /*
    |--------------------------------------------------------------------------
    | Billing Adjustment Values
    |--------------------------------------------------------------------------
    */

    $remainingBalance = (float) $billing->remaining_balance;

    $discount = (float) $billing->calculated_discount;

    $penalty = (float) $billing->calculated_penalty;

    $payableAmount = (float) $billing->payable_amount;

    $monthsOverdue = (int) $billing->months_overdue;

    $isOverdue =
        $monthsOverdue > 0
        && $billing->status !== 'paid';

    $isEarly =
        $discount > 0
        && ! $isOverdue
        && $billing->status !== 'paid';

    /*
    |--------------------------------------------------------------------------
    | Payment Source
    |--------------------------------------------------------------------------
    */

    $paymentSource = null;

    if ($verifiedPayment) {
        $paymentSource = match ($verifiedPayment?->source) {
            'office_payment' => 'Office / Admin Payment',
            'client_upload' => 'Client Uploaded Payment',
            default => 'Not paid yet',
        };
    }

    if ($billing->status === 'paid' && ! $verifiedPayment) {
        $paymentSource = 'Office / Admin Payment';
    }

    /*
    |--------------------------------------------------------------------------
    | Highlight
    |--------------------------------------------------------------------------
    */

    $isHighlighted =
        isset($highlight)
        && (int) $highlight === (int) $billing->id;
@endphp

<div
    wire:key="billing-card-{{ $billing->id }}"
    x-data="{
        shouldScroll: @js($isHighlighted)
    }"
    x-init="
        if (shouldScroll) {
            setTimeout(() => {
                $el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 500);
        }
    "
    class="
        bg-white rounded-2xl shadow-sm p-5
        hover:shadow-md active:scale-[0.98]
        transition-all duration-300

        {{ $isHighlighted
            ? 'ring-2 ring-green-500 bg-green-50'
            : '' }}

        {{ $isOverdue
            ? 'border border-red-100'
            : '' }}

        {{ $isEarly
            ? 'border border-green-100'
            : '' }}
    "
>

    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

        {{-- LEFT --}}
        <div class="flex-1 min-w-0">

            {{-- HEADER --}}
            <div class="flex items-center gap-3 mb-2 flex-wrap">

                <h4 class="text-xl font-light text-gray-900">
                    {{ $billing->due_date->format('M') }}
                </h4>

                {{-- Billing status --}}
                @if($billing->status === 'paid')

                    <span class="px-3 py-1 rounded-full text-xs font-light bg-green-100 text-green-600">
                        Paid
                    </span>

                @elseif($billing->status === 'partial')

                    <span class="px-3 py-1 rounded-full text-xs font-light bg-blue-100 text-blue-600">
                        Partial
                    </span>

                @elseif($billing->status === 'cancelled')

                    <span class="px-3 py-1 rounded-full text-xs font-light bg-red-100 text-red-600">
                        Cancelled
                    </span>

                @elseif($isOverdue)

                    <span class="px-3 py-1 rounded-full text-xs font-light bg-red-100 text-red-600">
                        Overdue
                    </span>

                @else

                    <span class="px-3 py-1 rounded-full text-xs font-light bg-orange-100 text-orange-600">
                        Unpaid
                    </span>

                @endif

                {{-- Pending --}}
                @if($hasPendingPayment)
                    <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-full text-xs font-light">
                        Pending Verification
                    </span>
                @endif

                {{-- Rejected --}}
                @if(
                    $rejectedPayment
                    && ! $hasPendingPayment
                    && $billing->status !== 'paid'
                )
                    <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-light">
                        Last Payment Rejected
                    </span>
                @endif

                {{-- Early discount --}}
                @if($isEarly)

                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium">

                        <x-icon
                            name="gift"
                            class="w-3.5 h-3.5"
                        />

                        Save ₱{{ number_format($discount, 2) }}

                    </span>

                @endif

            </div>

            <p class="text-sm font-light text-gray-500">
                Due date {{ $billing->due_date->format('F d Y') }}
            </p>

            <p class="text-xs text-gray-400 mt-1">
                {{ $billing->title }}
            </p>

            {{-- EARLY PAYMENT NOTICE --}}
            @if($isEarly)

                <div class="mt-4 rounded-xl border border-green-100 bg-green-50 p-4">

                    <div class="flex items-start gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white border border-green-100">
                            <x-icon
                                name="sparkles"
                                class="w-4 h-4 text-green-600"
                            />
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-green-800">
                                Early Payment Discount Available
                            </div>

                            <p class="mt-1 text-xs text-green-600">
                                Pay before
                                {{ $billing->due_date->format('F d, Y') }}
                                and save
                                ₱{{ number_format($discount, 2) }}.
                            </p>
                        </div>

                    </div>

                </div>

            @endif

            {{-- OVERDUE NOTICE --}}
            @if($isOverdue)

                <div class="mt-4 rounded-xl border border-red-100 bg-red-50 p-4">

                    <div class="flex items-start gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white border border-red-100">
                            <x-icon
                                name="exclamation-triangle"
                                class="w-4 h-4 text-red-600"
                            />
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-red-800">
                                Late Payment Penalty Applied
                            </div>

                            <p class="mt-1 text-xs text-red-600">
                                This bill is
                                {{ $monthsOverdue }}
                                penalty period{{ $monthsOverdue !== 1 ? 's' : '' }}
                                overdue.

                                {{ number_format(
                                    $billing->monthly_penalty_rate,
                                    0
                                ) }}%
                                penalty is applied per period.
                            </p>
                        </div>

                    </div>

                </div>

            @endif

            {{-- BILL SUMMARY --}}
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">

                {{-- Amount Due --}}
                <div class="bg-gray-50 rounded-xl p-3">

                    <div class="text-gray-500 mb-1">
                        Amount Due
                    </div>

                    <div class="font-semibold text-gray-900">
                        ₱{{ number_format(
                            $billing->amount_due,
                            2
                        ) }}
                    </div>

                </div>

                {{-- Paid --}}
                <div class="bg-gray-50 rounded-xl p-3">

                    <div class="text-gray-500 mb-1">
                        Paid
                    </div>

                    <div class="font-semibold text-green-700">
                        ₱{{ number_format(
                            $billing->amount_paid,
                            2
                        ) }}
                    </div>

                </div>

                {{-- Current Balance / Payable --}}
                <div
                    class="rounded-xl p-3
                        {{ $isOverdue
                            ? 'bg-red-50'
                            : (
                                $isEarly
                                    ? 'bg-green-50'
                                    : 'bg-gray-50'
                            ) }}"
                >

                    <div class="text-gray-500 mb-1">
                        Payable Now
                    </div>

                    <div
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
                    </div>

                    @if($discount > 0)
                        <div class="mt-1 text-[10px] text-green-600">
                            Includes ₱{{ number_format(
                                $discount,
                                2
                            ) }} discount
                        </div>
                    @endif

                    @if($penalty > 0)
                        <div class="mt-1 text-[10px] text-red-600">
                            Includes ₱{{ number_format(
                                $penalty,
                                2
                            ) }} penalty
                        </div>
                    @endif

                </div>

                {{-- Source --}}
                <div class="bg-gray-50 rounded-xl p-3">

                    <div class="text-gray-500 mb-1">
                        Payment Source
                    </div>

                    <div class="font-semibold text-gray-900">
                        {{ $paymentSource ?? 'Not paid yet' }}
                    </div>

                </div>

            </div>

            {{-- ADJUSTMENT BREAKDOWN --}}
            @if(
                $billing->status !== 'paid'
                && (
                    $discount > 0
                    || $penalty > 0
                )
            )

                <div class="mt-4 rounded-xl border border-gray-100 overflow-hidden">

                    <div class="bg-gray-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Payment Breakdown
                        </p>
                    </div>

                    <div class="p-4 space-y-2 text-xs">

                        <div class="flex justify-between gap-4 text-gray-500">

                            <span>
                                Remaining Balance
                            </span>

                            <span class="font-medium text-gray-700">
                                ₱{{ number_format(
                                    $remainingBalance,
                                    2
                                ) }}
                            </span>

                        </div>

                        @if($discount > 0)

                            <div class="flex justify-between gap-4 text-green-600">

                                <span>
                                    Early Payment Discount
                                </span>

                                <span class="font-medium">
                                    -₱{{ number_format(
                                        $discount,
                                        2
                                    ) }}
                                </span>

                            </div>

                        @endif

                        @if($penalty > 0)

                            <div class="flex justify-between gap-4 text-red-600">

                                <span>
                                    Late Payment Penalty

                                    <span class="text-red-400">
                                        (
                                        {{ $monthsOverdue }}
                                        ×
                                        {{ number_format(
                                            $billing->monthly_penalty_rate,
                                            0
                                        ) }}%
                                        )
                                    </span>
                                </span>

                                <span class="font-medium">
                                    +₱{{ number_format(
                                        $penalty,
                                        2
                                    ) }}
                                </span>

                            </div>

                        @endif

                        <div class="flex justify-between gap-4 border-t border-gray-100 pt-2">

                            <span class="font-semibold text-gray-900">
                                Total Payable
                            </span>

                            <span class="font-semibold text-gray-900">
                                ₱{{ number_format(
                                    $payableAmount,
                                    2
                                ) }}
                            </span>

                        </div>

                    </div>

                </div>

            @endif

            {{-- LATEST PAYMENT --}}
            @if($latestPayment)

                <div
                    class="mt-4 p-4 rounded-xl border
                        {{ $latestPayment->status === 'pending'
                            ? 'bg-purple-50 border-purple-200'
                            : '' }}

                        {{ $latestPayment->status === 'verified'
                            ? 'bg-green-50 border-green-200'
                            : '' }}

                        {{ $latestPayment->status === 'rejected'
                            ? 'bg-red-50 border-red-200'
                            : '' }}"
                >

                    <div
                        class="font-semibold text-sm mb-2
                            {{ $latestPayment->status === 'pending'
                                ? 'text-purple-900'
                                : '' }}

                            {{ $latestPayment->status === 'verified'
                                ? 'text-green-900'
                                : '' }}

                            {{ $latestPayment->status === 'rejected'
                                ? 'text-red-900'
                                : '' }}"
                    >
                        Payment Submission Details
                    </div>

                    <div class="grid md:grid-cols-3 gap-3 text-xs text-gray-700">

                        {{-- Amount --}}
                        <div>
                            <div class="text-gray-500">
                                Amount
                            </div>

                            <div class="font-semibold">
                                ₱{{ number_format(
                                    $latestPayment->amount,
                                    2
                                ) }}
                            </div>
                        </div>

                        {{-- Method --}}
                        <div>
                            <div class="text-gray-500">
                                Method
                            </div>

                            <div class="font-semibold">
                                {{ Str::headline(
                                    $latestPayment->payment_method
                                ) }}
                            </div>
                        </div>

                        {{-- Reference --}}
                        <div>
                            <div class="text-gray-500">
                                Reference
                            </div>

                            <div class="font-semibold">
                                {{ $latestPayment->reference_no ?? 'N/A' }}
                            </div>
                        </div>

                        {{-- Submitted --}}
                        <div>
                            <div class="text-gray-500">
                                Submitted
                            </div>

                            <div class="font-semibold">
                                {{ optional(
                                    $latestPayment->paid_at
                                )->format(
                                    'M d, Y h:i A'
                                ) ?? 'N/A' }}
                            </div>
                        </div>

                        {{-- Status --}}
                        <div>
                            <div class="text-gray-500">
                                Status
                            </div>

                            <div class="font-semibold">
                                {{ Str::headline(
                                    $latestPayment->status
                                ) }}
                            </div>
                        </div>

                        {{-- Verified At --}}
                        @if($latestPayment->verified_at)

                            <div>
                                <div class="text-gray-500">
                                    Verified At
                                </div>

                                <div class="font-semibold">
                                    {{ $latestPayment
                                        ->verified_at
                                        ->format(
                                            'M d, Y h:i A'
                                        ) }}
                                </div>
                            </div>

                        @endif

                        {{-- Base amount snapshot --}}
                        @if(! is_null($latestPayment->base_amount))

                            <div>
                                <div class="text-gray-500">
                                    Original Balance
                                </div>

                                <div class="font-semibold">
                                    ₱{{ number_format(
                                        $latestPayment->base_amount,
                                        2
                                    ) }}
                                </div>
                            </div>

                        @endif

                        {{-- Discount snapshot --}}
                        @if(
                            (float) $latestPayment
                                ->discount_amount > 0
                        )

                            <div>
                                <div class="text-gray-500">
                                    Discount Applied
                                </div>

                                <div class="font-semibold text-green-700">
                                    -₱{{ number_format(
                                        $latestPayment
                                            ->discount_amount,
                                        2
                                    ) }}
                                </div>
                            </div>

                        @endif

                        {{-- Penalty snapshot --}}
                        @if(
                            (float) $latestPayment
                                ->penalty_amount > 0
                        )

                            <div>
                                <div class="text-gray-500">
                                    Penalty Applied
                                </div>

                                <div class="font-semibold text-red-700">
                                    +₱{{ number_format(
                                        $latestPayment
                                            ->penalty_amount,
                                        2
                                    ) }}
                                </div>

                                @if(
                                    $latestPayment
                                        ->penalty_months > 0
                                )
                                    <div class="mt-0.5 text-[10px] text-red-500">
                                        {{ $latestPayment->penalty_months }}
                                        penalty period{{ $latestPayment->penalty_months !== 1 ? 's' : '' }}
                                    </div>
                                @endif

                            </div>

                        @endif

                    </div>

                    {{-- Proof --}}
                    @if($latestPayment->proof_of_payment)

                        <a
                            href="{{ asset(
                                'storage/'
                                . $latestPayment
                                    ->proof_of_payment
                            ) }}"
                            target="_blank"
                            class="mt-3 inline-flex items-center gap-2 text-sm font-semibold
                                {{ $latestPayment->status === 'pending'
                                    ? 'text-purple-700'
                                    : '' }}

                                {{ $latestPayment->status === 'verified'
                                    ? 'text-green-700'
                                    : '' }}

                                {{ $latestPayment->status === 'rejected'
                                    ? 'text-red-700'
                                    : '' }}"
                        >
                            <x-icon
                                name="arrow-top-right-on-square"
                                class="w-4 h-4"
                            />

                            View Proof
                        </a>

                    @endif

                    {{-- Remarks --}}
                    @if($latestPayment->remarks)

                        <div class="mt-3 text-xs text-gray-600">
                            Remarks:
                            {{ $latestPayment->remarks }}
                        </div>

                    @endif

                </div>

            @endif

        </div>

        {{-- RIGHT ACTION --}}
        <div class="flex items-center gap-3 shrink-0">

            @if(
                $isPayable
                && $billing->status !== 'paid'
                && ! $hasPendingPayment
            )

                <x-button
                    label="Pay ₱{{ number_format($payableAmount, 2) }}"
                    icon="banknotes"
                    x-on:click="
                        $wire.$set('billingId', {{ $billing->id }})
                            .then(() => {
                                $openModal('payBill');
                            });
                    "
                    class="bg-[#101727] text-white"
                />

            @else

                <x-icon
                    name="chevron-right"
                    class="h-5 w-5 text-gray-400"
                />

            @endif

        </div>

    </div>

</div>