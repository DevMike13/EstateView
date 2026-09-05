<?php

namespace App\Livewire\FilPages;

use App\Mail\ReservationDocumentsRejectedMail;
use App\Models\LotReservation;
use App\Models\Lot;
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
            'rejected',
            'cancelled',
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

    public function getCancelledCountProperty()
    {
        return LotReservation::where(
            'status',
            'cancelled'
        )->count();
    }

    // public function confirmCancel($reservationId)
    // {
    //     $this->dialog()->confirm([
    //         'title' => 'Cancel Reservation?',
    //         'description' => 'This reservation will be marked as cancelled.',
    //         'acceptLabel' => 'Yes, cancel',
    //         'rejectLabel' => 'No',
    //         'method' => 'cancelReservation',
    //         'params' => $reservationId,
    //         'icon' => 'warning',
    //     ]);
    // }

    // public function cancelReservation($reservationId)
    // {
    //     $reservation = LotReservation::findOrFail(
    //         $reservationId
    //     );

    //     if (! in_array(
    //         $reservation->status,
    //         [
    //             'pending',
    //             'awaiting_reservation_fee',
    //             'reservation_fee_submitted',
    //         ],
    //         true
    //     )) {
    //         Notification::make()
    //             ->title('Cannot Cancel Reservation')
    //             ->body(
    //                 'This reservation can no longer be cancelled.'
    //             )
    //             ->danger()
    //             ->send();

    //         return;
    //     }

    //     $reservation->update([
    //         'status' => 'cancelled',
    //     ]);

    //     Notification::make()
    //         ->title('Reservation Cancelled')
    //         ->body(
    //             'The reservation has been cancelled successfully.'
    //         )
    //         ->warning()
    //         ->send();

    //     $this->activeTab = 'cancelled';

    //     $this->dispatch('reload');

    //     return redirect()->back();
    // }

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
        $result = DB::transaction(function () use ($reservationId) {

            $reservation = LotReservation::findOrFail($reservationId);

            /*
            |--------------------------------------------------------------------------
            | Lock the lot while choosing the winning reservation
            |--------------------------------------------------------------------------
            |
            | This prevents two admins/staff members from approving two
            | reservations for the same lot at the same time.
            |
            */

            Lot::whereKey($reservation->lot_id)
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Make sure this reservation can still be processed
            |--------------------------------------------------------------------------
            */

            $reservation->refresh();

            if ($reservation->status !== 'pending') {
                return [
                    'status' => 'invalid_status',
                    'rejected_count' => 0,
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Check if another reservation already won this lot
            |--------------------------------------------------------------------------
            |
            | Once another reservation has reached Awaiting Reservation Fee,
            | Fee Submitted, or Approved, this reservation must not continue.
            |
            */

            $winningReservation = LotReservation::query()
                ->where('lot_id', $reservation->lot_id)
                ->where('id', '!=', $reservation->id)
                ->whereIn('status', [
                    'awaiting_reservation_fee',
                    'reservation_fee_submitted',
                    'approved',
                ])
                ->first();

            if ($winningReservation) {

                /*
                |--------------------------------------------------------------------------
                | Do not allow this reservation to push through
                |--------------------------------------------------------------------------
                |
                | Using a normal update intentionally triggers
                | LotReservationObserver so the client receives the existing
                | rejected email and in-app notification.
                |
                */

                $reservation->update([
                    'status' => 'rejected',
                    'notes' => 'Lot allocated to another reservation.',
                ]);

                return [
                    'status' => 'lot_unavailable',
                    'rejected_count' => 0,
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Approve the winning reservation
            |--------------------------------------------------------------------------
            |
            | The observer will notify the winning client that the submitted
            | documents were approved and the reservation fee is now required.
            |
            */

            $reservation->update([
                'status' => 'awaiting_reservation_fee',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Reject the other pending reservations for the same lot
            |--------------------------------------------------------------------------
            |
            | Only pending reservations are automatically rejected here.
            | Because we already checked for progressed reservations above,
            | nobody who has already been asked to pay or submitted a fee is
            | silently displaced.
            |
            | Normal update() is used so LotReservationObserver sends each
            | non-winning client the existing rejected email and notification.
            |
            */

            $otherPendingReservations = LotReservation::query()
                ->where('lot_id', $reservation->lot_id)
                ->where('id', '!=', $reservation->id)
                ->where('status', 'pending')
                ->get();

            foreach ($otherPendingReservations as $otherReservation) {
                $otherReservation->update([
                    'status' => 'rejected',
                    'notes' => 'Lot allocated to another reservation.',
                ]);
            }

            return [
                'status' => 'approved',
                'rejected_count' => $otherPendingReservations->count(),
            ];
        });


        if ($result['status'] === 'lot_unavailable') {
            Notification::make()
                ->title('Reservation Cannot Proceed')
                ->body(
                    'Another reservation has already progressed for this lot. '
                    . 'This reservation has been moved to Rejected.'
                )
                ->danger()
                ->send();

            $this->dispatch('reload');

            return redirect()->back();
        }


        if ($result['status'] === 'invalid_status') {
            Notification::make()
                ->title('Reservation Cannot Be Approved')
                ->body(
                    'This reservation is no longer pending and cannot be approved.'
                )
                ->warning()
                ->send();

            $this->dispatch('reload');

            return redirect()->back();
        }


        Notification::make()
            ->title('Requirements Approved')
            ->body(
                $result['rejected_count'] > 0
                    ? 'Reservation fee payment is now required. '
                        . $result['rejected_count']
                        . ' competing reservation'
                        . ($result['rejected_count'] > 1 ? 's were' : ' was')
                        . ' rejected because this lot has now been allocated.'
                    : 'Reservation fee payment is now required.'
            )
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
        $result = DB::transaction(function () use ($paymentId) {

            $payment = ReservationPayment::with([
                'reservation.lot',
            ])->findOrFail($paymentId);

            $reservation = $payment->reservation;


            /*
            |--------------------------------------------------------------------------
            | Lock the lot before verifying the winning reservation fee
            |--------------------------------------------------------------------------
            |
            | This is a final database-level safeguard against two reservation
            | fees being approved for the same lot at the same time.
            |
            */

            $lot = Lot::whereKey($reservation->lot_id)
                ->lockForUpdate()
                ->firstOrFail();

            $reservation->refresh();


            /*
            |--------------------------------------------------------------------------
            | The reservation must still be waiting for fee verification
            |--------------------------------------------------------------------------
            */

            if ($reservation->status !== 'reservation_fee_submitted') {
                return [
                    'status' => 'invalid_status',
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Final same-lot conflict check
            |--------------------------------------------------------------------------
            |
            | If another reservation somehow became approved for this lot,
            | do NOT automatically reject this one because this client has
            | already submitted a reservation fee. Admin/staff must resolve it.
            |
            */

            $anotherApprovedReservation = LotReservation::query()
                ->where('lot_id', $reservation->lot_id)
                ->where('id', '!=', $reservation->id)
                ->where('status', 'approved')
                ->exists();

            if ($anotherApprovedReservation) {
                return [
                    'status' => 'conflict',
                ];
            }


            $payment->update([
                'status' => 'verified',
            ]);


            $lot->update([
                'status' => 'reserved',
                'user_id' => $reservation->user_id,
                'house_model_id' => $reservation->house_model_id,
            ]);


            $reservation->update([
                'status' => 'approved',
            ]);


            return [
                'status' => 'approved',
            ];
        });


        if ($result['status'] === 'conflict') {
            Notification::make()
                ->title('Reservation Conflict')
                ->body(
                    'Another reservation is already approved for this lot. '
                    . 'This payment was not verified. Please resolve the conflict first.'
                )
                ->danger()
                ->send();

            $this->dispatch('reload');

            return redirect()->back();
        }


        if ($result['status'] === 'invalid_status') {
            Notification::make()
                ->title('Reservation Fee Cannot Be Verified')
                ->body(
                    'This reservation is no longer waiting for fee verification.'
                )
                ->warning()
                ->send();

            $this->dispatch('reload');

            return redirect()->back();
        }


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
                    "Client "
                    . ($reservation->user?->name ?? 'Unknown Client')
                    . ". Reservation fee for "
                    . ($reservation->lot?->name ?? 'the reservation')
                    . " was rejected. Updated by: {$performedBy}.",

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
