<?php

namespace App\Livewire\FilPages;

use App\Mail\ReservationDocumentsRejectedMail;
use App\Models\LotReservation;
use App\Models\ReservationPayment;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use WireUi\Traits\Actions;
use App\Mail\ReservationFeeRejectedMail;
use Illuminate\Support\Facades\Mail;

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

            $payment = ReservationPayment::with([
                'reservation.user',
                'reservation.lot',
                'reservation.houseModel',
                'reservation.preferredPayment',
            ])->findOrFail($paymentId);

            $payment->update([
                'status' => 'rejected',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Keep reservation waiting for another fee submission
            |--------------------------------------------------------------------------
            |
            | updateQuietly prevents LotReservationObserver from sending the
            | "Documents Approved" email again.
            |
            */

            $payment->reservation->updateQuietly([
                'status' => 'awaiting_reservation_fee',
            ]);

            $reservation = $payment->reservation;

            $performedBy = auth()->check()
                ? (
                    auth()->user()->role === 'staff'
                        ? auth()->user()->name
                        : 'Admin'
                )
                : 'System';


            /*
            |--------------------------------------------------------------------------
            | EMAIL
            |--------------------------------------------------------------------------
            */

            Mail::to($reservation->user->email)
                ->send(
                    new ReservationDocumentsRejectedMail(
                        $reservation,
                        $performedBy
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | CLIENT IN-APP NOTIFICATION
            |--------------------------------------------------------------------------
            */

            $notification = \App\Models\Notification::create([
                'title' => 'Reservation Fee Rejected',

                'message' =>
                    "Your submitted reservation fee for "
                    . ($reservation->lot?->name ?? 'your reservation')
                    . " was rejected. Updated by: {$performedBy}. ",

                'type' => 'reservation_fee_rejected',

                /*
                * Admin/staff destination if they happen to access
                * this notification record.
                */
                'url' => route(
                    'filament.ev-admin.pages.reservations',
                    [
                        'activeTab' => 'awaiting_reservation_fee',
                        'highlight' => $reservation->id,
                    ]
                ),

                'data' => [
                    'reservation_id' => $reservation->id,

                    'reservation_payment_id' => $payment->id,

                    'client_name' => $reservation->user?->name,

                    'lot_name' => $reservation->lot?->name,

                    'status' => 'awaiting_reservation_fee',

                    'performed_by' => $performedBy,

                    /*
                    * Client opens their reservation directly.
                    */
                    'client_url' => route(
                        'client.reservation',
                        [
                            'activeTab' => 'awaiting_reservation_fee',
                            'highlight' => $reservation->id,
                        ]
                    ),
                ],

                'created_by' => auth()->id(),
            ]);


            /*
            * Send this notification ONLY to the client or admin/staff who did not perform the action.
            */
            $staffAdmins = \App\Models\User::whereIn(
                'role',
                ['admin', 'staff']
            )
            ->when(
                auth()->check(),
                fn ($query) =>
                    $query->where(
                        'id',
                        '!=',
                        auth()->id()
                    )
            )
            ->pluck('id');

            $users = $staffAdmins;

            if ($reservation->user_id) {
                $users = $users->merge([
                    $reservation->user_id,
                ]);
            }

            $notification
                ->users()
                ->attach(
                    $users
                        ->filter()
                        ->unique()
                        ->toArray()
                );
        });


        /*
        |--------------------------------------------------------------------------
        | ADMIN / STAFF TOAST
        |--------------------------------------------------------------------------
        */

        Notification::make()
            ->title('Reservation Fee Rejected')
            ->body(
                'The submitted reservation fee has been rejected.'
            )
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
