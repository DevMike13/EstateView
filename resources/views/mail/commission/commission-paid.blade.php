<x-mail::message>

# Commission Payment Released

Hello {{ $commissionRequest->agent?->name }},

Your commission payment has been released successfully.

### Commission Details

- **Client:** {{ $commissionRequest->purchaseAccount?->user?->name ?? 'N/A' }}
- **Property:** {{ $commissionRequest->purchaseAccount?->lot?->name ?? 'N/A' }}
- **Period:** {{ $commissionRequest->period_label }}
- **Amount:** ₱{{ number_format($commissionRequest->requested_amount, 2) }}
- **Status:** Paid
- **Paid Date:** {{ optional($commissionRequest->paid_at)->format('M d, Y h:i A') ?? 'N/A' }}

@if($commissionRequest->remarks)

### Payment Information

{!! nl2br(e($commissionRequest->remarks)) !!}

@endif

Your commission payment has been recorded by the administration.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>