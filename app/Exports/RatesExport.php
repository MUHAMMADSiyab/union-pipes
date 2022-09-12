<?php

namespace App\Exports;

use App\Models\Rate;
use App\Services\ExportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class RatesExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return Rate::whereIn('id', $this->ids)
            ->with('product')
            ->orderBy('change_date', 'desc')
            ->get();
    }

    public function map($rate): array
    {
        return [
            $rate->id,
            $rate->product->name,
            $rate->rate,
            Carbon::parse($rate->change_date)->format('M d, Y'),
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Product',
            'Rate',
            'Change Date',
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
            "Rates",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
