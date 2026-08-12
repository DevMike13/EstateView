<?php

namespace App\Services;

use App\Mail\BillingNoticeMail;
use App\Models\Billing;
use App\Models\BillingNotice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BillingNoticeService
{
    /**
     * Process all eligible billings.
     */
    public function process(): array
    {
        $stats = [
            'processed' => 0,
            'sent' => 0,
            'skipped' => 0,
            'failed' => 0,
        ];

        if (! config('billing_notices.enabled', true)) {
            return $stats;
        }

        Billing::query()
            ->with([
                'purchaseAccount.user.info',
                'purchaseAccount.lot',
                'purchaseAccount.houseModel',
                'payments',
            ])
            ->whereIn('status', [
                'unpaid',
                'partial',
            ])
            ->orderBy('id')
            ->chunkById(
                100,
                function ($billings) use (&$stats) {

                    foreach ($billings as $billing) {
                        $stats['processed']++;

                        try {
                            $result = $this->processBilling(
                                $billing
                            );

                            if ($result) {
                                $stats['sent']++;
                            } else {
                                $stats['skipped']++;
                            }

                        } catch (Throwable $e) {

                            $stats['failed']++;

                            Log::error(
                                'Automatic billing notice failed.',
                                [
                                    'billing_id' =>
                                        $billing->id,

                                    'message' =>
                                        $e->getMessage(),
                                ]
                            );
                        }
                    }
                }
            );

        return $stats;
    }

    /**
     * Determine which notice should be sent.
     */
    public function processBilling(
        Billing $billing
    ): bool {
        $billing->loadMissing([
            'purchaseAccount.user.info',
            'purchaseAccount.lot',
            'purchaseAccount.houseModel',
            'payments',
        ]);

        $account = $billing->purchaseAccount;
        $user = $account?->user;

        /*
        |--------------------------------------------------------------------------
        | Basic Safety Checks
        |--------------------------------------------------------------------------
        */

        if (! $account || ! $user) {
            return false;
        }

        if (! $user->email) {
            return false;
        }

        if (
            ! in_array(
                $billing->status,
                [
                    'unpaid',
                    'partial',
                ],
                true
            )
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Pause Notice Escalation if Payment is Pending
        |--------------------------------------------------------------------------
        |
        | Important:
        |
        | Client may have already submitted payment proof,
        | but admin has not verified it yet.
        |
        */

        $hasPendingPayment =
            $billing->payments
                ->contains(
                    fn ($payment) =>
                        $payment->status === 'pending'
                );

        if ($hasPendingPayment) {
            return false;
        }

        $today = now()
            ->startOfDay();

        $dueDate = $billing
            ->due_date
            ->copy()
            ->startOfDay();

        /*
        |--------------------------------------------------------------------------
        | MONTHLY PAYMENT NOTICE
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | Due: August 15
        | Config: 5 days before
        | Send: August 10
        |
        */

        if ($today->lt($dueDate)) {

            $daysBefore = (int) config(
                'billing_notices.monthly_days_before',
                5
            );

            $reminderDate = $dueDate
                ->copy()
                ->subDays($daysBefore);

            /*
             * Do not send before reminder date.
             */
            if ($today->lt($reminderDate)) {
                return false;
            }

            return $this->sendNotice(
                $billing,
                'monthly_payment',
                0
            );
        }

        /*
         * Due date itself is not overdue yet.
         */
        if ($today->isSameDay($dueDate)) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Overdue Month
        |--------------------------------------------------------------------------
        */

        $overdueMonth =
            $this->calculateOverdueMonth(
                $dueDate,
                $today
            );

        /*
         * Example:
         *
         * Due July 15
         *
         * July 16 - Aug 14
         * still not "Month 1 notice period"
         *
         * Aug 15
         * = Month 1
         */
        if ($overdueMonth <= 0) {
            return false;
        }

        $cancellationMonth =
            (int) config(
                'billing_notices.cancellation_month',
                5
            );

        $forfeitureMonth =
            (int) config(
                'billing_notices.forfeiture_month',
                6
            );

        /*
        |--------------------------------------------------------------------------
        | NON-PAYMENT: MONTHS 1-4
        |--------------------------------------------------------------------------
        */

        if (
            $overdueMonth >= 1
            && $overdueMonth < $cancellationMonth
        ) {
            return $this->sendNotice(
                $billing,
                'non_payment',
                $overdueMonth
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CANCELLATION: MONTH 5
        |--------------------------------------------------------------------------
        */

        if (
            $overdueMonth === $cancellationMonth
        ) {
            return $this->sendNotice(
                $billing,
                'cancellation',
                $cancellationMonth
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FORFEITURE: MONTH 6+
        |--------------------------------------------------------------------------
        |
        | Only ONE forfeiture notice should ever be sent.
        |
        */

        if (
            $overdueMonth >= $forfeitureMonth
        ) {
            /*
             * Make sure cancellation was sent first.
             */
            $cancellationSent =
                BillingNotice::query()
                    ->where(
                        'billing_id',
                        $billing->id
                    )
                    ->where(
                        'notice_type',
                        'cancellation'
                    )
                    ->where(
                        'status',
                        'sent'
                    )
                    ->exists();

            /*
             * If cron somehow missed month 5,
             * send cancellation first.
             */
            if (! $cancellationSent) {
                return $this->sendNotice(
                    $billing,
                    'cancellation',
                    $cancellationMonth
                );
            }

            /*
             * Never send another forfeiture notice.
             */
            $forfeitureAlreadySent =
                BillingNotice::query()
                    ->where(
                        'billing_id',
                        $billing->id
                    )
                    ->where(
                        'notice_type',
                        'forfeiture'
                    )
                    ->where(
                        'status',
                        'sent'
                    )
                    ->exists();

            if ($forfeitureAlreadySent) {
                return false;
            }

            return $this->sendNotice(
                $billing,
                'forfeiture',
                $forfeitureMonth
            );
        }

        return false;
    }

    /**
     * Calculate the completed overdue billing periods.
     *
     * Example:
     *
     * Due July 15
     *
     * August 15   = 1
     * September 15 = 2
     * October 15   = 3
     * November 15  = 4
     * December 15  = 5
     * January 15   = 6
     */
    private function calculateOverdueMonth(
        Carbon $dueDate,
        Carbon $today
    ): int {
        if ($today->lte($dueDate)) {
            return 0;
        }

        $months = 0;

        /*
         * We only need to distinguish up to
         * forfeiture month.
         *
         * Going slightly above allows the method
         * to continue working later.
         */
        for ($i = 1; $i <= 24; $i++) {

            $triggerDate = $dueDate
                ->copy()
                ->addMonthsNoOverflow($i);

            if ($today->gte($triggerDate)) {
                $months = $i;
            } else {
                break;
            }
        }

        return $months;
    }

    /**
     * Create database record,
     * generate PDF and send email.
     */
    private function sendNotice(
        Billing $billing,
        string $noticeType,
        int $overdueMonth
    ): bool {
        $billing->loadMissing([
            'purchaseAccount.user.info',
            'purchaseAccount.lot',
            'purchaseAccount.houseModel',
            'payments',
        ]);

        $account =
            $billing->purchaseAccount;

        $user =
            $account?->user;

        if (
            ! $account
            || ! $user
            || ! $user->email
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Notice Period
        |--------------------------------------------------------------------------
        |
        | Monthly reminder:
        | based on billing due month.
        |
        | Non-payment:
        | based on overdue period.
        |
        | Cancellation / forfeiture:
        | their respective escalation month.
        |
        */

        $noticePeriod = match ($noticeType) {

            'monthly_payment' =>
                $billing->due_date
                    ->format('Y-m'),

            'non_payment' =>
                $billing->due_date
                    ->copy()
                    ->addMonthsNoOverflow(
                        $overdueMonth
                    )
                    ->format('Y-m'),

            'cancellation' =>
                $billing->due_date
                    ->copy()
                    ->addMonthsNoOverflow(
                        (int) config(
                            'billing_notices.cancellation_month',
                            5
                        )
                    )
                    ->format('Y-m'),

            'forfeiture' =>
                $billing->due_date
                    ->copy()
                    ->addMonthsNoOverflow(
                        (int) config(
                            'billing_notices.forfeiture_month',
                            6
                        )
                    )
                    ->format('Y-m'),

            default =>
                now()->format('Y-m'),
        };

        /*
        |--------------------------------------------------------------------------
        | Duplicate Protection
        |--------------------------------------------------------------------------
        */

        $existingNotice =
            BillingNotice::query()
                ->where(
                    'billing_id',
                    $billing->id
                )
                ->where(
                    'notice_type',
                    $noticeType
                )
                ->where(
                    'notice_period',
                    $noticePeriod
                )
                ->first();

        if (
            $existingNotice
            && $existingNotice->status === 'sent'
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Use Existing Billing Adjustment Logic
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | This uses the same payable amount as:
        |
        | - Client My Bills
        | - Admin office payments
        |
        | Therefore the 3% overdue penalty is included.
        |
        */

        $amount =
            (float) $billing->payable_amount;

        /*
        |--------------------------------------------------------------------------
        | Deadline
        |--------------------------------------------------------------------------
        */

        $deadlineDate =
            $this->getDeadline(
                $noticeType
            );

        /*
        |--------------------------------------------------------------------------
        | Effective Date
        |--------------------------------------------------------------------------
        */

        $effectiveDate =
            in_array(
                $noticeType,
                [
                    'cancellation',
                    'forfeiture',
                ],
                true
            )
                ? now()->toDateString()
                : null;

        /*
        |--------------------------------------------------------------------------
        | Create / Retry Notice
        |--------------------------------------------------------------------------
        */

        if (! $existingNotice) {

            $notice =
                BillingNotice::create([
                    'user_id' =>
                        $user->id,

                    'purchase_account_id' =>
                        $account->id,

                    'billing_id' =>
                        $billing->id,

                    'notice_type' =>
                        $noticeType,

                    'amount' =>
                        $amount,

                    'billing_due_date' =>
                        $billing->due_date,

                    'overdue_month' =>
                        $overdueMonth,

                    'notice_period' =>
                        $noticePeriod,

                    'effective_date' =>
                        $effectiveDate,

                    'deadline_date' =>
                        $deadlineDate,

                    'email' =>
                        $user->email,

                    'status' =>
                        'pending',
                ]);

        } else {

            /*
             * Retry previously failed notice.
             */
            $notice =
                $existingNotice;

            $notice->update([
                'amount' =>
                    $amount,

                'overdue_month' =>
                    $overdueMonth,

                'effective_date' =>
                    $effectiveDate,

                'deadline_date' =>
                    $deadlineDate,

                'email' =>
                    $user->email,

                'status' =>
                    'pending',

                'error_message' =>
                    null,
            ]);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Generate PDF
            |--------------------------------------------------------------------------
            */

            $pdfPath =
                $this->generatePdf(
                    $notice
                );

            $notice->update([
                'pdf_path' =>
                    $pdfPath,

                'error_message' =>
                    null,
            ]);

            /*
             * Reload relationships + new PDF path
             * before passing it to Mailable.
             */
            $notice->refresh();

            $notice->loadMissing([
                'user',
                'billing',
                'purchaseAccount',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Send Email with PDF Attachment
            |--------------------------------------------------------------------------
            */

            Mail::to(
                $user->email
            )->send(
                new BillingNoticeMail(
                    $notice
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Mark Sent
            |--------------------------------------------------------------------------
            */

            $notice->update([
                'status' =>
                    'sent',

                'sent_at' =>
                    now(),

                'error_message' =>
                    null,
            ]);

            Log::info(
                'Billing notice sent.',
                [
                    'billing_id' =>
                        $billing->id,

                    'notice_id' =>
                        $notice->id,

                    'notice_type' =>
                        $noticeType,

                    'overdue_month' =>
                        $overdueMonth,

                    'notice_period' =>
                        $noticePeriod,

                    'email' =>
                        $user->email,
                ]
            );

            return true;

        } catch (Throwable $e) {

            $notice->update([
                'status' =>
                    'failed',

                'error_message' =>
                    $e->getMessage(),
            ]);

            Log::error(
                'Failed to send billing notice.',
                [
                    'billing_id' =>
                        $billing->id,

                    'notice_id' =>
                        $notice->id,

                    'notice_type' =>
                        $noticeType,

                    'error' =>
                        $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Generate official PDF notice.
     */
    private function generatePdf(
        BillingNotice $notice
    ): string {
        $notice->loadMissing([
            'user.info',
            'billing',
            'purchaseAccount.lot',
            'purchaseAccount.houseModel',
        ]);

        $pdf =
            Pdf::loadView(
                'pdf.notices.billing-notice',
                [
                    'notice' =>
                        $notice,

                    'noticeTitle' =>
                        $this->getNoticeTitle(
                            $notice->notice_type
                        ),

                    'client' =>
                        $notice->user,

                    'billing' =>
                        $notice->billing,

                    'account' =>
                        $notice->purchaseAccount,

                    'lot' =>
                        $notice
                            ->purchaseAccount
                            ?->lot,
                ]
            );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        /*
        |--------------------------------------------------------------------------
        | Storage
        |--------------------------------------------------------------------------
        |
        | Private:
        |
        | storage/app/billing-notices/...
        |
        */

        $directory =
            'billing-notices/'
            . $notice->user_id
            . '/'
            . now()->format('Y');

        $filename =
            $notice->notice_type
            . '-billing-'
            . $notice->billing_id
            . '-'
            . $notice->notice_period
            . '.pdf';

        $path =
            $directory
            . '/'
            . $filename;

        Storage::disk(
            config(
                'billing_notices.disk',
                'local'
            )
        )->put(
            $path,
            $pdf->output()
        );

        return $path;
    }

    /**
     * Calculate deadline shown in notice.
     */
    private function getDeadline(
        string $noticeType
    ): ?string {
        $responseDays =
            (int) config(
                'billing_notices.response_days',
                7
            );

        return match ($noticeType) {

            'non_payment',
            'cancellation',
            'forfeiture' =>
                now()
                    ->addDays(
                        $responseDays
                    )
                    ->toDateString(),

            default =>
                null,
        };
    }

    /**
     * Human readable title.
     */
    private function getNoticeTitle(
        string $type
    ): string {
        return match ($type) {

            'monthly_payment' =>
                'Notice of Monthly Payment',

            'non_payment' =>
                'Notice of Non-Payment',

            'cancellation' =>
                'Notice of Cancellation',

            'forfeiture' =>
                'Notice of Forfeiture',

            default =>
                'Billing Notice',
        };
    }
}