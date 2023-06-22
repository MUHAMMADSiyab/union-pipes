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

class SellsExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return Sell::query()
            ->with('customer')
            ->when(request()->boolean('local'), function ($q) {
                $q->local();
            })
            ->when(!request()->boolean('local'), function ($q) {
                $q->notLocal();
            })
            ->whereIn('id', $this->ids)
            ->get();
    }

    public function map($sell): array
    {
        return [
            $sell->id,
            $sell->date,
            $sell->invoice_no,
            $sell->customer->name,
            $sell->sales_tax_percentage,
            $sell->category,
            $sell->unit,
            $sell->total_amount,
            $sell->paid == 0 ? "0.00" : $sell->paid,
            $sell->balance == 0 ? "0.00" : $sell->balance,
            $sell->status,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Date',
            'Invoice #',
            'Customer',
            'Sales Tax %',
            'Category',
            'Unit',
            'Total Amount',
            'Total Paid',
            'Balance',
            'Status'
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
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
