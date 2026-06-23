<?php

namespace App\Exports;

use App\Exports\Sheets\AdvancedPaymentsSheet;
use App\Exports\Sheets\DelayedPaymentsSheet;
use App\Exports\Sheets\MonthlyCollectionsSheet;
use App\Exports\Sheets\PropertyStatusSheet;
use App\Exports\Sheets\SummarySheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReportsExport implements WithMultipleSheets
{

    public function sheets(): array
    {
        return [
            new SummarySheet(),
            new PropertyStatusSheet(),
            new MonthlyCollectionsSheet(),
            new DelayedPaymentsSheet(),
            new AdvancedPaymentsSheet(),
        ];
    }
}
