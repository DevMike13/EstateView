<div class="w-full h-auto pt-40">
    <div class="w-full h-auto max-w-5xl mx-auto">
        <x-button icon="clipboard-list" label="Create Reservation" x-on:click="$openModal('createReservation')" class="bg-[#101727] text-white hover:text-[#101727] rounded-xl" />
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
                <x-select
                    key="{{ $lotApiUrl }}"
                    label="Lot Location"
                    wire:model.defer="lotLocationId"
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
