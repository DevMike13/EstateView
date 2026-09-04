<main class="flex-1 min-w-0">
    <div class="w-full mx-auto space-y-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Payment QR Codes</h1>
            <p class="text-sm md:text-base text-gray-600">Upload QR codes for GCash, Maya, and bank payments.</p>
        </div>

        <div class="bg-white rounded-xl border shadow-sm p-6">
            <div class="grid md:grid-cols-2 gap-4">
                <x-select
                    label="Payment Method"
                    wire:model.defer="payment_method"
                    :options="[
                        ['id' => 'gcash', 'name' => 'GCash'],
                        ['id' => 'maya', 'name' => 'Maya'],
                        ['id' => 'bank_transfer', 'name' => 'Bank Transfer'],
                    ]"
                    option-label="name"
                    option-value="id"
                />

                <x-input
                    label="Account Name"
                    wire:model.defer="account_name"
                    maxlength="50"
                    oninput="
                        this.value = this.value
                            .replace(/[^A-Za-z ]/g, '')
                            .slice(0, 50)
                    "
                />
                <x-input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    label="Account Number"
                    wire:model.defer="account_number"
                    maxlength="50"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                />

                <div class="col-span-2">
                    <label class="text-sm font-medium text-gray-700">
                        QR Image
                    </label>

                    <div class="mt-2">
                        <x-filepond::upload
                            wire:model="qr_image"
                            accepted-file-types="image/png,image/jpeg,image/jpg,image/webp"
                        />
                    </div>

                    @error('qr_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <x-button primary label="Upload QR Code" wire:click="save" spinner="save" rounded />
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @forelse($qrCodes as $qr)
                <div class="bg-white rounded-xl border shadow-sm p-4">
                    <img
                        src="{{ asset('storage/' . $qr->qr_image) }}"
                        class="w-full h-56 object-contain rounded-lg border"
                    >

                    <div class="mt-4">
                        <div class="font-semibold">{{ Str::headline($qr->payment_method) }}</div>
                        <div class="text-sm text-gray-600">{{ $qr->account_name }}</div>
                        <div class="text-sm text-gray-600">{{ $qr->account_number }}</div>

                        <div class="mt-3">
                            @if($qr->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <x-button
                            gray
                            sm
                            icon="pencil"
                            label="Edit"
                            wire:click="edit({{ $qr->id }})"
                            x-on:click="$openModal('editQrCodeModal')"
                            rounded
                        />

                        <x-button
                            red
                            sm
                            icon="trash"
                            label="Delete"
                            wire:click="confirmDelete({{ $qr->id }})"
                            rounded
                        />
                    </div>
                </div>
            @empty
                <div class="md:col-span-3 border border-dashed rounded-xl p-8 text-center text-gray-400">
                    No QR codes uploaded yet.
                </div>
            @endforelse
        </div>

        <x-modal blur name="editQrCodeModal" max-width="2xl">
            <x-card title="Edit QR Code">
                <div class="space-y-4">
                    <x-select
                        label="Payment Method"
                        wire:model.defer="edit_payment_method"
                        :options="[
                            ['id' => 'gcash', 'name' => 'GCash'],
                            ['id' => 'maya', 'name' => 'Maya'],
                            ['id' => 'bank_transfer', 'name' => 'Bank Transfer'],
                        ]"
                        option-label="name"
                        option-value="id"
                    />

                    <x-input
                        label="Account Name"
                        wire:model.defer="edit_account_name"
                        maxlength="50"
                        oninput="
                            this.value = this.value
                                .replace(/[^A-Za-z ]/g, '')
                                .slice(0, 50)
                        "
                    />

                    <x-input
                        label="Account Number"
                        wire:model.defer="edit_account_number"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="50"
                        oninput="
                            this.value = this.value
                                .replace(/[^0-9]/g, '')
                                .slice(0, 50)
                        "
                    />

                    @if($edit_existing_qr_image)
                        <div>
                            <label class="text-sm font-medium text-gray-700">
                                Current QR Image
                            </label>

                            <div class="relative mt-2">
                                <img
                                    src="{{ asset('storage/' . $edit_existing_qr_image) }}"
                                    class="h-48 w-full object-contain rounded-lg border"
                                >

                                <button
                                    type="button"
                                    wire:click="removeExistingQrImage"
                                    class="
                                        absolute top-2 right-2
                                        flex h-7 w-7 items-center justify-center
                                        rounded-full
                                        bg-red-600 text-white
                                        shadow
                                        hover:bg-red-700
                                        transition
                                    "
                                    title="Remove QR image"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M18 6 6 18"/>
                                        <path d="m6 6 12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="text-sm font-medium text-gray-700">
                            Replace QR Image
                        </label>

                        <div class="mt-2">
                            <x-filepond::upload
                                wire:model="edit_qr_image"
                                accepted-file-types="image/png,image/jpeg,image/jpg,image/webp"
                            />
                        </div>

                        @error('edit_qr_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="inline-flex items-center gap-2">
                        <input
                            type="checkbox"
                            wire:model.defer="edit_is_active"
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        >
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-2">
                        <x-button flat label="Cancel" x-on:click="close" />

                        <x-button
                            primary
                            label="Save Changes"
                            wire:click="confirmUpdate"
                        />
                    </div>
                </x-slot>
            </x-card>
        </x-modal>
    </div>
</main>