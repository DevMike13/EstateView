<div class="bg-white">
    <section class="py-24 lg:py-32">
        <div class="bg-gray-50">
            <section class="bg-white border-b border-gray-100 py-16">
                <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
                <h1 class="text-4xl lg:text-5xl font-light text-gray-900 mb-4">Agent Dashboard</h1>
                <p class="text-lg text-gray-600">Welcome back, {{ auth()->user()->name }}! Here's an overview of your activities.</p>
                </div>
            </section>
            <section class="py-16">
                <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 rounded-lg flex items-center justify-center bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-6 w-6 text-blue-600">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-light text-gray-900 mb-2">{{ number_format($activeClients) }}</div>
                    <div class="text-sm text-gray-600 uppercase tracking-wide">Active Clients</div>
                    </div>
                    <div class="bg-white p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 rounded-lg flex items-center justify-center bg-green-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-6 w-6 text-green-600">
                            <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path>
                            <path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-light text-gray-900 mb-2">{{ number_format($propertiesManaged) }}</div>
                    <div class="text-sm text-gray-600 uppercase tracking-wide">Properties Managed</div>
                    </div>
                    <div class="bg-white p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 rounded-lg flex items-center justify-center bg-purple-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dollar-sign h-6 w-6 text-purple-600">
                            <line x1="12" x2="12" y1="2" y2="22"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-light text-gray-900 mb-2">
                        ₱{{ number_format($commissionEarned, 2) }}
                    </div>

                    <div class="text-sm text-gray-600 uppercase tracking-wide">
                        Commission Earned
                    </div>
                    </div>
                    <div class="bg-white p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-12 w-12 rounded-lg flex items-center justify-center bg-orange-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-6 w-6 text-orange-600">
                            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                            <polyline points="16 7 22 7 22 13"></polyline>
                        </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-light text-gray-900 mb-2">
                        ₱{{ number_format($commissionPending, 2) }}
                    </div>

                    <div class="text-sm text-gray-600 uppercase tracking-wide">
                        Commission Pending
                    </div>
                    </div>
                </div>
                </div>
            </section>
            <section class="pb-16">
                <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
                <h2 class="text-2xl font-light text-gray-900 mb-8">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('user.properties') }}" data-discover="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-8 w-8 text-gray-400 mb-4 group-hover:text-gray-900 transition-colors">
                        <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path>
                        <path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    </svg>
                    <h3 class="text-lg text-gray-900 mb-2">View Properties</h3>
                    <p class="text-sm text-gray-600">Browse and manage property listings</p>
                    </a>
                    <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('client.cost.breakdown') }}" data-discover="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-dollar-sign h-8 w-8 text-gray-400 mb-4 group-hover:text-gray-900 transition-colors">
                        <line x1="12" x2="12" y1="2" y2="22"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    <h3 class="text-lg text-gray-900 mb-2">Cost Calculator</h3>
                    <p class="text-sm text-gray-600">Calculate payment plans for clients</p>
                    </a>
                    <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('client.notification') }}" data-discover="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell h-8 w-8 text-gray-400 mb-4 group-hover:text-gray-900 transition-colors">
                        <path d="M10.268 21a2 2 0 0 0 3.464 0"></path>
                        <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"></path>
                    </svg>
                    <h3 class="text-lg text-gray-900 mb-2">Notifications</h3>
                    <p class="text-sm text-gray-600">Check your latest updates</p>
                    </a>
                </div>
                </div>
            </section>
            <section class="pb-24">
                <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-light text-gray-900">
                            Recent Activity
                        </h2>

                        <button
                            type="button"
                            wire:click="loadDashboardData"
                            wire:loading.attr="disabled"
                            class="text-sm font-medium text-blue-600 hover:text-blue-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="loadDashboardData">
                                Refresh
                            </span>

                            <span wire:loading wire:target="loadDashboardData">
                                Refreshing...
                            </span>
                        </button>
                    </div>

                    <div class="bg-white shadow-sm">
                        @forelse ($recentActivities as $activity)
                            @php
                                $type = data_get($activity->data, 'type', '');

                                $style = match (true) {
                                    str_contains($type, 'payment') => [
                                        'background' => 'bg-purple-100',
                                        'icon' => 'text-purple-600',
                                        'name' => 'payment',
                                    ],

                                    str_contains($type, 'reservation') => [
                                        'background' => 'bg-blue-100',
                                        'icon' => 'text-blue-600',
                                        'name' => 'reservation',
                                    ],

                                    default => [
                                        'background' => 'bg-gray-100',
                                        'icon' => 'text-gray-600',
                                        'name' => 'default',
                                    ],
                                };
                            @endphp

                            <div
                                wire:key="activity-{{ $activity->id }}"
                                class="p-6 flex items-center gap-4 border-b border-gray-100 last:border-b-0"
                            >
                                <div
                                    class="h-10 w-10 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $style['background'] }}"
                                >
                                    @if ($style['name'] === 'payment')
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
                                            class="h-5 w-5 {{ $style['icon'] }}"
                                        >
                                            <line x1="12" x2="12" y1="2" y2="22"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                    @elseif ($style['name'] === 'reservation')
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
                                            class="h-5 w-5 {{ $style['icon'] }}"
                                        >
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
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
                                            class="h-5 w-5 {{ $style['icon'] }}"
                                        >
                                            <path d="M10.268 21a2 2 0 0 0 3.464 0"></path>
                                            <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"></path>
                                        </svg>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-gray-900 font-medium">
                                        {{ $activity->title }}
                                    </p>

                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                        {{ $activity->message }}
                                    </p>

                                    <p
                                        class="text-xs text-gray-500 mt-2"
                                        title="{{ $activity->created_at->format('F j, Y g:i A') }}"
                                    >
                                        {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                @if (! $activity->is_read)
                                    <span
                                        class="h-2.5 w-2.5 rounded-full bg-blue-600 flex-shrink-0"
                                        title="Unread"
                                    ></span>
                                @endif
                            </div>
                        @empty
                            <div class="py-14 px-6 text-center">
                                <div
                                    class="mx-auto h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center mb-4"
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
                                        <path d="M10.268 21a2 2 0 0 0 3.464 0"></path>
                                        <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"></path>
                                    </svg>
                                </div>

                                <p class="text-gray-900 font-medium">
                                    No recent activity
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    Your reservation and payment updates will appear here.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </section>
</div>
