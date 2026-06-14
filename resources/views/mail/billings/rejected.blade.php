<x-mail::message>

# Payment Rejected

Hello {{ $payment->purchaseAccount->user->name }},

Unfortunately, your submitted payment could not be verified.

### Payment Details

- Billing: {{ $payment->billing->title }}
- Amount: ₱{{ number_format($payment->amount, 2) }}
- Method: {{ Str::headline($payment->payment_method) }}

Please review your payment details and submit a new proof of payment.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>