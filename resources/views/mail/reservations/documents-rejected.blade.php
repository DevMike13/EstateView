<x-mail::message>

# Reservation Fee Rejected

Hello {{ $reservation->user->name }},

We regret to inform you that your **reservation fee submission have been reviewed and were not approved**.

Please review your submitted fee information and contact our team if you need clarification regarding the rejected fee.

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

- **Status:** Reservation Fee Rejected
- **Performed By:** {{ $performedBy }}

{{-- <x-mail::panel>
Your reservation cannot proceed to the next stage until the required documents have been corrected or resubmitted.
</x-mail::panel> --}}

If you need assistance, please contact our team for more information.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>