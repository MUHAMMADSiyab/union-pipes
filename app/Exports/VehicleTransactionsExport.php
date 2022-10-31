<?php

namespace App\Exports;

use App\Models\VehicleTransaction;
use App\Models\Vehicle;
use App\Services\ExportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class VehicleTransactionsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithCustomStartCell
{
    private $ids;
    private $vehicle;

    public function __construct($ids)
    {
        $this->ids = $ids;
        $this->vehicle = Vehicle::find(request('vehicle_id'));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return VehicleTransaction::query()
            ->whereIn('id', $this->ids)
            ->where('vehicle_id', request('vehicle_id'))
            ->with('purchase', function ($q) {
                $q->select('id', 'invoice_no');
            })
            ->orderBy('date')
            ->get();
    }

    public function map($account): array
    {
        return [
            $account->id,
            $account->vehicle_charges,
            $account->expense,
            $account->balance,
            $account->driver,
            $account->purchase->invoice_no,
            $account->date,
        ];
    }

    public function headings(): array
    {
        return [
            'S#',
            'Vehicle Charges',
            'Expense',
            'Balance',
            'Driver',
            'Purchase (Invoice #)',
            'Date',
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
            $this->vehicle->name . "'s Transaction Entries",
            $exportService->getHeadingCellsRange($this->headings()),
            PageSetup::ORIENTATION_LANDSCAPE,
            $this->collection()->count(),
        );
    }
}
