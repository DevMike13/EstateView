<div>
    <div class="w-full h-fit flex justify-end items-end mb-6">
        <x-button md icon="plus" label="Add Agent Member" class="bg-[#f54900] hover:bg-[#d94400] text-white" x-on:click="$openModal('newAgentMemberAccount')" />
    </div>
    
    <!-- Table -->
    <div class="min-w-full">
        <div class="border border-table-line rounded-lg overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-none [&::-webkit-scrollbar-track]:bg-scrollbar-track [&::-webkit-scrollbar-thumb]:bg-scrollbar-thumb">
            <table class="min-w-full divide-y divide-table-line">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-muted-foreground-1 dark:text-gray-500 uppercase"
                        >
                            Agent Member
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-muted-foreground-1 dark:text-gray-500 uppercase"
                        >
                            Professional Information
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-muted-foreground-1 dark:text-gray-500 uppercase"
                        >
                            Commission
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-muted-foreground-1 dark:text-gray-500 uppercase"
                        >
                            Status
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-start text-xs font-medium text-muted-foreground-1 dark:text-gray-500 uppercase"
                        >
                            Joined
                        </th>

                        <th
                            scope="col"
                            class="px-6 py-3 text-end text-xs font-medium text-muted-foreground-1 dark:text-gray-500 uppercase"
                        >
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-table-line">
                    @forelse ($this->agents as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    type="button"
                                    x-on:click="
                                        $wire.openAgentCommissionModal({{ $user->id }});
                                        $openModal('agentCommissionDetails');
                                    "
                                    class="group flex items-center gap-3 text-left"
                                >
                                    <div class="relative shrink-0">
                                        <img
                                            src="{{ $user->profile_picture
                                                ? asset(ltrim($user->profile_picture, '/'))
                                                : asset('images/default-avatar.png') }}"
                                            alt="{{ $user->name }}"
                                            class="h-11 w-11 rounded-full border border-gray-200 object-cover"
                                        >

                                        @if(($user->pending_commission_requests_count ?? 0) > 0)
                                            <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4">
                                                <span
                                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"
                                                ></span>

                                                <span
                                                    class="relative inline-flex h-4 w-4 items-center justify-center rounded-full bg-orange-500 text-[9px] font-bold text-white"
                                                >
                                                    {{ min($user->pending_commission_requests_count, 9) }}
                                                </span>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="truncate font-semibold text-gray-900 group-hover:text-orange-600">
                                                {{ $user->name }}
                                            </h4>

                                            @if(($user->pending_commission_requests_count ?? 0) > 0)
                                                <span class="rounded-full bg-yellow-100 px-2 py-0.5 text-[10px] font-semibold text-yellow-700">
                                                    Commission Request
                                                </span>
                                            {{-- @elseif(($user->commission_requests_count ?? 0) > 0)
                                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-700">
                                                    Commission History
                                                </span> --}}
                                            @endif
                                        </div>

                                        <div class="mt-1 flex items-center gap-1">
                                            <x-icon
                                                name="at-symbol"
                                                class="h-4 w-4"
                                            />

                                            <p class="text-xs text-gray-500">
                                                {{ $user->email }}
                                            </p>
                                        </div>

                                        <div class="mt-1 flex items-center gap-1">
                                            <x-icon
                                                name="phone"
                                                class="h-4 w-4"
                                            />

                                            <p class="text-xs text-gray-500">
                                                {{ $user->info?->phone ?? 'No phone' }}
                                            </p>
                                        </div>
                                    </div>
                                </button>
                            </td>

                            <td class="px-6 py-4 min-w-[220px]">
                                <div>
                                    <div class="text-xs text-gray-400">
                                        Agent ID
                                    </div>

                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $user->info?->professional_agent_id ?? 'Not provided' }}
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="text-xs text-gray-400">
                                        Real Estate License
                                    </div>

                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $user->info?->real_estate_license_number ?? 'Not provided' }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(! is_null($user->info?->commission_percentage))
                                    <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-sm font-semibold text-orange-700">
                                        {{ number_format(
                                            $user->info->commission_percentage,
                                            2
                                        ) }}%
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">
                                        Not set
                                    </span>
                                @endif
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
                                <x-button.circle xs icon="pencil" wire:click="getSelectedAgentMember({{ $user->id }})" x-on:click="$openModal('editAgentMemberAccount')" />
                                <x-button.circle xs negative icon="trash" wire:click="deleteAgentMemberConfirmation({{$user->id}}, '{{$user->name}}')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No agent members found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- End Table -->

    {{-- CREATE AGENT ACCOUNT --}}
    <x-modal blur name="newAgentMemberAccount" persistent align="center" max-width="xl">
        <form wire:submit.prevent="createAgentMemberAccount" class="w-full">
            <x-card title="Create Agent Account">

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

                <div class="mt-5 border-t border-gray-100 pt-5">
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Professional Information
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            These fields are optional and may be added later.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input
                            label="Agent ID"
                            placeholder="Example: AGT-2026-001"
                            wire:model.defer="professionalAgentId"
                        />

                        <x-input
                            label="Real Estate License Number"
                            placeholder="Enter license number"
                            wire:model.defer="realEstateLicenseNumber"
                        />
                    </div>

                    <div class="mt-4">
                        <x-input
                            label="Commission Percentage"
                            placeholder="Example: 5"
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            suffix="%"
                            wire:model.defer="commissionPercentage"
                        />

                        <p class="mt-1 text-xs text-gray-500">
                            Enter a percentage from 0 to 100.
                        </p>
                    </div>
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

    {{-- EDIT AGENT ACCOUNT --}}
    <x-modal blur name="editAgentMemberAccount" persistent align="center" max-width="xl">
        <x-card title="Edit Agent Account">

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

            <div class="mt-5 border-t border-gray-100 pt-5">
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Professional Information
                    </h3>

                    <p class="mt-1 text-xs text-gray-500">
                        Update the agent's identification and commission rate.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input
                        label="Agent ID"
                        placeholder="Example: AGT-2026-001"
                        wire:model.defer="editProfessionalAgentId"
                    />

                    <x-input
                        label="Real Estate License Number"
                        placeholder="Enter license number"
                        wire:model.defer="editRealEstateLicenseNumber"
                    />
                </div>

                <div class="mt-4">
                    <x-input
                        label="Commission Percentage"
                        placeholder="Example: 5"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        suffix="%"
                        wire:model.defer="editCommissionPercentage"
                    />
                </div>
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
                <x-button primary label="Save" wire:click="editAgentMemberConfirmation('{{$editName}}')" />
            </x-slot>

        </x-card>
    </x-modal>


    <x-modal
        blur
        name="agentCommissionDetails"
        align="center"
        max-width="6xl"
    >
        <x-card>
            @if($this->commissionAgent)

                @php
                    $commissionAgent = $this->commissionAgent;
                @endphp

                <div class="space-y-6">

                    {{-- Agent header --}}
                    <div class="flex flex-col gap-4 border-b border-gray-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <img
                                src="{{ $commissionAgent->profile_picture
                                    ? asset(ltrim(
                                        $commissionAgent->profile_picture,
                                        '/'
                                    ))
                                    : asset('images/default-avatar.png') }}"
                                alt="{{ $commissionAgent->name }}"
                                class="h-14 w-14 rounded-full border border-gray-200 object-cover"
                            >

                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">
                                    {{ $commissionAgent->name }}
                                </h2>

                                <p class="text-sm text-gray-500">
                                    {{ $commissionAgent->email }}
                                </p>

                                <p class="mt-1 text-xs text-purple-600">
                                    {{ number_format(
                                        $commissionAgent->info
                                            ?->commission_percentage ?? 0,
                                        2
                                    ) }}% commission rate
                                </p>
                            </div>
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $this->commissionClients->count() }}
                            assigned client{{ $this->commissionClients->count() !== 1 ? 's' : '' }}
                        </div>
                    </div>

                    @if(! $showCommissionClientDetails)

                        {{-- QR Codes --}}
                        <div>
                            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Payment QR Codes
                            </p>

                            <div class="flex gap-3 overflow-x-auto pb-2">
                                @forelse($commissionAgent->qrCodes as $qrCode)
                                    <div class="w-36 shrink-0 rounded-xl border border-gray-200 bg-white p-3">
                                        <div class="relative">
                                            <img
                                                src="{{ $qrCode->image_url }}"
                                                alt="{{ $qrCode->provider_name }}"
                                                class="h-28 w-full rounded-lg object-contain"
                                            >

                                            @if($qrCode->is_primary)
                                                <span class="absolute left-1 top-1 rounded-full bg-blue-600 px-2 py-0.5 text-[9px] font-semibold text-white">
                                                    Primary
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-2 truncate text-xs font-semibold text-gray-900">
                                            {{ $qrCode->provider_name }}
                                        </p>

                                        <p class="truncate text-[10px] text-gray-500">
                                            {{ $qrCode->account_name }}
                                        </p>

                                        @if($qrCode->account_number)
                                            <p class="truncate text-[10px] text-gray-400">
                                                {{ $qrCode->account_number }}
                                            </p>
                                        @endif

                                        <a
                                            href="{{ $qrCode->image_url }}"
                                            target="_blank"
                                            class="mt-2 block text-center text-[10px] font-medium text-blue-600"
                                        >
                                            View full QR
                                        </a>
                                    </div>
                                @empty
                                    <div class="rounded-xl border border-dashed border-gray-200 px-5 py-8 text-sm text-gray-400">
                                        This agent has not uploaded a QR code.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Clients --}}
                        <div>
                            <p class="mb-3 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <x-icon
                                    name="users"
                                    class="h-3.5 w-3.5"
                                />

                                Clients ({{ $this->commissionClients->count() }})
                            </p>

                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @forelse($this->commissionClients as $account)
                                    <button
                                        type="button"
                                        wire:click="selectCommissionClient({{ $account->id }})"
                                        class="flex items-center justify-between gap-3 rounded-xl border bg-white p-5 text-left transition-all hover:shadow-md
                                            {{ $account->has_pending_commission_request
                                                ? 'border-yellow-300 hover:border-yellow-400'
                                                : 'border-gray-100 hover:border-orange-200' }}"
                                    >
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="truncate font-medium text-gray-900">
                                                    {{ $account->user?->name }}
                                                </p>

                                                @if($account->has_pending_commission_request)
                                                    <span class="flex shrink-0 items-center gap-1 rounded-full bg-yellow-100 px-1.5 py-0.5 text-[10px] font-semibold text-yellow-700">
                                                        <x-icon
                                                            name="clock"
                                                            class="h-3 w-3"
                                                        />

                                                        To Pay
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="mt-0.5 truncate text-xs text-gray-400">
                                                {{ $account->lot?->name ?? 'No lot information' }}
                                            </p>

                                            <p class="mt-2 text-sm font-medium text-gray-700">
                                                ₱{{ number_format(
                                                    $account->total_contract_price,
                                                    2
                                                ) }}
                                            </p>

                                            @if($account->pending_commission_request_count > 0)
                                                <p class="mt-1 text-[10px] text-yellow-600">
                                                    {{ $account->pending_commission_request_count }}
                                                    pending commission request{{ $account->pending_commission_request_count !== 1 ? 's' : '' }}
                                                </p>
                                            @endif
                                        </div>

                                        <x-icon
                                            name="chevron-right"
                                            class="h-4 w-4 shrink-0 text-gray-400"
                                        />
                                    </button>
                                @empty
                                    <div class="col-span-full rounded-xl border border-dashed border-gray-200 p-8 text-center text-sm text-gray-400">
                                        No assigned client ledgers found.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    @elseif($this->commissionAccount)

                        @php
                            $account = $this->commissionAccount;
                        @endphp

                        {{-- Client details header --}}
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                wire:click="backToCommissionClients"
                                class="rounded-lg p-2 transition-colors hover:bg-gray-100"
                            >
                                <x-icon
                                    name="arrow-left"
                                    class="h-5 w-5 text-gray-600"
                                />
                            </button>

                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    {{ $account->user?->name }}
                                </h2>

                                <p class="text-sm text-gray-500">
                                    {{ $account->lot?->name ?? 'No lot information' }}
                                </p>
                            </div>
                        </div>

                        {{-- Summary --}}
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <div class="rounded-xl border border-gray-100 bg-white p-5">
                                <p class="mb-1 text-xs uppercase tracking-wide text-gray-500">
                                    Total Contract Price
                                </p>

                                <p class="text-lg font-semibold text-gray-900">
                                    ₱{{ number_format(
                                        $account->total_contract_price,
                                        2
                                    ) }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-purple-100 bg-purple-50 p-5">
                                <p class="mb-1 text-xs uppercase tracking-wide text-purple-500">
                                    Commission Rate
                                </p>

                                <p class="text-lg font-semibold text-purple-700">
                                    {{ number_format(
                                        $account->agent_commission_percentage,
                                        2
                                    ) }}%
                                </p>
                            </div>

                            <div class="rounded-xl border border-green-100 bg-green-50 p-5">
                                <p class="mb-1 text-xs uppercase tracking-wide text-green-600">
                                    Total Commission
                                </p>

                                <p class="text-lg font-semibold text-green-700">
                                    ₱{{ number_format(
                                        $account->total_agent_commission,
                                        2
                                    ) }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-blue-100 bg-blue-50 p-5">
                                <p class="mb-1 text-xs uppercase tracking-wide text-blue-500">
                                    Paid to Agent
                                </p>

                                <p class="text-lg font-semibold text-blue-700">
                                    ₱{{ number_format(
                                        $account->paid_commission,
                                        2
                                    ) }}
                                </p>

                                <p class="mt-1 text-xs text-blue-400">
                                    {{ $account->paid_period_count }}
                                    of {{ $account->total_commission_periods }}
                                    periods paid
                                </p>
                            </div>
                        </div>

                        {{-- Dynamic commission periods --}}
                        <div>
                            <p class="mb-3 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <x-icon
                                    name="banknotes"
                                    class="h-3.5 w-3.5"
                                />

                                Commission Ledger — Every 3 Paid Months
                            </p>

                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                @foreach($account->commission_periods as $period)

                                    @php
                                        $request = $period['request'];

                                        $borderClass = match(true) {
                                            $request?->status === 'pending'
                                                => 'border-yellow-300',

                                            $request?->status === 'paid'
                                                => 'border-green-300',

                                            $request?->status === 'rejected'
                                                => 'border-red-300',

                                            default
                                                => 'border-gray-100',
                                        };
                                    @endphp

                                    <div class="space-y-3 rounded-xl border bg-white p-5 {{ $borderClass }}">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-600">
                                                    {{ $period['label'] }}
                                                </p>

                                                <p class="mt-0.5 text-[10px] text-gray-400">
                                                    {{ $period['months_label'] }}
                                                </p>
                                            </div>

                                            @if($request?->status === 'pending')
                                                <x-icon
                                                    name="clock"
                                                    class="h-4 w-4 text-yellow-500"
                                                />
                                            @elseif($request?->status === 'paid')
                                                <x-icon
                                                    name="check-circle"
                                                    class="h-4 w-4 text-green-500"
                                                />
                                            @endif
                                        </div>

                                        <p class="text-lg font-semibold text-blue-700">
                                            ₱{{ number_format(
                                                $period['amount'],
                                                2
                                            ) }}
                                        </p>

                                        <div class="flex flex-wrap gap-2">
                                            @foreach($period['billings'] as $billing)
                                                @php
                                                    $billingPaid =
                                                        $billing->status === 'paid'
                                                        || (float) $billing->amount_paid
                                                            >= (float) $billing->amount_due;
                                                @endphp

                                                <div class="flex items-center gap-1 text-xs text-gray-400">
                                                    <span class="inline-block h-2 w-2 rounded-full
                                                        {{ $billingPaid
                                                            ? 'bg-green-500'
                                                            : 'bg-gray-200' }}"
                                                    ></span>

                                                    {{ $billing->due_date->format('M') }}
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($request?->status === 'pending')

                                            <button
                                                type="button"
                                                x-on:click="
                                                    $wire.openCommissionPayment({{ $request->id }});
                                                    $openModal('commissionPaymentModal');
                                                "
                                                class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-gray-900 py-2 text-xs font-medium text-white transition-colors hover:bg-gray-700"
                                            >
                                                <x-icon
                                                    name="banknotes"
                                                    class="h-3.5 w-3.5"
                                                />

                                                Pay Commission
                                            </button>

                                        @elseif($request?->status === 'paid')

                                            <div class="rounded-lg bg-green-100 px-3 py-2 text-center text-xs font-medium text-green-700">
                                                Commission paid
                                            </div>

                                            @if($request->receipt_path)
                                                <a
                                                    href="{{ asset(
                                                        'storage/' . ltrim(
                                                            $request->receipt_path,
                                                            '/'
                                                        )
                                                    ) }}"
                                                    target="_blank"
                                                    class="block text-center text-xs font-medium text-blue-600"
                                                >
                                                    View payment receipt
                                                </a>
                                            @endif

                                        @elseif($request?->status === 'rejected')

                                            <div class="rounded-lg bg-red-100 px-3 py-2 text-center text-xs font-medium text-red-700">
                                                Request rejected
                                            </div>

                                        @elseif($period['all_paid'])

                                            <p class="text-center text-xs text-gray-400">
                                                Agent has not requested this period.
                                            </p>

                                        @else

                                            <p class="text-center text-xs text-gray-400">
                                                Client payments are incomplete.
                                            </p>

                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    @endif
                </div>

            @else
                <div class="py-12 text-center text-sm text-gray-400">
                    Select an agent to view commission details.
                </div>
            @endif

            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-button
                        flat
                        label="Close"
                        {{-- x-on:click="close" --}}
                        x-on:click="
                            $wire.closeAgentCommissionModal();
                            close();
                        "
                        {{-- wire:click="closeAgentCommissionModal" --}}
                    />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <x-modal
        blur
        name="commissionPaymentModal"
        align="center"
        max-width="2xl"
        x-on:commission-paid.window="close()"
    >
        <x-card title="Pay Agent Commission">

            @if($this->payingCommissionRequest)

                @php
                    $paymentRequest =
                        $this->payingCommissionRequest;

                    $paymentAgent =
                        $paymentRequest->agent;

                    $paymentAccount =
                        $paymentRequest->purchaseAccount;
                @endphp

                <div class="space-y-6">

                    {{-- Payment summary --}}
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">

                        <div class="flex items-start justify-between gap-4">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Commission Payment
                                </p>

                                <h3 class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $paymentAgent?->name }}
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Client:
                                    {{ $paymentAccount?->user?->name }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    {{ $paymentAccount?->lot?->name
                                        ?? 'No lot information' }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-xs uppercase tracking-wide text-gray-400">
                                    Amount
                                </p>

                                <p class="mt-1 text-2xl font-semibold text-green-700">
                                    ₱{{ number_format(
                                        $paymentRequest->requested_amount,
                                        2
                                    ) }}
                                </p>
                            </div>

                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                                {{ $paymentRequest->period_label }}
                            </span>

                            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                Pending
                            </span>
                        </div>
                    </div>

                    {{-- Agent QR --}}
                    <div>
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Agent Payment QR Codes
                        </p>

                        <div class="flex gap-3 overflow-x-auto pb-2">

                            @forelse(
                                $paymentAgent?->qrCodes
                                    ?->sortByDesc('is_primary')
                                    ?? collect()
                                as $qrCode
                            )

                                <div
                                    class="w-40 shrink-0 rounded-xl border bg-white p-3
                                        {{ $qrCode->is_primary
                                            ? 'border-blue-300 ring-2 ring-blue-100'
                                            : 'border-gray-200' }}"
                                >

                                    <div class="relative">

                                        <a
                                            href="{{ $qrCode->image_url }}"
                                            target="_blank"
                                        >
                                            <img
                                                src="{{ $qrCode->image_url }}"
                                                alt="{{ $qrCode->provider_name }}"
                                                class="h-32 w-full rounded-lg object-contain"
                                            >
                                        </a>

                                        @if($qrCode->is_primary)
                                            <span class="absolute left-1 top-1 rounded-full bg-blue-600 px-2 py-0.5 text-[9px] font-semibold text-white">
                                                Primary
                                            </span>
                                        @endif

                                    </div>

                                    <p class="mt-2 truncate text-xs font-semibold text-gray-900">
                                        {{ $qrCode->provider_name }}
                                    </p>

                                    <p class="truncate text-[10px] text-gray-500">
                                        {{ $qrCode->account_name }}
                                    </p>

                                    @if($qrCode->account_number)
                                        <p class="mt-0.5 truncate text-[10px] text-gray-400">
                                            {{ $qrCode->account_number }}
                                        </p>
                                    @endif

                                </div>

                            @empty

                                <div class="w-full rounded-xl border border-dashed border-gray-200 p-5 text-center">
                                    <x-icon
                                        name="qr-code"
                                        class="mx-auto h-7 w-7 text-gray-300"
                                    />

                                    <p class="mt-2 text-xs text-gray-400">
                                        Agent has not uploaded a payment QR code.
                                    </p>
                                </div>

                            @endforelse

                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- Receipt --}}
                    <div>
                        <p class="mb-3 text-sm font-semibold text-gray-900">
                            Payment Receipt
                        </p>

                        <x-filepond::upload
                            wire:model="commissionReceipt"
                            multiple="false"
                            max-files="1"
                            accepted-file-types="image/jpeg,image/png,image/webp"
                            required
                        />

                        @error('commissionReceipt')
                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Reference --}}
                    <x-input
                        label="Payment Reference"
                        placeholder="Example: GCash reference number"
                        wire:model.defer="commissionPaymentReference"
                    />

                    {{-- Notes --}}
                    <x-textarea
                        label="Payment Notes"
                        placeholder="Optional notes about this commission payout..."
                        wire:model.defer="commissionPaymentNotes"
                    />

                    {{-- Confirmation --}}
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <div class="flex items-start gap-3">

                            <x-icon
                                name="information-circle"
                                class="mt-0.5 h-5 w-5 shrink-0 text-blue-600"
                            />

                            <p class="text-xs leading-relaxed text-blue-700">
                                Confirming this payment will mark
                                {{ $paymentRequest->period_label }}
                                as paid and notify the agent that their
                                ₱{{ number_format(
                                    $paymentRequest->requested_amount,
                                    2
                                ) }}
                                commission has been released.
                            </p>

                        </div>
                    </div>

                </div>

            @endif

            <x-slot name="footer">
                <div class="flex justify-end gap-2">

                    <x-button
                        flat
                        label="Cancel"
                        {{-- x-on:click="close" --}}
                        x-on:click="
                            $wire.closeCommissionPaymentModal();
                            close();
                        "
                        {{-- wire:click="closeCommissionPaymentModal" --}}
                    />

                    <x-button
                        primary
                        icon="check"
                        label="Confirm Commission Payment"
                        wire:click="payCommissionRequest"
                        wire:loading.attr="disabled"
                        wire:target="payCommissionRequest,commissionReceipt"
                    />

                </div>
            </x-slot>

        </x-card>
    </x-modal>
</div>
