<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-20">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-16 mb-16">

            <div>
                <div class="flex items-center gap-3 mb-6">
                    <img
                        src="{{ asset('images/logo-white.png') }}"
                        alt="Estate View"
                        class="h-10 brightness-0 invert"
                    >
                </div>

                <p class="text-gray-400 leading-relaxed">
                    Your trusted partner in finding your dream home at Manhattan Residences Candelaria.
                </p>
            </div>

            <div>
                <h3 class="text-sm tracking-wide uppercase mb-6 font-normal">
                    Quick Links
                </h3>

                <ul class="space-y-3">

                    {{-- NOT LOGGED IN --}}
                    @guest
                        <li>
                            <a
                                class="text-gray-400 hover:text-white transition-colors"
                                href="{{ route('user.home') }}"
                            >
                                Home
                            </a>
                        </li>

                        <li>
                            <a
                                class="text-gray-400 hover:text-white transition-colors"
                                href="{{ route('user.about') }}"
                            >
                                About
                            </a>
                        </li>

                        <li>
                            <a
                                class="text-gray-400 hover:text-white transition-colors"
                                href="{{ route('user.properties') }}"
                            >
                                Properties
                            </a>
                        </li>
                    @endguest


                    {{-- LOGGED IN --}}
                    @auth

                        {{-- AGENT --}}
                        @if(auth()->user()->role === 'agent')

                            <li>
                                <a
                                    class="text-gray-400 hover:text-white transition-colors"
                                    href="{{ route('agent.dashboard') }}"
                                >
                                    Home
                                </a>
                            </li>

                            <li>
                                <a
                                    class="text-gray-400 hover:text-white transition-colors"
                                    href="{{ route('user.properties') }}"
                                >
                                    Properties
                                </a>
                            </li>

                            <li>
                                <a
                                    class="text-gray-400 hover:text-white transition-colors"
                                    href="{{ route('client.cost.breakdown') }}"
                                >
                                    Cost Breakdown
                                </a>
                            </li>

                            <li>
                                <a
                                    class="text-gray-400 hover:text-white transition-colors"
                                    href="{{ route('agent.commission') }}"
                                >
                                    Commission
                                </a>
                            </li>


                        {{-- CLIENT / USER --}}
                        @elseif(auth()->user()->role === 'user')

                            <li>
                                <a
                                    class="text-gray-400 hover:text-white transition-colors"
                                    href="{{ route('user.home') }}"
                                >
                                    Home
                                </a>
                            </li>

                            <li>
                                <a
                                    class="text-gray-400 hover:text-white transition-colors"
                                    href="{{ route('user.about') }}"
                                >
                                    About
                                </a>
                            </li>

                            <li>
                                <a
                                    class="text-gray-400 hover:text-white transition-colors"
                                    href="{{ route('user.properties') }}"
                                >
                                    Properties
                                </a>
                            </li>

                            <li>
                                <a
                                    class="text-gray-400 hover:text-white transition-colors"
                                    href="{{ route('client.appointment') }}"
                                >
                                    Appointments
                                </a>
                            </li>

                        @endif

                    @endauth

                </ul>
            </div>

            <div>
                <h3 class="text-sm tracking-wide uppercase mb-6 font-normal">
                    Contact
                </h3>

                <ul class="space-y-3 text-gray-400">
                    <li>DGA Realty Corporation</li>
                    <li>Manhattan Residences Candelaria</li>
                    <li>info@estateview.com</li>
                    <li>(123) 456-7890</li>
                </ul>
            </div>

        </div>

        <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-500">
            © 2026 EstateView by DGA Realty Corporation. All rights reserved.
        </div>

    </div>
</footer>