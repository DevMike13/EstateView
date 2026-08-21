<div class="min-h-screen bg-gray-50 py-24 lg:py-32">
    <main>
        <section class="pb-24 pt-10">
            <div class="mx-auto max-w-7xl px-6 sm:px-8 lg:px-12">

                {{-- Header Controls --}}
                <div class="mb-6 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                    {{-- Search --}}
                    <div class="relative w-full sm:w-80">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                        >
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>

                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Search client..."
                            class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                    </div>

                    {{-- Agent Payment QR Codes --}}
                    <div class="w-full sm:w-auto">
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                    My Payment QR Codes
                                </p>

                                <p class="mt-0.5 text-[10px] text-gray-400">
                                    Admin will use these for commission payouts.
                                </p>
                            </div>

                            @if($this->qrCodes->isNotEmpty())
                                <button
                                    type="button"
                                    x-on:click="$openModal('createQrCode')"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white transition hover:bg-gray-700"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="h-3.5 w-3.5"
                                    >
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5v14"></path>
                                    </svg>
                                    Add QR
                                </button>
                            @endif
                        </div>

                        <div class="flex max-w-full gap-3 overflow-x-auto pb-2 sm:max-w-md">

                            @forelse($this->qrCodes as $qrCode)

                                <div
                                    wire:key="agent-qr-code-{{ $qrCode->id }}"
                                    class="relative w-36 shrink-0 rounded-xl border bg-white p-3 shadow-sm
                                        {{ $qrCode->is_primary
                                            ? 'border-blue-300 ring-2 ring-blue-100'
                                            : 'border-gray-200' }}"
                                >
                                    @if($qrCode->is_primary)
                                        <span class="absolute left-2 top-2 z-10 rounded-full bg-blue-600 px-2 py-0.5 text-[9px] font-semibold text-white">
                                            Primary
                                        </span>
                                    @endif

                                    <a
                                        href="{{ $qrCode->image_url }}"
                                        target="_blank"
                                        class="block"
                                    >
                                        <img
                                            src="{{ $qrCode->image_url }}"
                                            alt="{{ $qrCode->provider_name }} QR Code"
                                            class="h-28 w-full rounded-lg object-cover"
                                        >
                                    </a>

                                    <div class="mt-2 min-w-0">
                                        <p class="truncate text-xs font-semibold text-gray-900">
                                            {{ $qrCode->label ?: $qrCode->provider_name }}
                                        </p>

                                        <p class="mt-0.5 truncate text-[10px] text-gray-500">
                                            {{ $qrCode->account_name }}
                                        </p>

                                        @if($qrCode->account_number)
                                            <p class="mt-0.5 truncate text-[10px] text-gray-400">
                                                {{ $qrCode->account_number }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex items-center justify-between gap-1">
                                        @if(! $qrCode->is_primary)
                                            <button
                                                type="button"
                                                wire:click="setPrimaryQrCode({{ $qrCode->id }})"
                                                class="rounded-md px-2 py-1 text-[10px] font-medium text-blue-600 hover:bg-blue-50"
                                            >
                                                Set primary
                                            </button>
                                        @else
                                            <span class="text-[10px] text-blue-600">
                                                Default
                                            </span>
                                        @endif

                                        <div class="flex items-center gap-1">
                                            <button
                                                type="button"
                                                x-on:click="
                                                    $wire.openEditQrCode({{ $qrCode->id }});
                                                    $openModal('editQrCode');
                                                "
                                                class="rounded-md p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                                                title="Edit"
                                            >
                                                <x-icon
                                                    name="pencil"
                                                    class="h-3.5 w-3.5"
                                                />
                                            </button>

                                            <button
                                                type="button"
                                                wire:click="confirmDeleteQrCode({{ $qrCode->id }})"
                                                class="rounded-md p-1.5 text-gray-500 hover:bg-red-50 hover:text-red-600"
                                                title="Delete"
                                            >
                                                <x-icon
                                                    name="trash"
                                                    class="h-3.5 w-3.5"
                                                />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            @empty

                                <button
                                    type="button"
                                    x-on:click="$openModal('createQrCode')"
                                    class="flex h-36 w-36 shrink-0 flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white text-gray-400 transition hover:border-gray-500 hover:text-gray-600"
                                >
                                    <x-icon
                                        name="qr-code"
                                        class="h-8 w-8"
                                    />

                                    <span class="mt-2 text-[10px] font-medium">
                                        Add payment QR
                                    </span>
                                </button>

                            @endforelse
                        </div>
                    </div>
                </div>

                <div
                    x-data="{ openAccount: null }"
                    class="space-y-3"
                >

                    @forelse($this->accounts as $account)

                        <div
                            wire:key="commission-account-wrapper-{{ $account->id }}"
                            class="bg-white shadow-sm"
                        >

                            {{-- CLIENT CARD / COLLAPSE BUTTON --}}
                            <button
                                type="button"
                                x-on:click="
                                    if (openAccount === {{ $account->id }}) {
                                        openAccount = null;
                                    } else {
                                        openAccount = {{ $account->id }};
                                        $wire.selectAccount({{ $account->id }});
                                    }
                                "
                                class="w-full border-l-4 p-5 text-left transition-all hover:shadow-md"
                                x-bind:class="
                                    openAccount === {{ $account->id }}
                                        ? 'border-gray-900'
                                        : 'border-transparent'
                                "
                            >
                                <div class="flex items-start justify-between gap-3">

                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-gray-900">
                                            {{ $account->user?->name ?? 'Unknown Client' }}
                                        </p>

                                        <p class="mt-0.5 truncate text-xs text-gray-500">
                                            {{ $account->lot?->name ?? 'No lot information' }}
                                        </p>

                                        <p class="mt-1 text-[11px] text-gray-400">
                                            {{ \Illuminate\Support\Str::headline($account->payment_scheme) }}
                                        </p>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-3">

                                        @if($account->is_current_month_paid)
                                            <span class="mt-0.5 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-600">
                                                Paid
                                            </span>
                                        @else
                                            <span class="mt-0.5 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-600">
                                                Unpaid
                                            </span>
                                        @endif

                                        {{-- COLLAPSE ARROW --}}
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="h-5 w-5 text-gray-400 transition-transform duration-200"
                                            x-bind:class="
                                                openAccount === {{ $account->id }}
                                                    ? 'rotate-180'
                                                    : ''
                                            "
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m19.5 8.25-7.5 7.5-7.5-7.5"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-3 flex flex-wrap items-center gap-4">

                                    <div class="flex items-center gap-1 text-xs text-gray-500">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-3 w-3"
                                        >
                                            <line x1="19" x2="5" y1="5" y2="19"></line>
                                            <circle cx="6.5" cy="6.5" r="2.5"></circle>
                                            <circle cx="17.5" cy="17.5" r="2.5"></circle>
                                        </svg>

                                        {{ number_format(
                                            $account->agent_commission_percentage,
                                            2
                                        ) }}% commission
                                    </div>

                                    <div class="text-xs font-medium text-purple-700">
                                        ₱{{ number_format(
                                            $account->total_agent_commission,
                                            2
                                        ) }}
                                    </div>

                                </div>
                            </button>

                            <div
                                x-show="openAccount === {{ $account->id }}"
                                x-collapse
                                x-cloak
                            >

                                @if(
                                    $this->selectedAccount
                                    && $this->selectedAccount->id === $account->id
                                )

                                    @php
                                        $account = $this->selectedAccount;
                                    @endphp

                                    <div class="space-y-6 border-t border-gray-100 bg-white p-5 sm:p-7">
                                        {{-- Client Header --}}
                                        {{-- <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                            <div>
                                                <h2 class="text-xl font-medium text-gray-900">
                                                    {{ $account->user?->name ?? 'Unknown Client' }}
                                                </h2>

                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ $account->lot?->name ?? 'No lot information' }}
                                                </p>

                                                <p class="mt-1 text-xs text-gray-400">
                                                    {{ \Illuminate\Support\Str::headline($account->payment_scheme) }}
                                                </p>
                                            </div>

                                            @if($account->is_current_month_paid)

                                                <span class="flex w-fit shrink-0 items-center gap-1.5 rounded-full bg-green-100 px-3 py-1.5 text-sm font-medium text-green-600">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="24"
                                                        height="24"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="h-4 w-4"
                                                    >
                                                        <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                                                        <path d="m9 11 3 3L22 4"></path>
                                                    </svg>

                                                    Paid this month
                                                </span>

                                            @else

                                                <span class="flex w-fit shrink-0 items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-sm font-medium text-red-600">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="24"
                                                        height="24"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="h-4 w-4"
                                                    >
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <path d="m15 9-6 6"></path>
                                                        <path d="m9 9 6 6"></path>
                                                    </svg>

                                                    Not paid this month
                                                </span>

                                            @endif
                                        </div> --}}

                                        <div class="border-t border-gray-100"></div>

                                        {{-- Financial Summary --}}
                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                            <div class="rounded-lg bg-gray-50 p-4">
                                                <p class="mb-1 text-xs uppercase tracking-wide text-gray-500">
                                                    Total Contract Price
                                                </p>

                                                <p class="break-words text-lg font-medium text-gray-900">
                                                    ₱{{ number_format(
                                                        $account->total_contract_price,
                                                        2
                                                    ) }}
                                                </p>
                                            </div>

                                            <div class="rounded-lg bg-purple-50 p-4">
                                                <p class="mb-1 text-xs uppercase tracking-wide text-purple-500">
                                                    Commission Rate
                                                </p>

                                                <p class="text-lg font-medium text-purple-700">
                                                    {{ number_format(
                                                        $account->agent_commission_percentage,
                                                        2
                                                    ) }}%
                                                </p>
                                            </div>

                                            <div class="rounded-lg bg-green-50 p-4">
                                                <p class="mb-1 text-xs uppercase tracking-wide text-green-600">
                                                    Total Commission
                                                </p>

                                                <p class="break-words text-lg font-medium text-green-700">
                                                    ₱{{ number_format(
                                                        $account->total_agent_commission,
                                                        2
                                                    ) }}
                                                </p>
                                            </div>

                                            <div class="rounded-lg bg-blue-50 p-4">
                                                <p class="mb-1 text-xs uppercase tracking-wide text-blue-600">
                                                    Received Commission
                                                </p>

                                                <p class="break-words text-lg font-medium text-blue-700">
                                                    ₱{{ number_format(
                                                        $account->received_commission,
                                                        2
                                                    ) }}
                                                </p>
                                            </div>
                                        </div>


                                        {{-- Commission Periods --}}
                                        <div>
                                            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">

                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">
                                                        {{ $account->payment_scheme === 'cash'
                                                            ? 'Cash Commission'
                                                            : 'Commission Every 3 Paid Months' }}
                                                    </p>

                                                    <p class="mt-1 text-xs text-gray-400">
                                                        Commission is released by completed payment period.
                                                    </p>
                                                </div>

                                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                                    {{ $account->total_commission_periods }}
                                                    period{{ $account->total_commission_periods !== 1 ? 's' : '' }}
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                                @forelse($account->commission_periods as $period)
                                                    @php
                                                        $request = $period['request'];

                                                        $cardClass = match(true) {
                                                            $request?->status === 'paid'
                                                                => 'border-green-200 bg-green-50',

                                                            $request?->status === 'approved'
                                                                => 'border-blue-200 bg-blue-50',

                                                            $request?->status === 'pending'
                                                                => 'border-amber-200 bg-amber-50',

                                                            $request?->status === 'rejected'
                                                                => 'border-red-200 bg-red-50',

                                                            $period['eligible']
                                                                => 'border-green-200 bg-green-50',

                                                            default
                                                                => 'border-gray-200 bg-gray-50',
                                                        };
                                                    @endphp

                                                    <div
                                                        wire:key="commission-period-{{ $account->id }}-{{ $period['period_number'] }}"
                                                        class="rounded-lg border p-4 {{ $cardClass }}"
                                                    >

                                                        <div class="mb-3 flex items-start justify-between gap-3">
                                                            <div class="min-w-0">

                                                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">
                                                                    {{ $period['label'] }}
                                                                </p>

                                                                <p class="mt-1 text-xs text-gray-400">
                                                                    {{ $period['months_label'] }}
                                                                </p>

                                                                <p class="mt-2 text-base font-medium text-blue-800">
                                                                    ₱{{ number_format(
                                                                        $period['amount'],
                                                                        2
                                                                    ) }}
                                                                </p>
                                                            </div>

                                                            @if($period['all_paid'])
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    width="24"
                                                                    height="24"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="h-5 w-5 shrink-0 text-green-500"
                                                                >
                                                                    <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                                                                    <path d="m9 11 3 3L22 4"></path>
                                                                </svg>
                                                            @else
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    width="24"
                                                                    height="24"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="h-5 w-5 shrink-0 text-gray-300"
                                                                >
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <path d="m15 9-6 6"></path>
                                                                    <path d="m9 9 6 6"></path>
                                                                </svg>
                                                            @endif
                                                        </div>

                                                        {{-- Covered Billings --}}
                                                        <div class="mb-4 flex flex-wrap gap-2">
                                                            @foreach($period['billings'] as $billing)
                                                                @php
                                                                    $billingPaid =
                                                                        $billing->status === 'paid'
                                                                        || (float) $billing->amount_paid
                                                                            >= (float) $billing->amount_due;
                                                                @endphp

                                                                <div class="flex items-center gap-1 rounded-full bg-white/70 px-2 py-1 text-xs text-gray-500">
                                                                    <span
                                                                        class="inline-block h-2 w-2 rounded-full
                                                                        {{ $billingPaid
                                                                            ? 'bg-green-500'
                                                                            : 'bg-gray-300' }}"
                                                                    ></span>

                                                                    {{ $billing->due_date->format('M Y') }}
                                                                </div>
                                                            @endforeach

                                                            @if(! $period['required_billings_exist'])
                                                                <span class="text-xs text-gray-400">
                                                                    Billing schedule is incomplete.
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Existing Request --}}
                                                        @if($request)
                                                            @if($request->status === 'pending')
                                                                <div class="rounded-lg bg-amber-100 px-3 py-2 text-center text-xs font-medium text-amber-700">
                                                                    Waiting for admin review
                                                                </div>
                                                            @elseif($request->status === 'approved')
                                                                <div class="rounded-lg bg-blue-100 px-3 py-2 text-center text-xs font-medium text-blue-700">
                                                                    Request approved
                                                                </div>
                                                            @elseif($request->status === 'rejected')
                                                                <div class="rounded-lg bg-red-100 px-3 py-2 text-center text-xs font-medium text-red-700">
                                                                    Request rejected
                                                                </div>

                                                                @if($request->remarks)

                                                                    <p class="mt-2 rounded-lg bg-white/70 p-2 text-xs text-red-600">
                                                                        {{ $request->remarks }}
                                                                    </p>

                                                                @endif
                                                            @elseif($request->status === 'paid')

                                                                <div class="rounded-lg bg-green-100 px-3 py-2 text-center text-xs font-medium text-green-700">
                                                                    Commission paid
                                                                </div>

                                                                @if($request->receipt_path)

                                                                    <a
                                                                        href="{{ asset('storage/' . ltrim($request->receipt_path, '/')) }}"
                                                                        target="_blank"
                                                                        class="mt-2 flex w-full items-center justify-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 py-2 text-xs font-medium text-blue-700 hover:bg-blue-100"
                                                                    >
                                                                        <svg
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            width="24"
                                                                            height="24"
                                                                            viewBox="0 0 24 24"
                                                                            fill="none"
                                                                            stroke="currentColor"
                                                                            stroke-width="2"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            class="h-3.5 w-3.5"
                                                                        >
                                                                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                                                            <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                                                            <path d="M10 9H8"></path>
                                                                            <path d="M16 13H8"></path>
                                                                            <path d="M16 17H8"></path>
                                                                        </svg>

                                                                        View Receipt
                                                                    </a>
                                                                @endif
                                                            @endif

                                                        {{-- Request Available --}}
                                                        @elseif($period['eligible'])

                                                            <button
                                                                type="button"
                                                                wire:click="requestCommission({{ $account->id }}, {{ $period['period_number'] }})"
                                                                wire:loading.attr="disabled"
                                                                wire:target="requestCommission({{ $account->id }}, {{ $period['period_number'] }})"
                                                                class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-gray-900 py-2 text-xs font-medium text-white transition-colors hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                                                            >

                                                                <svg
                                                                    wire:loading.remove
                                                                    wire:target="requestCommission({{ $account->id }}, {{ $period['period_number'] }})"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    width="24"
                                                                    height="24"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="h-3.5 w-3.5"
                                                                >
                                                                    <path d="M3.714 3.048a.498.498 0 0 0-.683.627l2.843 7.627a2 2 0 0 1 0 1.396l-2.842 7.627a.498.498 0 0 0 .682.627l18-8.5a.5.5 0 0 0 0-.904z"></path>
                                                                    <path d="M6 12h16"></path>
                                                                </svg>

                                                                <svg
                                                                    wire:loading
                                                                    wire:target="requestCommission({{ $account->id }}, {{ $period['period_number'] }})"
                                                                    class="h-4 w-4 animate-spin"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <circle
                                                                        class="opacity-25"
                                                                        cx="12"
                                                                        cy="12"
                                                                        r="10"
                                                                        stroke="currentColor"
                                                                        stroke-width="4"
                                                                    ></circle>

                                                                    <path
                                                                        class="opacity-75"
                                                                        fill="currentColor"
                                                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                                                    ></path>
                                                                </svg>

                                                                <span
                                                                    wire:loading.remove
                                                                    wire:target="requestCommission({{ $account->id }}, {{ $period['period_number'] }})"
                                                                >
                                                                    Request Commission
                                                                </span>

                                                                <span
                                                                    wire:loading
                                                                    wire:target="requestCommission({{ $account->id }}, {{ $period['period_number'] }})"
                                                                >
                                                                    Sending...
                                                                </span>
                                                            </button>

                                                        {{-- Not Yet Eligible --}}
                                                        @else
                                                            <p class="rounded-lg bg-white/60 px-3 py-2 text-center text-xs text-gray-400">
                                                                Complete all required payments to request.
                                                            </p>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <div class="col-span-full rounded-lg border border-dashed border-gray-200 p-6 text-center">

                                                        <p class="text-sm text-gray-500">
                                                            No commission periods are available.
                                                        </p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="border-t border-gray-100"></div>

                                        {{-- Monthly Billing Status --}}
                                        <div>
                                            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">

                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">
                                                        Payment Status
                                                    </p>

                                                    <p class="mt-1 text-xs text-gray-400">
                                                        Monthly payment progress of this client.
                                                    </p>
                                                </div>

                                                <span class="text-xs text-gray-400">
                                                    {{ $account->billings->count() }}
                                                    billing{{ $account->billings->count() !== 1 ? 's' : '' }}
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6 xl:grid-cols-8">

                                                @forelse(
                                                    $account->billings->sortBy('due_date')
                                                    as $billing
                                                )

                                                    @php
                                                        $paid =
                                                            $billing->status === 'paid'
                                                            || (float) $billing->amount_paid
                                                                >= (float) $billing->amount_due;

                                                        $partial =
                                                            $billing->status === 'partial'
                                                            && ! $paid;

                                                        $current =
                                                            $billing->due_date->isSameMonth(now());

                                                        $overdue =
                                                            ! $paid
                                                            && $billing->due_date->isBefore(
                                                                now()->startOfDay()
                                                            );
                                                    @endphp

                                                    <div
                                                        wire:key="billing-status-{{ $billing->id }}"
                                                        class="rounded-lg border p-2 text-center
                                                            {{ $paid
                                                                ? 'border-green-200 bg-green-50'
                                                                : ($partial
                                                                ? 'border-blue-200 bg-blue-50'
                                                                : ($overdue
                                                                ? 'border-red-300 bg-red-50'
                                                                : ($current
                                                                ? 'border-orange-300 bg-orange-50'
                                                                : 'border-gray-200 bg-gray-50'))) 
                                                            }}"
                                                        title="{{ $billing->title }} | Due: {{ $billing->due_date->format('F d, Y') }} | Amount: ₱{{ number_format($billing->amount_due, 2) }}"
                                                    >
                                                        <span class="block text-[10px] font-semibold text-gray-600">
                                                            {{ $billing->due_date->format('M') }}
                                                        </span>

                                                        <span class="mt-0.5 block text-[9px] text-gray-400">
                                                            {{ $billing->due_date->format('Y') }}
                                                        </span>

                                                        <div class="mt-2 flex justify-center">
                                                            @if($paid)
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    width="24"
                                                                    height="24"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="h-4 w-4 text-green-500"
                                                                >
                                                                    <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                                                                    <path d="m9 11 3 3L22 4"></path>
                                                                </svg>
                                                            @elseif($partial)
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    width="24"
                                                                    height="24"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="h-4 w-4 text-blue-500"
                                                                >
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <path d="M12 6v6l4 2"></path>
                                                                </svg>
                                                            @elseif($overdue)
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    width="24"
                                                                    height="24"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="h-4 w-4 text-red-500"
                                                                >
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <path d="m15 9-6 6"></path>
                                                                    <path d="m9 9 6 6"></path>
                                                                </svg>
                                                            @elseif($current)
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    width="24"
                                                                    height="24"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="h-4 w-4 text-orange-500"
                                                                >
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <path d="M12 6v6l4 2"></path>
                                                                </svg>
                                                            @else
                                                                <svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    width="24"
                                                                    height="24"
                                                                    viewBox="0 0 24 24"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    class="h-4 w-4 text-gray-300"
                                                                >
                                                                    <circle cx="12" cy="12" r="10"></circle>
                                                                    <path d="m15 9-6 6"></path>
                                                                    <path d="m9 9 6 6"></path>
                                                                </svg>
                                                            @endif
                                                        </div>

                                                        <p
                                                            class="mt-1 truncate text-[9px] font-medium
                                                                {{ $paid
                                                                    ? 'text-green-600'
                                                                    : ($partial
                                                                    ? 'text-blue-600'
                                                                    : ($overdue
                                                                    ? 'text-red-600'
                                                                    : ($current
                                                                    ? 'text-orange-600'
                                                                    : 'text-gray-400'))) 
                                                                }}"
                                                        >
                                                            @if($paid)
                                                                Paid
                                                            @elseif($partial)
                                                                Partial
                                                            @elseif($overdue)
                                                                Overdue
                                                            @elseif($current)
                                                                Current
                                                            @else
                                                                Upcoming
                                                            @endif
                                                        </p>
                                                    </div>
                                                @empty
                                                    <div class="col-span-full rounded-lg border border-dashed border-gray-200 p-6 text-center">

                                                        <p class="text-sm text-gray-500">
                                                            No billing schedule found.
                                                        </p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border-2 border-dashed border-gray-200 bg-white p-8 text-center">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="mx-auto h-9 w-9 text-gray-300"
                            >
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>

                            <p class="mt-3 text-sm font-medium text-gray-700">
                                No assigned clients
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Clients who selected you as their agent will appear here.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <x-modal
        blur
        name="createQrCode"
        align="center"
        max-width="lg"
        :show="$showCreateQrModal"
    >
        <x-card title="Add Payment QR Code">

            <div class="space-y-4">

                <x-input
                    label="Label"
                    placeholder="Example: Primary BDO Account"
                    wire:model.defer="qrLabel"
                />

                <x-input
                    label="Bank or Wallet"
                    placeholder="Example: BDO, BPI, GCash"
                    wire:model.defer="qrProviderName"
                />

                <x-input
                    label="Account Name"
                    placeholder="Enter the registered account name"
                    wire:model.defer="qrAccountName"
                />

                <x-input
                    label="Account Number"
                    placeholder="Optional account number"
                    wire:model.defer="qrAccountNumber"
                />

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        QR Code Image
                    </label>

                    <input
                        type="file"
                        wire:model="qrImage"
                        accept="image/jpeg,image/png,image/webp"
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2 text-sm text-gray-600"
                    >

                    @error('qrImage')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                    <div wire:loading wire:target="qrImage">
                        <p class="mt-2 text-xs text-blue-600">
                            Uploading image...
                        </p>
                    </div>

                    @if($qrImage)
                        <img
                            src="{{ $qrImage->temporaryUrl() }}"
                            alt="QR preview"
                            class="mt-3 h-44 w-full rounded-xl border object-contain"
                        >
                    @endif
                </div>

                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-3">
                    <input
                        type="checkbox"
                        wire:model.defer="qrIsPrimary"
                        class="rounded border-gray-300"
                    >

                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            Set as primary QR code
                        </p>

                        <p class="text-xs text-gray-500">
                            Admin will see this payment option first.
                        </p>
                    </div>
                </label>
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
                        label="Save QR Code"
                        wire:click="createQrCode"
                        wire:loading.attr="disabled"
                    />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <x-modal
        blur
        name="editQrCode"
        align="center"
        max-width="lg"
        :show="$showEditQrModal"
    >
        <x-card title="Edit Payment QR Code">

            <div class="space-y-4">

                <x-input
                    label="Label"
                    wire:model.defer="editQrLabel"
                />

                <x-input
                    label="Bank or Wallet"
                    wire:model.defer="editQrProviderName"
                />

                <x-input
                    label="Account Name"
                    wire:model.defer="editQrAccountName"
                />

                <x-input
                    label="Account Number"
                    wire:model.defer="editQrAccountNumber"
                />

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Replace QR Code Image
                    </label>

                    <input
                        type="file"
                        wire:model="editQrImage"
                        accept="image/jpeg,image/png,image/webp"
                        class="block w-full rounded-lg border border-gray-300 bg-white p-2 text-sm text-gray-600"
                    >

                    <p class="mt-1 text-xs text-gray-500">
                        Leave empty to keep the current QR image.
                    </p>

                    @error('editQrImage')
                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                    @if($editQrImage)
                        <img
                            src="{{ $editQrImage->temporaryUrl() }}"
                            alt="New QR preview"
                            class="mt-3 h-44 w-full rounded-xl border object-contain"
                        >
                    @endif
                </div>

                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-3">
                    <input
                        type="checkbox"
                        wire:model.defer="editQrIsPrimary"
                        class="rounded border-gray-300"
                    >

                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            Set as primary QR code
                        </p>

                        <p class="text-xs text-gray-500">
                            This QR will be shown first to admin.
                        </p>
                    </div>
                </label>
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
                        label="Update QR Code"
                        wire:click="updateQrCode"
                        wire:loading.attr="disabled"
                    />
                </div>
            </x-slot>
        </x-card>
    </x-modal>

</div>