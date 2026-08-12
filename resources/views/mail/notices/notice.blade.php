<x-mail::message>

# {{ $noticeTitle }}

Hello {{ $notice->user->name }},

@if($notice->notice_type === 'monthly_payment')

This is a reminder regarding your upcoming monthly payment.

### Billing Details

- Billing: {{ $notice->billing->title }}
- Amount Due: ₱{{ number_format($notice->amount, 2) }}
- Due Date: {{ $notice->billing_due_date->format('F d, Y') }}

Please settle your monthly payment on or before the due date.

@elseif($notice->notice_type === 'non_payment')

This is a notice regarding your outstanding monthly payment.

### Outstanding Payment Details

- Billing: {{ $notice->billing->title }}
- Outstanding Amount: ₱{{ number_format($notice->amount, 2) }}
- Original Due Date: {{ $notice->billing_due_date->format('F d, Y') }}
- Months Overdue: {{ $notice->overdue_month }}

@if($notice->deadline_date)
- Settlement Deadline: {{ $notice->deadline_date->format('F d, Y') }}
@endif

Our records indicate that this payment remains unsettled.

If you have already submitted your payment, please disregard this notice while your payment is being verified.

@elseif($notice->notice_type === 'cancellation')

This email contains an official **Notice of Cancellation** regarding your account.

### Account Details

- Billing: {{ $notice->billing->title }}
- Outstanding Amount: ₱{{ number_format($notice->amount, 2) }}
- Original Due Date: {{ $notice->billing_due_date->format('F d, Y') }}
- Months Overdue: {{ $notice->overdue_month }}

@if($notice->effective_date)
- Notice Effective Date: {{ $notice->effective_date->format('F d, Y') }}
@endif

@if($notice->deadline_date)
- Response Deadline: {{ $notice->deadline_date->format('F d, Y') }}
@endif

Please review the attached official notice carefully and contact our office regarding your account.

@elseif($notice->notice_type === 'forfeiture')

This email contains an official **Notice of Forfeiture** regarding your account.

### Account Details

- Billing: {{ $notice->billing->title }}
- Outstanding Amount: ₱{{ number_format($notice->amount, 2) }}
- Original Due Date: {{ $notice->billing_due_date->format('F d, Y') }}
- Months Overdue: {{ $notice->overdue_month }}

@if($notice->effective_date)
- Effective Date: {{ $notice->effective_date->format('F d, Y') }}
@endif

Please review the attached official notice carefully and contact our office for clarification regarding your account.

@endif

<x-mail::panel>
The official {{ $noticeTitle }} is attached to this email as a PDF document.
</x-mail::panel>

If you have any questions or concerns, please contact our office.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>