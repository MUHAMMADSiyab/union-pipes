<?php

namespace App\Exports;

use App\Models\Sell;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SellsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell
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
        return Sell::whereIn('id', $this->ids)
            ->orderBy('sell_date', 'desc')
            ->get();;
    }

    public function map($sell): array
    {
        return [
            $sell->id,
            $sell->sell_date,
            $sell->petrol_price,
            $sell->diesel_price,
            $sell->total_initial_reading,
            $sell->total_final_reading,
            $sell->sold_quantity,
            $sell->initial_reading_amount,
            $sell->final_reading_amount,
            $sell->total_amount,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Sell Date',
            'Petrol Price',
            'Diesel Price',
            'Total Initial Reading (Ltrs.)',
            'Total Final Reading (Ltrs.)',
            'Quantity Sold (Ltrs.)',
            'Initial Reading Amount',
            'Final Reading Amount',
            'Total Sell Amount',
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
            "Sells",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_LANDSCAPE,
            $this->collection()->count(),
        );
    }
}
