<?php

namespace App\Exports;

use App\Models\RawMaterial;
use App\Services\ExportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class RawMaterialsExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return RawMaterial::query()
            ->whereIn('id', $this->ids)
            ->withSum('entries', 'amount')
            ->get();
    }

    public function map($raw_material): array
    {
        return [
            $raw_material->id,
            Carbon::parse($raw_material->month)->format('M Y'),
            number_format($raw_material->entries_sum_amount, 2),
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Month',
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
            "Raw Materials",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
