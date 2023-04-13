<?php

namespace App\Exports;

use App\Models\Production;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ProductionsExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return Production::query()
            ->whereIn('id', $this->ids)
            ->get();
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->machine->name,
            $product->employee->name,
            $product->product->name,
            $product->date,
            $product->shift,
            $product->weight,
            $product->quantity,
            $product->total_weight,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Machine',
            'Operator',
            'Product',
            'Date',
            'Shift',
            'Weight',
            'Quantity',
            'Total Weight',
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
            "Productions",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
