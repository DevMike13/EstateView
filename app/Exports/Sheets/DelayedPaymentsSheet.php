<?php

namespace App\Exports\Sheets;

use App\Models\Billing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DelayedPaymentsSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle, WithStyles, WithEvents, WithColumnFormatting
{
    public function title(): string
    {
        return 'Delayed Payments';
    }

    public function headings(): array
    {
        return ['Client', 'Lot', 'Amount Due', 'Due Date', 'Days Delayed'];
    }

    public function collection()
    {
        return Billing::with(['purchaseAccount.user', 'purchaseAccount.lot'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->get()
            ->map(function ($billing) {
                return [
                    $billing->purchaseAccount?->user?->name ?? 'N/A',
                    $billing->purchaseAccount?->lot?->name ?? 'N/A',
                    $billing->amount_due - $billing->amount_paid,
                    $billing->due_date?->format('M d, Y'),
                    now()->diffInDays($billing->due_date),
                ];
            });
    }

    public function columnFormats(): array
    {
        return [
            'C' => '₱#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'Delayed Payments Report');
                $sheet->setCellValue('A2', 'Clients with overdue payments');

                $sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '991B1B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A2:E2')->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A3:E3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A3:E' . $sheet->getHighestRow())
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle('C4:C' . $sheet->getHighestRow())
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle('D4:E' . $sheet->getHighestRow())
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->freezePane('A4');
            },
        ];
    }
}
