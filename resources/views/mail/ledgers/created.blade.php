<x-mail::message>

# Purchase Account Created

Hello {{ $user->name }},

Your purchase account has been created successfully.

### Account Details

- Property: {{ $account->lot?->name }}
- Payment Scheme: {{ Str::headline($account->payment_scheme) }}
- Total Contract Price: ₱{{ number_format($account->total_contract_price, 2) }}
- Remaining Balance: ₱{{ number_format($account->remaining_balance, 2) }}

You may now review your billing schedule through the client portal.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>