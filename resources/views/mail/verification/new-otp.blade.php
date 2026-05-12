<x-mail::message>
# Account Verification Code

Hello,

We received a request to verify your account. Please use the One-Time Password (OTP) below to continue:

<x-mail::panel>
{{ $otp }}
</x-mail::panel>

This code is valid for the next 10 minutes. For your security, please do not share this code with anyone.

If you did not request this verification, you may safely ignore this email.

Regards,<br>
{{ config('app.name') }}
</x-mail::message>