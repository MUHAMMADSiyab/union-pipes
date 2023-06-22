<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Purchase;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PurchasesExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return Purchase::query()
            ->with('company')
            ->whereIn('id', $this->ids)
            ->get();
    }

    public function map($purchase): array
    {
        return [
            $purchase->id,
            $purchase->date,
            $purchase->invoice_no,
            $purchase->company->name,
            $purchase->sales_tax_percentage,
            $purchase->category,
            $purchase->total_amount,
            $purchase->paid == 0 ? "0.00" : $purchase->paid,
            $purchase->balance == 0 ? "0.00" : $purchase->balance,
            $purchase->status,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Date',
            'Invoice #',
            'Company',
            'Sales Tax %',
            'Category',
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
            "Purchases",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
