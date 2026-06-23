<?php

namespace App\Livewire\FilPages;

use App\Models\Billing;
use App\Models\BillingPayment;
use App\Models\Lot;
use App\Models\LotReservation;
use App\Models\PurchaseAccount;
use Livewire\Component;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class Reports extends Component
{
    public function exportReport()
    {
        return Excel::download(
            new ReportsExport,
            'estateview-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function render()
    {
        $availableLots = Lot::where('status', 'available')->count();
        $reservedLots = Lot::where('status', 'reserved')->count();
        $soldLots = Lot::whereIn('status', ['sold', 'occupied'])->count();

        $totalReservations = LotReservation::whereYear('created_at', now()->year)->count();

        $verifiedPayments = BillingPayment::where('status', 'verified')
            ->whereYear('paid_at', now()->year)
            ->count();

        $totalRevenue = BillingPayment::where('status', 'verified')
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $avgSalePrice = PurchaseAccount::whereNotNull('total_contract_price')
            ->avg('total_contract_price');

        $delayedPayments = Billing::with([
                'purchaseAccount.user',
                'purchaseAccount.lot',
            ])
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->get();

        $advancedPayments = BillingPayment::with([
                'billing',
                'purchaseAccount.user',
                'purchaseAccount.lot',
            ])
            ->where('status', 'verified')
            ->whereNotNull('paid_at')
            ->whereHas('billing', function ($query) {
                $query->whereColumn('billing_payments.paid_at', '<', 'billings.due_date');
            })
            ->latest('paid_at')
            ->get();

        $monthlyCollections = collect(range(5, 0))
            ->map(function ($monthsAgo) {
                $date = now()->subMonths($monthsAgo);

                return [
                    'month' => $date->format('M'),
                    'amount' => BillingPayment::where('status', 'verified')
                        ->whereYear('paid_at', $date->year)
                        ->whereMonth('paid_at', $date->month)
                        ->sum('amount'),
                ];
            });

            /**
         * Dynamic Performance Summary
         */
        $currentQuarter = now()->quarter;
        $currentYear = now()->year;

        $qStart = now()->firstOfQuarter()->startOfDay();
        $qEnd = now()->lastOfQuarter()->endOfDay();

        $quarterSales = BillingPayment::where('status', 'verified')
            ->whereBetween('paid_at', [$qStart, $qEnd])
            ->sum('amount');

        $quarterReservations = LotReservation::whereBetween('created_at', [$qStart, $qEnd])
            ->count();

        $totalBillings = Billing::whereBetween('due_date', [$qStart, $qEnd])
            ->count();

        $paidBillings = Billing::whereBetween('due_date', [$qStart, $qEnd])
            ->where('status', 'paid')
            ->count();

        $collectionRate = $totalBillings > 0
            ? round(($paidBillings / $totalBillings) * 100)
            : 0;

        /**
         * Static targets for now.
         * You can later move these to a settings table.
         */
        $salesTarget = 45000000;
        $reservationTarget = 40;
        $collectionTarget = 90;

        $salesAchievement = $salesTarget > 0
            ? round(($quarterSales / $salesTarget) * 100)
            : 0;

        $reservationAchievement = $reservationTarget > 0
            ? round(($quarterReservations / $reservationTarget) * 100)
            : 0;

        $collectionAchievement = $collectionTarget > 0
            ? round(($collectionRate / $collectionTarget) * 100)
            : 0;

        return view('livewire.fil-pages.reports', [
            'totalReservations' => $totalReservations,
            'verifiedPayments' => $verifiedPayments,
            'totalRevenue' => $totalRevenue,
            'avgSalePrice' => $avgSalePrice,

            'availableLots' => $availableLots,
            'reservedLots' => $reservedLots,
            'soldLots' => $soldLots,

            'delayedPayments' => $delayedPayments,
            'advancedPayments' => $advancedPayments,
            'monthlyCollections' => $monthlyCollections,

            'maxCollection' => max($monthlyCollections->max('amount'), 1),

            'currentQuarter' => $currentQuarter,
            'currentYear' => $currentYear,

            'quarterSales' => $quarterSales,
            'quarterReservations' => $quarterReservations,
            'collectionRate' => $collectionRate,

            'salesTarget' => $salesTarget,
            'reservationTarget' => $reservationTarget,
            'collectionTarget' => $collectionTarget,

            'salesAchievement' => $salesAchievement,
            'reservationAchievement' => $reservationAchievement,
            'collectionAchievement' => $collectionAchievement,
        ]);
    }
}
