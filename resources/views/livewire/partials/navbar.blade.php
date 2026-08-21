<header class="fixed top-0 left-0 z-50 w-full bg-white shadow-sm">
    <nav
        class="mx-auto flex w-full max-w-[97rem] flex-wrap items-center justify-between px-5 py-4 sm:px-8 lg:px-14"
        aria-label="Global Navigation"
    >
        {{-- Logo --}}
        <a
            href="{{ route('user.home') }}"
            wire:navigate
            class="flex items-center gap-3 text-lg font-bold text-[#2b2b31] lg:text-3xl"
        >
            <img
                src="{{ asset('images/estate-view-logo.png') }}"
                class="h-10 w-auto sm:h-12"
                alt="EstateView"
            >
        </a>

        @unless(
            request()->routeIs('login') ||
            request()->routeIs('register') ||
            request()->routeIs('password.request') ||
            request()->routeIs('password.reset') ||
            request()->routeIs('account.verify') ||
            request()->routeIs('account.resend-verification') ||
            request()->routeIs('activate-account')
        )

            {{-- Mobile Toggle --}}
            <button
                type="button"
                class="hs-collapse-toggle inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2.5 text-gray-800 shadow-sm hover:bg-gray-50 lg:hidden"
                data-hs-collapse="#estateview-navbar"
                aria-controls="estateview-navbar"
                aria-label="Toggle navigation"
            >
                {{-- Menu icon --}}
                <svg
                    class="hs-collapse-open:hidden size-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <line x1="3" x2="21" y1="6" y2="6"/>
                    <line x1="3" x2="21" y1="12" y2="12"/>
                    <line x1="3" x2="21" y1="18" y2="18"/>
                </svg>

                {{-- Close icon --}}
                <svg
                    class="hs-collapse-open:block hidden size-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>

            {{-- Responsive Navbar --}}
            <div
                id="estateview-navbar"
                class="hs-collapse hidden w-full basis-full overflow-hidden transition-all duration-300 lg:block lg:w-auto lg:basis-auto"
            >
                <div class="mt-5 flex flex-col gap-1 border-t border-gray-100 pt-5 lg:mt-0 lg:flex-row lg:items-center lg:gap-2 lg:border-0 lg:pt-0">
                    @php
                        $currentUser = auth()->user();

                        $isAgent = auth()->check()
                            && $currentUser->role === 'agent';

                        $isClient = auth()->check()
                            && $currentUser->role === 'user';
                    @endphp

                    {{-- HOME --}}
                    <a
                        href="{{ $isAgent ? route('agent.dashboard') : route('user.home') }}"
                        wire:navigate
                        class="rounded-lg px-3 py-2 text-sm font-medium transition
                            {{ $isAgent
                                ? (request()->routeIs('agent.dashboard')
                                    ? 'text-[#d6b685]'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]')
                                : (request()->routeIs('user.home')
                                    ? 'text-[#d6b685]'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]') }}"
                    >
                        HOME
                    </a>

                    {{-- ABOUT: hidden from agents --}}
                    @if(!$isAgent)
                        <a
                            href="{{ route('user.about') }}"
                            wire:navigate
                            class="rounded-lg px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('user.about')
                                    ? 'text-[#d6b685]'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]' }}"
                        >
                            ABOUT
                        </a>
                    @endif

                    {{-- PROPERTIES: available to everyone --}}
                    <a
                        href="{{ route('user.properties') }}"
                        wire:navigate
                        class="rounded-lg px-3 py-2 text-sm font-medium transition
                            {{ request()->routeIs('user.properties')
                                ? 'text-[#d6b685]'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]' }}"
                    >
                        PROPERTIES
                    </a>

                    {{-- Guest Navigation --}}
                    @guest
                        <div class="mt-3 border-t border-gray-100 pt-4 lg:mt-0 lg:border-0 lg:pt-0">
                            <a
                                href="{{ route('login') }}"
                                wire:navigate
                                class="inline-flex w-full items-center justify-center bg-[#2b2b31] px-7 py-3 text-sm font-semibold text-white transition hover:bg-black lg:w-auto"
                            >
                                LOGIN
                            </a>
                        </div>
                    @endguest

                    @auth
                        {{-- ================================================= --}}
                        {{-- CLIENT NAVIGATION --}}
                        {{-- ================================================= --}}
                        @if($isClient)
                            <a
                                href="{{ route('client.cost.breakdown') }}"
                                wire:navigate
                                class="rounded-lg px-3 py-2 text-sm font-medium transition
                                    {{ request()->routeIs('client.cost.breakdown')
                                        ? 'text-[#d6b685]'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]' }}"
                            >
                                COST BREAKDOWN
                            </a>

                            <a
                                href="{{ route('client.appointment') }}"
                                wire:navigate
                                class="rounded-lg px-3 py-2 text-sm font-medium transition
                                    {{ request()->routeIs('client.appointment')
                                        ? 'text-[#d6b685]'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]' }}"
                            >
                                APPOINTMENTS
                            </a>

                            <a
                                href="{{ route('client.reservation') }}"
                                wire:navigate
                                class="rounded-lg px-3 py-2 text-sm font-medium transition
                                    {{ request()->routeIs('client.reservation')
                                        ? 'text-[#d6b685]'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]' }}"
                            >
                                RESERVATIONS
                            </a>

                            {{-- MOVED: MY BILLS AFTER RESERVATIONS --}}
                            <a
                                href="{{ route('client.bills') }}"
                                wire:navigate
                                class="rounded-lg px-3 py-2 text-sm font-medium transition
                                    {{ request()->routeIs('client.bills')
                                        ? 'text-[#d6b685]'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]' }}"
                            >
                                MY BILLS
                            </a>

                            {{-- CLIENT ACCOUNT NAV REMOVED --}}
                            <div class="px-3 py-2">
                                <livewire:partials.notification-badge />
                            </div>
                        @endif

                        {{-- ================================================= --}}
                        {{-- AGENT NAVIGATION --}}
                        {{-- ================================================= --}}
                        @if($isAgent)
                            <a
                                href="{{ route('client.cost.breakdown') }}"
                                wire:navigate
                                class="rounded-lg px-3 py-2 text-sm font-medium transition
                                    {{ request()->routeIs('client.cost.breakdown')
                                        ? 'text-[#d6b685]'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]' }}"
                            >
                                COST BREAKDOWN
                            </a>

                            <a
                                href="{{ route('agent.commission') }}"
                                wire:navigate
                                class="rounded-lg px-3 py-2 text-sm font-medium transition
                                    {{ request()->routeIs('agent.commission')
                                        ? 'text-[#d6b685]'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-[#d6b685]' }}"
                            >
                                COMMISSION
                            </a>

                            {{-- AGENT ACCOUNT NAV REMOVED --}}
                            <div class="px-3 py-2">
                                <livewire:partials.notification-badge />
                            </div>
                        @endif

                        {{-- ================================================= --}}
                        {{-- DESKTOP USER PROFILE --}}
                        {{-- ================================================= --}}
                        @if($isAgent || $isClient)
                            <a
                                href="{{ route('client.account') }}"
                                wire:navigate
                                class="ml-2 hidden items-center gap-3 border-l border-gray-200 pl-5 pr-3 py-1 rounded-lg transition hover:bg-gray-50 lg:flex"
                            >
                                @if($currentUser->profile_picture)
                                    <img
                                        src="{{ asset($currentUser->profile_picture) }}"
                                        alt="{{ $currentUser->name }}"
                                        class="h-10 w-10 rounded-full object-cover"
                                    >
                                @else
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-gray-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5.121 17.804A4 4 0 017 16h10a4 4 0 011.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                            />

                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p
                                        class="max-w-36 truncate text-sm font-semibold text-gray-800"
                                    >
                                        {{ $currentUser->name }}
                                    </p>


                                    <p
                                        class="text-xs font-medium text-[#d6b685]"
                                    >
                                        {{ $isClient ? 'Client' : 'Agent' }}
                                    </p>
                                </div>
                            </a>
                        @endif

                        {{-- ================================================= --}}
                        {{-- MOBILE PROFILE --}}
                        {{-- ================================================= --}}
                        @if($isAgent || $isClient)

                            <div
                                class="mt-4 border-t border-gray-100 pt-4 lg:hidden"
                            >
                                <a
                                    href="{{ route('client.account') }}"
                                    wire:navigate
                                    class="mb-3 flex items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-gray-50"
                                >
                                    @if($currentUser->profile_picture)

                                        <img
                                            src="{{ asset($currentUser->profile_picture) }}"
                                            alt="{{ $currentUser->name }}"
                                            class="h-11 w-11 rounded-full object-cover"
                                        >
                                    @else
                                        <div
                                            class="flex h-11 w-11 items-center justify-center rounded-full bg-gray-200"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6 text-gray-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5.121 17.804A4 4 0 017 16h10a4 4 0 011.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-sm font-semibold text-gray-800"
                                        >
                                            {{ $currentUser->name }}
                                        </p>

                                        <p
                                            class="truncate text-xs text-gray-500"
                                        >
                                            {{ $currentUser->email }}
                                        </p>

                                        <p
                                            class="text-xs font-medium text-[#d6b685]"
                                        >
                                            {{ $isClient ? 'Client' : 'Agent' }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        @endunless
    </nav>
</header>