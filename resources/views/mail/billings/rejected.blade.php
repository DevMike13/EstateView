<x-mail::message>

# Payment Rejected

Hello {{ $payment->purchaseAccount->user->name }},

Unfortunately, your submitted payment could not be verified.

### Payment Details

- Billing: {{ $payment->billing->title }}
- Amount: ₱{{ number_format($payment->amount, 2) }}
- Method: {{ Str::headline($payment->payment_method) }}
- Rejected By: {{ $payment->verifier?->name ?? 'Admin / Staff' }}
- Rejected At: {{ $payment->verified_at->format('F j, Y g:i A') }}

Please review your payment details and submit a new proof of payment.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>