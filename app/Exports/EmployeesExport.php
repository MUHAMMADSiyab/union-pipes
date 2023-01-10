<?php

namespace App\Exports;

use App\Models\Employee;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return Employee::whereIn('id', $this->ids)
            ->orderBy('id', 'desc')
            ->get();;
    }

    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->name,
            $employee->cnic,
            $employee->phone,
            $employee->join_date,
            $employee->address,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Name',
            'CNIC',
            'Phone',
            'Join Date',
            'Address',
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
            "Employees",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_LANDSCAPE,
            $this->collection()->count(),
        );
    }
}
