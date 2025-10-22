<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Journal;
use App\Models\Staff;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard
     */
    public function index(Request $request)
    {
        $staffId = $request->get('staff_id');
        $clientId = $request->get('client_id');
        $quarter = $request->get('quarter', now()->quarter);
        $year = $request->get('year', now()->year);
        
        $startDate = Carbon::create($year, ($quarter - 1) * 3 + 1, 1);
        $endDate = $startDate->copy()->endOfQuarter();
        
        // Get clients based on staff assignment or all if admin
        $clients = Client::where('active', true);
        
        if ($staffId && $staffId !== 'all') {
            $clients->whereHas('staff', function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            });
        }
        
        $clients = $clients->get();
        
        // Get journal entries for the quarter
        $journalQuery = Journal::whereBetween('date', [$startDate, $endDate]);
        
        if ($clientId) {
            $journalQuery->where('client_id', $clientId);
        }
        
        if ($staffId && $staffId !== 'all') {
            $journalQuery->where('staff_id', $staffId);
        }
        
        $journalEntries = $journalQuery->with(['client', 'staff'])->get();
        
        // Calculate summary statistics
        $totalHours = $journalEntries->sum('hours');
        $billableHours = $journalEntries->where('billable', true)->sum('hours');
        
        return view('dashboard', compact(
            'clients', 'journalEntries', 'totalHours', 'billableHours',
            'quarter', 'year', 'staffId', 'clientId'
        ));
    }
}
