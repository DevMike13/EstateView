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
            'appointment_created'
        );
    }

    /**
     * Handle the ClientAppointment "updated" event.
     */
    public function updated(ClientAppointment $appointment): void
    {
        if ($appointment->wasChanged('status')) {
            $this->notify(
                $appointment,
                'Appointment Status Updated',
                $this->buildMessage($appointment),
                'appointment_updated'
            );

            match ($appointment->status) {
                'approved' => Mail::to($appointment->user->email)
                    ->send(new AppointmentApprovedMail($appointment)),

                'completed' => Mail::to($appointment->user->email)
                    ->send(new AppointmentCompletedMail($appointment)),

                'declined' => Mail::to($appointment->user->email)
                    ->send(new AppointmentDeclinedMail($appointment)),

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
        string $type
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
            ],

            'created_by' => auth()->id(),
        ]);

        // Send to all users (same as reservations)
        $notification->users()->attach(
            \App\Models\User::pluck('id')->toArray()
        );
    }
}
