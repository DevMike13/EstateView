<div class="w-full h-auto pt-28 md:pt-40 p-5 md:p-0">
    <div class="w-full h-auto max-w-5xl mx-auto flex justify-end">
        <x-button icon="clipboard-list" label="Create Reservation" x-on:click="$openModal('createReservation')" class="bg-[#101727] text-white hover:text-[#101727] rounded-xl ml-auto block" />
    </div>

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

                {{-- Awaiting Fee --}}
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

                {{-- Fee Submitted --}}
                <button
                    wire:click="setTab('reservation_fee_submitted')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'reservation_fee_submitted'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}"
                >
                    <span>Fee Submitted</span>

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
                    wire:key="reservation-{{ $reservation->id }}"
                    x-data="{
                        shouldScroll: @js((int) $highlight === (int) $reservation->id),
                        open: @js((int) $highlight === (int) $reservation->id)
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
                    x-on:click="open = !open"
                    class="bg-white rounded-xl p-5 shadow border mb-4 transition-all cursor-pointer
                        {{ (int) $highlight === (int) $reservation->id
                            ? 'ring-2 ring-green-500 bg-green-50'
                            : '' }}"
                >

                    {{-- COLLAPSED SUMMARY (shown when card is closed) --}}
                    <div
                        x-show="!open"
                        class="w-full flex items-center justify-between gap-3 text-left"
                    >
                        <div class="flex items-center gap-3 md:gap-4 min-w-0">

                            <div class="h-10 w-10 md:h-12 md:w-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <x-icon name="home-modern" class="w-6 h-6 text-green-600" />
                            </div>

                            <div class="min-w-0">
                                <h3 class="text-base md:text-lg font-semibold text-gray-900 truncate">
                                    {{ $reservation->lot?->name }}
                                </h3>

                                <p class="text-xs md:text-sm text-gray-500">
                                    Click to view reservation details
                                </p>
                            </div>

                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">

                            <span class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap
                                @if($reservation->status === 'pending')
                                    bg-yellow-100 text-yellow-700

                                @elseif($reservation->status === 'awaiting_reservation_fee')
                                    bg-blue-100 text-blue-700

                                @elseif($reservation->status === 'reservation_fee_submitted')
                                    bg-purple-100 text-purple-700

                                @elseif($reservation->status === 'approved')
                                    bg-green-100 text-green-700

                                @else
                                    bg-red-100 text-red-700
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                            </span>

                            <x-icon name="chevron-down" class="w-5 h-5 text-gray-500" />

                        </div>
                    </div>

                    {{-- EXPANDED DETAILS (shown when card is open) --}}
                    <div x-show="open" x-cloak>

                    {{-- HEADER --}}
                    <div class="flex items-start mb-4 gap-3 md:gap-4">

                        <div class="h-10 w-10 md:h-12 md:w-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-icon name="home-modern" class="w-6 h-6 text-green-600" />
                        </div>
                        
                        <div class="w-full">

                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-1">

                                <h3 class="text-base md:text-lg font-semibold text-gray-900">
                                    {{ $reservation->lot?->name }}
                                </h3>

                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($reservation->status === 'pending')
                                        bg-yellow-100 text-yellow-700

                                    @elseif($reservation->status === 'awaiting_reservation_fee')
                                        bg-blue-100 text-blue-700

                                    @elseif($reservation->status === 'reservation_fee_submitted')
                                        bg-purple-100 text-purple-700

                                    @elseif($reservation->status === 'approved')
                                        bg-green-100 text-green-700

                                    @else
                                        bg-red-100 text-red-700
                                    @endif
                                ">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </div>

                            <div class="text-xs md:text-sm text-gray-600 space-y-1">

                                <div class="flex items-center gap-2">
                                    <x-icon name="map-pin" class="w-4 h-4" />

                                    <span>
                                        {{ $reservation->type }}
                                    </span>
                                </div>

                                @if($reservation->houseModel)
                                    <div class="flex items-center gap-2">
                                        <x-icon name="building-office-2" class="w-4 h-4" />

                                        <span>
                                            {{ $reservation->houseModel->model_name }}
                                        </span>
                                    </div>
                                @endif

                            </div>

                        </div>

                        <div class="flex-shrink-0 p-1">
                            <x-icon name="chevron-up" class="w-5 h-5 text-gray-500" />
                        </div>

                    </div>

                    {{-- DETAILS --}}
                    <div class="grid md:grid-cols-4 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">

                        <div>
                            <div class="text-xs text-gray-500 mb-1">
                                Preferred Payment
                            </div>

                            <div class="font-medium text-gray-900">
                                {{ $reservation->preferredPayment?->payment_type }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 mb-1">
                                Documents Uploaded
                            </div>

                            <div class="font-medium text-gray-900">
                                {{ $reservation->requiredDocuments->count() }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 mb-1">
                                Reserved At
                            </div>

                            <div class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($reservation->reserved_at)->format('M d, Y') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500 mb-1">
                                Submitted
                            </div>

                            <div class="font-medium text-gray-900">
                                {{ $reservation->created_at->diffForHumans() }}
                            </div>
                        </div>

                    </div>

                    {{-- NOTES --}}
                    @if($reservation->notes)
                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">

                            <div class="text-xs font-medium text-blue-900 mb-1">
                                Notes
                            </div>

                            <div class="text-sm text-blue-800">
                                {{ $reservation->notes }}
                            </div>

                        </div>
                    @endif

                    {{-- AWAITING PAYMENT --}}
                    @if($reservation->status === 'awaiting_reservation_fee')

                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">

                            <div class="flex justify-between items-center">

                                <div>

                                    <div class="text-xs text-blue-700">
                                        Reservation Fee Required
                                    </div>

                                    <div class="text-xl font-bold text-blue-900">
                                        ₱{{ number_format(
                                            $reservation->type === 'House & Lot'
                                                ? 50000
                                                : 20000,
                                            2
                                        ) }}
                                    </div>

                                </div>

                                <x-button
                                    label="Proceed to Payment"
                                    icon="banknotes"
                                    x-on:click.stop="
                                        $wire.reservationId = {{ $reservation->id }};
                                        $openModal('reservationPayment')
                                    "
                                    class="bg-[#101727] text-white hover:!bg-[#3b4a68] hover:!text-white transition-colors"
                                />

                            </div>

                        </div>

                    @endif

                    {{-- PAYMENT DETAILS --}}
                    @if(
                        in_array($reservation->status, [
                            'reservation_fee_submitted',
                            'approved'
                        ])
                        && $reservation->latestReservationPayment
                    )

                        <div class="mb-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">

                            <div class="text-sm font-semibold mb-3">

                                @if($reservation->status === 'approved')
                                    Reservation Fee Verified
                                @else
                                    Payment Verification In Progress
                                @endif

                            </div>

                            <div class="grid md:grid-cols-3 gap-4">

                                <div>
                                    <div class="text-xs text-gray-500">
                                        Amount
                                    </div>

                                    <div class="font-semibold">
                                        ₱{{ number_format(
                                            $reservation->latestReservationPayment->amount,
                                            2
                                        ) }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500">
                                        Payment Status
                                    </div>

                                    <div class="font-semibold">
                                        {{ Str::headline($reservation->latestReservationPayment->status) }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500">
                                        Method
                                    </div>

                                    <div class="font-semibold">
                                        {{ Str::headline(
                                            $reservation->latestReservationPayment->payment_method
                                        ) }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500">
                                        Reference
                                    </div>

                                    <div class="font-semibold">
                                        {{ $reservation->latestReservationPayment->reference_no ?? 'N/A' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500">
                                        Verified Date
                                    </div>

                                    <div class="font-semibold">
                                        {{ $reservation->updated_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>

                            </div>

                            <a
                                href="{{ asset('storage/' . $reservation->latestReservationPayment->proof_of_payment) }}"
                                target="_blank"
                                x-on:click.stop
                                class="mt-3 inline-flex text-purple-700 font-medium"
                            >
                                View Uploaded Proof
                            </a>

                        </div>

                    @endif
                    
                    {{-- APPROVED SUCCESS MESSAGE --}}
                    @if($reservation->status === 'approved')

                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">

                            <div class="flex items-start gap-3">

                                <x-icon
                                    name="check-circle"
                                    class="w-6 h-6 text-green-600"
                                />

                                <div>

                                    <div class="font-semibold text-green-900">
                                        Reservation Approved
                                    </div>

                                    <div class="text-sm text-green-700">
                                        Your reservation fee has been verified and your lot is now officially reserved.
                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif
                    {{-- DOCUMENTS --}}
                    <div class="border border-dashed rounded-lg p-4">

                        <div class="text-sm font-semibold mb-3">
                            Uploaded Documents
                        </div>

                        <div class="grid md:grid-cols-2 gap-3">

                            @foreach($reservation->requiredDocuments as $document)

                                <a
                                    href="{{ asset('storage/' . $document->file_path) }}"
                                    target="_blank"
                                    x-on:click.stop
                                    class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition"
                                >
                                    <div>
                                        <div class="text-sm font-medium text-gray-800">
                                            {{ Str::headline($document->document_type) }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $document->original_name }}
                                        </div>
                                    </div>

                                    <x-icon name="arrow-top-right-on-square" class="w-4 h-4 text-gray-500" />
                                </a>

                            @endforeach

                        </div>

                    </div>

                    </div>
                    {{-- END EXPANDED DETAILS --}}

                </div>

            @empty

                <div class="col-span-3 text-center py-10 text-gray-500 border-2 border-dashed rounded-lg">
                    <p class="italic text-gray-400">
                        No reservations found.
                    </p>
                </div>

            @endforelse

        </div>

    </div>

    {{-- CREATE MODAL --}}
    <x-modal
        blur
        name="createReservation"
        persistent
        align="center"
        max-width="xl"

        x-on:reservation-created.window="
            close();

            setTimeout(() => {
                $wire.showReservationSuccess();
            }, 350);
        "
    >
        <x-card title="Create New Reservation">
            
            <div class="mt-3">
                <h2 class="text-[#15233C] font-tertiary font-semibold text-md mb-3">
                    Reservation Type
                </h2>

                <div class="grid w-full gap-2 grid-cols-2">
                    @php
                        $options = [
                            'House & Lot' => 'House & Lot',
                            'Lot Only' => 'Lot Only',
                        ];
                    @endphp

                    @foreach($options as $value => $label)
                        <div>
                            <input
                                wire:model.live="reservationType"
                                type="radio"
                                id="reservationType{{ $value }}"
                                name="reservationType"
                                value="{{ $value }}"
                                class="hidden peer"
                            >

                            <label
                                for="reservationType{{ $value }}"
                                class="inline-flex items-center justify-center w-full p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer
                                    peer-checked:border-2 peer-checked:border-blue-600 peer-checked:text-blue-600
                                    hover:text-gray-600 hover:bg-gray-100 transition text-sm font-medium"
                            >
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>

                @error('reservationType')
                    <span class="text-red-500 text-[10px] italic">
                        {{ $message }}
                    </span>
                @enderror
            </div>
           
            {{-- <div class="mt-3 relative">
                <div class="pointer-events-none">
                    <x-select
                        label="Agent"
                        placeholder="Select an agent (optional)"
                        wire:model.defer="agentId"
                        :options="$this->agents"
                        :template="[
                            'name' => 'user-option',
                            'config' => ['src' => 'profile_picture']
                        ]"
                        option-label="name"
                        option-value="id"
                        option-description="email"
                        searchable
                        clearable
                    />
                </div>

                @error('agentId')
                    <span class="text-xs italic text-red-500">
                        {{ $message }}
                    </span>
                @enderror
            </div> --}}
            
            @if ($reservationType && $reservationType === "House & Lot")
                <div class="mt-3">
                    <x-select
                        label="House Model"
                        wire:model.defer="houseModelId"
                        placeholder="Select some house model"
                        :async-data="route('api.house-models.index')"
                        :template="[
                            'name'   => 'user-option',
                            'config' => ['src' => 'image']
                        ]"
                        option-label="name"
                        option-value="id"
                        option-description="description"
                    />
                </div>
            @endif
            
            <div class="mt-3">
                {{-- <x-select
                    key="{{ $lotApiUrl }}"
                    label="Lot Location"
                    wire:model.defer="lotLocationId"
                    placeholder="Select lot location"
                    :async-data="$lotApiUrl"
                    option-label="name"
                    option-value="id"
                    option-description="description"
                /> --}}
                <x-select
                    key="lot-select-{{ $reservationType }}"
                    label="Lot Location"
                    wire:model.live="lotLocationId"
                    placeholder="Select lot location"
                    :async-data="$lotApiUrl"
                    option-label="name"
                    option-value="id"
                    option-description="description"
                />
            </div>

            <div class="mt-3">
                <x-select
                    label="Preferred Payment"
                    placeholder="Select preferred payment"
                    :options="
                        $reservationType === 'Lot Only'
                            ? [
                                ['id' => 'cash', 'name' => 'Cash'],
                                ['id' => 'bank_loan', 'name' => 'Bank Loan'],
                                ['id' => 'deferred_payment', 'name' => 'Deferred Payment'],
                            ]
                            : [
                                ['id' => 'cash', 'name' => 'Cash'],
                                ['id' => 'bank_loan', 'name' => 'Bank Loan'],
                            ]
                    "
                    option-label="name"
                    option-value="id"
                    wire:model.live="preferredPayment"
                />
             </div>


            @if($preferredPayment === 'bank_loan')

                <div
                    x-data="{
                        percentage: @entangle('downpaymentPercentage').live
                    }"
                    class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg"
                >

                    <div class="mb-4">

                        <div class="flex justify-between mb-2">
                            <label class="text-sm font-medium text-blue-900">
                                Downpayment Percentage
                            </label>

                            <span class="text-sm font-semibold text-blue-900">
                                <span x-text="percentage"></span>%
                            </span>
                        </div>

                        <input
                            type="range"
                            min="20"
                            max="100"
                            step="5"
                            x-model="percentage"
                            class="w-full accent-blue-700"
                        >

                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>20%</span>
                            <span>100%</span>
                        </div>

                        <div class="mt-2 text-xs text-blue-700">
                            Minimum downpayment is 20%. Adjustments are by 5%.
                        </div>

                    </div>

                    <x-select
                        label="Downpayment Term"
                        wire:model.live="downpaymentTermMonths"
                        :options="[
                            ['id' => 12, 'name' => '12 Months'],
                            ['id' => 18, 'name' => '18 Months'],
                            ['id' => 24, 'name' => '24 Months'],
                        ]"
                        option-label="name"
                        option-value="id"
                    />

                </div>

            @endif

            <div class="mt-3 border-[1.5px] border-gray-100 border-dashed p-5 rounded-lg">
                <h2 class="font-semibold text-sm mb-3">
                    Required Documents
                </h2>

                {{-- 1x1 Pictures --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">5 pcs 1x1 Picture</label>
                    <x-filepond::upload
                        wire:model="doc_1x1"
                        multiple
                        :accepted-file-types="['image/png','image/jpeg','image/webp']"
                    />

                    @error('doc_1x1')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('doc_1x1.*')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Primary IDs --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">Primary IDs + Signatures</label>
                    <x-filepond::upload
                        wire:model="doc_primary_ids"
                        multiple
                    />

                    @error('doc_primary_ids')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('doc_primary_ids.*')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Billing --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">Proof of Billing</label>
                    <x-filepond::upload
                        wire:model="doc_billing"
                        multiple
                    />

                    @error('doc_billing')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('doc_billing.*')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- PSA --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">PSA Birth / Marriage / Spouse IDs</label>
                    <x-filepond::upload
                        wire:model="doc_psa"
                        multiple
                    />

                    @error('doc_psa')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('doc_psa.*')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Income --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">Proof of Income</label>
                    <x-filepond::upload
                        wire:model="doc_income"
                        multiple
                    />

                    @error('doc_income')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('doc_income.*')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- TIN --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">TIN ID</label>
                    <x-filepond::upload
                        wire:model="doc_tin"
                        multiple
                    />

                    @error('doc_tin')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('doc_tin.*')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <x-textarea label="Additional Notes" placeholder="write your notes" wire:model="reservationNotes" />
            </div>

            <x-slot name="footer" class="flex justify-end gap-x-4">
                <x-button flat label="Cancel" @click="closeModal()" x-on:click="close"/>
                <x-button primary label="Save" wire:click="confirmReservation" />
            </x-slot>

        </x-card>
    </x-modal>

    <x-modal blur name="reservationPayment" align="center">

        <x-card title="Reservation Fee Payment">

            <div class="mb-4 p-4 bg-blue-50 rounded-lg">

                <div class="text-xs text-blue-600">
                    Reservation Fee
                </div>

                <div class="text-2xl font-bold text-blue-900">
                    Amount will be verified automatically
                </div>

                <div class="text-sm text-gray-600 mt-1">
                    Lot Only = ₱20,000
                    <br>
                    House & Lot = ₱50,000
                </div>

            </div>

            <x-select
                label="Payment Method"
                wire:model.live="paymentMethod"
                :options="[
                    ['id' => 'cash', 'name' => 'Cash'],
                    ['id' => 'bank_transfer', 'name' => 'Bank Transfer'],
                    ['id' => 'gcash', 'name' => 'GCash'],
                    ['id' => 'maya', 'name' => 'Maya'],
                ]"
                option-label="name"
                option-value="id"
            />

            @if($this->selectedQrCode)
                <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4 text-center">
                    <div class="text-sm font-medium text-gray-900 mb-2">
                        {{ Str::headline($this->selectedQrCode->payment_method) }} QR Code
                    </div>

                    <img
                        src="{{ asset('storage/' . $this->selectedQrCode->qr_image) }}"
                        class="mx-auto h-64 w-full object-contain rounded-lg border bg-white"
                    >

                    <div class="mt-3 text-sm text-gray-700">
                        {{ $this->selectedQrCode->account_name }}
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ $this->selectedQrCode->account_number }}
                    </div>
                </div>
            @elseif($paymentMethod && $paymentMethod !== 'cash')
                <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">
                    No active QR code available for {{ Str::headline($paymentMethod) }}.
                </div>
            @endif

            <div class="mt-4">
                <x-input
                    label="Reference Number"
                    wire:model="referenceNo"
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    required
                />
            </div>

            <div class="mt-4">
                <label class="text-sm font-medium">
                    Proof of Payment
                </label>

                <x-filepond::upload
                    wire:model="proofOfPayment"
                    required
                />

                @error('proofOfPayment')
                    <span class="text-sm text-red-500">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <x-slot name="footer">

                <div class="flex justify-end gap-2">

                    <x-button
                        flat
                        label="Cancel"
                        x-on:click="close"
                    />

                    <x-button
                        primary
                        label="Submit Payment"
                        wire:click="submitReservationPayment"
                    />

                </div>

            </x-slot>

        </x-card>

    </x-modal>      
</div>
