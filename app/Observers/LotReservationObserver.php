<?php

namespace App\Observers;

use App\Mail\ReservationApprovedMail;
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
            'lot_reservation_created'
        );
    }

    /**
     * Handle the LotReservation "updated" event.
     */
    public function updated(LotReservation $lotReservation): void
    {
        if ($lotReservation->wasChanged('status')) {
            $this->notify(
                $lotReservation,
                'Reservation Status Updated',
                $this->buildMessage($lotReservation),
                'lot_reservation_updated'
            );

            $lotReservation->load('user', 'lot', 'houseModel');

            if ($lotReservation->status === 'approved') {
                Mail::to($lotReservation->user->email)
                    ->send(new ReservationApprovedMail($lotReservation));
            }

            if ($lotReservation->status === 'rejected') {
                Mail::to($lotReservation->user->email)
                    ->send(new ReservationRejectedMail($lotReservation));
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
        $lotReservation->loadMissing(['user', 'lot', 'houseModel']);

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
        string $type
    ): void {

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

        $staffAdmins = \App\Models\User::whereIn('role', ['admin', 'staff'])->pluck('id');
        $client = $lotReservation->user_id;

        $notification->users()->attach(
            $staffAdmins->merge([$client])->unique()->toArray()
        );
    }
}
