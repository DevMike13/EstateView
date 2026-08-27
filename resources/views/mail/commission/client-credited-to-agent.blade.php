<x-mail::message>

# New Client Credited to You

Hello {{ $agent->name }},

A new client has registered using your Professional Agent ID and has been associated with your account.

<x-mail::panel>
Client: {{ $client->name }}

Email: {{ $client->email ?? 'N/A' }}

Status: Credited to You
</x-mail::panel>

This client will now be associated with you in EstateView.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>