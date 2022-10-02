<?php

namespace App\Exports;

use App\Models\Bank;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class BanksExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
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
        return Bank::whereIn('id', $this->ids)->get();
    }

    public function map($bank): array
    {
        return [
            $bank->id,
            $bank->name,
            $bank->account_no,
            $bank->branch_name,
            $bank->branch_code,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Name',
            'Account no.',
            'Branch name',
            'Branch code',
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
            "Banks",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
