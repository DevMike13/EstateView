<?php

namespace App\Exports\Sheets;

use App\Models\BillingPayment;
use App\Models\LotReservation;
use App\Models\PurchaseAccount;
use Illuminate\Support\Collection;
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

class SummarySheet implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle, WithStyles, WithEvents, WithColumnFormatting
{
    public function title(): string
    {
        return 'Summary';
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function collection()
    {
        return new Collection([
            ['Total Reservations', LotReservation::count()],
            ['Verified Payments', BillingPayment::where('status', 'verified')->count()],
            ['Total Revenue', BillingPayment::where('status', 'verified')->sum('amount')],
            ['Average Sale Price', PurchaseAccount::avg('total_contract_price') ?? 0],
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'B' => '₱#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
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
                $sheet->mergeCells('A1:B1');
                $sheet->setCellValue('A1', 'EstateView Reports Summary');
                $sheet->setCellValue('A2', 'Generated: ' . now()->format('M d, Y h:i A'));

                $sheet->getStyle('A1:B1')->applyFromArray($this->titleStyle());
                $sheet->getStyle('A2:B2')->applyFromArray($this->subTitleStyle());

                $this->styleTable($sheet, 'A3:B' . $sheet->getHighestRow());

                $sheet->freezePane('A4');
            },
        ];
    }

    private function titleStyle(): array
    {
        return [
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111827']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
    }

    private function subTitleStyle(): array
    {
        return [
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
    }

    private function styleTable(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($range)->getBorders()->getAllBorders()->getColor()->setRGB('E5E7EB');
        $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:B3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A4:A' . $sheet->getHighestRow())->getFont()->setBold(true);
        $sheet->getStyle('B4:B' . $sheet->getHighestRow())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
}
