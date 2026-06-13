<div class="w-full h-auto">
    <div class="w-full max-w-5xl my-5 mx-auto p-4 bg-[#f9fafc] rounded-xl shadow">

        {{-- TAB NAV --}}
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex gap-x-1 whitespace-nowrap min-w-max">

                {{-- Pending --}}
                <button
                    wire:click="setTab('pending')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'pending'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}"
                >
                    <span>Pending</span>

                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->pendingCount }}
                    </span>
                </button>

                <button
                    wire:click="setTab('awaiting_reservation_fee')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'awaiting_reservation_fee'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}"
                >
                    <span>Awaiting Fee</span>

                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->awaitingReservationFeeCount }}
                    </span>
                </button>

                <button
                    wire:click="setTab('reservation_fee_submitted')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'reservation_fee_submitted'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}"
                >
                    <span>Fee Verification</span>

                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->reservationFeeSubmittedCount }}
                    </span>
                </button>

                {{-- Approved --}}
                <button
                    wire:click="setTab('approved')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'approved'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}"
                >
                    <span>Approved</span>

                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->approvedCount }}
                    </span>
                </button>

                {{-- Rejected --}}
                <button
                    wire:click="setTab('rejected')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'rejected'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}"
                >
                    <span>Rejected</span>

                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->rejectedCount }}
                    </span>
                </button>

            </nav>
        </div>

        {{-- TAB CONTENT --}}
        <div class="mt-5">

            @forelse($this->reservations as $reservation)

                <div
                    x-data="{
                        shouldScroll: @js($highlight == $reservation->id),
                    }"
                    x-init="
                        if (shouldScroll) {
                            setTimeout(() => {
                                $el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }, 500);
                        }
                    "
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5 {{ $highlight == $reservation->id ? 'ring-2 ring-green-500' : '' }}"
                >

                    {{-- HEADER --}}
                    <div class="p-4 md:p-6 bg-gradient-to-r from-green-50 to-blue-50 border-b">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                            <div class="flex items-start gap-3 md:gap-4">

                                <div class="h-10 w-10 md:h-12 md:w-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <x-icon name="document-check" class="h-5 w-5 md:h-6 md:w-6 text-white" />
                                </div>

                                <div>

                                    <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-1">
                                        {{ $reservation->user->name }}
                                    </h3>

                                    <div class="text-xs md:text-sm text-gray-600 space-y-1">

                                        <div class="flex items-center gap-2">
                                            <x-icon name="user" class="h-4 w-4 flex-shrink-0" />

                                            <span class="break-all">
                                                {{ $reservation->user->email }}
                                            </span>

                                            <span class="hidden sm:inline">•</span>

                                            <span class="hidden sm:inline">
                                                {{ $reservation->user->info->phone ?? 'N/A' }}
                                            </span>
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            Submitted {{ $reservation->created_at->diffForHumans() }}
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- STATUS --}}
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $reservation->status === 'awaiting_reservation_fee' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $reservation->status === 'reservation_fee_submitted' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $reservation->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $reservation->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                            ">
                                {{ Str::headline($reservation->status) }}
                            </span>

                        </div>
                    </div>

                    {{-- BODY --}}
                    <div class="p-4 md:p-6">

                        <div class="grid md:grid-cols-2 gap-6 mb-6">

                            {{-- LEFT --}}
                            <div class="space-y-4">

                                <div class="flex items-start gap-3">
                                    <x-icon name="map-pin" class="h-5 w-5 text-blue-600 mt-0.5" />

                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">
                                            Lot Location
                                        </div>

                                        <div class="font-medium text-gray-900">
                                            {{ $reservation->lot->name ?? '' }}
                                            -
                                            {{ $reservation->lot->lot_number ?? '' }}
                                            ({{ $reservation->lot->lot_area ?? '' }} sqm)
                                        </div>
                                    </div>
                                </div>

                                @if($reservation->houseModel)
                                    <div class="flex items-start gap-3">
                                        <x-icon name="home" class="h-5 w-5 text-blue-600 mt-0.5" />

                                        <div>
                                            <div class="text-xs text-gray-500 mb-1">
                                                House Model
                                            </div>

                                            <div class="font-medium text-gray-900">
                                                {{ $reservation->houseModel->name }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>

                            {{-- RIGHT --}}
                            <div class="space-y-4">

                                <div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Reservation Type
                                    </div>

                                    <div class="font-medium text-gray-900">
                                        {{ $reservation->type }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Preferred Payment
                                    </div>

                                    <div class="font-medium text-gray-900">
                                        {{ $reservation->preferredPayment->payment_type ?? 'N/A' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Reserved At
                                    </div>

                                    <div class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($reservation->reserved_at)->format('M d, Y h:i A') }}
                                    </div>
                                </div>

                            </div>

                        </div>

                        {{-- DOCUMENTS --}}
                        <div class="mb-6">

                            <div class="flex items-center gap-2 mb-3">
                                <x-icon name="document-text" class="h-5 w-5 text-blue-600" />

                                <h4 class="font-medium text-gray-900">
                                    Submitted Documents
                                </h4>
                            </div>

                            <div class="grid md:grid-cols-2 gap-3">

                                @forelse($reservation->requiredDocuments as $document)

                                    <a
                                        href="{{ asset('storage/' . $document->file_path) }}"
                                        target="_blank"
                                        class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition"
                                    >
                                        <div>

                                            <div class="text-sm font-medium text-gray-800">
                                                {{ Str::headline($document->document_type) }}
                                            </div>

                                            <div class="text-xs text-gray-500 break-all">
                                                {{ $document->original_name }}
                                            </div>

                                        </div>

                                        <x-icon
                                            name="arrow-top-right-on-square"
                                            class="w-4 h-4 text-gray-500"
                                        />

                                    </a>

                                @empty

                                    <div class="col-span-2 text-sm text-gray-400 italic">
                                        No uploaded documents.
                                    </div>

                                @endforelse

                            </div>

                        </div>

                        {{-- NOTES --}}
                        @if($reservation->notes)
                            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">

                                <div class="text-xs font-medium text-blue-900 mb-1">
                                    Notes
                                </div>

                                <div class="text-sm text-blue-800">
                                    {{ $reservation->notes }}
                                </div>

                            </div>
                        @endif
                        
                        @if(in_array($reservation->status, ['reservation_fee_submitted', 'approved']) && $reservation->latestReservationPayment)
                            <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">

                                <div class="flex items-center gap-2 mb-3">
                                    <x-icon name="banknotes" class="h-5 w-5 text-purple-600" />

                                    <h4 class="font-medium text-gray-900">
                                        Reservation Fee Payment
                                    </h4>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4 text-sm">

                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Amount</div>
                                        <div class="font-semibold text-gray-900">
                                            ₱{{ number_format($reservation->latestReservationPayment->amount, 2) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Payment Status</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ Str::headline($reservation->latestReservationPayment->status) }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Payment Method</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ Str::headline($reservation->latestReservationPayment->payment_method) }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Reference No.</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $reservation->latestReservationPayment->reference_no ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Paid At</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ optional($reservation->latestReservationPayment->paid_at)->format('M d, Y h:i A') ?? 'N/A' }}
                                        </div>
                                    </div>

                                </div>

                                @if($reservation->latestReservationPayment->proof_of_payment)
                                    <a
                                        href="{{ asset('storage/' . $reservation->latestReservationPayment->proof_of_payment) }}"
                                        target="_blank"
                                        class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-purple-700 hover:text-purple-900"
                                    >
                                        <x-icon name="arrow-top-right-on-square" class="w-4 h-4" />
                                        View Proof of Payment
                                    </a>
                                @endif

                            </div>
                        @endif
                        {{-- ACTIONS --}}
                        <div class="flex flex-col sm:flex-row gap-3">

                            @if($reservation->status === 'pending')

                                <div class="sm:w-4/5 w-full">
                                    <x-button
                                        wire:click="confirmApprove({{ $reservation->id }})"
                                        icon="check"
                                        label="Approve Reservation"
                                        class="w-full bg-[#00a73e] hover:text-[#101727] text-white rounded-lg font-semibold"
                                    />
                                </div>

                                <div class="sm:w-1/5 w-full">
                                    <x-button
                                        wire:click="confirmReject({{ $reservation->id }})"
                                        icon="x-mark"
                                        label="Reject"
                                        class="w-full border border-red-400 text-red-900 rounded-lg font-semibold"
                                    />
                                </div>
                            @elseif($reservation->status === 'reservation_fee_submitted' && $reservation->latestReservationPayment)

                                <div class="sm:w-4/5 w-full">
                                    <x-button
                                        wire:click="confirmApproveReservationFee({{ $reservation->latestReservationPayment->id }})"
                                        icon="check"
                                        label="Verify Reservation Fee"
                                        class="w-full bg-[#00a73e] hover:text-[#101727] text-white rounded-lg font-semibold"
                                    />
                                </div>

                                <div class="sm:w-1/5 w-full">
                                    <x-button
                                        wire:click="confirmRejectReservationFee({{ $reservation->latestReservationPayment->id }})"
                                        icon="x-mark"
                                        label="Reject"
                                        class="w-full border border-red-400 text-red-900 rounded-lg font-semibold"
                                    />
                                </div>
                                
                            @elseif($reservation->status === 'rejected')

                                <x-button
                                    wire:click="confirmRestore({{ $reservation->id }})"
                                    icon="arrow-uturn-left"
                                    label="Restore"
                                    class="w-full bg-gray-600 hover:bg-gray-800 text-white rounded-lg font-semibold"
                                />

                            @endif

                        </div>

                    </div>
                </div>

            @empty

                <div class="text-center py-10 text-gray-500 border-2 border-dashed rounded-lg">
                    <p class="italic text-gray-400">
                        No reservations found.
                    </p>
                </div>

            @endforelse

        </div>

    </div>
</div>
