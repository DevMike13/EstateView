<x-mail::message>
# Appointment Declined

Hi {{ $appointment->name ?? $user->name }},

Unfortunately, your appointment was declined.

<x-mail::panel>
Date: {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}
Time: {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
</x-mail::panel>

Please contact support for more info.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>