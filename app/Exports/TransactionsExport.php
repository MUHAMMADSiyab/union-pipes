<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
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
        return Transaction::whereIn('id', $this->ids)
            ->with('payment')
            ->orderBy('id', 'desc')
            ->get();;
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->title,
            $transaction->type,
            $transaction->amount,
            $transaction->payment->payment_method,
            $transaction->payment->bank->name,
            $transaction->payment->cheque_no,
            $transaction->payment->cheque_type,
            $transaction->payment->cheque_due_date,
            $transaction->description,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Title',
            'Type',
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
            "Transactions",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_LANDSCAPE,
            $this->collection()->count(),
        );
    }
}
