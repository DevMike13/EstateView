<?php

namespace App\Observers;

use App\Mail\AppointmentApprovedMail;
use App\Mail\AppointmentCompletedMail;
use App\Mail\AppointmentDeclinedMail;
use App\Models\ClientAppointment;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class AppointmentObserver
{
    /**
     * Handle the ClientAppointment "created" event.
     */
    public function created(ClientAppointment $appointment): void
    {
        $this->notify(
            $appointment,
            'New Appointment Request',
            $this->buildMessage($appointment),
            'appointment_created',
            false
        );
    }

    /**
     * Handle the ClientAppointment "updated" event.
     */
    public function updated(ClientAppointment $appointment): void
    {
        if ($appointment->wasChanged('status')) {

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

            // Don't send the in-app notification back to the client
            // when the client cancelled their own appointment.
            $includeClient = !(
                $appointment->status === 'cancelled'
                && auth()->check()
                && auth()->user()->role === 'user'
            );

            $this->notify(
                $appointment,
                'Appointment Status Updated',
                $this->buildMessage($appointment)
                    . " Updated by: {$performedBy}.",
                'appointment_updated',
                $includeClient
);

            match ($appointment->status) {
                'approved' => Mail::to($appointment->user->email)
                    ->send(new AppointmentApprovedMail($appointment, $performedBy)),

                'completed' => Mail::to($appointment->user->email)
                    ->send(new AppointmentCompletedMail($appointment, $performedBy)),

                'declined' => Mail::to($appointment->user->email)
                    ->send(new AppointmentDeclinedMail($appointment, $performedBy)),

                default => null,
            };
        }
    }

    /**
     * Handle the ClientAppointment "deleted" event.
     */
    public function deleted(ClientAppointment $appointment): void
    {
        $this->notify(
            $appointment,
            'Appointment Deleted',
            $this->buildMessage($appointment),
            'appointment_deleted'
        );
    }

    /**
     * Handle the ClientAppointment "restored" event.
     */
    public function restored(ClientAppointment $appointment): void
    {
        $this->notify(
            $appointment,
            'Appointment Restored',
            $this->buildMessage($appointment),
            'appointment_restored'
        );
    }

    /**
     * Handle the ClientAppointment "force deleted" event.
     */
    public function forceDeleted(ClientAppointment $appointment): void
    {
        $this->notify(
            $appointment,
            'Appointment Permanently Deleted',
            $this->buildMessage($appointment),
            'appointment_force_deleted'
        );
    }

    private function buildMessage(ClientAppointment $appointment): string
    {
        $appointment->loadMissing(['user']);

        $client = $appointment->name ?? $appointment->user?->name ?? 'Unknown Client';
        $date = $appointment->appointment_date;
        $time = $appointment->appointment_time;
        $type = $appointment->appointment_type;
        $status = $appointment->status;

        return "Client {$client} scheduled a {$type} appointment on {$date} at {$time}. Status: {$status}.";
    }

    /**
     * Save notification
     */
    private function notify(
    ClientAppointment $appointment,
        string $title,
        string $message,
        string $type,
        bool $includeClient = true
    ): void {

        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,

            // 🔥 Deep link (adjust route if needed)
            'url' => route('filament.ev-admin.pages.appointments', [
                'activeTab' => $appointment->status,
                'highlight' => $appointment->id,
            ]),

            'data' => [
                'client_name' => $appointment->name ?? $appointment->user?->name,
                'phone' => $appointment->phone,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'appointment_type' => $appointment->appointment_type,
                'status' => $appointment->status,
                'appointment_id' => $appointment->id,

                'client_url' => route('client.appointment', [
                    'activeTab' => $appointment->status,
                    'highlight' => $appointment->id,
                ]),
            ],

            'created_by' => auth()->id(),
        ]);

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

        if ($includeClient && $appointment->user_id) {
            $users = $users->merge([
                $appointment->user_id,
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
