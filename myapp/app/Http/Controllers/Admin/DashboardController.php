<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\Officer;
use App\Models\CaseFir;
use App\Models\CitizenComplaint;
use App\Models\Criminal;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the Super Admin overview dashboard.
     */
    public function index()
    {
        $now = Carbon::now();

        // ── Overview stat cards ────────────────────────────────────────
        $stats = [
            'total_stations'   => Station::count(),
            'total_officers'   => Officer::count(),
            'active_cases'     => CaseFir::whereNotIn('status', ['Closed', 'closed'])->count(),
            'pending_complaints' => CitizenComplaint::where('status', 'Pending')->count(),
            'total_criminals'  => Criminal::count(),
            'closed_this_month' => CaseFir::whereIn('status', ['Closed', 'closed'])
                                          ->whereMonth('updated_at', $now->month)
                                          ->whereYear('updated_at', $now->year)
                                          ->count(),
        ];

        // ── Recent FIRs (last 5, newest first) ────────────────────────
        $recentCases = CaseFir::with(['station', 'officer'])
            ->latest('date_filed')
            ->limit(5)
            ->get();

        // ── Recent complaints (last 5) ─────────────────────────────────
        $recentComplaints = CitizenComplaint::with('station')
            ->latest('submitted_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentCases',
            'recentComplaints',
        ));
    }
}
