<x-mail::message>

# Client Payment Submitted

A client submitted a payment for verification.

### Details

- Client: {{ $payment->purchaseAccount->user->name }}
- Billing: {{ $payment->billing->title }}
- Amount: ₱{{ number_format($payment->amount, 2) }}
- Method: {{ Str::headline($payment->payment_method) }}

Please review the payment in the admin panel.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>