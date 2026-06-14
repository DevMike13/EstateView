<x-mail::message>

# Payment Approved

Hello {{ $payment->purchaseAccount->user->name }},

Your payment has been approved successfully.

### Payment Details

- Billing: {{ $payment->billing->title }}
- Amount: ₱{{ number_format($payment->amount, 2) }}
- Method: {{ Str::headline($payment->payment_method) }}

Thank you for your payment.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>