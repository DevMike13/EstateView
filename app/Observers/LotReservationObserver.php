<?php

namespace App\Observers;

use App\Mail\ReservationApprovedMail;
use App\Mail\ReservationAwaitingFeeMail;
use App\Mail\ReservationRejectedMail;
use App\Models\LotReservation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class LotReservationObserver
{
    /**
     * Handle the LotReservation "created" event.
     */
    public function created(LotReservation $lotReservation): void
    {
        $this->notify(
            $lotReservation,
            'New Lot Reservation',
            $this->buildMessage($lotReservation),
            'lot_reservation_created',
            false
        );
    }

    /**
     * Handle the LotReservation "updated" event.
     */
    public function updated(LotReservation $lotReservation): void
    {
        if ($lotReservation->wasChanged('status')) {

            $performedBy = 'System';

            if (auth()->check()) {
                $performedBy = match (auth()->user()->role) {
                    'staff' => auth()->user()->name,
                    'user'  => auth()->user()->name,
                    'agent' => auth()->user()->name,
                    'admin' => 'Admin',
                    default => auth()->user()->name ?? 'System',
                };
            }

            $isLotUnavailable =
                $lotReservation->status === 'rejected'
                && $lotReservation->notes === 'Lot allocated to another reservation.';

            $title = match ($lotReservation->status) {
                'awaiting_reservation_fee' => 'Reservation Submitted Documents Approved',
                'reservation_fee_submitted' => 'Reservation Fee Submitted',
                'approved' => 'Reservation Approved',
                'rejected' => $isLotUnavailable
                    ? 'Reserved Lot No Longer Available'
                    : 'Reservation Rejected',
                'cancelled' => 'Reservation Cancelled',
                default => 'Reservation Status Updated',
            };

            $includeClient = ! in_array(
                $lotReservation->status,
                [
                    'reservation_fee_submitted',
                    'cancelled',
                ],
                true
            );

            if ($isLotUnavailable) {

                $lotReservation->loadMissing([
                    'user',
                    'lot',
                ]);

                $message =
                    "The reservation of "
                    . ($lotReservation->user?->name ?? 'the client')
                    . " for "
                    . ($lotReservation->lot?->name ?? 'the selected lot')
                    . " can no longer proceed because the property has been allocated to another reservation. "
                    . "Updated by: {$performedBy}.";

            } elseif ($lotReservation->status === 'cancelled') {

                $lotReservation->loadMissing([
                    'user',
                    'lot',
                ]);

                $message =
                    "Client {$lotReservation->user?->name} cancelled their reservation for Lot "
                    . ($lotReservation->lot?->name ?? 'Unknown Lot')
                    . ".";

            } else {

                $message =
                    $this->buildMessage($lotReservation)
                    . " Updated by: {$performedBy}.";
            }

            $this->notify(
                $lotReservation,
                $title,
                $message,
                'lot_reservation_updated',
                $includeClient
            );

            $lotReservation->load('user', 'lot', 'houseModel');

            if ($lotReservation->status === 'awaiting_reservation_fee') {
                Mail::to($lotReservation->user->email)
                    ->send(new ReservationAwaitingFeeMail($lotReservation, $performedBy));
            }

            if ($lotReservation->status === 'approved') {
                Mail::to($lotReservation->user->email)
                    ->send(new ReservationApprovedMail($lotReservation, $performedBy));
            }

            if ($lotReservation->status === 'rejected') {
                Mail::to($lotReservation->user->email)
                    ->send(new ReservationRejectedMail($lotReservation, $performedBy));
            }
        }
    }

    /**
     * Handle the LotReservation "deleted" event.
     */
    public function deleted(LotReservation $lotReservation): void
    {
        $this->notify(
            $lotReservation,
            'Lot Reservation Deleted',
            $this->buildMessage($lotReservation),
            'lot_reservation_deleted'
        );
    }

    // private function notify(
    //     LotReservation $lotReservation,
    //     string $title,
    //     string $message,
    //     string $type
    // ): void {

    //     $notification = Notification::create([
    //         'title' => $title,
    //         'message' => $message,
    //         'type' => $type,

    //         // 👇 Filament deep link (IMPORTANT)
    //         'url' => route('filament.ev-admin.pages.reservations', [
    //             'record' => $lotReservation->id
    //         ]),

    //         'created_by' => auth()->id(),
    //     ]);

    //     // attach model
    //     $notification->notifiable()->associate($lotReservation);
    //     $notification->save();

    //     // 👇 send to all users (you can change later to roles only)
    //     $users = User::all();

    //     $notification->users()->attach(
    //         $users->pluck('id')->toArray()
    //     );
    // }

    // private function notify(
    //     LotReservation $lotReservation,
    //     string $title,
    //     string $type
    // ): void {

    //     // Load relationships safely
    //     $lotReservation->loadMissing(['user', 'lot', 'houseModel']);

    //     $clientName = $lotReservation->user?->name ?? 'Unknown Client';
    //     $lotName = $lotReservation->lot?->title ?? 'No Lot';
    //     $houseModel = $lotReservation->houseModel?->name ?? 'No House Model';

    //     // Build detailed message
    //     $message = "Client {$clientName} made a reservation";

    //     if ($lotReservation->houseModel) {
    //         $message .= " using House Model: {$houseModel}";
    //     }

    //     $message .= ". Status: {$lotReservation->status}.";

    //     $notification = Notification::create([
    //         'title' => $title,
    //         'message' => $message,
    //         'type' => $type,

    //         'url' => route('filament.ev-admin.pages.reservations', [
    //             'activeTab' => $lotReservation->status,
    //             'highlight' => $lotReservation->id,
    //         ]),

    //         'data' => [
    //             'client_name' => $lotReservation->user?->name,
    //             'client_email' => $lotReservation->user?->email,
    //             'lot_name' => $lotReservation->lot?->name,
    //             'house_model' => $lotReservation->houseModel?->name,
    //             'status' => $lotReservation->status,
    //             'reservation_id' => $lotReservation->id,
    //         ],


    //         'created_by' => auth()->id(),
    //     ]);

    //     $notification->notifiable()->associate($lotReservation);
    //     $notification->save();

    //     $users = \App\Models\User::all();

    //     $notification->users()->attach(
    //         $users->pluck('id')->toArray()
    //     );
    // }

    /**
     * Handle the LotReservation "restored" event.
     */
    public function restored(LotReservation $lotReservation): void
    {
        $this->notify(
            $lotReservation,
            'Reservation Restored',
            $this->buildMessage($lotReservation),
            'lot_reservation_restored'
        );
    }

    /**
     * Handle the LotReservation "force deleted" event.
     */
    public function forceDeleted(LotReservation $lotReservation): void
    {
        $this->notify(
            $lotReservation,
            'Reservation Permanently Deleted',
            $this->buildMessage($lotReservation),
            'lot_reservation_force_deleted'
        );
    }

    private function buildMessage(LotReservation $lotReservation): string
    {
        $lotReservation->loadMissing([
            'user',
            'agent',
            'lot',
            'houseModel',
        ]);

        $client = $lotReservation->user?->name ?? 'Unknown Client';
        $lot = $lotReservation->lot?->name ?? 'No Lot';
        $house = $lotReservation->houseModel?->name ?? 'No House Model';
        $status = $lotReservation->status;

        return "Client {$client} reserved Lot {$lot}"
            . ($house ? " using {$house}" : "")
            . ". Status: {$status}.";
    }

    private function notify(
        LotReservation $lotReservation,
        string $title,
        string $message,
        string $type,
        bool $includeClient = true
    ): void {

        $lotReservation->loadMissing([
            'user',
            'agent',
            'lot',
            'houseModel',
        ]);

        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,

            'url' => route('filament.ev-admin.pages.reservations', [
                'activeTab' => $lotReservation->status,
                'highlight' => $lotReservation->id,
            ]),

            'data' => [
                'client_name' => $lotReservation->user?->name,
                'client_email' => $lotReservation->user?->email,
                'agent_id' => $lotReservation->agent_id,
                'agent_name' => $lotReservation->agent?->name,
                'lot_name' => $lotReservation->lot?->name,
                'house_model' => $lotReservation->houseModel?->name,
                'status' => $lotReservation->status,
                'reservation_id' => $lotReservation->id,

                'client_url' => route('client.reservation', [
                    'activeTab' => $lotReservation->status,
                    'highlight' => $lotReservation->id,
                ]),
            ],

            'created_by' => auth()->id(),
        ]);

        $staffAdmins = User::whereIn('role', ['admin', 'staff'])
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

        if ($includeClient && $lotReservation->user_id) {
            $users = $users->merge([
                $lotReservation->user_id,
            ]);
        }

        $notification->users()->attach(
            $users
                ->filter()
                ->unique()
                ->toArray()
        );
    }
}
