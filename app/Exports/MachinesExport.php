<?php

namespace App\Exports;

use App\Models\Machine;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class MachinesExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return Machine::query()
            ->whereIn('id', $this->ids)
            ->get();
    }

    public function map($machine): array
    {
        return [
            $machine->id,
            $machine->name,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Name',
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
            "Machines",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
