<div
    class="bg-white"
    x-data="{
        editingProfile: false
    }"
>

    {{-- ========================================================= --}}
    {{-- AGENT ACCOUNT OVERVIEW --}}
    {{-- ========================================================= --}}

    @if(auth()->user()->role === 'agent')

        <section
            x-show="!editingProfile"
            x-cloak
            class="py-24 lg:py-32"
        >

            <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">

                @php
                    /*
                    |--------------------------------------------------------------------------
                    | AGENT CLIENTS
                    |--------------------------------------------------------------------------
                    |
                    | Clients who selected / were credited to this agent through
                    | user_infos.agent_id.
                    |
                    */
                    $agentClients = \App\Models\User::query()
                        ->with([
                            'info',
                            'purchaseAccounts.lot',
                        ])
                        ->where('role', 'user')
                        ->whereHas('info', function ($query) {
                            $query->where(
                                'agent_id',
                                auth()->id()
                            );
                        })
                        ->orderBy('name')
                        ->get();
                @endphp


                {{-- PROFILE CARD --}}
                <div
                    class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 md:p-8"
                >

                    <div
                        class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
                    >

                        {{-- AGENT --}}
                        <div class="flex items-center gap-4">

                            {{-- AVATAR --}}
                            <div
                                class="h-16 w-16 md:h-20 md:w-20 rounded-full bg-[#101727] flex items-center justify-center overflow-hidden shrink-0"
                            >

                                @if(auth()->user()->profile_picture)

                                    <img
                                        src="{{ auth()->user()->profile_picture }}"
                                        alt="{{ auth()->user()->name }}"
                                        class="h-full w-full object-cover"
                                    >

                                @else

                                    <x-icon
                                        name="user"
                                        class="h-8 w-8 text-white"
                                    />

                                @endif

                            </div>


                            {{-- DETAILS --}}
                            <div class="min-w-0">

                                <h1
                                    class="text-xl md:text-2xl font-semibold text-gray-900 truncate"
                                >
                                    {{ auth()->user()->name }}
                                </h1>


                                <p class="mt-1 text-xs text-gray-400">
                                    {{ $professionalAgentId ?? 'Agent' }}
                                </p>


                                <button
                                    type="button"
                                    x-on:click="editingProfile = true; window.scrollTo({ top: 0, behavior: 'smooth' })"
                                    class="mt-3 inline-flex items-center gap-2 rounded-lg bg-[#101727] px-3 py-2 text-xs font-medium text-white transition hover:bg-gray-800"
                                >

                                    <x-icon
                                        name="pencil-square"
                                        class="h-3.5 w-3.5"
                                    />

                                    Edit Profile

                                </button>

                            </div>

                        </div>


                        {{-- LOGOUT --}}
                        <button
                            type="button"
                            wire:click="logout"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-100 px-4 py-2.5 text-sm font-medium text-red-500 transition hover:bg-red-50"
                        >

                            <x-icon
                                name="arrow-left-on-rectangle"
                                class="h-4 w-4"
                            />

                            Logout

                        </button>

                    </div>

                </div>


                {{-- ========================================================= --}}
                {{-- MY CLIENTS --}}
                {{-- ========================================================= --}}

                <div class="mt-8">

                    <div class="mb-4 flex items-center justify-between">

                        <div class="flex items-center gap-2">

                            <x-icon
                                name="user-group"
                                class="h-4 w-4 text-gray-400"
                            />

                            <h2
                                class="text-sm font-semibold uppercase tracking-wide text-gray-700"
                            >
                                My Clients
                            </h2>

                        </div>


                        <span
                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-500"
                        >
                            {{ $agentClients->count() }}
                        </span>

                    </div>


                    @if($agentClients->count())

                        <div
                            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3"
                        >

                            @foreach($agentClients as $client)

                                @php
                                    $purchaseAccount = $client
                                        ->purchaseAccounts
                                        ?->sortByDesc('created_at')
                                        ->first();

                                    $lotName =
                                        $purchaseAccount?->lot?->name
                                        ?? 'No assigned lot';

                                    $isPaid =
                                        $purchaseAccount
                                        && $purchaseAccount->status === 'fully_paid';

                                    $hasAccount =
                                        ! is_null($purchaseAccount);
                                @endphp


                                <div
                                    class="flex items-center justify-between gap-4 rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition hover:shadow-md"
                                >

                                    {{-- CLIENT INFO --}}
                                    <div
                                        class="flex min-w-0 items-center gap-3"
                                    >

                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-100"
                                        >

                                            @if($client->profile_picture)

                                                <img
                                                    src="{{ $client->profile_picture }}"
                                                    alt="{{ $client->name }}"
                                                    class="h-full w-full object-cover"
                                                >

                                            @else

                                                <x-icon
                                                    name="user"
                                                    class="h-5 w-5 text-gray-400"
                                                />

                                            @endif

                                        </div>


                                        <div class="min-w-0">

                                            <p
                                                class="truncate text-sm font-semibold text-gray-900"
                                            >
                                                {{ $client->name }}
                                            </p>


                                            <p
                                                class="mt-0.5 truncate text-[10px] text-gray-400"
                                            >
                                                {{ $lotName }}
                                            </p>

                                        </div>

                                    </div>


                                    {{-- STATUS --}}
                                    @if($isPaid)

                                        <span
                                            class="shrink-0 rounded-full bg-green-100 px-2.5 py-1 text-[10px] font-semibold text-green-600"
                                        >
                                            Paid
                                        </span>

                                    @elseif($hasAccount)

                                        <span
                                            class="shrink-0 rounded-full bg-red-100 px-2.5 py-1 text-[10px] font-semibold text-red-600"
                                        >
                                            Unpaid
                                        </span>

                                    @else

                                        <span
                                            class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-semibold text-gray-500"
                                        >
                                            Pending
                                        </span>

                                    @endif

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div
                            class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-6 py-12 text-center"
                        >

                            <x-icon
                                name="user-group"
                                class="mx-auto h-9 w-9 text-gray-300"
                            />

                            <p
                                class="mt-3 text-sm font-medium text-gray-600"
                            >
                                No clients assigned yet
                            </p>

                            <p
                                class="mt-1 text-xs text-gray-400"
                            >
                                Clients credited to you will appear here.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </section>

    @endif



    {{-- ========================================================= --}}
    {{-- CLIENT ACCOUNT OVERVIEW --}}
    {{-- ========================================================= --}}

    @if(auth()->user()->role === 'user')

        <section
            x-show="!editingProfile"
            x-cloak
            class="py-24 lg:py-32"
        >

            <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">

                @php
                    /*
                    |--------------------------------------------------------------------------
                    | CLIENT PROPERTIES
                    |--------------------------------------------------------------------------
                    |
                    | Purchase accounts belonging to the currently authenticated
                    | client. These are displayed as "My Properties".
                    |
                    */
                    $clientProperties = auth()->user()
                        ->purchaseAccounts()
                        ->with([
                            'lot',
                            'houseModel',
                            'reservation',
                        ])
                        ->latest()
                        ->get();
                @endphp


                {{-- PROFILE CARD --}}
                <div
                    class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 md:p-8 mt-11"
                >

                    <div
                        class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
                    >

                        {{-- CLIENT --}}
                        <div class="flex items-center gap-4">

                            {{-- AVATAR --}}
                            <div
                                class="h-16 w-16 md:h-20 md:w-20 rounded-full bg-[#101727] flex items-center justify-center overflow-hidden shrink-0"
                            >

                                @if(auth()->user()->profile_picture)

                                    <img
                                        src="{{ auth()->user()->profile_picture }}"
                                        alt="{{ auth()->user()->name }}"
                                        class="h-full w-full object-cover"
                                    >

                                @else

                                    <x-icon
                                        name="user"
                                        class="h-8 w-8 text-white"
                                    />

                                @endif

                            </div>


                            {{-- DETAILS --}}
                            <div class="min-w-0">

                                <h1
                                    class="text-xl md:text-2xl font-semibold text-gray-900 truncate"
                                >
                                    {{ auth()->user()->name }}
                                </h1>


                                <p class="mt-1 text-xs text-gray-400">
                                    Client
                                </p>


                                <button
                                    type="button"
                                    x-on:click="editingProfile = true; window.scrollTo({ top: 0, behavior: 'smooth' })"
                                    class="mt-3 inline-flex items-center gap-2 rounded-lg bg-[#101727] px-3 py-2 text-xs font-medium text-white transition hover:bg-gray-800"
                                >

                                    <x-icon
                                        name="pencil-square"
                                        class="h-3.5 w-3.5"
                                    />

                                    Edit Profile

                                </button>

                            </div>

                        </div>


                        {{-- LOGOUT --}}
                        <button
                            type="button"
                            wire:click="logout"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-100 px-4 py-2.5 text-sm font-medium text-red-500 transition hover:bg-red-50"
                        >

                            <x-icon
                                name="arrow-left-on-rectangle"
                                class="h-4 w-4"
                            />

                            Logout

                        </button>

                    </div>

                </div>


                {{-- ========================================================= --}}
                {{-- MY PROPERTIES --}}
                {{-- ========================================================= --}}

                <div class="mt-8">

                    <div class="mb-4 flex items-center justify-between">

                        <div class="flex items-center gap-2">

                            <x-icon
                                name="home"
                                class="h-4 w-4 text-gray-400"
                            />

                            <h2
                                class="text-sm font-semibold uppercase tracking-wide text-gray-700"
                            >
                                My Properties
                            </h2>

                        </div>


                        <span
                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-500"
                        >
                            {{ $clientProperties->count() }}
                        </span>

                    </div>


                    @if($clientProperties->count())

                        <div
                            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3"
                        >

                            @foreach($clientProperties as $property)

                               @php
                                    $isPaid =
                                        $property->status === 'fully_paid';

                                    $hasAccount =
                                        ! is_null($property);

                                    $lotName =
                                        $property->lot?->name
                                        ?? 'No assigned lot';

                                    // Property type from lots table
                                    $propertyType =
                                        $property->lot?->type
                                        ?? 'Property';

                                    // House model, if the purchase has one
                                    $houseModel =
                                        $property->houseModel?->model_name
                                        ?? null;

                                    // Reservation linked to this property
                                    $reservationId =
                                        $property->reservation?->id;
                                @endphp


                                <a
                                    @if($reservationId)
                                        href="{{ route('client.reservation', [
                                            'activeTab' => 'approved',
                                            'highlight' => $reservationId,
                                        ]) }}"
                                    @endif
                                    class="flex items-center justify-between gap-4 rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition hover:shadow-md
                                        {{ $reservationId
                                            ? 'cursor-pointer hover:border-blue-200 hover:shadow-md'
                                            : 'cursor-default'
                                        }}"
                                >
                                    {{-- PROPERTY INFO --}}
                                    <div
                                        class="flex min-w-0 items-center gap-3"
                                    >

                                        {{-- PROPERTY ICON --}}
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                                        >

                                            <x-icon
                                                name="home"
                                                class="h-5 w-5 text-gray-400"
                                            />

                                        </div>


                                        <div class="min-w-0">

                                            {{-- LOT --}}
                                            <p
                                                class="truncate text-sm font-semibold text-gray-900"
                                            >
                                                {{ $lotName }}
                                            </p>

                                            {{-- PROPERTY TYPE --}}
                                            <p class="mt-0.5 truncate text-[10px] font-medium text-gray-500">
                                                {{ $propertyType }}
                                            </p>

                                            {{-- HOUSE MODEL --}}
                                            @if($houseModel)

                                                <p
                                                    class="mt-0.5 truncate text-[10px] text-gray-400"
                                                >
                                                    House and Lot - {{ $houseModel }}
                                                </p>

                                            @endif

                                            <p          
                                                class="mt-0.5 text-[10px] leading-relaxed text-gray-400 whitespace-normal break-words"
                                            >
                                                Manhattan Residences Candelaria
                                            </p>
                                            {{-- PAYMENT SCHEME --}}
                                            {{-- @if($property->payment_scheme)

                                                <p
                                                    class="mt-0.5 truncate text-[10px] text-gray-400"
                                                >
                                                    {{ str($property->payment_scheme)->headline() }}
                                                </p>

                                            @endif --}}


                                            {{-- CONTRACT PRICE --}}
                                            {{-- @if(! is_null($property->total_contract_price))

                                                <p
                                                    class="mt-1 text-xs font-medium text-gray-700"
                                                >
                                                    ₱{{ number_format($property->total_contract_price, 2) }}
                                                </p>

                                            @endif --}}

                                        </div>

                                    </div>


                                    {{-- STATUS --}}
                                    @if($isPaid)

                                        <span
                                            class="shrink-0 rounded-full bg-green-100 px-2.5 py-1 text-[10px] font-semibold text-green-600"
                                        >
                                            Paid
                                        </span>

                                    @elseif($hasAccount)

                                        <span
                                            class="inline-flex shrink-0 items-center gap-1 rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-semibold text-blue-600"
                                        >

                                            <x-icon
                                                name="clock"
                                                class="h-3 w-3"
                                            />

                                            On-going Payment

                                        </span>

                                    @else

                                        <span
                                            class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-semibold text-gray-500"
                                        >
                                            Pending
                                        </span>

                                    @endif

                                    </a>

                            @endforeach

                        </div>

                    @else

                        <div
                            class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-6 py-12 text-center"
                        >

                            <x-icon
                                name="home"
                                class="mx-auto h-9 w-9 text-gray-300"
                            />

                            <p
                                class="mt-3 text-sm font-medium text-gray-600"
                            >
                                No properties yet
                            </p>

                            <p
                                class="mt-1 text-xs text-gray-400"
                            >
                                Your properties will appear here once you have an approved reservation.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </section>

    @endif



    {{-- ========================================================= --}}
    {{-- EXISTING ACCOUNT SETTINGS --}}
    {{-- ========================================================= --}}

    <section
        class="py-24 lg:py-32"

        {{-- CHANGED: apply edit mode to both agent and client --}}
        @if(in_array(auth()->user()->role, ['agent', 'user']))
            x-show="editingProfile"
            x-cloak
        @endif
    >

        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">


            {{-- BACK BUTTON --}}
            {{-- CHANGED: show for both agent and client --}}
            @if(in_array(auth()->user()->role, ['agent', 'user']))

                <div class="mb-8">

                    <button
                        type="button"
                        x-on:click="editingProfile = false; window.scrollTo({ top: 0, behavior: 'smooth' })"
                        class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-900"
                    >

                        <x-icon
                            name="arrow-left"
                            class="h-4 w-4"
                        />

                        Back to Account

                    </button>

                </div>

            @endif


            <div class="text-center mb-16">

                <h1
                    class="text-4xl lg:text-5xl font-light text-gray-900 mb-4"
                >
                    Account Settings
                </h1>

                <p
                    class="text-lg text-gray-600 max-w-2xl mx-auto"
                >
                    Manage your profile and account preferences
                </p>

            </div>


            @if(session()->has('profile_success'))

                <div
                    class="max-w-3xl mx-auto mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg"
                >
                    {{ session('profile_success') }}
                </div>

            @endif


            @if(session()->has('password_success'))

                <div
                    class="max-w-3xl mx-auto mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg"
                >
                    {{ session('password_success') }}
                </div>

            @endif


            <div class="grid lg:grid-cols-3 gap-8">


                {{-- ========================================================= --}}
                {{-- PROFILE PICTURE --}}
                {{-- ========================================================= --}}

                <div class="lg:col-span-1">

                    <div
                        class="bg-white shadow-sm p-8 border border-gray-100 rounded-xl"
                    >

                        <h2
                            class="text-lg text-gray-900 mb-6 uppercase tracking-wide font-medium"
                        >
                            Profile Picture
                        </h2>


                        <div class="flex flex-col items-center">

                            <div
                                class="h-32 w-32 bg-gray-900 flex items-center justify-center mb-6 relative overflow-hidden group rounded-lg"
                            >

                                @if($photo)

                                    <img
                                        src="{{ $photo->temporaryUrl() }}"
                                        class="h-full w-full object-cover"
                                    >

                                @elseif(auth()->user()->profile_picture)

                                    <img
                                        src="{{ auth()->user()->profile_picture }}"
                                        class="h-full w-full object-cover"
                                    >

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
                                        class="h-16 w-16 text-white"
                                    >
                                        <path
                                            d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"
                                        ></path>

                                        <circle
                                            cx="12"
                                            cy="7"
                                            r="4"
                                        ></circle>

                                    </svg>

                                @endif


                                <div
                                    wire:loading
                                    wire:target="photo"
                                    class="absolute inset-0 bg-gray-950/70 z-10"
                                >

                                    <div
                                        class="w-full h-full flex items-center justify-center"
                                    >
                                        <span
                                            class="text-xs text-white animate-pulse font-medium"
                                        >
                                            Uploading...
                                        </span>
                                    </div>

                                </div>

                            </div>


                            <label class="w-full">

                                <input
                                    type="file"
                                    wire:model="photo"
                                    class="hidden"
                                    accept="image/*"
                                >

                                <div
                                    class="px-6 py-3 bg-gray-900 text-white hover:bg-gray-800 transition-colors text-sm mb-3 uppercase tracking-wide text-center cursor-pointer font-medium rounded"
                                >
                                    Upload Photo
                                </div>

                            </label>


                            @if(auth()->user()->profile_picture || $photo)

                                <button
                                    type="button"
                                    wire:click="removePhoto"
                                    class="px-6 py-3 text-red-600 hover:text-red-700 text-sm uppercase tracking-wide w-full font-medium transition"
                                >
                                    Remove Photo
                                </button>

                            @endif


                            @error('photo')

                                <span
                                    class="text-xs text-red-600 mt-2 text-center"
                                >
                                    {{ $message }}
                                </span>

                            @enderror

                        </div>

                    </div>

                </div>



                {{-- ========================================================= --}}
                {{-- RIGHT SIDE --}}
                {{-- ========================================================= --}}

                <div class="lg:col-span-2 space-y-6">


                    {{-- PROFILE FORM --}}
                    <form
                        wire:submit.prevent="confirmProfileUpdate"
                        class="space-y-6"
                    >


                        {{-- PERSONAL INFORMATION --}}
                        <div
                            class="bg-white shadow-sm p-8 space-y-6 border border-gray-100 rounded-xl"
                        >

                            <h2
                                class="text-lg text-gray-900 mb-2 flex items-center gap-3 uppercase tracking-wide font-medium"
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
                                    class="h-6 w-6 text-gray-400"
                                >

                                    <path
                                        d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"
                                    ></path>

                                    <circle
                                        cx="12"
                                        cy="7"
                                        r="4"
                                    ></circle>

                                </svg>

                                Personal Information

                            </h2>


                            <div class="grid md:grid-cols-2 gap-6">

                                {{-- AGENT: FULL NAME ONLY --}}
                                @if(auth()->user()->role === 'agent')

                                    <div class="md:col-span-2">

                                        <label
                                            for="fullName"
                                            class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                        >
                                            Full Name
                                        </label>

                                        <input
                                            id="fullName"
                                            type="text"
                                            wire:model.blur="fullName"
                                            maxlength="50"
                                            class="w-full px-4 py-3 border
                                                @error('fullName')
                                                    border-red-500 focus:ring-red-500
                                                @else
                                                    border-gray-300 focus:ring-gray-900
                                                @enderror
                                                rounded focus:ring-2 focus:border-transparent outline-none"
                                        >

                                        @error('fullName')
                                            <span class="text-xs text-red-600 mt-1 block">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>

                                {{-- CLIENT: KEEP EXISTING NAME FIELDS --}}
                                @else

                                    <div>

                                        <label
                                            for="firstName"
                                            class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                        >
                                            First Name
                                        </label>

                                        <input
                                            id="firstName"
                                            type="text"
                                            wire:model.blur="firstName"
                                            class="w-full px-4 py-3 border
                                                @error('firstName')
                                                    border-red-500 focus:ring-red-500
                                                @else
                                                    border-gray-300 focus:ring-gray-900
                                                @enderror
                                                rounded focus:ring-2 focus:border-transparent outline-none"
                                        >

                                        @error('firstName')
                                            <span class="text-xs text-red-600 mt-1 block">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>


                                    <div>

                                        <label
                                            for="middleName"
                                            class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                        >
                                            Middle Name
                                        </label>

                                        <input
                                            id="middleName"
                                            type="text"
                                            wire:model.blur="middleName"
                                            class="w-full px-4 py-3 border
                                                @error('middleName')
                                                    border-red-500 focus:ring-red-500
                                                @else
                                                    border-gray-300 focus:ring-gray-900
                                                @enderror
                                                rounded focus:ring-2 focus:border-transparent outline-none"
                                        >

                                        @error('middleName')
                                            <span class="text-xs text-red-600 mt-1 block">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>


                                    <div>

                                        <label
                                            for="lastName"
                                            class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                        >
                                            Last Name
                                        </label>

                                        <input
                                            id="lastName"
                                            type="text"
                                            wire:model.blur="lastName"
                                            class="w-full px-4 py-3 border
                                                @error('lastName')
                                                    border-red-500 focus:ring-red-500
                                                @else
                                                    border-gray-300 focus:ring-gray-900
                                                @enderror
                                                rounded focus:ring-2 focus:border-transparent outline-none"
                                        >

                                        @error('lastName')
                                            <span class="text-xs text-red-600 mt-1 block">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>


                                    <div>

                                        <label
                                            for="suffix"
                                            class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                        >
                                            Suffix
                                        </label>

                                        <input
                                            id="suffix"
                                            type="text"
                                            placeholder="Jr., Sr., III"
                                            wire:model.blur="suffix"
                                            class="w-full px-4 py-3 border border-gray-300 focus:ring-2 focus:ring-gray-900 focus:border-transparent rounded outline-none"
                                        >

                                        @error('suffix')
                                            <span class="text-xs text-red-600 mt-1 block">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                    </div>

                                @endif

                            </div>

                        </div>



                        {{-- ================================================= --}}
                        {{-- PROFESSIONAL INFORMATION --}}
                        {{-- ================================================= --}}

                        {{-- UNCHANGED: AGENT ONLY --}}
                        @if(auth()->user()->role === 'agent')

                            <div
                                class="bg-white shadow-sm p-8 space-y-6 border border-gray-100 rounded-xl"
                            >

                                <div
                                    class="flex items-start justify-between gap-4"
                                >

                                    <div>

                                        <h2
                                            class="text-lg text-gray-900 flex items-center gap-3 uppercase tracking-wide font-medium"
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
                                                class="h-6 w-6 text-gray-400"
                                            >

                                                <path
                                                    d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                                                ></path>

                                                <circle
                                                    cx="9"
                                                    cy="7"
                                                    r="4"
                                                ></circle>

                                                <path
                                                    d="M22 21v-2a4 4 0 0 0-3-3.87"
                                                ></path>

                                                <path
                                                    d="M16 3.13a4 4 0 0 1 0 7.75"
                                                ></path>

                                            </svg>

                                            Professional Information

                                        </h2>


                                        <p
                                            class="mt-2 text-xs text-gray-500"
                                        >
                                            Professional details assigned and managed by the administrator.
                                        </p>

                                    </div>


                                    <span
                                        class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-500"
                                    >
                                        Read Only
                                    </span>

                                </div>


                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                >

                                    {{-- AGENT ID --}}
                                    <div>

                                        <label
                                            class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                        >
                                            Agent ID
                                        </label>


                                        <div
                                            class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3"
                                        >

                                            <div
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white border border-gray-200"
                                            >

                                                <x-icon
                                                    name="identification"
                                                    class="h-4 w-4 text-gray-500"
                                                />

                                            </div>


                                            <div class="min-w-0">

                                                @if($professionalAgentId)

                                                    <p
                                                        class="truncate text-sm font-semibold text-gray-900"
                                                    >
                                                        {{ $professionalAgentId }}
                                                    </p>

                                                @else

                                                    <p class="text-sm text-gray-400">
                                                        Not assigned
                                                    </p>

                                                @endif


                                                <p
                                                    class="mt-0.5 text-[10px] text-gray-400"
                                                >
                                                    Company agent identifier
                                                </p>

                                            </div>

                                        </div>

                                    </div>


                                    {{-- REAL ESTATE LICENSE --}}
                                    <div>

                                        <label
                                            class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                        >
                                            Real Estate License Number
                                        </label>


                                        <div
                                            class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3"
                                        >

                                            <div
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white border border-gray-200"
                                            >

                                                <x-icon
                                                    name="document-check"
                                                    class="h-4 w-4 text-gray-500"
                                                />

                                            </div>


                                            <div class="min-w-0">

                                                @if($realEstateLicenseNumber)

                                                    <p
                                                        class="truncate text-sm font-semibold text-gray-900"
                                                    >
                                                        {{ $realEstateLicenseNumber }}
                                                    </p>

                                                @else

                                                    <p class="text-sm text-gray-400">
                                                        Not provided
                                                    </p>

                                                @endif


                                                <p
                                                    class="mt-0.5 text-[10px] text-gray-400"
                                                >
                                                    Registered professional license
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                {{-- COMMISSION --}}
                                <div
                                    class="border-t border-gray-100 pt-5"
                                >

                                    <div
                                        class="flex items-center justify-between rounded-xl border border-purple-100 bg-purple-50 p-4"
                                    >

                                        <div>

                                            <p
                                                class="text-xs font-semibold uppercase tracking-wide text-purple-500"
                                            >
                                                Current Commission Rate
                                            </p>

                                            <p
                                                class="mt-1 text-xs text-purple-400"
                                            >
                                                This percentage is configured by the administrator.
                                            </p>

                                        </div>


                                        <div class="text-right">

                                            @if(! is_null($commissionPercentage))

                                                <p
                                                    class="text-2xl font-semibold text-purple-700"
                                                >
                                                    {{ number_format(
                                                        $commissionPercentage,
                                                        2
                                                    ) }}%
                                                </p>

                                            @else

                                                <p
                                                    class="text-sm font-medium text-purple-400"
                                                >
                                                    Not set
                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endif



                        {{-- CONTACT INFORMATION --}}
                        <div
                            class="bg-white shadow-sm p-8 space-y-6 border border-gray-100 rounded-xl"
                        >

                            <h2
                                class="text-lg text-gray-900 mb-2 flex items-center gap-3 uppercase tracking-wide font-medium"
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
                                    class="h-6 w-6 text-gray-400"
                                >

                                    <rect
                                        width="20"
                                        height="16"
                                        x="2"
                                        y="4"
                                        rx="2"
                                    ></rect>

                                    <path
                                        d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"
                                    ></path>

                                </svg>

                                Contact Information

                            </h2>


                            <div class="space-y-4">

                                <div>

                                    <label
                                        for="email"
                                        class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                    >
                                        Email Address
                                    </label>

                                    <input
                                        id="email"
                                        type="email"
                                        wire:model.blur="email"
                                        class="w-full px-4 py-3 border
                                            @error('email')
                                                border-red-500 focus:ring-red-500
                                            @else
                                                border-gray-300 focus:ring-gray-900
                                            @enderror
                                            rounded focus:ring-2 focus:border-transparent outline-none"
                                    >

                                    @error('email')
                                        <span class="text-xs text-red-600 mt-1 block">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>


                                <div>

                                    <label
                                        for="phone"
                                        class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                    >
                                        Contact Number
                                    </label>


                                    <div class="relative flex items-center">

                                        <span
                                            class="absolute left-4 text-gray-400 text-sm font-medium select-none pointer-events-none"
                                        >
                                            +63
                                        </span>

                                        <input
                                            id="phone"
                                            type="text"
                                            inputmode="numeric"
                                            maxlength="10"
                                            placeholder="9123456789"
                                            wire:model.blur="phone"
                                            x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                            class="w-full pl-14 pr-4 py-3 border
                                                @error('phone')
                                                    border-red-500 focus:ring-red-500
                                                @else
                                                    border-gray-300 focus:ring-gray-900
                                                @enderror
                                                rounded focus:ring-2 focus:border-transparent outline-none"
                                        >

                                    </div>


                                    @error('phone')
                                        <span class="text-xs text-red-600 mt-1 block">
                                            {{ $message }}
                                        </span>
                                    @enderror

                                </div>

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="w-full px-6 py-4 bg-gray-900 text-white hover:bg-gray-800 transition-colors flex items-center justify-center gap-2 uppercase tracking-wide text-sm font-medium rounded-lg shadow-sm"
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
                                class="h-5 w-5"
                            >
                                <path
                                    d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"
                                ></path>

                                <path
                                    d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"
                                ></path>

                                <path
                                    d="M7 3v4a1 1 0 0 0 1 1h7"
                                ></path>
                            </svg>

                            <span>
                                Save Profile Changes
                            </span>

                        </button>

                    </form>


                    {{-- ========================================================= --}}
                    {{-- PASSWORD FORM - UNCHANGED --}}
                    {{-- ========================================================= --}}

                    <form
                        wire:submit.prevent="updatePassword"
                        class="bg-white shadow-sm p-8 space-y-6 border border-gray-100 rounded-xl"
                        x-data="{
                            showCurrentPassword: false,
                            showNewPassword: false,
                            showConfirmPassword: false,
                            newPasswordValue: '',

                            hasMinLength() {
                                return this.newPasswordValue.length >= 8;
                            },

                            hasMaxLength() {
                                return this.newPasswordValue.length <= 20;
                            },

                            hasLowercase() {
                                return /[a-z]/.test(this.newPasswordValue);
                            },

                            hasUppercase() {
                                return /[A-Z]/.test(this.newPasswordValue);
                            },

                            hasNumber() {
                                return /[0-9]/.test(this.newPasswordValue);
                            },

                            hasSpecialCharacter() {
                                return /[@$!%*?&#^()_\-+=]/.test(this.newPasswordValue);
                            }
                        }"
                    >

                        <h2
                            class="text-lg text-gray-900 mb-2 flex items-center gap-3 uppercase tracking-wide font-medium"
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
                                class="h-6 w-6 text-gray-400"
                            >
                                <rect
                                    width="18"
                                    height="11"
                                    x="3"
                                    y="11"
                                    rx="2"
                                    ry="2"
                                ></rect>

                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>

                            Change Password
                        </h2>


                        <div class="space-y-4">

                            {{-- CURRENT PASSWORD --}}
                            <div>

                                <label
                                    for="currentPassword"
                                    class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                >
                                    Current Password
                                </label>


                                <div class="relative">

                                    <input
                                        id="currentPassword"
                                        x-bind:type="showCurrentPassword ? 'text' : 'password'"
                                        placeholder="••••••••"
                                        wire:model.blur="currentPassword"
                                        class="w-full pl-4 pr-12 py-3 border
                                            @error('currentPassword')
                                                border-red-500 focus:ring-red-500
                                            @else
                                                border-gray-300 focus:ring-gray-900
                                            @enderror
                                            rounded focus:ring-2 focus:border-transparent outline-none"
                                    >


                                    <button
                                        type="button"
                                        x-on:mousedown.prevent
                                        x-on:click="showCurrentPassword = !showCurrentPassword"
                                        class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-gray-400 hover:text-gray-700 transition"
                                        aria-label="Toggle current password visibility"
                                    >

                                        <svg
                                            x-show="showCurrentPassword"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-5 w-5"
                                        >
                                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>


                                        <svg
                                            x-show="!showCurrentPassword"
                                            x-cloak
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-5 w-5"
                                        >
                                            <path d="m2 2 20 20"/>
                                            <path d="M6.71 6.71C4.9 7.9 3.45 9.6 2.62 11.55a1.2 1.2 0 0 0 0 .9C4.18 16.13 7.78 18.5 12 18.5c1.5 0 2.92-.3 4.19-.84"/>
                                            <path d="M10.73 5.58A10.9 10.9 0 0 1 12 5.5c4.22 0 7.82 2.37 9.38 6.05a1.2 1.2 0 0 1 0 .9 10.7 10.7 0 0 1-1.67 2.62"/>
                                            <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88"/>
                                        </svg>

                                    </button>

                                </div>


                                @error('currentPassword')
                                    <span class="text-xs text-red-600 mt-1 block">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>


                            {{-- NEW PASSWORD --}}
                            <div>

                                <label
                                    for="newPassword"
                                    class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                >
                                    New Password
                                </label>


                                <div class="relative">

                                    <input
                                        id="newPassword"
                                        x-bind:type="showNewPassword ? 'text' : 'password'"
                                        placeholder="••••••••"
                                        wire:model.blur="newPassword"
                                        x-model="newPasswordValue"
                                        maxlength="20"
                                        autocomplete="new-password"
                                        class="w-full pl-4 pr-12 py-3 border
                                            @error('newPassword')
                                                border-red-500 focus:ring-red-500
                                            @else
                                                border-gray-300 focus:ring-gray-900
                                            @enderror
                                            rounded focus:ring-2 focus:border-transparent outline-none"
                                    >


                                    <button
                                        type="button"
                                        x-on:mousedown.prevent
                                        x-on:click="showNewPassword = !showNewPassword"
                                        class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-gray-400 hover:text-gray-700 transition"
                                        aria-label="Toggle new password visibility"
                                    >

                                        <svg
                                            x-show="showNewPassword"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-5 w-5"
                                        >
                                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>


                                        <svg
                                            x-show="!showNewPassword"
                                            x-cloak
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-5 w-5"
                                        >
                                            <path d="m2 2 20 20"/>
                                            <path d="M6.71 6.71C4.9 7.9 3.45 9.6 2.62 11.55a1.2 1.2 0 0 0 0 .9C4.18 16.13 7.78 18.5 12 18.5c1.5 0 2.92-.3 4.19-.84"/>
                                            <path d="M10.73 5.58A10.9 10.9 0 0 1 12 5.5c4.22 0 7.82 2.37 9.38 6.05a1.2 1.2 0 0 1 0 .9 10.7 10.7 0 0 1-1.67 2.62"/>
                                            <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88"/>
                                        </svg>

                                    </button>

                                </div>

                                {{-- PASSWORD REQUIREMENTS --}}
                                <div class="mt-3 rounded-lg border border-gray-100 bg-gray-50 p-4">

                                    <p class="mb-2 text-xs font-medium text-gray-600">
                                        Password must contain:
                                    </p>

                                    <div class="space-y-1.5">

                                        {{-- 8 CHARACTERS --}}
                                        <div
                                            class="flex items-center gap-2 text-xs"
                                            :class="hasMinLength()
                                                ? 'text-green-600'
                                                : 'text-gray-400'"
                                        >
                                            <span
                                                class="flex h-4 w-4 items-center justify-center rounded-full border"
                                                :class="hasMinLength()
                                                    ? 'border-green-500 bg-green-500 text-white'
                                                    : 'border-gray-300'"
                                            >
                                                <span x-show="hasMinLength()">✓</span>
                                            </span>

                                            At least 8 characters
                                        </div>


                                        {{-- MAX 20 CHARACTERS --}}
                                        <div
                                            class="flex items-center gap-2 text-xs"
                                            :class="hasMaxLength() && newPasswordValue.length > 0
                                                ? 'text-green-600'
                                                : 'text-gray-400'"
                                        >
                                            <span
                                                class="flex h-4 w-4 items-center justify-center rounded-full border"
                                                :class="hasMaxLength() && newPasswordValue.length > 0
                                                    ? 'border-green-500 bg-green-500 text-white'
                                                    : 'border-gray-300'"
                                            >
                                                <span
                                                    x-show="hasMaxLength() && newPasswordValue.length > 0"
                                                >
                                                    ✓
                                                </span>
                                            </span>

                                            Maximum of 20 characters
                                        </div>


                                        {{-- LOWERCASE --}}
                                        <div
                                            class="flex items-center gap-2 text-xs"
                                            :class="hasLowercase()
                                                ? 'text-green-600'
                                                : 'text-gray-400'"
                                        >
                                            <span
                                                class="flex h-4 w-4 items-center justify-center rounded-full border"
                                                :class="hasLowercase()
                                                    ? 'border-green-500 bg-green-500 text-white'
                                                    : 'border-gray-300'"
                                            >
                                                <span x-show="hasLowercase()">✓</span>
                                            </span>

                                            One lowercase letter
                                        </div>


                                        {{-- UPPERCASE --}}
                                        <div
                                            class="flex items-center gap-2 text-xs"
                                            :class="hasUppercase()
                                                ? 'text-green-600'
                                                : 'text-gray-400'"
                                        >
                                            <span
                                                class="flex h-4 w-4 items-center justify-center rounded-full border"
                                                :class="hasUppercase()
                                                    ? 'border-green-500 bg-green-500 text-white'
                                                    : 'border-gray-300'"
                                            >
                                                <span x-show="hasUppercase()">✓</span>
                                            </span>

                                            One uppercase letter
                                        </div>


                                        {{-- NUMBER --}}
                                        <div
                                            class="flex items-center gap-2 text-xs"
                                            :class="hasNumber()
                                                ? 'text-green-600'
                                                : 'text-gray-400'"
                                        >
                                            <span
                                                class="flex h-4 w-4 items-center justify-center rounded-full border"
                                                :class="hasNumber()
                                                    ? 'border-green-500 bg-green-500 text-white'
                                                    : 'border-gray-300'"
                                            >
                                                <span x-show="hasNumber()">✓</span>
                                            </span>

                                            One number
                                        </div>


                                        {{-- SPECIAL CHARACTER --}}
                                        <div
                                            class="flex items-center gap-2 text-xs"
                                            :class="hasSpecialCharacter()
                                                ? 'text-green-600'
                                                : 'text-gray-400'"
                                        >
                                            <span
                                                class="flex h-4 w-4 items-center justify-center rounded-full border"
                                                :class="hasSpecialCharacter()
                                                    ? 'border-green-500 bg-green-500 text-white'
                                                    : 'border-gray-300'"
                                            >
                                                <span x-show="hasSpecialCharacter()">✓</span>
                                            </span>

                                            One special character
                                            <span class="text-gray-400">
                                                (@$!%*?&#^()_-+=)
                                            </span>
                                        </div>

                                    </div>

                                </div>

                                @error('newPassword')
                                    <span class="text-xs text-red-600 mt-1 block">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>


                            {{-- CONFIRM NEW PASSWORD --}}
                            <div>

                                <label
                                    for="confirmPassword"
                                    class="block text-xs text-gray-500 mb-2 uppercase tracking-wide"
                                >
                                    Confirm New Password
                                </label>


                                <div class="relative">

                                    <input
                                        id="confirmPassword"
                                        x-bind:type="showConfirmPassword ? 'text' : 'password'"
                                        placeholder="••••••••"
                                        wire:model.blur="confirmPassword"
                                        class="w-full pl-4 pr-12 py-3 border
                                            @error('confirmPassword')
                                                border-red-500 focus:ring-red-500
                                            @else
                                                border-gray-300 focus:ring-gray-900
                                            @enderror
                                            rounded focus:ring-2 focus:border-transparent outline-none"
                                    >


                                    <button
                                        type="button"
                                        x-on:mousedown.prevent
                                        x-on:click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-gray-400 hover:text-gray-700 transition"
                                        aria-label="Toggle confirm password visibility"
                                    >

                                        <svg
                                            x-show="showConfirmPassword"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-5 w-5"
                                        >
                                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>


                                        <svg
                                            x-show="!showConfirmPassword"
                                            x-cloak
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-5 w-5"
                                        >
                                            <path d="m2 2 20 20"/>
                                            <path d="M6.71 6.71C4.9 7.9 3.45 9.6 2.62 11.55a1.2 1.2 0 0 0 0 .9C4.18 16.13 7.78 18.5 12 18.5c1.5 0 2.92-.3 4.19-.84"/>
                                            <path d="M10.73 5.58A10.9 10.9 0 0 1 12 5.5c4.22 0 7.82 2.37 9.38 6.05a1.2 1.2 0 0 1 0 .9 10.7 10.7 0 0 1-1.67 2.62"/>
                                            <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88"/>
                                        </svg>

                                    </button>

                                </div>


                                @error('confirmPassword')
                                    <span class="text-xs text-red-600 mt-1 block">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="w-full px-6 py-4 bg-gray-900 text-white hover:bg-gray-800 transition-colors flex items-center justify-center gap-2 uppercase tracking-wide text-sm font-medium rounded-lg shadow-sm"
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
                                class="h-5 w-5"
                            >
                                <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
                                <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"></path>
                                <path d="M7 3v4a1 1 0 0 0 1 1h7"></path>
                            </svg>

                            <span>
                                Update Password
                            </span>

                        </button>

                    </form>


                    {{-- LOGOUT --}}
                    <div
                        class="mt-8 pt-8 border-t border-gray-200"
                    >

                        <button
                            type="button"
                            wire:click="logout"
                            class="w-full px-6 py-4 bg-red-600 text-white hover:bg-red-700 transition-colors flex items-center justify-center gap-2 uppercase tracking-wide text-sm font-medium rounded-lg shadow-sm"
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
                                class="h-5 w-5"
                            >

                                <path
                                    d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                                ></path>

                                <polyline
                                    points="16 17 21 12 16 7"
                                ></polyline>

                                <line
                                    x1="21"
                                    x2="9"
                                    y1="12"
                                    y2="12"
                                ></line>

                            </svg>

                            <span>
                                Logout Account
                            </span>

                        </button>


                        <p
                            class="text-center text-xs text-gray-500 mt-3"
                        >
                            You will be logged out of your secure session and returned to the authentication landing portal.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>