<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\Product;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    private $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function getPayments()
    {
        return Payment::query()
            ->with('bank')
            ->whereIn('id', $this->ids)
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->getPayments();
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->amount,
            $payment->payment_date,
            $payment->payment_method,
            $payment->bank->name,
            $payment->description,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Amount',
            'Payment Date',
            'Payment Method',
            'Bank',
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
            explode("\\", $this->getPayments()[0]->model)[2] .  " Payments",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_PORTRAIT,
            $this->collection()->count(),
        );
    }
}
