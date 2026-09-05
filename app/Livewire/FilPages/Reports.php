<?php

namespace App\Livewire\FilPages;

use App\Models\Billing;
use App\Models\BillingPayment;
use App\Models\ClientAppointment;
use App\Models\CommissionRequest; // CHANGED — was App\Models\Commission
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

        $delayedPaymentEarnings = Billing::where('status', 'paid')
            ->where('penalty_amount', '>', 0)
            ->whereYear('updated_at', now()->year)
            ->sum('penalty_amount');

        $advancePaymentRebates = Billing::where('status', 'paid')
            ->where('discount_amount', '>', 0)
            ->whereYear('updated_at', now()->year)
            ->sum('discount_amount');

        $agentPaymentsDisbursed = CommissionRequest::where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereYear('paid_at', now()->year)
            ->sum('requested_amount');

        $totalAppointments = ClientAppointment::whereYear('created_at', now()->year)
            ->count();

        $cancelledLedgers = PurchaseAccount::where('status', 'cancelled')
            ->whereYear('updated_at', now()->year)
            ->count();

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

        $monthlyActivity = collect(range(5, 0))
            ->map(function ($monthsAgo) {
                $date = now()->subMonths($monthsAgo);

                return [
                    'month' => $date->format('M'),
                    'reservations' => LotReservation::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count(),
                    'appointments' => ClientAppointment::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count(),
                    'cancelled_ledgers' => PurchaseAccount::where('status', 'cancelled')
                        ->whereYear('updated_at', $date->year)
                        ->whereMonth('updated_at', $date->month)
                        ->count(),
                ];
            });

        // ADDED — monthly delayed vs advanced payment counts (last 6 months)
        $monthlyDelayedAdvance = collect(range(5, 0))
            ->map(function ($monthsAgo) {
                $date = now()->subMonths($monthsAgo);

                $delayedCount = Billing::whereIn('status', ['unpaid', 'partial'])
                    ->whereDate('due_date', '<', now())
                    ->whereYear('due_date', $date->year)
                    ->whereMonth('due_date', $date->month)
                    ->count();

                $advancedCount = BillingPayment::where('status', 'verified')
                    ->whereNotNull('paid_at')
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->whereHas('billing', function ($query) {
                        $query->whereColumn('billing_payments.paid_at', '<', 'billings.due_date');
                    })
                    ->count();

                return [
                    'month' => $date->format('M'),
                    'delayed' => $delayedCount,
                    'advanced' => $advancedCount,
                ];
            });

        // CHANGED — monthly agent commissions using CommissionRequest (paid_at + status 'paid')
        $monthlyCommissions = collect(range(5, 0))
            ->map(function ($monthsAgo) {
                $date = now()->subMonths($monthsAgo);

                $amount = CommissionRequest::where('status', 'paid')
                    ->whereNotNull('paid_at')
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->sum('requested_amount');

                return [
                    'month' => $date->format('M'),
                    'amount' => $amount,
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

            'delayedPaymentEarnings' => $delayedPaymentEarnings,
            'advancePaymentRebates' => $advancePaymentRebates,
            'agentPaymentsDisbursed' => $agentPaymentsDisbursed,
            'totalAppointments' => $totalAppointments,
            'cancelledLedgers' => $cancelledLedgers,

            'delayedPayments' => $delayedPayments,
            'advancedPayments' => $advancedPayments,
            'monthlyCollections' => $monthlyCollections,
            'monthlyActivity' => $monthlyActivity,
            'monthlyDelayedAdvance' => $monthlyDelayedAdvance,
            'monthlyCommissions' => $monthlyCommissions,

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
