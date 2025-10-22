<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;
    
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }
    
    /**
     * Generate quarterly report for a client
     */
    public function quarterly(Client $client, Request $request)
    {
        $quarter = $request->get('quarter', now()->quarter);
        $year = $request->get('year', now()->year);
        
        return $this->reportService->generateQuarterlyReport($client, $quarter, $year);
    }
    
    /**
     * Generate batch reports for all clients
     */
    public function batch(Request $request)
    {
        $quarter = $request->get('quarter', now()->quarter);
        $year = $request->get('year', now()->year);
        
        $clients = Client::where('active', true)->get();
        $files = [];
        
        foreach ($clients as $client) {
            $pdf = $this->reportService->generateQuarterlyReport($client, $quarter, $year);
            $files[] = $pdf;
        }
        
        // Create ZIP file with all reports
        // This would need additional implementation for ZIP creation
        return response()->json(['message' => 'Batch reports generated', 'count' => count($files)]);
    }
}
