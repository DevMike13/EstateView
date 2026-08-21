<main class="flex-1 min-w-0">
  <div class="min-h-screen bg-gray-50">
    <div class="space-y-8 md:space-y-12">
      <div class="bg-white shadow-sm p-4 md:p-8">
        <h1 class="text-2xl md:text-4xl font-light text-gray-900 mb-2">
            {{ ucfirst(auth()->user()?->role ?? 'guest') }} Dashboard
        </h1>
        <p class="text-base md:text-lg text-gray-600">Complete overview and control of EstateView system</p>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.client-records') }}" data-discover="true">
          <div class="flex items-center justify-between mb-6"> 
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-10 w-10 text-gray-400 group-hover:text-gray-900 transition-colors">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <div class="flex items-center gap-1 text-sm text-green-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                <polyline points="16 7 22 7 22 13"></polyline>
              </svg>+12%
            </div>
          </div>
          <div class="text-4xl font-light text-gray-900 mb-2">{{ $totalClients }}</div>
          <div class="text-sm text-gray-600 uppercase tracking-wide">Total Clients</div>
        </a>
        <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.property-management') }}" data-discover="true">
          <div class="flex items-center justify-between mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building h-10 w-10 text-gray-400 group-hover:text-gray-900 transition-colors">
              <rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect>
              <path d="M9 22v-4h6v4"></path>
              <path d="M8 6h.01"></path>
              <path d="M16 6h.01"></path>
              <path d="M12 6h.01"></path>
              <path d="M12 10h.01"></path>
              <path d="M12 14h.01"></path>
              <path d="M16 10h.01"></path>
              <path d="M16 14h.01"></path>
              <path d="M8 10h.01"></path>
              <path d="M8 14h.01"></path>
            </svg>
            <div class="flex items-center gap-1 text-sm text-red-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                <polyline points="16 7 22 7 22 13"></polyline>
              </svg>-5
            </div>
          </div>
          <div class="text-4xl font-light text-gray-900 mb-2">{{ $availableLots }}</div>
          <div class="text-sm text-gray-600 uppercase tracking-wide">Available Lots</div>
        </a>
        <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.reservations') }}" data-discover="true">
          <div class="flex items-center justify-between mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check h-10 w-10 text-gray-400 group-hover:text-gray-900 transition-colors">
              <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
              <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
              <path d="m9 15 2 2 4-4"></path>
            </svg>
            <div class="flex items-center gap-1 text-sm text-green-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                <polyline points="16 7 22 7 22 13"></polyline>
              </svg>+8
            </div>
          </div>
          <div class="text-4xl font-light text-gray-900 mb-2">{{ $reservedLots }}</div>
          <div class="text-sm text-gray-600 uppercase tracking-wide">Reserved Lots</div>
        </a>
        <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.property-management') }}" data-discover="true">
          <div class="flex items-center justify-between mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building h-10 w-10 text-gray-400 group-hover:text-gray-900 transition-colors">
              <rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect>
              <path d="M9 22v-4h6v4"></path>
              <path d="M8 6h.01"></path>
              <path d="M16 6h.01"></path>
              <path d="M12 6h.01"></path>
              <path d="M12 10h.01"></path>
              <path d="M12 14h.01"></path>
              <path d="M16 10h.01"></path>
              <path d="M16 14h.01"></path>
              <path d="M8 10h.01"></path>
              <path d="M8 14h.01"></path>
            </svg>
            <div class="flex items-center gap-1 text-sm text-green-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                <polyline points="16 7 22 7 22 13"></polyline>
              </svg>+15
            </div>
          </div>
          <div class="text-4xl font-light text-gray-900 mb-2">{{ $soldLots }}</div>
          <div class="text-sm text-gray-600 uppercase tracking-wide">Sold Lots</div>
        </a>
        @if(auth()->user()?->role !== 'staff')
          <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.agent-management') }}" data-discover="true">
            <div class="flex items-center justify-between mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-10 w-10 text-gray-400 group-hover:text-gray-900 transition-colors">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <div class="flex items-center gap-1 text-sm text-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4">
                  <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                  <polyline points="16 7 22 7 22 13"></polyline>
                </svg>+3
              </div>
            </div>
            <div class="text-4xl font-light text-gray-900 mb-2">{{ $totalAgents }}</div>
            <div class="text-sm text-gray-600 uppercase tracking-wide">Total Agents</div>
          </a>
          <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.reports') }}" data-discover="true">
            <div class="flex items-center justify-between mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-10 w-10 text-gray-400 group-hover:text-gray-900 transition-colors">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <div class="flex items-center gap-1 text-sm text-orange-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4">
                  <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                  <polyline points="16 7 22 7 22 13"></polyline>
                </svg>-1
              </div>
            </div>
            <div class="text-4xl font-light text-gray-900 mb-2">{{ $delayedPayments }}</div>
            <div class="text-sm text-gray-600 uppercase tracking-wide">Delayed Payments</div>
          </a>
          <a class="bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.reports') }}" data-discover="true">
            <div class="flex items-center justify-between mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-10 w-10 text-gray-400 group-hover:text-gray-900 transition-colors">
                <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                <path d="m9 11 3 3L22 4"></path>
              </svg>
              <div class="flex items-center gap-1 text-sm text-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4">
                  <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                  <polyline points="16 7 22 7 22 13"></polyline>
                </svg>+1
              </div>
            </div>
            <div class="text-4xl font-light text-gray-900 mb-2">{{ $advancedPayments }}</div>
            <div class="text-sm text-gray-600 uppercase tracking-wide">Advanced Payments</div>
          </a>
        @endif
      </div>
      <div class="bg-yellow-50 border border-yellow-200 p-4 md:p-8">
        <div class="flex items-start gap-3 md:gap-4 mb-4 md:mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert h-5 w-5 md:h-6 md:w-6 text-yellow-600 flex-shrink-0 mt-1">
            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path>
            <path d="M12 9v4"></path>
            <path d="M12 17h.01"></path>
          </svg>
          <div>
            <h3 class="text-base md:text-lg text-gray-900 mb-2">Action Required</h3>
            <p class="text-sm md:text-base text-gray-700 leading-relaxed">There are pending items that require your attention.</p>
          </div>
        </div>
        <div class="grid sm:grid-cols-3 gap-6">
          <a class="bg-white p-6 border border-yellow-200 hover:shadow-md transition-all" href="{{ route('filament.ev-admin.pages.reservations') }}" data-discover="true">
            <div class="text-3xl font-light text-gray-900 mb-2">{{ $pendingReservations }}</div>
            <div class="text-sm text-gray-700 uppercase tracking-wide">Pending Reservations</div>
          </a>
          <a class="bg-white p-6 border border-yellow-200 hover:shadow-md transition-all" href="{{ route('filament.ev-admin.pages.reservations') }}" data-discover="true">
            <div class="text-3xl font-light text-gray-900 mb-2">{{ $pendingAppointments }}</div>
            <div class="text-sm text-gray-700 uppercase tracking-wide">Pending Appointments</div>
          </a>
          <a class="bg-white p-6 border border-yellow-200 hover:shadow-md transition-all" href="{{ route('filament.ev-admin.pages.payment-q-r-codes') }}" data-discover="true">
            <div class="text-3xl font-light text-gray-900 mb-2">{{ $paymentQrCodes }}</div>
            <div class="text-sm text-gray-700 uppercase tracking-wide">Payment QR Codes</div>
          </a>
        </div>
      </div>
      <div class="grid lg:grid-cols-2 gap-8">
        <div>
          <h2 class="text-xl md:text-2xl font-light text-gray-900 mb-4 md:mb-6">Recent Activity</h2>
          <div class="bg-white shadow-sm divide-y divide-gray-100">
            @forelse($recentActivities as $activity)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <div class="h-2 w-2 rounded-full mt-2 bg-orange-600"></div>

                            <div>
                                <div class="text-gray-900 mb-1">
                                    {{ $activity->title }}
                                </div>

                                <div class="text-sm text-gray-600">
                                    {{ $activity->message }}
                                </div>
                            </div>
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $activity->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-sm text-gray-400">
                    No recent activity yet.
                </div>
            @endforelse
          </div>
        </div>
        <div>
          <h2 class="text-xl md:text-2xl font-light text-gray-900 mb-4 md:mb-6">Quick Actions</h2>
          <div class="space-y-4">
            <a class="block bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.staff-management') }}" data-discover="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-10 w-10 text-gray-400 mb-4 group-hover:text-gray-900 transition-colors">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <h3 class="text-lg text-gray-900 mb-2">Manage Staff</h3>
              <p class="text-sm text-gray-600">Add, edit, or remove staff members</p>
            </a>
            <a class="block bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.property-management') }}" data-discover="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building h-10 w-10 text-gray-400 mb-4 group-hover:text-gray-900 transition-colors">
                <rect width="16" height="20" x="4" y="2" rx="2" ry="2"></rect>
                <path d="M9 22v-4h6v4"></path>
                <path d="M8 6h.01"></path>
                <path d="M16 6h.01"></path>
                <path d="M12 6h.01"></path>
                <path d="M12 10h.01"></path>
                <path d="M12 14h.01"></path>
                <path d="M16 10h.01"></path>
                <path d="M16 14h.01"></path>
                <path d="M8 10h.01"></path>
                <path d="M8 14h.01"></path>
              </svg>
              <h3 class="text-lg text-gray-900 mb-2">Property Management</h3>
              <p class="text-sm text-gray-600">Update lot availability and pricing</p>
            </a>
            <a class="block bg-white p-8 shadow-sm hover:shadow-md transition-all group" href="{{ route('filament.ev-admin.pages.reports') }}" data-discover="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chart-column h-10 w-10 text-gray-400 mb-4 group-hover:text-gray-900 transition-colors">
                <path d="M3 3v16a2 2 0 0 0 2 2h16"></path>
                <path d="M18 17V9"></path>
                <path d="M13 17V5"></path>
                <path d="M8 17v-3"></path>
              </svg>
              <h3 class="text-lg text-gray-900 mb-2">View Reports</h3>
              <p class="text-sm text-gray-600">Analytics and performance insights</p>
            </a>
          </div>
        </div>
      </div>
      <div class="bg-white shadow-sm p-4 md:p-8">
        <h2 class="text-xl md:text-2xl font-light text-gray-900 mb-4 md:mb-8">System Overview</h2>
        <div class="grid md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="text-4xl font-light text-gray-900 mb-2">98%</div>
            <div class="text-sm text-gray-600 uppercase tracking-wide">System Uptime</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-light text-gray-900 mb-2">{{ number_format($totalTransactions) }}</div>
            <div class="text-sm text-gray-600 uppercase tracking-wide">Total Transactions</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-light text-gray-900 mb-2">₱{{ number_format($totalSales, 2) }}</div>
            <div class="text-sm text-gray-600 uppercase tracking-wide">Total Sales</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-light text-gray-900 mb-2">{{ $activeAccounts }}</div>
            <div class="text-sm text-gray-600 uppercase tracking-wide">Active Accounts</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>