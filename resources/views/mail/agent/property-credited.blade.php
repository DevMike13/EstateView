<x-mail::message>

# Property Credited to You

Hello {{ $reservation->agent?->name ?? 'Agent' }},

A property has been **credited to your account** for one of your clients.

### Property Details

<x-mail::panel>
**Client:** {{ $reservation->user?->name ?? 'N/A' }}  
**Client Email:** {{ $reservation->user?->email ?? 'N/A' }}

**Lot:** {{ $reservation->lot?->name ?? 'N/A' }}

@if($reservation->lot?->lot_number)
**Lot Number:** {{ $reservation->lot->lot_number }}
@endif

@if($reservation->houseModel)
**House Model:** {{ $reservation->houseModel->model_name ?? $reservation->houseModel->name ?? 'N/A' }}
@endif

**Reservation Type:** {{ $reservation->type ?? 'N/A' }}  
**Payment Scheme:** {{ \Illuminate\Support\Str::headline($account->payment_scheme) }}

**Total Contract Price:** ₱{{ number_format($account->total_contract_price, 2) }}

**Performed By:** {{ $performedBy }}
</x-mail::panel>

This property is now credited to you and can be viewed from your commission page.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>