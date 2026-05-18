<?php

namespace App\Livewire\FilPages;

use App\Models\LotReservation;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use WireUi\Traits\Actions;

class Reservations extends Component
{
    use Actions;

    public $activeTab = 'pending';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getReservationsProperty()
    {
        return LotReservation::with([
                'lot',
                'preferredPayment',
                'requiredDocuments',
                'houseModel',
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

    public function confirmApprove($reservationId)
    {
        $this->dialog()->confirm([
            'title' => 'Approve Reservation?',
            'description' => 'This will reserve the selected lot for the client.',
            'acceptLabel' => 'Yes, approve',
            'method' => 'approveReservation',
            'params' => $reservationId,
            'icon' => 'success',
        ]);
    }

    public function approveReservation($reservationId)
    {   
        DB::transaction(function () use ($reservationId) {

            $reservation = LotReservation::with([
                'lot',
                'houseModel',
            ])->findOrFail($reservationId);

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
            ->title('Reservation Approved')
            ->body("The reservation has been approved successfully.")
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

    public function render()
    {
        return view('livewire.fil-pages.reservations');
    }
}
