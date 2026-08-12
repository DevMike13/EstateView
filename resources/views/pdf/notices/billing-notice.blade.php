<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 28px 55px 25px 55px;
        }

        body {
            margin: 0;
            padding: 0;
            color: #111;
            font-family: DejaVu Serif, serif;
            font-size: 13px;
            line-height: 1.8;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 45px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo {
            width: 80px;
        }

        .header-center {
            text-align: center;
        }

        .company {
            font-weight: bold;
            font-size: 18px;
            line-height: 1.2;
        }

        .project {
            font-weight: bold;
            font-size: 17px;
            line-height: 1.2;
        }

        .address {
            font-size: 11px;
            line-height: 1.4;
        }

        .contact {
            font-size: 9px;
            line-height: 1.4;
        }

        h1 {
            text-align: center;
            font-size: 17px;
            margin: 0 0 40px 0;
        }

        p {
            margin: 0 0 22px 0;
            text-align: justify;
        }

        .salutation {
            text-align: left;
        }

        .signature {
            margin-top: 35px;
        }

        .company-signature {
            font-weight: bold;
            font-size: 15px;
        }

        .computer-generated {
            font-size: 8px;
            font-style: italic;
            margin-top: 3px;
        }

        .houses {
            position: fixed;
            bottom: 8px;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .houses img {
            width: 100%;
            max-height: 215px;
            object-fit: contain;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <table class="header-table">

        <tr>

            <td style="width: 15%; text-align: center;">
                <img
                    class="logo"
                    src="{{ public_path(
                        'images/notices/dga-logo.png'
                    ) }}"
                >
            </td>

            <td
                class="header-center"
                style="width: 70%;"
            >

                <div class="company">
                    {{ config(
                        'billing_notices.company_name'
                    ) }}
                </div>

                <div class="project">
                    {{ config(
                        'billing_notices.project_name'
                    ) }}
                </div>

                <div class="address">
                    {{ config(
                        'billing_notices.address'
                    ) }}
                </div>

                <div class="contact">

                    {{ config(
                        'billing_notices.phone'
                    ) }}

                    •

                    {{ config(
                        'billing_notices.email'
                    ) }}

                    •

                    Facebook:

                    {{ config(
                        'billing_notices.facebook'
                    ) }}

                </div>

            </td>

            <td style="width: 15%; text-align: center;">

                <img
                    class="logo"
                    src="{{ public_path(
                        'images/notices/manhattan-logo.png'
                    ) }}"
                >

            </td>

        </tr>

    </table>


    {{-- MONTHLY PAYMENT --}}
    @if($notice->notice_type === 'monthly_payment')

        <h1>
            NOTICE OF MONTHLY PAYMENT
        </h1>

        <p class="salutation">
            Dear Valued Client,
        </p>

        <p class="salutation">
            Good day,
            {{ $client->name }},
        </p>

        <p>
            This is a gentle reminder regarding your
            <span class="bold">
                MONTHLY PAYMENT
            </span>
            for the month of

            {{ $billing->due_date->format('F Y') }},

            amounting to

            <span class="bold">
                ₱{{ number_format(
                    $notice->amount,
                    2
                ) }}
            </span>,

            which is due on

            <span class="bold">
                {{ $billing->due_date->format(
                    'F d, Y'
                ) }}.
            </span>
        </p>

        <p>
            We kindly request your prompt settlement to help
            us maintain accurate records and continue providing
            community services. If payment has already been made,
            please disregard this message.
        </p>

        <p>
            Thank you for your cooperation.
        </p>

    @endif


    {{-- NON-PAYMENT --}}
    @if($notice->notice_type === 'non_payment')

        <h1>
            NOTICE OF NON-PAYMENT
        </h1>

        <p class="salutation">
            Dear Valued Client,
        </p>

        <p class="salutation">
            Good day,
            {{ $client->name }},
        </p>

        <p>
            This letter serves as formal notice regarding the

            <span class="bold">
                NON-PAYMENT
            </span>

            of the outstanding balance amounting to

            <span class="bold">
                ₱{{ number_format(
                    $notice->amount,
                    2
                ) }}
            </span>,

            which was due on

            <span class="bold">
                {{ $billing->due_date->format(
                    'F d, Y'
                ) }}.
            </span>
        </p>

        <p>
            As of today, we have not yet received the payment.
            We respectfully request that you settle the
            outstanding balance on or before

            <span class="bold">
                {{ optional(
                    $notice->deadline_date
                )->format('F d, Y') }}
            </span>

            to avoid further actions or penalties.
        </p>

        <p>
            If payment has already been made, please disregard
            this notice and accept our thanks. Otherwise, kindly
            contact us immediately should you have any question
            or concern regarding this matter.
        </p>

    @endif


    {{-- CANCELLATION --}}
    @if($notice->notice_type === 'cancellation')

        <h1>
            NOTICE OF CANCELLATION
        </h1>

        <p class="salutation">
            Dear Valued Client,
        </p>

        <p class="salutation">
            Good day,
            {{ $client->name }},
        </p>

        <p>
            This letter serves as a formal

            <span class="bold">
                NOTICE OF CANCELLATION
            </span>

            of your contract for

            {{ $lot?->name ?? 'the subject property' }},

            located at

            {{ config(
                'billing_notices.project_name'
            ) }},

            due to failure to comply with the agreed
            monthly payment obligations.
        </p>

        <p>
            Despite previous reminders, the required payment
            remains unpaid, which constitutes a violation of the
            terms and conditions of the contract. As such the
            contract is hereby considered cancelled effective

            <span class="bold">
                {{ optional(
                    $notice->effective_date
                )->format('F d, Y') }}.
            </span>
        </p>

        <p>
            Please coordinate with our office within

            <span class="bold">
                {{ config(
                    'billing_notices.response_days'
                ) }} days
            </span>

            from receipt of this notice for proper clarification
            and settlement of any remaining matters.
        </p>

        <p>
            Thank you.
        </p>

    @endif


    {{-- FORFEITURE --}}
    @if($notice->notice_type === 'forfeiture')

        <h1>
            NOTICE OF FORFEITURE
        </h1>

        <p class="salutation">
            Dear Valued Client,
        </p>

        <p class="salutation">
            Good day,
            {{ $client->name }},
        </p>

        <p>
            This letter serves as a formal

            <span class="bold">
                NOTICE OF FORFEITURE
            </span>

            of your rights and interests over

            {{ $lot?->name ?? 'the subject property' }},

            located at

            {{ config(
                'billing_notices.project_name'
            ) }}.
        </p>

        <p>
            Due to your continuous failure to settle the
            required monthly amortizations, despite prior
            reminders and notices, your account is now considered
            delinquent. In accordance with the terms and
            conditions of your Contract to Sell and applicable
            policies, all payments previously made are hereby
            forfeited effective

            <span class="bold">
                {{ optional(
                    $notice->effective_date
                )->format('F d, Y') }}.
            </span>
        </p>

        <p>
            Please be advised that your right to occupy or claim
            ownership of the said property is now terminated.
            We respectfully request that you coordinate with our
            office within

            <span class="bold">
                {{ config(
                    'billing_notices.response_days'
                ) }} days
            </span>

            from receipt of this notice for proper turnover and
            account closure.
        </p>

        <p>
            Should you have any questions or wish to clarify this
            matter, you may contact us during office hours.
        </p>

    @endif


    {{-- SIGNATURE --}}
    <div class="signature">

        <p class="salutation">
            Sincerely,
        </p>

        <div class="company-signature">
            {{ config(
                'billing_notices.company_name'
            ) }}
        </div>

        <div class="computer-generated">
            This is a computer-generated letter and if issued
            without alteration does not require a signature.
        </div>

    </div>


    {{-- HOUSES --}}
    <div class="houses">

        <img
            src="{{ public_path(
                'images/notices/houses.png'
            ) }}"
        >

    </div>

</body>
</html>