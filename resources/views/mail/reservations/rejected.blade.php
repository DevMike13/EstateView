<x-mail::message>
# Reservation Rejected

Hi {{ $reservation->user->name }},

We regret to inform you that your lot reservation has been **rejected**.

<x-mail::panel>
Client: {{ $reservation->user->name }}  
Email: {{ $reservation->user->email }}  

Lot: {{ $reservation->lot->name ?? 'N/A' }} - {{ $reservation->lot->lot_number ?? '' }}  
House Model: {{ $reservation->houseModel->name ?? 'N/A' }}

Status: Rejected  
Reason: Please contact support for details
</x-mail::panel>

You may try again or contact our team for assistance.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>