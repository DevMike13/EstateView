<x-mail::message>
# Reservation Approved 🎉

Hi {{ $reservation->user->name }},

Great news! Your lot reservation has been **approved**.

<x-mail::panel>
Client: {{ $reservation->user->name }}  
Email: {{ $reservation->user->email }}  

Lot: {{ $reservation->lot->name ?? 'N/A' }} 
{{-- - {{ $reservation->lot->lot_number ?? '' }}   --}}
@if($reservation->houseModel)
House Model: {{ $reservation->houseModel->model_name }}
@endif

Status: Approved  
Performed By: {{ $performedBy }}<br>
Reserved At: {{ \Carbon\Carbon::parse($reservation->reserved_at)->format('F d, Y h:i A') }}
</x-mail::panel>

You may now proceed with the next steps.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>