<?php

namespace App\Exports;

use App\Models\Purchase;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PurchasesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell
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
        return Purchase::whereIn('id', $this->ids)
            ->with(['payment', 'payment.bank', 'company'])
            ->orderBy('id', 'desc')
            ->get();;
    }

    public function map($purchase): array
    {
        return [
            $purchase->id,
            $purchase->date,
            $purchase->invoice_no,
            $purchase->company->name,
            $purchase->petrol_quantity,
            $purchase->diesel_quantity,
            $purchase->petrol_price,
            $purchase->diesel_price,
            $purchase->total_amount,
            $purchase->payment->amount,
            $purchase->payment->payment_method,
            $purchase->payment->bank->name,
            $purchase->payment->cheque_no,
            $purchase->payment->cheque_type,
            $purchase->payment->cheque_due_date,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Date',
            'Invoice #',
            'Company',
            'Petrol Quantity',
            'Diesel Quantity',
            'Petrol Price',
            'Diesel Price',
            'Total Amount',
            'Amount Paid',
            'Payment method',
            'Bank',
            'Cheque no.',
            'Cheque type',
            'Cheque due date',
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
            "Purchases",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_LANDSCAPE,
            $this->collection()->count(),
        );
    }
}
