<x-mail::message>
# New Appointment Scheduled

Hi {{ $appointment->name ?? $user->name }},

Your appointment has been scheduled. Please review the appointment details below and confirm or decline the appointment from your account.

<x-mail::panel>
Date: {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}<br>
Time: {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}<br>
Type: {{ $appointment->appointment_type }}<br>
Created By: {{ $performedBy }}
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>