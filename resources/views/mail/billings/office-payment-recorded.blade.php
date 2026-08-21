<x-mail::message>

# Office Payment Recorded

Hello {{ $payment->purchaseAccount->user->name }},

A payment has been recorded on your account by our office.

### Payment Details

- **Billing:** {{ $payment->billing?->title ?? 'N/A' }}
- **Amount:** ₱{{ number_format($payment->amount, 2) }}
- **Payment Method:** {{ Str::headline($payment->payment_method) }}
- **Reference Number:** {{ $payment->reference_no ?? 'N/A' }}
- **Recorded By:** {{ $payment->verifier?->name ?? 'Admin / Staff' }}
- **Payment Date:** {{ $payment->paid_at?->format('M d, Y h:i A') ?? 'N/A' }}
- **Status:** Verified

@if($payment->remarks)
- **Remarks:** {{ $payment->remarks }}
@endif

The payment has already been verified and applied to your billing account.

If you have any questions regarding this transaction, please contact our office.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>