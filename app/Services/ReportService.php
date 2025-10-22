<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Journal;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate a quarterly report for a client
     */
    public function generateQuarterlyReport(Client $client, int $quarter, int $year): string
    {
        $startDate = Carbon::create($year, ($quarter - 1) * 3 + 1, 1);
        $endDate = $startDate->copy()->endOfQuarter();
        
        // Get journal entries for the quarter
        $journalEntries = $client->journal()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('billable', true)
            ->orderBy('date', 'desc')
            ->get();
            
        // Get attendance for the year
        $attendance = $client->attendance()
            ->where('year', $year)
            ->get();
            
        // Calculate hours usage
        $totalHours = $journalEntries->sum('hours');
        $hoursRemaining = $client->total_hours - $totalHours;
        
        // Calculate usage status
        $usageStatus = $this->calculateUsageStatus($totalHours, $client->total_hours, $quarter);
        
        // Calculate contact pattern
        $contactCategories = $journalEntries->groupBy('category')->count();
        $hasEstablishedPattern = $contactCategories >= 2;
        
        $data = [
            'client' => $client,
            'quarter' => $quarter,
            'year' => $year,
            'journal_entries' => $journalEntries,
            'attendance' => $attendance,
            'total_hours' => $totalHours,
            'hours_remaining' => $hoursRemaining,
            'usage_status' => $usageStatus,
            'has_established_pattern' => $hasEstablishedPattern,
            'contact_categories' => $contactCategories,
        ];
        
        $pdf = Pdf::loadView('reports.quarterly', $data);
        $filename = "CBLD_{$year}_Q{$quarter}-" . $this->slugify($client->name) . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Calculate hours usage status
     */
    private function calculateUsageStatus(float $usedHours, float $totalHours, int $quarter): string
    {
        if ($totalHours == 0) return 'no_plan';
        
        $expected = $totalHours * ($quarter / 4);
        $tolerance = 0.2;
        $lowerBound = $expected * (1 - $tolerance);
        $upperBound = $expected * (1 + $tolerance);
        
        if ($usedHours > $totalHours) {
            return 'over';
        } elseif ($usedHours < $lowerBound) {
            return 'behind';
        } elseif ($usedHours > $upperBound) {
            return 'ahead';
        } else {
            return 'balanced';
        }
    }
    
    /**
     * Create a URL-friendly slug
     */
    private function slugify(string $text): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }
    
    /**
     * Get usage status message
     */
    public function getUsageStatusMessage(string $status): string
    {
        return match($status) {
            'over' => 'Over budget',
            'behind' => 'Behind pace',
            'ahead' => 'Ahead of pace',
            'balanced' => 'Balanced',
            default => 'N/A'
        };
    }
}
