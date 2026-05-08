<x-mail::message>
# Verify Your Account

Hello,

We received a request to verify your account. Please use the One-Time Password (OTP) below to continue:

<x-mail::panel>
{{ $otp }}
</x-mail::panel>

This OTP is valid for a limited time. For your security, please do not share this code with anyone.

If you did not request this verification, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>