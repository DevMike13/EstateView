<?php

namespace App\Console\Commands;

use App\Services\BillingNoticeService;
use Illuminate\Console\Command;

class ProcessBillingNotices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing-notices:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send automatic billing notices to clients';

    /**
     * Execute the console command.
     */
    public function handle(
        BillingNoticeService $service
    ): int {
        $this->info(
            'Processing automatic billing notices...'
        );

        $this->newLine();

        try {

            $stats = $service->process();

            $this->table(
                [
                    'Processed',
                    'Sent',
                    'Skipped',
                    'Failed',
                ],
                [[
                    $stats['processed'] ?? 0,
                    $stats['sent'] ?? 0,
                    $stats['skipped'] ?? 0,
                    $stats['failed'] ?? 0,
                ]]
            );

            $this->newLine();

            /*
             * Some individual notices failed.
             */
            if (($stats['failed'] ?? 0) > 0) {

                $this->warn(
                    'Some billing notices failed.'
                );

                $this->warn(
                    'Check storage/logs/laravel.log for details.'
                );

                return self::FAILURE;
            }

            /*
             * Nothing needed to be sent.
             */
            if (($stats['sent'] ?? 0) === 0) {

                $this->info(
                    'No billing notices needed to be sent today.'
                );

                return self::SUCCESS;
            }

            /*
             * Successfully sent notices.
             */
            $this->info(
                ($stats['sent'] ?? 0)
                . ' billing notice(s) sent successfully.'
            );

            return self::SUCCESS;

        } catch (\Throwable $e) {

            $this->error(
                'Billing notice processing failed.'
            );

            $this->error(
                $e->getMessage()
            );

            report($e);

            return self::FAILURE;
        }
    }
}
