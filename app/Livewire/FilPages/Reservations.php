<?php

namespace App\Livewire\FilPages;

use App\Models\LotReservation;
use App\Models\ReservationPayment;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use WireUi\Traits\Actions;

class Reservations extends Component
{
    use Actions;

    #[Url]
    public $activeTab = 'pending';

    #[Url]
    public $highlight = null;

    protected $queryString = [
        'activeTab',
        'highlight',
    ];


    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->highlight = null;
    }

    public function mount()
    {
        // ensure tab is valid
        if (!in_array($this->activeTab, [
            'pending',
            'awaiting_reservation_fee',
            'reservation_fee_submitted',
            'approved',
            'rejected'
        ])) {
            $this->activeTab = 'pending';
        }
    }

    public function getReservationsProperty()
    {
        return LotReservation::with([
                'user.info',
                'lot',
                'preferredPayment',
                'requiredDocuments',
                'houseModel',
                'latestReservationPayment',
            ])
            ->where('status', $this->activeTab)
            ->latest()
            ->get();
    }

    public function getPendingCountProperty()
    {
        return LotReservation::where('status', 'pending')
            ->count();
    }

    public function getApprovedCountProperty()
    {
        return LotReservation::where('status', 'approved')
            ->count();
    }

    public function getRejectedCountProperty()
    {
        return LotReservation::where('status', 'rejected')
            ->count();
    }

    public function getAwaitingReservationFeeCountProperty()
    {
        return LotReservation::where(
            'status',
            'awaiting_reservation_fee'
        )->count();
    }

    public function getReservationFeeSubmittedCountProperty()
    {
        return LotReservation::where(
            'status',
            'reservation_fee_submitted'
        )->count();
    }

    public function confirmApprove($reservationId)
    {
        $this->dialog()->confirm([
            'title' => 'Approve Requirements?',
            'description' => 'This will notify the client to pay the reservation fee.',
            'acceptLabel' => 'Yes, approve requirements',
            'method' => 'approveReservation',
            'params' => $reservationId,
            'icon' => 'success',
        ]);
    }

    public function approveReservation($reservationId)
    {
        DB::transaction(function () use ($reservationId) {

            $reservation = LotReservation::findOrFail($reservationId);

            $reservation->update([
                'status' => 'awaiting_reservation_fee',
            ]);
        });

        Notification::make()
            ->title('Requirements Approved')
            ->body('Reservation fee payment is now required.')
            ->success()
            ->send();

        $this->dispatch('reload');

        return redirect()->back();
    }

    public function confirmReject($reservationId)
    {
        $this->dialog()->confirm([
            'title' => 'Reject Reservation?',
            'description' => 'This reservation will be marked as rejected.',
            'acceptLabel' => 'Yes, reject',
            'method' => 'rejectReservation',
            'params' => $reservationId,
            'icon' => 'error',
        ]);
    }

    public function rejectReservation($reservationId)
    {
        $reservation = LotReservation::findOrFail($reservationId);

        $reservation->update([
            'status' => 'rejected',
        ]);

        Notification::make()
            ->title('Reservation Rejected')
            ->body("The reservation has been rejected successfully.")
            ->danger()
            ->send();

        $this->dispatch('reload');
        return redirect()->back();
    }

    public function confirmApproveReservationFee($paymentId)
    {
        $this->dialog()->confirm([
            'title' => 'Verify Reservation Fee?',
            'description' => 'This will approve the reservation fee and officially reserve the lot.',
            'acceptLabel' => 'Yes, verify payment',
            'method' => 'approveReservationFee',
            'params' => $paymentId,
            'icon' => 'success',
        ]);
    }

    public function approveReservationFee($paymentId)
    {
        DB::transaction(function () use ($paymentId) {

            $payment = ReservationPayment::with([
                'reservation.lot',
            ])->findOrFail($paymentId);

            $payment->update([
                'status' => 'verified',
            ]);

            $reservation = $payment->reservation;

            $reservation->lot->update([
                'status' => 'reserved',
                'user_id' => $reservation->user_id,
                'house_model_id' => $reservation->house_model_id,
            ]);

            $reservation->update([
                'status' => 'approved',
            ]);
        });

        Notification::make()
            ->title('Reservation Fee Verified')
            ->body('The lot is now officially reserved for the client.')
            ->success()
            ->send();

        $this->dispatch('reload');

        return redirect()->back();
    }

    public function confirmRejectReservationFee($paymentId)
    {
        $this->dialog()->confirm([
            'title' => 'Reject Reservation Fee?',
            'description' => 'This will reject the uploaded payment proof.',
            'acceptLabel' => 'Yes, reject payment',
            'method' => 'rejectReservationFee',
            'params' => $paymentId,
            'icon' => 'error',
        ]);
    }

    public function rejectReservationFee($paymentId)
    {
        DB::transaction(function () use ($paymentId) {

            $payment = ReservationPayment::with('reservation')
                ->findOrFail($paymentId);

            $payment->update([
                'status' => 'rejected',
            ]);

            $payment->reservation->update([
                'status' => 'awaiting_reservation_fee',
            ]);
        });

        Notification::make()
            ->title('Reservation Fee Rejected')
            ->body('The client needs to upload a valid payment proof again.')
            ->danger()
            ->send();

        $this->dispatch('reload');

        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.fil-pages.reservations');
    }
}
