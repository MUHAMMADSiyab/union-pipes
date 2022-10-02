<?php

namespace App\Exports;

use App\Models\Utility;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class UtilitiesExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return Utility::whereIn('id', $this->ids)
            ->with('payment')
            ->orderBy('id', 'desc')
            ->get();;
    }

    public function map($utility): array
    {
        return [
            $utility->id,
            $utility->name,
            $utility->amount,
            $utility->payment->payment_method,
            $utility->payment->bank->name,
            $utility->payment->cheque_no,
            $utility->payment->cheque_type,
            $utility->payment->cheque_due_date,
            $utility->description,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Name',
            'Amount',
            'Payment method',
            'Bank',
            'Cheque no.',
            'Cheque type',
            'Cheque due date',
            'Description',
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
            "Utilities",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_LANDSCAPE,
            $this->collection()->count(),
        );
    }
}
