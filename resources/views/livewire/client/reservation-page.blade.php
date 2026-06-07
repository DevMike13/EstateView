<div class="w-full h-auto pt-10 md:pt-40 p-5 md:p-0">
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

                <div class="bg-white rounded-xl p-5 shadow border mb-4">

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
                                    @elseif($reservation->status === 'approved')
                                        bg-green-100 text-green-700
                                    @elseif($reservation->status === 'completed')
                                        bg-blue-100 text-blue-700
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
                                            {{ $reservation->houseModel->name }}
                                        </span>
                                    </div>
                                @endif

                            </div>

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
    <x-modal blur name="createReservation" persistent align="center" max-width="xl">
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

            @if ($reservationType && $reservationType === "House & Lot")
                <div class="mt-3">
                    <x-select
                        label="House Model"
                        wire:model.defer="houseModelId"
                        placeholder="Select some client"
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
                    :options="['Loanable', 'Deferred']"
                    wire:model.live="preferredPayment"
                />
            </div>

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
                </div>

                {{-- Primary IDs --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">Primary IDs + Signatures</label>
                    <x-filepond::upload
                        wire:model="doc_primary_ids"
                        multiple
                    />
                </div>

                {{-- Billing --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">Proof of Billing</label>
                    <x-filepond::upload
                        wire:model="doc_billing"
                        multiple
                    />
                </div>

                {{-- PSA --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">PSA Birth / Marriage / Spouse IDs</label>
                    <x-filepond::upload
                        wire:model="doc_psa"
                        multiple
                    />
                </div>

                {{-- Income --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">Proof of Income</label>
                    <x-filepond::upload
                        wire:model="doc_income"
                        multiple
                    />
                </div>

                {{-- TIN --}}
                <div class="mb-4">
                    <label class="text-xs font-medium">TIN ID</label>
                    <x-filepond::upload
                        wire:model="doc_tin"
                        multiple
                    />
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
</div>
