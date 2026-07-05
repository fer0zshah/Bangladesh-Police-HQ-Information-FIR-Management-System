<?php

namespace App\Http\Controllers\Oc;

use App\Http\Controllers\Controller;
use App\Models\CaseFir;
use App\Models\CitizenComplaint;
use App\Models\Evidence;
use App\Models\Officer;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $oc = Officer::query()
            ->with('station')
            ->where('user_id', auth()->id())
            ->where('is_oc', true)
            ->firstOrFail();

        abort_unless($oc->station_id && $oc->station, 403, 'This OC account is not assigned to a station.');

        $stationId = $oc->station_id;
        $stats = [
            'active_cases' => CaseFir::where('station_id', $stationId)
                ->whereRaw('LOWER(status) <> ?', ['closed'])->count(),
            'pending_complaints' => CitizenComplaint::where('station_id', $stationId)
                ->whereRaw('LOWER(status) = ?', ['pending'])->count(),
            'officers' => Officer::where('station_id', $stationId)->count(),
            'evidence' => Evidence::whereHas('case', fn ($query) => $query->where('station_id', $stationId))->count(),
            'closed_this_month' => CaseFir::where('station_id', $stationId)
                ->whereRaw('LOWER(status) = ?', ['closed'])
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),
        ];

        $recentCases = CaseFir::with('officer')
            ->where('station_id', $stationId)
            ->orderByDesc('date_filed')
            ->limit(5)
            ->get();

        $recentComplaints = CitizenComplaint::where('station_id', $stationId)
            ->orderByDesc('submitted_date')
            ->limit(5)
            ->get();

        return view('oc.dashboard', [
            'oc' => $oc,
            'station' => $oc->station,
            'stats' => $stats,
            'recentCases' => $recentCases,
            'recentComplaints' => $recentComplaints,
        ]);
    }
}
