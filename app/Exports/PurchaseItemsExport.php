<?php

namespace App\Exports;

use App\Models\PurchaseItem;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PurchaseItemsExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
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
        return PurchaseItem::whereIn('id', $this->ids)->get();
    }

    public function map($purchase_item): array
    {
        return [
            $purchase_item->id,
            $purchase_item->name,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Name'
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
            "Purchase Items",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
