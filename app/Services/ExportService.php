<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportService
{
    /**
     * Export Date
     *
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\BinaryFileResponse 
     */
    public function exportData(Request $request)
    {
        $parts = [];

        foreach (explode('_', $request->module) as $part) {
            array_push($parts, ucfirst($part));
        }

        $exportClassName = implode("", $parts);
        $exportClassPath =
            "App\\Exports\\" . $exportClassName . "Export";

        return $this->exportUsingType(
            $exportClassPath,
            $request->module,
            $request->exportType,
            $request->ids
        );
    }

    /**
     * Export using specific export type. i.e Excel, PDF, CSV
     *
     * @param string $exportClassPath
     * @param string $module
     * @param string $exportType
     * @param array $ids 
     * @return Symfony\Component\HttpFoundation\BinaryFileResponse 
     */
    public function exportUsingType(
        String $exportClassPath,
        String $module,
        String $exportType,
        array $ids
    ) {
        $options = [];

        switch ($exportType) {
            case 'xlsx':
                $options = [];
                break;

            case 'csv':
                $options = [\Maatwebsite\Excel\Excel::CSV, [
                    'Content-Type' => 'text\csv'
                ]];
                break;

            case 'pdf':
                $options = [\Maatwebsite\Excel\Excel::DOMPDF];
                break;
        }

        return Excel::download(
            new $exportClassPath($ids),
            $module . "." . $exportType,
            ...$options
        );
    }


    /**
     * Register export events
     *
     * @param String $title
     * @param String $range
     * @return array
     */
    public function registerExportEvents(
        String $title,
        String $range,
        string $orientation,
        int $dataCount,
        string $additionalRowData = null
    ): array {
        return [
            BeforeSheet::class => function (BeforeSheet $event) use ($orientation) {
                $event->sheet
                    ->getPageSetup()
                    ->setOrientation($orientation);
            },

            AfterSheet::class => function (AfterSheet $event) use ($title, $range, $dataCount, $additionalRowData) {
                $event->sheet->getDelegate()->mergeCells("A1:" . $range . "2");

                $event->sheet->getDelegate()->setCellValue(
                    "A1",
                    $title . " - " . Setting::first()->app_name
                );

                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setSize(18);

                // Additional row data
                if (!is_null($additionalRowData)) {
                    $event->sheet->getDelegate()->mergeCells("A3:" . $range . "3");

                    $event->sheet->getDelegate()->setCellValue(
                        "A3",
                        $additionalRowData
                    );
                }


                $cellsEnd = !is_null($additionalRowData) ? 4 : 3;
                $targetCell = "A1:" . $range . ((int)$dataCount + $cellsEnd);

                $event->sheet->getDelegate()->getStyle($targetCell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle($targetCell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }

    /**
     * Get heading cells range
     *
     * @param array $headings
     * @return string
     */
    public function getHeadingCellsRange(array $headings): string
    {
        return range("A", "Z")[count($headings) - 1];
    }
}
