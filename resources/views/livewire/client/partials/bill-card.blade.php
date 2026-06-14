@php
    $balance = $billing->amount_due - $billing->amount_paid;

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
@endphp

<div class="bg-white rounded-2xl shadow-sm p-5 hover:shadow-md transition-shadow active:scale-[0.98] transition-transform">

    <div class="flex items-start justify-between gap-4">

        <div class="flex-1">

            <div class="flex items-center gap-3 mb-2 flex-wrap">

                <h4 class="text-xl font-light text-gray-900">
                    {{ $billing->due_date->format('M') }}
                </h4>

                <span class="px-3 py-1 rounded-full text-xs font-light
                    {{ $billing->status === 'paid' ? 'bg-green-100 text-green-600' : '' }}
                    {{ $billing->status === 'partial' ? 'bg-blue-100 text-blue-600' : '' }}
                    {{ $billing->status === 'unpaid' ? 'bg-orange-100 text-orange-600' : '' }}
                    {{ $billing->status === 'cancelled' ? 'bg-red-100 text-red-600' : '' }}
                ">
                    {{ Str::headline($billing->status) }}
                </span>

                @if($hasPendingPayment)
                    <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-full text-xs font-light">
                        Pending Verification
                    </span>
                @endif

                @if($rejectedPayment && ! $hasPendingPayment && $billing->status !== 'paid')
                    <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-light">
                        Last Payment Rejected
                    </span>
                @endif

            </div>

            <p class="text-sm font-light text-gray-500">
                Due date {{ $billing->due_date->format('F d Y') }}
            </p>

            <p class="text-xs text-gray-400 mt-1">
                {{ $billing->title }}
            </p>

            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">

                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="text-gray-500 mb-1">Amount Due</div>
                    <div class="font-semibold text-gray-900">
                        ₱{{ number_format($billing->amount_due, 2) }}
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="text-gray-500 mb-1">Paid</div>
                    <div class="font-semibold text-green-700">
                        ₱{{ number_format($billing->amount_paid, 2) }}
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="text-gray-500 mb-1">Balance</div>
                    <div class="font-semibold text-red-700">
                        ₱{{ number_format($balance, 2) }}
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="text-gray-500 mb-1">Payment Source</div>
                    <div class="font-semibold text-gray-900">
                        {{ $paymentSource ?? 'Not paid yet' }}
                    </div>
                </div>

            </div>

            @if($latestPayment)
                <div class="mt-4 p-4 rounded-xl border
                    {{ $latestPayment->status === 'pending' ? 'bg-purple-50 border-purple-200' : '' }}
                    {{ $latestPayment->status === 'verified' ? 'bg-green-50 border-green-200' : '' }}
                    {{ $latestPayment->status === 'rejected' ? 'bg-red-50 border-red-200' : '' }}
                ">

                    <div class="font-semibold text-sm mb-2
                        {{ $latestPayment->status === 'pending' ? 'text-purple-900' : '' }}
                        {{ $latestPayment->status === 'verified' ? 'text-green-900' : '' }}
                        {{ $latestPayment->status === 'rejected' ? 'text-red-900' : '' }}
                    ">
                        Payment Submission Details
                    </div>

                    <div class="grid md:grid-cols-3 gap-3 text-xs text-gray-700">

                        <div>
                            <div class="text-gray-500">Amount</div>
                            <div class="font-semibold">
                                ₱{{ number_format($latestPayment->amount, 2) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">Method</div>
                            <div class="font-semibold">
                                {{ Str::headline($latestPayment->payment_method) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">Reference</div>
                            <div class="font-semibold">
                                {{ $latestPayment->reference_no ?? 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">Submitted</div>
                            <div class="font-semibold">
                                {{ optional($latestPayment->paid_at)->format('M d, Y h:i A') ?? 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500">Status</div>
                            <div class="font-semibold">
                                {{ Str::headline($latestPayment->status) }}
                            </div>
                        </div>

                        @if($latestPayment->verified_at)
                            <div>
                                <div class="text-gray-500">Verified At</div>
                                <div class="font-semibold">
                                    {{ $latestPayment->verified_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        @endif

                    </div>

                    @if($latestPayment->proof_of_payment)
                        <a
                            href="{{ asset('storage/' . $latestPayment->proof_of_payment) }}"
                            target="_blank"
                            class="mt-3 inline-flex items-center gap-2 text-sm font-semibold
                                {{ $latestPayment->status === 'pending' ? 'text-purple-700' : '' }}
                                {{ $latestPayment->status === 'verified' ? 'text-green-700' : '' }}
                                {{ $latestPayment->status === 'rejected' ? 'text-red-700' : '' }}
                            "
                        >
                            <x-icon name="arrow-top-right-on-square" class="w-4 h-4" />
                            View Proof
                        </a>
                    @endif

                    @if($latestPayment->remarks)
                        <div class="mt-3 text-xs text-gray-600">
                            Remarks: {{ $latestPayment->remarks }}
                        </div>
                    @endif

                </div>
            @endif

        </div>

        <div class="flex items-center gap-3">

            @if($isPayable && $billing->status !== 'paid' && ! $hasPendingPayment)
                <x-button
                    label="Pay"
                    icon="banknotes"
                    x-on:click="
                        $wire.billingId = {{ $billing->id }};
                        $openModal('payBill')
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