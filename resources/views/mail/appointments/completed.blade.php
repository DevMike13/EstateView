<x-mail::message>
# Appointment Completed

Hi {{ $appointment->name ?? $user->name }},

Your appointment has been successfully completed. Thank you!

<x-mail::panel>
Date: {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}
Time: {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
</x-mail::panel>

We appreciate your trust.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>