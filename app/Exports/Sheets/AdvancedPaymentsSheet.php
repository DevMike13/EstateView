<?php

namespace App\Exports\Sheets;

use App\Models\BillingPayment;
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

class AdvancedPaymentsSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle, WithStyles, WithEvents, WithColumnFormatting
{
    public function title(): string
    {
        return 'Advanced Payments';
    }

    public function headings(): array
    {
        return ['Client', 'Lot', 'Amount Paid', 'Due Date', 'Paid At'];
    }

    public function collection()
    {
        return BillingPayment::with(['billing', 'purchaseAccount.user', 'purchaseAccount.lot'])
            ->where('status', 'verified')
            ->whereNotNull('paid_at')
            ->whereHas('billing', function ($query) {
                $query->whereColumn('billing_payments.paid_at', '<', 'billings.due_date');
            })
            ->latest('paid_at')
            ->get()
            ->map(function ($payment) {
                return [
                    $payment->purchaseAccount?->user?->name ?? 'N/A',
                    $payment->purchaseAccount?->lot?->name ?? 'N/A',
                    $payment->amount,
                    $payment->billing?->due_date?->format('M d, Y') ?? 'N/A',
                    $payment->paid_at?->format('M d, Y') ?? 'N/A',
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
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
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
                $sheet->setCellValue('A1', 'Advanced Payments Report');
                $sheet->setCellValue('A2', 'Clients who paid before due date');

                $sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A2:E2')->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('A3:E3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
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
