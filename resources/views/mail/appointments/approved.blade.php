<x-mail::message>
# Appointment Approved 🎉

Hi {{ $appointment->name ?? $user->name }},

Your appointment has been **approved**.

<x-mail::panel>
Date: {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}<br>
Time: {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}<br>
Type: {{ $appointment->appointment_type }}<br>
Performed By: {{ $performedBy }}
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>