<x-mail::message>

# Payment Approved

Hello {{ $payment->purchaseAccount->user->name }},

Your payment has been approved successfully.

### Payment Details

- Billing: {{ $payment->billing->title }}
- Amount: ₱{{ number_format($payment->amount, 2) }}
- Method: {{ Str::headline($payment->payment_method) }}
- Approved By: {{ $payment->verifier?->name ?? 'Admin / Staff' }}
- Approved At: {{ $payment->verified_at->format('F j, Y g:i A') }}

Thank you for your payment.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>