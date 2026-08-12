<?php

namespace App\Console\Commands;

use App\Mail\BillingNoticeMail;
use App\Models\Billing;
use App\Models\BillingNotice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TestBillingNoticeEmail extends Command
{
    /**
     * Example:
     *
     * php artisan billing-notices:test safemike13@gmail.com 25 monthly_payment
     * php artisan billing-notices:test safemike13@gmail.com 25 non_payment
     * php artisan billing-notices:test safemike13@gmail.com 25 cancellation
     * php artisan billing-notices:test safemike13@gmail.com 25 forfeiture
     * 
     */
    protected $signature = 'billing-notices:test
                            {email : Email address that will receive the test}
                            {billing_id : Billing ID to use for the test}
                            {notice_type=monthly_payment : monthly_payment|non_payment|cancellation|forfeiture}';

    protected $description = 'Generate and send a test billing notice to a custom email address';

    public function handle(): int
    {
        /*
        |--------------------------------------------------------------------------
        | Get Command Arguments
        |--------------------------------------------------------------------------
        */

        $email = $this->argument('email');

        $billingId = (int) $this->argument('billing_id');

        $noticeType = $this->argument('notice_type');


        /*
        |--------------------------------------------------------------------------
        | Validate Email
        |--------------------------------------------------------------------------
        */

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $this->error('Invalid email address.');

            return self::FAILURE;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Notice Type
        |--------------------------------------------------------------------------
        */

        $allowedTypes = [
            'monthly_payment',
            'non_payment',
            'cancellation',
            'forfeiture',
        ];

        if (! in_array($noticeType, $allowedTypes, true)) {

            $this->error('Invalid notice type.');

            $this->line(
                'Allowed types: ' . implode(', ', $allowedTypes)
            );

            return self::FAILURE;
        }


        /*
        |--------------------------------------------------------------------------
        | Find Billing
        |--------------------------------------------------------------------------
        */

        $billing = Billing::query()
            ->with([
                'purchaseAccount.user.info',
                'purchaseAccount.lot',
                'purchaseAccount.houseModel',
                'payments',
            ])
            ->find($billingId);


        if (! $billing) {

            $this->error(
                "Billing ID {$billingId} was not found."
            );

            return self::FAILURE;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Purchase Account
        |--------------------------------------------------------------------------
        */

        $account = $billing->purchaseAccount;


        if (! $account) {

            $this->error(
                'The selected billing does not have a PurchaseAccount.'
            );

            return self::FAILURE;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Client
        |--------------------------------------------------------------------------
        */

        $client = $account->user;


        if (! $client) {

            $this->error(
                'The selected billing does not have a client.'
            );

            return self::FAILURE;
        }


        /*
        |--------------------------------------------------------------------------
        | Display Test Information
        |--------------------------------------------------------------------------
        */

        $this->newLine();

        $this->info('Preparing billing notice TEST...');

        $this->newLine();

        $this->table(
            [
                'Field',
                'Value',
            ],
            [
                [
                    'Billing ID',
                    $billing->id,
                ],
                [
                    'Billing',
                    $billing->title,
                ],
                [
                    'Client',
                    $client->name,
                ],
                [
                    'Client Real Email',
                    $client->email,
                ],
                [
                    'TEST Recipient',
                    $email,
                ],
                [
                    'Notice Type',
                    $noticeType,
                ],
                [
                    'Due Date',
                    optional($billing->due_date)
                        ->format('M d, Y'),
                ],
                [
                    'Amount Due',
                    '₱' . number_format(
                        (float) $billing->amount_due,
                        2
                    ),
                ],
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Confirm Test
        |--------------------------------------------------------------------------
        */

        if (! $this->confirm(
            "Send TEST {$noticeType} notice to {$email}?",
            true
        )) {

            $this->warn('Test cancelled.');

            return self::SUCCESS;
        }


        /*
        |--------------------------------------------------------------------------
        | Determine Amount
        |--------------------------------------------------------------------------
        */

        $amountDue = (float) $billing->amount_due;

        $amountPaid = (float) $billing->amount_paid;

        $balance = max(
            $amountDue - $amountPaid,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | Determine Overdue Month
        |--------------------------------------------------------------------------
        */

        $overdueMonth = match ($noticeType) {

            'monthly_payment' => 0,

            'non_payment' => 1,

            'cancellation' => 5,

            'forfeiture' => 6,

            default => 0,
        };


        /*
        |--------------------------------------------------------------------------
        | Response Deadline
        |--------------------------------------------------------------------------
        */

        $responseDays = (int) config(
            'billing_notices.response_days',
            7
        );


        $deadlineDate = match ($noticeType) {

            'non_payment',
            'cancellation',
            'forfeiture'
                => now()
                    ->addDays($responseDays)
                    ->toDateString(),

            default => null,
        };


        /*
        |--------------------------------------------------------------------------
        | Effective Date
        |--------------------------------------------------------------------------
        */

        $effectiveDate = match ($noticeType) {

            'cancellation',
            'forfeiture'
                => now()->toDateString(),

            default => null,
        };


        /*
        |--------------------------------------------------------------------------
        | Create TEMPORARY BillingNotice
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | We intentionally DO NOT call:
        |
        | BillingNotice::create(...)
        |
        | Therefore this test will NOT create a real notice
        | inside your billing_notices table.
        |
        */

        $notice = new BillingNotice();


        $notice->id = 0;

        $notice->user_id = $client->id;

        $notice->purchase_account_id = $account->id;

        $notice->billing_id = $billing->id;

        $notice->notice_type = $noticeType;

        $notice->amount = $balance;

        $notice->billing_due_date = $billing->due_date;

        $notice->overdue_month = $overdueMonth;

        $notice->notice_period = now()->format('Y-m');

        $notice->effective_date = $effectiveDate;

        $notice->deadline_date = $deadlineDate;

        $notice->email = $email;

        $notice->status = 'test';


        /*
        |--------------------------------------------------------------------------
        | Attach Existing Relationships
        |--------------------------------------------------------------------------
        */

        $notice->setRelation(
            'user',
            $client
        );

        $notice->setRelation(
            'billing',
            $billing
        );

        $notice->setRelation(
            'purchaseAccount',
            $account
        );


        /*
        |--------------------------------------------------------------------------
        | Notice Title
        |--------------------------------------------------------------------------
        */

        $noticeTitle = $this->getNoticeTitle(
            $noticeType
        );


        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        */

        try {

            $pdf = Pdf::loadView(
                'pdf.notices.billing-notice',
                [
                    'notice' => $notice,

                    'noticeTitle' => $noticeTitle,

                    'client' => $client,

                    'billing' => $billing,

                    'account' => $account,

                    'lot' => $account->lot,
                ]
            );


            $pdf->setPaper(
                'a4',
                'portrait'
            );


            /*
            |--------------------------------------------------------------------------
            | Test PDF Filename
            |--------------------------------------------------------------------------
            */

            $filename =
                'TEST-'
                . $noticeType
                . '-billing-'
                . $billing->id
                . '-'
                . now()->format('YmdHis')
                . '.pdf';


            $path =
                'billing-notices/tests/'
                . $filename;


            /*
            |--------------------------------------------------------------------------
            | Storage Disk
            |--------------------------------------------------------------------------
            */

            $disk = config(
                'billing_notices.disk',
                'local'
            );


            Storage::disk($disk)->put(
                $path,
                $pdf->output()
            );


            /*
            |--------------------------------------------------------------------------
            | Give PDF Path To Temporary Notice
            |--------------------------------------------------------------------------
            */

            $notice->pdf_path = $path;


            /*
            |--------------------------------------------------------------------------
            | Send Email
            |--------------------------------------------------------------------------
            |
            | VERY IMPORTANT:
            |
            | We send ONLY to the email passed into the command.
            |
            | We DO NOT use:
            |
            | $client->email
            |
            */

            Mail::to($email)
                ->send(
                    new BillingNoticeMail(
                        $notice
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            $this->newLine();

            $this->info(
                'TEST billing notice sent successfully!'
            );


            $this->newLine();


            $this->table(
                [
                    'Result',
                    'Value',
                ],
                [
                    [
                        'Recipient',
                        $email,
                    ],
                    [
                        'Client Data Used',
                        $client->name,
                    ],
                    [
                        'Notice',
                        $noticeTitle,
                    ],
                    [
                        'Billing',
                        $billing->title,
                    ],
                    [
                        'Amount',
                        '₱' . number_format(
                            $balance,
                            2
                        ),
                    ],
                    [
                        'PDF',
                        $path,
                    ],
                ]
            );


            $this->newLine();

            $this->warn(
                'TEST MODE: No BillingNotice database record was created.'
            );


            return self::SUCCESS;

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Error
            |--------------------------------------------------------------------------
            */

            $this->newLine();

            $this->error(
                'Failed to send TEST billing notice.'
            );

            $this->error(
                $e->getMessage()
            );


            report($e);


            return self::FAILURE;
        }
    }


    /**
     * Get the title for each notice type.
     */
    private function getNoticeTitle(
        string $type
    ): string {

        return match ($type) {

            'monthly_payment'
                => 'Notice of Monthly Payment',

            'non_payment'
                => 'Notice of Non-Payment',

            'cancellation'
                => 'Notice of Cancellation',

            'forfeiture'
                => 'Notice of Forfeiture',

            default
                => 'Billing Notice',
        };
    }
}