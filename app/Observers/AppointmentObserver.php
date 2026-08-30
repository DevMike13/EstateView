<?php

namespace App\Observers;

use App\Mail\AppointmentApprovedMail;
use App\Mail\AppointmentCompletedMail;
use App\Mail\AppointmentDeclinedMail;
use App\Models\ClientAppointment;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class AppointmentObserver
{
    /**
     * Handle the ClientAppointment "created" event.
     */
    public function created(ClientAppointment $appointment): void
    {
        $isStaffCreated = in_array(
            $appointment->created_by_role,
            ['admin', 'staff'],
            true
        );

        $title = $isStaffCreated
            ? 'New Appointment Scheduled'
            : 'New Appointment Request';

        $type = $isStaffCreated
            ? 'appointment_scheduled'
            : 'appointment_created';

        $this->notify(
            $appointment,
            $title,
            $this->buildMessage($appointment),
            $type,
            true
        );
    }

    /**
     * Handle the ClientAppointment "updated" event.
     */
    public function updated(ClientAppointment $appointment): void
    {
        if (! $appointment->wasChanged('status')) {
            return;
        }

        $appointment->loadMissing([
            'user',
            'creator',
        ]);

        $actor = auth()->user();

        $performedBy = 'System';

        if ($actor) {
            $performedBy = match ($actor->role) {
                'admin' => 'Admin',
                default => $actor->name ?? 'System',
            };
        }

        /*
        |--------------------------------------------------------------------------
        | CLIENT CONFIRMED ADMIN / STAFF APPOINTMENT
        |--------------------------------------------------------------------------
        */

        if (
            $appointment->status === 'approved'
            && $actor
            && $actor->role === 'user'
            && in_array(
                $appointment->created_by_role,
                ['admin', 'staff'],
                true
            )
        ) {
            $this->notify(
                $appointment,
                'Appointment Confirmed by Client',
                $this->buildClientConfirmationMessage(
                    $appointment
                ),
                'appointment_client_confirmed',
                false
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CLIENT DECLINED ADMIN / STAFF APPOINTMENT
        |--------------------------------------------------------------------------
        */

        if (
            $appointment->status === 'declined'
            && $actor
            && $actor->role === 'user'
            && in_array(
                $appointment->created_by_role,
                ['admin', 'staff'],
                true
            )
        ) {
            $this->notify(
                $appointment,
                'Appointment Declined by Client',
                $this->buildClientDeclineMessage(
                    $appointment
                ),
                'appointment_client_declined',
                false
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN / STAFF APPROVED APPOINTMENT
        |--------------------------------------------------------------------------
        */

        if (
            $appointment->status === 'approved'
            && $actor
            && in_array(
                $actor->role,
                ['admin', 'staff'],
                true
            )
        ) {
            $this->notify(
                $appointment,
                'Appointment Approved',
                $this->buildStaffApproveMessage(
                    $appointment,
                    $performedBy
                ),
                'appointment_approved',
                true
            );

            // Mail::to(
            //     $appointment->user->email
            // )->send(
            //     new AppointmentApprovedMail(
            //         $appointment,
            //         $performedBy
            //     )
            // );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN / STAFF DECLINED APPOINTMENT
        |--------------------------------------------------------------------------
        */

        if (
            $appointment->status === 'declined'
            && $actor
            && in_array(
                $actor->role,
                ['admin', 'staff'],
                true
            )
        ) {
            $this->notify(
                $appointment,
                'Appointment Declined',
                $this->buildStaffDeclineMessage(
                    $appointment,
                    $performedBy
                ),
                'appointment_declined',
                true
            );

            // Mail::to(
            //     $appointment->user->email
            // )->send(
            //     new AppointmentDeclinedMail(
            //         $appointment,
            //         $performedBy
            //     )
            // );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | APPOINTMENT COMPLETED
        |--------------------------------------------------------------------------
        */

        if (
            $appointment->status === 'completed'
            && $actor
            && in_array(
                $actor->role,
                ['admin', 'staff'],
                true
            )
        ) {
            $this->notify(
                $appointment,
                'Appointment Completed',
                $this->buildCompletedMessage(
                    $appointment,
                    $performedBy
                ),
                'appointment_completed',
                true
            );

            Mail::to(
                $appointment->user->email
            )->send(
                new AppointmentCompletedMail(
                    $appointment,
                    $performedBy
                )
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | NORMAL STATUS UPDATE
        |--------------------------------------------------------------------------
        */

        $includeClient = ! (
            $appointment->status === 'cancelled'
            && $actor
            && $actor->role === 'user'
        );

        $this->notify(
            $appointment,
            'Appointment Status Updated',
            $this->buildMessage($appointment)
                . " Updated by: {$performedBy}.",
            'appointment_updated',
            $includeClient
        );

        /*
        |--------------------------------------------------------------------------
        | EMAIL
        |--------------------------------------------------------------------------
        */

        match ($appointment->status) {
            'approved' =>
                Mail::to(
                    $appointment->user->email
                )->send(
                    new AppointmentApprovedMail(
                        $appointment,
                        $performedBy
                    )
                ),

            'completed' =>
                Mail::to(
                    $appointment->user->email
                )->send(
                    new AppointmentCompletedMail(
                        $appointment,
                        $performedBy
                    )
                ),

            'declined' =>
                Mail::to(
                    $appointment->user->email
                )->send(
                    new AppointmentDeclinedMail(
                        $appointment,
                        $performedBy
                    )
                ),

            default => null,
        };
    }

    private function buildStaffDeclineMessage(
        ClientAppointment $appointment,
        string $performedBy
    ): string {
        $appointment->loadMissing('user');

        $client =
            $appointment->name
            ?? $appointment->user?->name
            ?? 'Unknown Client';

        $date =
            Carbon::parse(
                $appointment->appointment_date
            )->format('M d, Y');

        $time =
            Carbon::parse(
                $appointment->appointment_time
            )->format('h:i A');

        $type =
            $appointment->appointment_type
            ?? 'appointment';

        return "The {$type} appointment for {$client} scheduled on {$date} at {$time} was declined by {$performedBy}.";
    }

    private function buildStaffApproveMessage(
        ClientAppointment $appointment,
        string $performedBy
    ): string {
        $appointment->loadMissing('user');

        $client = $appointment->name ?? $appointment->user?->name ?? 'Unknown Client';

        $date = Carbon::parse($appointment->appointment_date)->format('M d, Y');

        $time = Carbon::parse($appointment->appointment_time)->format('h:i A');

        $type = $appointment->appointment_type ?? 'appointment';

        return "The {$type} appointment for {$client} scheduled on {$date} at {$time} was approved by {$performedBy}.";
    }

    private function buildClientConfirmationMessage(
        ClientAppointment $appointment
    ): string {
        $client = $appointment->name ?? $appointment->user?->name ?? 'The client';

        $date = Carbon::parse($appointment->appointment_date)->format('M d, Y');

        $time = Carbon::parse($appointment->appointment_time)->format('h:i A');

        $type =$appointment->appointment_type?? 'appointment';

        return "{$client} confirmed the {$type} appointment scheduled for {$date} at {$time}.";
    }

    private function buildCompletedMessage(
        ClientAppointment $appointment,
        string $performedBy
    ): string {
        $appointment->loadMissing('user');

        $client = $appointment->name ?? $appointment->user?->name ?? 'Unknown Client';

        $date = Carbon::parse($appointment->appointment_date)->format('M d, Y');

        $time = Carbon::parse($appointment->appointment_time)->format('h:i A');

        $type = $appointment->appointment_type ?? 'appointment';

        return "The {$type} appointment for {$client} scheduled on {$date} at {$time} was marked as completed by {$performedBy}.";
    }

    private function buildClientDeclineMessage(
        ClientAppointment $appointment
    ): string {
        $client = $appointment->name ?? $appointment->user?->name ?? 'The client';

        $date = Carbon::parse($appointment->appointment_date)->format('M d, Y');

        $time = Carbon::parse($appointment->appointment_time)->format('h:i A');

        $type = $appointment->appointment_type ?? 'appointment';

        return "{$client} declined the {$type} appointment scheduled for {$date} at {$time}.";
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
        $appointment->loadMissing(['user', 'creator']);

        $client =
            $appointment->name
            ?? $appointment->user?->name
            ?? 'Unknown Client';

        $date = Carbon::parse(
            $appointment->appointment_date
        )->format('M d, Y');

        $time = Carbon::parse(
            $appointment->appointment_time
        )->format('h:i A');

        $type = $appointment->appointment_type;
        $status = $appointment->status;

        if (
            in_array(
                $appointment->created_by_role,
                ['admin', 'staff'],
                true
            )
        ) {
            $creator = $appointment->creator?->name ?? ucfirst($appointment->created_by_role);

            return "{$creator} scheduled a {$type} appointment for {$client} on {$date} at {$time}. Status: {$status}.";
        }

        return "{$client} scheduled a {$type} appointment on {$date} at {$time}. Status: {$status}.";
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
        $appointment->loadMissing(['user']);

        $performedById = auth()->id();

        if (
            $type === 'appointment_scheduled'
            && $appointment->created_by
        ) {
            $performedById = $appointment->created_by;
        }

        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => route(
                'filament.ev-admin.pages.appointments',
                [
                    'activeTab' => $appointment->status,
                    'highlight' => $appointment->id,
                ]
            ),
            'data' => [
                'client_name' => $appointment->name ?? $appointment->user?->name,
                'phone' => $appointment->phone,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'appointment_type' => $appointment->appointment_type,
                'status' => $appointment->status,
                'appointment_id' => $appointment->id,
                'client_url' => route(
                    'client.appointment',
                    [
                        'activeTab' => $appointment->status,
                        'highlight' => $appointment->id,
                    ]
                ),
            ],
            'created_by' => $performedById,
        ]);

        $staffAdmins = \App\Models\User::query()
            ->whereIn('role', [
                'admin',
                'staff',
            ])
            ->when(
                $performedById,
                fn ($query) =>
                    $query->where(
                        'id',
                        '!=',
                        $performedById
                    )
            )
            ->pluck('id');

        $users = $staffAdmins;

        if (
            $includeClient
            && $appointment->user_id
            && (int) $appointment->user_id !== (int) $performedById
        ) {
            $users->push(
                $appointment->user_id
            );
        }

        $notification->users()->attach(
            $users
                ->filter()
                ->unique()
                ->values()
                ->toArray()
        );
    }
}
