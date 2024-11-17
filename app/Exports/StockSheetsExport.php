<?php

namespace App\Exports;

use App\Models\StockSheet;
use App\Services\ExportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class StockSheetsExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    private $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return StockSheet::query()
            ->whereIn('id', $this->ids)
            ->withSum('entries', 'quantity')
            ->withSum('entries', 'total_weight')
            ->withSum('entries', 'total_amount')
            ->get();
    }

    public function map($stock_sheet): array
    {
        return [
            $stock_sheet->id,
            Carbon::parse($stock_sheet->month)->format('M Y'),
            number_format($stock_sheet->entries_sum_quantity, 2),
            number_format($stock_sheet->entries_sum_total_weight, 2),
            number_format($stock_sheet->entries_sum_total_amount, 2),
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Month',
            'Total Quantity',
            'Total Weight',
            'Total Amount',
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function registerEvents(): array
    {
        $exportService = new ExportService();
        return $exportService->registerExportEvents(
            "Stock Sheets",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
