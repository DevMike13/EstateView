<div>
    <div class="w-full h-fit flex justify-end items-end mb-6">
        <x-button md icon="plus" label="Add Staff Member" class="bg-[#f54900] hover:bg-[#d94400] text-white" x-on:click="$openModal('newStaffMemberAccount')" />
    </div>
    
    <!-- Table -->
    <div class="min-w-full">
        <div class="border border-table-line rounded-lg overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-none [&::-webkit-scrollbar-track]:bg-scrollbar-track [&::-webkit-scrollbar-thumb]:bg-scrollbar-thumb">
            <table class="min-w-full divide-y divide-table-line">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-muted-foreground-1 uppercase">Staff Member</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-muted-foreground-1 uppercase">Status</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-muted-foreground-1 uppercase">Joined</th>
                        <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-muted-foreground-1 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-table-line">
                    @forelse ($staff as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <h4 class="font-semibold">{{ $user->name }}</h4>
                                <div class="flex items-center gap-1 mt-1">
                                    <x-icon name="at-symbol" class="w-4 h-4" />
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                <div class="flex items-center gap-1 mt-1">
                                    <x-icon name="phone" class="w-4 h-4" />
                                    <p class="text-xs text-gray-500">{{ $user->info?->phone ?? 'No phone' }}</p>
                                </div>
                                
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">
                                @if($user->is_active)
                                    <x-badge lg rounded label="Active" class="bg-green-100 text-green-700" />
                                @else
                                    <x-badge lg rounded label="Inactive" class="bg-red-100 text-red-700" />
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">
                                {{ $user->created_at->format('M. Y') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                <x-button.circle xs icon="pencil" wire:click="getSelectedStaffMember({{ $user->id }})" x-on:click="$openModal('editStaffMemberAccount')" />
                                <x-button.circle xs negative icon="trash" wire:click="deleteStaffMemberConfirmation({{$user->id}}, '{{$user->name}}')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                No staff members found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- End Table -->

    {{-- CREATE STAFF ACCOUNT --}}
    <x-modal blur name="newStaffMemberAccount" persistent align="center" max-width="xl">
        <form wire:submit.prevent="createStaffMemberAccount" class="w-full">
            <x-card title="Create Staff Account">

                <div class="mt-3">
                    <x-input
                        label="Full Name"
                        placeholder="John Doe"
                        wire:model.defer="name"
                    />
                </div>

                <div class="mt-3">
                    <x-input
                        label="Email"
                        placeholder="email@example.com"
                        wire:model.defer="email"
                    />
                </div>

                <div class="mt-3">
                    <x-inputs.phone label="Mobile No." placeholder="+63 912 345 6789" mask="['+63 ### ### ####']" class="py-3 -mt-1" wire:model="phone" />
                </div>

                <div class="mt-3">
                    <x-inputs.password label="Password" wire:model.defer="password" placeholder="Enter Initial Password" />
                </div>

                <div class="mt-3">
                    <h2 class="text-[#15233C] font-tertiary font-semibold text-md mb-3">
                        Status
                    </h2>

                    <div class="grid w-full gap-2 grid-cols-2">
                        @php
                            $options = [
                                '1' => 'Active',
                                '0' => 'Inactive',
                            ];
                        @endphp

                        @foreach($options as $value => $label)
                            <div>
                                <input
                                    wire:model.live="is_active"
                                    type="radio"
                                    id="is_active{{ $value }}"
                                    name="is_active"
                                    value="{{ $value }}"
                                    class="hidden peer"
                                >

                                <label
                                    for="is_active{{ $value }}"
                                    class="inline-flex items-center justify-center w-full p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer
                                        peer-checked:border-2 peer-checked:border-blue-600 peer-checked:text-blue-600
                                        hover:text-gray-600 hover:bg-gray-100 transition text-sm font-medium"
                                >
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    @error('is_active')
                        <span class="text-red-500 text-[10px] italic">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <x-slot name="footer" class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" @click="closeModal()" x-on:click="close"/>
                    <x-button primary label="Save" type="submit" />
                </x-slot>

            </x-card>
        </form>
    </x-modal>

    {{-- EDIT STAFF ACCOUNT --}}
    <x-modal blur name="editStaffMemberAccount" persistent align="center" max-width="xl">
        <x-card title="Edit Staff Account">

            <div class="mt-3">
                <x-input
                    label="Full Name"
                    placeholder="John Doe"
                    wire:model.defer="editName"
                />
            </div>

            <div class="mt-3">
                <x-input
                    label="Email"
                    placeholder="email@example.com"
                    wire:model.defer="editEmail"
                />
            </div>

            <div class="mt-3">
                <x-inputs.phone label="Mobile No." placeholder="+63 912 345 6789" mask="['+63 ### ### ####']" class="py-3 -mt-1" wire:model="editPhone" />
            </div>

            <div class="mt-3">
                <x-inputs.password label="Password" wire:model.defer="editPassword" placeholder="Enter Initial Password" />
            </div>

            <div class="mt-3">
                <h2 class="text-[#15233C] font-tertiary font-semibold text-md mb-3">
                    Status
                </h2>

                <div class="grid w-full gap-2 grid-cols-2">
                    @php
                        $options = [
                            '1' => 'Active',
                            '0' => 'Inactive',
                        ];
                    @endphp

                    @foreach($options as $value => $label)
                        <div>
                            <input
                                wire:model.live="edit_is_active"
                                type="radio"
                                id="edit_is_active{{ $value }}"
                                name="edit_is_active"
                                value="{{ $value }}"
                                class="hidden peer"
                            >

                            <label
                                for="edit_is_active{{ $value }}"
                                class="inline-flex items-center justify-center w-full p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer
                                    peer-checked:border-2 peer-checked:border-blue-600 peer-checked:text-blue-600
                                    hover:text-gray-600 hover:bg-gray-100 transition text-sm font-medium"
                            >
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>

                @error('edit_is_active')
                    <span class="text-red-500 text-[10px] italic">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <x-slot name="footer" class="flex justify-end gap-x-4">
                <x-button flat label="Cancel" @click="closeModal()" x-on:click="close"/>
                <x-button primary label="Save" wire:click="editStaffMemberConfirmation('{{$editName}}')" />
            </x-slot>

        </x-card>
    </x-modal>
</div>
