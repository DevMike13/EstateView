<main class="flex-1 min-w-0">
  <div class="space-y-6 md:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Reports</h1>
        <p class="text-sm md:text-base text-gray-600">View comprehensive data and insights</p>
      </div>
      <button
          type="button"
          wire:click="exportReport"
          wire:loading.attr="disabled"
          class="px-4 md:px-6 py-2 md:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2 whitespace-nowrap disabled:opacity-50"
      >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l5 5 5-5" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15V3" />
          </svg>

          <span class="hidden sm:inline" wire:loading.remove wire:target="exportReport">
              Export Report
          </span>

          <span class="sm:hidden" wire:loading.remove wire:target="exportReport">
              Export
          </span>

          <span wire:loading wire:target="exportReport">
              Exporting...
          </span>
      </button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
      <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4 md:h-5 md:w-5 text-green-600">
            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
            <polyline points="16 7 22 7 22 13"></polyline>
          </svg>
          <span class="text-xs md:text-sm text-gray-500">This Year</span>
        </div>
        <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">{{ $totalReservations }}</div>
        <div class="text-xs md:text-sm text-gray-600">Total Reservations</div>
      </div>
      <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4 md:h-5 md:w-5 text-green-600">
            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
            <polyline points="16 7 22 7 22 13"></polyline>
          </svg>
          <span class="text-xs md:text-sm text-gray-500">This Year</span>
        </div>
        <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">{{ $verifiedPayments }}</div>
        <div class="text-xs md:text-sm text-gray-600">Verified Payments</div>
      </div>
      <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-4 w-4 md:h-5 md:w-5 text-green-600">
            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
            <polyline points="16 7 22 7 22 13"></polyline>
          </svg>
          <span class="text-xs md:text-sm text-gray-500">This Year</span>
        </div>
        <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">₱{{ number_format($totalRevenue, 2) }}</div>
        <div class="text-xs md:text-sm text-gray-600">Total Revenue</div>
      </div>
      <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-circle h-4 w-4 md:h-5 md:w-5 text-red-600">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="m15 9-6 6"></path>
            <path d="m9 9 6 6"></path>
          </svg>
          <span class="text-xs md:text-sm text-gray-500">This Year</span>
        </div>
        <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">{{ $cancelledLedgers }}</div>
        <div class="text-xs md:text-sm text-gray-600">Cancelled Ledgers</div>
      </div>
    </div>

    {{-- ADDED: Financial and Activity Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
      <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xs md:text-sm text-gray-500">This Year</span>
        </div>
        <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">
          ₱{{ number_format($delayedPaymentEarnings, 2) }}
        </div>
        <div class="text-xs md:text-sm text-gray-600">Delayed Payment Earnings</div>
      </div>

      <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xs md:text-sm text-gray-500">This Year</span>
        </div>
        <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">
          ₱{{ number_format($advancePaymentRebates, 2) }}
        </div>
        <div class="text-xs md:text-sm text-gray-600">Advance Payment Rebates</div>
      </div>

      <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xs md:text-sm text-gray-500">This Year</span>
        </div>
        <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">
          ₱{{ number_format($agentPaymentsDisbursed, 2) }}
        </div>
        <div class="text-xs md:text-sm text-gray-600">Agent Payments Disbursed</div>
      </div>

      <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xs md:text-sm text-gray-500">This Year</span>
        </div>
        <div class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">{{ $totalAppointments }}</div>
        <div class="text-xs md:text-sm text-gray-600">Total Appointments</div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
      <div class="bg-white rounded-xl border shadow-sm p-6">
          <h3 class="text-lg font-semibold mb-4">
              Property Status Distribution
          </h3>

          <div class="h-[350px]" wire:ignore>
              <canvas id="propertyStatusChart"></canvas>
          </div>
          <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t">

            <div class="text-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ $availableLots }}
                    </span>
                </div>

                <div class="text-xs text-gray-600">
                    Available
                </div>
            </div>

            <div class="text-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <div class="h-3 w-3 rounded-full bg-amber-500"></div>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ $reservedLots }}
                    </span>
                </div>

                <div class="text-xs text-gray-600">
                    Reserved
                </div>
            </div>

            <div class="text-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <div class="h-3 w-3 rounded-full bg-indigo-500"></div>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ $soldLots }}
                    </span>
                </div>

                <div class="text-xs text-gray-600">
                    Sold
                </div>
            </div>

        </div>
      </div>
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">

        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-900">
                Monthly Payment Collections
            </h3>

            <p class="mt-1 text-xs text-gray-500">
                Collections compared with reservations, appointments,
                and cancelled ledgers over the last 6 months.
            </p>
        </div>

        <div class="h-[380px]" wire:ignore>
            <canvas id="collectionsChart"></canvas>
        </div>

    </div>
    </div>

    {{-- ADDED: Delayed vs Advanced Payments + Agent Commissions --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
      <div class="bg-white rounded-xl border shadow-sm p-6">
          <h3 class="text-lg font-semibold mb-4">
              Delayed vs Advanced Payments (Monthly)
          </h3>

          <div class="h-[350px]" wire:ignore>
              <canvas id="delayedAdvanceChart"></canvas>
          </div>
      </div>
      <div class="bg-white rounded-xl border shadow-sm p-6">
          <h3 class="text-lg font-semibold mb-4">
              Agent Commissions (Monthly)
          </h3>

          <div class="h-[350px]" wire:ignore>
              <canvas id="commissionsChart"></canvas>
          </div>
      </div>
    </div>
    {{-- END ADDED --}}

    <div
      x-data="{ openDelayedPayments: false }"
      class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
    >
      <button
        type="button"
        x-on:click="openDelayedPayments = !openDelayedPayments"
        class="w-full p-6 border-b bg-red-50 flex items-center justify-between gap-4 text-left transition hover:bg-red-100/60"
      >
        <div>
          <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <span class="h-3 w-3 bg-red-500 rounded-full"></span>
            Delayed Payments
          </h2>

          <p class="text-sm text-gray-600 mt-1">
            Clients with overdue payments
          </p>
        </div>

        <div class="flex items-center gap-3">
          <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-red-700 shadow-sm">
            {{ $delayedPayments->count() }}
          </span>

          <x-icon
            name="chevron-down"
            class="h-5 w-5 text-red-500 transition-transform duration-200"
            x-bind:class="openDelayedPayments ? 'rotate-180' : ''"
          />
        </div>
      </button>

      <div
        x-show="openDelayedPayments"
        x-collapse
        x-cloak
      >
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Client</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Lot</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Amount Due</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Due Date</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Days Delayed</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
              @forelse($delayedPayments as $billing)
                  @php
                      $daysDelayed = now()->diffInDays($billing->due_date);
                      $balance = $billing->amount_due - $billing->amount_paid;
                  @endphp

                  <tr class="hover:bg-gray-50">
                      <td class="px-6 py-4 font-medium text-gray-900">
                          {{ $billing->purchaseAccount?->user?->name ?? 'N/A' }}
                      </td>

                      <td class="px-6 py-4 text-gray-700">
                          {{ $billing->purchaseAccount?->lot?->name ?? 'N/A' }}
                      </td>

                      <td class="px-6 py-4 text-gray-700">
                          ₱{{ number_format($balance, 2) }}
                      </td>

                      <td class="px-6 py-4 text-gray-700">
                          {{ $billing->due_date?->format('M d, Y') }}
                      </td>

                      <td class="px-6 py-4">
                          <span class="px-3 py-1 rounded-full text-sm font-medium {{ $daysDelayed >= 15 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                              {{ $daysDelayed }} days
                          </span>
                      </td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                          No delayed payments found.
                      </td>
                  </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div
      x-data="{ openAdvancedPayments: false }"
      class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
    >
      <button
        type="button"
        x-on:click="openAdvancedPayments = !openAdvancedPayments"
        class="w-full p-6 border-b bg-green-50 flex items-center justify-between gap-4 text-left transition hover:bg-green-100/60"
      >
        <div>
          <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <span class="h-3 w-3 bg-green-500 rounded-full"></span>
            Advanced Payments
          </h2>

          <p class="text-sm text-gray-600 mt-1">
            Clients who paid in advance
          </p>
        </div>

        <div class="flex items-center gap-3">
          <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-green-700 shadow-sm">
            {{ $advancedPayments->count() }}
          </span>

          <x-icon
            name="chevron-down"
            class="h-5 w-5 text-green-500 transition-transform duration-200"
            x-bind:class="openAdvancedPayments ? 'rotate-180' : ''"
          />
        </div>
      </button>

      <div
        x-show="openAdvancedPayments"
        x-collapse
        x-cloak
      >
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Client</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Lot</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Amount Paid</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Next Due Date</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Months Advanced</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
              @forelse($advancedPayments as $payment)
                  @php
                      $monthsAdvanced = $payment->billing?->due_date
                          ? max(1, $payment->paid_at->diffInMonths($payment->billing->due_date))
                          : 0;
                  @endphp

                  <tr class="hover:bg-gray-50">
                      <td class="px-6 py-4 font-medium text-gray-900">
                          {{ $payment->purchaseAccount?->user?->name ?? 'N/A' }}
                      </td>

                      <td class="px-6 py-4 text-gray-700">
                          {{ $payment->purchaseAccount?->lot?->name ?? 'N/A' }}
                      </td>

                      <td class="px-6 py-4 text-gray-700">
                          ₱{{ number_format($payment->amount, 2) }}
                      </td>

                      <td class="px-6 py-4 text-gray-700">
                          {{ $payment->billing?->due_date?->format('M d, Y') ?? 'N/A' }}
                      </td>

                      <td class="px-6 py-4">
                          <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                              {{ $monthsAdvanced }} month{{ $monthsAdvanced > 1 ? 's' : '' }}
                          </span>
                      </td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                          No advanced payments found.
                      </td>
                  </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-6 border-b">
        <h2 class="text-lg font-semibold text-gray-900">Performance Summary</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Metric</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Q1 2026</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Target</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Achievement</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @php
                $salesAchievement = $salesTarget > 0 ? round(($quarterSales / $salesTarget) * 100) : 0;
                $reservationAchievement = $reservationTarget > 0 ? round(($quarterReservations / $reservationTarget) * 100) : 0;
                $collectionAchievement = $collectionTarget > 0 ? round(($collectionRate / $collectionTarget) * 100) : 0;
            @endphp

            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-900">Total Sales</td>
                <td class="px-6 py-4 text-gray-700">₱{{ number_format($quarterSales, 2) }}</td>
                <td class="px-6 py-4 text-gray-700">₱{{ number_format($salesTarget, 2) }}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $salesAchievement >= 100 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $salesAchievement }}%
                    </span>
                </td>
            </tr>

            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-900">Reservations</td>
                <td class="px-6 py-4 text-gray-700">{{ $quarterReservations }}</td>
                <td class="px-6 py-4 text-gray-700">{{ $reservationTarget }}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $reservationAchievement >= 100 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $reservationAchievement }}%
                    </span>
                </td>
            </tr>

            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-900">Payment Collection Rate</td>
                <td class="px-6 py-4 text-gray-700">{{ $collectionRate }}%</td>
                <td class="px-6 py-4 text-gray-700">{{ $collectionTarget }}%</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $collectionAchievement >= 100 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $collectionAchievement }}%
                    </span>
                </td>
            </tr>
        </tbody>
        </table>
      </div>
    </div>
  </div>

    @push('scripts')
        <script>
            function initReportsCharts() {
                initPropertyChart();
                initCollectionChart();
                initDelayedAdvanceChart(); // ADDED
                initCommissionsChart();    // ADDED
            }

            function initPropertyChart() {
                const ctx = document.getElementById('propertyStatusChart');

                if (!ctx || typeof Chart === 'undefined') return;

                if (window.propertyChart) {
                    window.propertyChart.destroy();
                }

                window.propertyChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Available', 'Reserved', 'Sold'],
                        datasets: [{
                            label: 'Properties',
                            data: [
                                {{ $availableLots }},
                                {{ $reservedLots }},
                                {{ $soldLots }}
                            ],
                            backgroundColor: [
                                '#10b981',
                                '#f59e0b',
                                '#6366f1'
                            ],
                            borderColor: [
                                '#059669',
                                '#d97706',
                                '#4f46e5'
                            ],
                            borderWidth: 1,
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.raw;
                                    }
                                }
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            function initCollectionChart() {
                const ctx = document.getElementById('collectionsChart');

                if (!ctx || typeof Chart === 'undefined') return;

                if (window.collectionChart) {
                    window.collectionChart.destroy();
                }

                window.collectionChart = new Chart(ctx, {
                    data: {
                        labels: [
                            @foreach($monthlyCollections as $month)
                                '{{ $month["month"] }}',
                            @endforeach
                        ],

                        datasets: [
                            {
                                type: 'bar',
                                label: 'Collections',
                                data: [
                                    @foreach($monthlyCollections as $month)
                                        {{ $month["amount"] }},
                                    @endforeach
                                ],

                                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                borderColor: '#059669',
                                borderWidth: 1,
                                borderRadius: 10,
                                borderSkipped: false,
                                maxBarThickness: 48,
                                categoryPercentage: 0.65,
                                barPercentage: 0.8,
                                yAxisID: 'yMoney',
                                order: 4,
                            },

                            {
                                type: 'line',
                                label: 'Reservations',
                                data: [
                                    @foreach($monthlyActivity as $month)
                                        {{ $month["reservations"] }},
                                    @endforeach
                                ],

                                borderColor: '#2563eb',
                                backgroundColor: '#2563eb',
                                borderWidth: 3,
                                tension: 0.35,

                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBorderWidth: 2,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#2563eb',

                                fill: false,
                                yAxisID: 'yCount',
                                order: 1,
                            },

                            {
                                type: 'line',
                                label: 'Appointments',
                                data: [
                                    @foreach($monthlyActivity as $month)
                                        {{ $month["appointments"] }},
                                    @endforeach
                                ],

                                borderColor: '#f59e0b',
                                backgroundColor: '#f59e0b',
                                borderWidth: 3,
                                tension: 0.35,

                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBorderWidth: 2,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#f59e0b',

                                fill: false,
                                yAxisID: 'yCount',
                                order: 2,
                            },

                            {
                                type: 'line',
                                label: 'Cancelled Ledgers',

                                data: [
                                    @foreach($monthlyActivity as $month)
                                        {{ $month["cancelled_ledgers"] }},
                                    @endforeach
                                ],

                                borderColor: '#ef4444',
                                backgroundColor: '#ef4444',

                                borderWidth: 3,
                                borderDash: [6, 4],
                                tension: 0.25,

                                pointRadius: 5,
                                pointHoverRadius: 7,

                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#ef4444',
                                pointBorderWidth: 3,

                                fill: false,
                                yAxisID: 'yCount',

                                // Draw this above the other datasets
                                order: 0,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        layout: {
                            padding: {
                                top: 10,
                                right: 10,
                                bottom: 0,
                                left: 0,
                            },
                        },

                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },

                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',

                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                    boxHeight: 8,
                                    padding: 18,

                                    font: {
                                        size: 12,
                                        weight: '500',
                                    },
                                },
                            },

                            tooltip: {
                                backgroundColor: '#111827',
                                titleColor: '#ffffff',
                                bodyColor: '#e5e7eb',
                                padding: 12,
                                cornerRadius: 10,
                                displayColors: true,

                                callbacks: {
                                    title: function(items) {
                                        return items[0]?.label ?? '';
                                    },

                                    label: function(context) {
                                        if (context.dataset.yAxisID === 'yMoney') {
                                            return context.dataset.label
                                                + ': ₱'
                                                + Number(context.raw)
                                                    .toLocaleString(
                                                        'en-PH',
                                                        {
                                                            minimumFractionDigits: 2,
                                                            maximumFractionDigits: 2,
                                                        }
                                                    );
                                        }

                                        return context.dataset.label
                                            + ': '
                                            + Number(context.raw)
                                                .toLocaleString();
                                    },
                                },
                            },
                        },

                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },

                                border: {
                                    display: false,
                                },

                                ticks: {
                                    color: '#6b7280',

                                    font: {
                                        size: 12,
                                        weight: '500',
                                    },
                                },
                            },

                            yMoney: {
                                type: 'linear',
                                position: 'left',
                                beginAtZero: true,

                                grid: {
                                    color: 'rgba(148, 163, 184, 0.16)',
                                    drawBorder: false,
                                },

                                border: {
                                    display: false,
                                },

                                title: {
                                    display: true,
                                    text: 'Collections (₱)',
                                    color: '#6b7280',

                                    font: {
                                        size: 11,
                                        weight: '600',
                                    },
                                },

                                ticks: {
                                    color: '#6b7280',

                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return '₱'
                                                + (value / 1000000)
                                                    .toFixed(
                                                        value % 1000000 === 0
                                                            ? 0
                                                            : 1
                                                    )
                                                + 'M';
                                        }

                                        if (value >= 1000) {
                                            return '₱'
                                                + (value / 1000)
                                                    .toFixed(
                                                        value % 1000 === 0
                                                            ? 0
                                                            : 1
                                                    )
                                                + 'K';
                                        }

                                        return '₱'
                                            + Number(value)
                                                .toLocaleString();
                                    },
                                },
                            },

                            yCount: {
                                type: 'linear',
                                position: 'right',
                                beginAtZero: true,

                                suggestedMin: 0,
                                suggestedMax: 5,

                                grid: {
                                    drawOnChartArea: false,
                                },

                                border: {
                                    display: false,
                                },

                                title: {
                                    display: true,
                                    text: 'Activity Count',
                                    color: '#6b7280',

                                    font: {
                                        size: 11,
                                        weight: '600',
                                    },
                                },

                                ticks: {
                                    color: '#6b7280',
                                    precision: 0,
                                    stepSize: 1,
                                },
                            },
                        },
                    },
                });
            }

            // ADDED: Delayed vs Advanced Payments Chart
            function initDelayedAdvanceChart() {
                const ctx = document.getElementById('delayedAdvanceChart');

                if (!ctx || typeof Chart === 'undefined') return;

                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }

                window.delayedAdvanceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [
                            @foreach($monthlyDelayedAdvance as $month)
                                '{{ $month["month"] }}',
                            @endforeach
                        ],
                        datasets: [
                            {
                                label: 'Delayed',
                                data: [
                                    @foreach($monthlyDelayedAdvance as $month)
                                        {{ $month["delayed"] }},
                                    @endforeach
                                ],
                                backgroundColor: '#ef4444',
                                borderColor: '#dc2626',
                                borderWidth: 1,
                                borderRadius: 8,
                            },
                            {
                                label: 'Advanced',
                                data: [
                                    @foreach($monthlyDelayedAdvance as $month)
                                        {{ $month["advanced"] }},
                                    @endforeach
                                ],
                                backgroundColor: '#22c55e',
                                borderColor: '#16a34a',
                                borderWidth: 1,
                                borderRadius: 8,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw;
                                    }
                                }
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // ADDED: Agent Commissions Chart
            function initCommissionsChart() {
                const ctx = document.getElementById('commissionsChart');

                if (!ctx || typeof Chart === 'undefined') return;

                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }

                window.commissionsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [
                            @foreach($monthlyCommissions as $month)
                                '{{ $month["month"] }}',
                            @endforeach
                        ],
                        datasets: [{
                            label: 'Agent Commissions',
                            data: [
                                @foreach($monthlyCommissions as $month)
                                    {{ $month["amount"] }},
                                @endforeach
                            ],
                            backgroundColor: 'rgba(99, 102, 241, 0.2)',
                            borderColor: '#6366f1',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#6366f1',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                            },

                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return '₱' + Number(context.raw).toLocaleString();
                                    }
                                }
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + Number(value).toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', initReportsCharts);
            document.addEventListener('livewire:navigated', initReportsCharts);

            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', () => {
                    setTimeout(initReportsCharts, 100);
                });
            });
        </script>
    @endpush
</main>