<?php

namespace App\Exports;

use App\Models\Account;
use App\Models\Customer;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class AccountsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell
{
    private $ids;
    private $customer;

    public function __construct($ids)
    {
        $this->ids = $ids;
        $this->customer = Customer::find(request('customer_id'));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Account::query()
            ->whereIn('id', $this->ids)
            ->where('customer_id', request('customer_id'))
            ->with('payment')
            ->orderBy('date')
            ->get();
    }

    public function map($account): array
    {
        return [
            $account->id,
            $account->date,
            $account->vehicle_no,
            $account->product,
            $account->product_price,
            $account->product_quantity,
            $account->total_amount,
            $account->payment?->amount,
            $account->balance,
            $account->payment?->payment_method,
            $account->payment?->bank->name,
            $account->payment?->cheque_no,
            $account->payment?->cheque_type,
            $account->payment?->cheque_due_date,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Date',
            'Vehicle',
            'Product',
            'Product Rate',
            'Product Quantity',
            'Total Amount',
            'Total Paid',
            'Balance',
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
            $this->customer->name . "'s Account Entries",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_LANDSCAPE,
            $this->collection()->count(),
        );
    }
}
