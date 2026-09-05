<x-mail::message>
@if($reservation->notes === 'Lot allocated to another reservation.')
# Reserved Lot No Longer Available

Hi {{ $reservation->user->name }},

We would like to inform you that your reservation for the property below can no longer proceed because the lot has been **allocated to another reservation**.

<x-mail::panel>
Client: {{ $reservation->user->name }}  
Email: {{ $reservation->user->email }}  

Lot: {{ $reservation->lot->name ?? 'N/A' }}
@if($reservation->houseModel)
House Model: {{ $reservation->houseModel->model_name ?? 'N/A' }}
@endif

Status: Rejected  
Performed By: {{ $performedBy }}  
Reason: The selected lot has been allocated to another reservation.
</x-mail::panel>

Your submitted requirements were not the reason for this outcome. You may select another available property and submit a new reservation.

Thanks,<br>
{{ config('app.name') }}

@else
# Reservation Rejected

Hi {{ $reservation->user->name }},

We regret to inform you that your lot reservation has been **rejected**.

<x-mail::panel>
Client: {{ $reservation->user->name }}  
Email: {{ $reservation->user->email }}  

Lot: {{ $reservation->lot->name ?? 'N/A' }}
{{-- - {{ $reservation->lot->lot_number ?? '' }}   --}}
@if($reservation->houseModel)
House Model: {{ $reservation->houseModel->model_name ?? 'N/A' }}
@endif

Status: Rejected  
Performed By: {{ $performedBy }}<br>
Reason: Please contact support for details
</x-mail::panel>

You may try again or contact our team for assistance.

Thanks,<br>
{{ config('app.name') }}
@endif
</x-mail::message>
