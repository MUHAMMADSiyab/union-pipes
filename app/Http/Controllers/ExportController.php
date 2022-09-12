<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    /**
     * Send the exported data object in response
     *
     * @param Request $request
     * @param ExportService $exportService
     * @return Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request, ExportService $exportService)
    {
        return $exportService->exportData($request);
    }
}
