<x-mail::message>

# Reservation Documents Approved

Hello {{ $reservation->user->name }},

We are pleased to inform you that your **reservation submission and required documents have been reviewed and approved**.

Your reservation is now ready for the next step.

### Reservation Details

- **Reservation Reference:** #{{ $reservation->id }}
- **Reservation Type:** {{ $reservation->type }}
- **Lot:** {{ $reservation->lot?->name ?? 'N/A' }}
@if($reservation->houseModel)
- **House Model:** {{ $reservation->houseModel->model_name }}
@endif
@if($reservation->preferredPayment)
- **Preferred Payment:** {{ Str::headline($reservation->preferredPayment->payment_type) }}
@endif
- **Status:** Awaiting Reservation Fee
- **Performed By:** {{ $performedBy }}

### Reservation Fee

To continue processing your reservation, please proceed with the required reservation fee.

@if($reservation->type === 'House & Lot')
<x-mail::panel>
**Reservation Fee: ₱50,000.00**
</x-mail::panel>
@else
<x-mail::panel>
**Reservation Fee: ₱20,000.00**
</x-mail::panel>
@endif

Please log in to your Estate View account and proceed to the **Reservations** page to submit your reservation fee payment.

<x-mail::button :url="route('user.home')">
View My Reservation
</x-mail::button>

Once your payment has been submitted, our team will verify your proof of payment.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>