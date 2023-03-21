<?php

namespace App\Exports;

use App\Models\Expense;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return Expense::whereIn('id', $this->ids)
            ->with('payment')
            ->orderBy('id', 'desc')
            ->get();;
    }

    public function map($expense): array
    {
        return [
            $expense->id,
            $expense->name,
            $expense->amount,
            $expense->payment->payment_method,
            $expense->payment->bank->name,
            $expense->payment->cheque_no,
            $expense->payment->cheque_type,
            $expense->payment->cheque_due_date,
            $expense->description,
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
            "Expenses",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_LANDSCAPE,
            $this->collection()->count(),
        );
    }
}
