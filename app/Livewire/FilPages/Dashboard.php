<?php

namespace App\Livewire\FilPages;

use App\Models\Billing;
use App\Models\BillingPayment;
use App\Models\ClientAppointment;
use App\Models\Lot;
use App\Models\LotReservation;
use App\Models\Notification;
use App\Models\PurchaseAccount;
use App\Models\User;
use App\Models\PaymentQrCode;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.fil-pages.dashboard', [
            'totalClients' => User::where('role', 'user')->count(),
            'availableLots' => Lot::where('status', 'available')->count(),
            'reservedLots' => Lot::where('status', 'reserved')->count(),
            'soldLots' => Lot::where('status', 'sold')->count(),
            'totalAgents' => User::where('role', 'agent')->count(),

            'delayedPayments' => Billing::whereIn('status', ['unpaid', 'partial'])
                ->whereDate('due_date', '<', now())
                ->count(),

            'advancedPayments' => BillingPayment::where('status', 'verified')
                ->whereHas('billing', function ($query) {
                    $query->whereColumn('billing_payments.paid_at', '<', 'billings.due_date');
                })
                ->count(),

            'pendingReservations' => LotReservation::whereIn('status', [
                'pending',
                'awaiting_reservation_fee',
                'reservation_fee_submitted',
            ])->count(),

            'pendingAppointments' => ClientAppointment::where('status', 'pending')->count(),

            'paymentVerifications' => BillingPayment::where('status', 'pending')->count(),

            'recentActivities' => Notification::latest()
                ->limit(5)
                ->get(),

            'totalTransactions' => BillingPayment::where('status', 'verified')->count(),

            'totalSales' => PurchaseAccount::sum('total_paid'),

            'paymentQrCodes' => PaymentQrCode::count(),

            'activePaymentQrCodes' => PaymentQrCode::where('is_active', true)->count(),
            
            'activeAccounts' => PurchaseAccount::whereIn('status', [
                'downpayment_pending',
                'bank_processing',
                'active',
            ])->count(),
        ]);
    }
}
