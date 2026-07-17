<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use App\Models\CaseFir;
use App\Models\CitizenComplaint;
use App\Models\Officer;
use App\Models\Station;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $headquarters = Station::query()->findOrFail($user->station_id);
        $stationIds = Station::query()
            ->where('parent_id', $headquarters->station_id)
            ->where('type', 'thana')
            ->pluck('station_id');
        $now = Carbon::now();

        $stats = [
            'total_stations' => $stationIds->count(),
            'total_officers' => Officer::query()->whereIn('station_id', $stationIds)->count(),
            'active_cases' => CaseFir::query()
                ->whereIn('station_id', $stationIds)
                ->whereRaw('LOWER(status) NOT IN (?, ?)', ['closed', 'transferred'])
                ->count(),
            'pending_complaints' => CitizenComplaint::query()
                ->whereIn('station_id', $stationIds)
                ->whereRaw('LOWER(status) IN (?, ?)', ['pending', 'under review'])
                ->count(),
            'station_ocs' => Officer::query()
                ->whereIn('station_id', $stationIds)
                ->where('is_oc', true)
                ->count(),
            'closed_this_month' => CaseFir::query()
                ->whereIn('station_id', $stationIds)
                ->whereRaw('LOWER(status) = ?', ['closed'])
                ->whereMonth('updated_at', $now->month)
                ->whereYear('updated_at', $now->year)
                ->count(),
        ];

        $recentCases = CaseFir::query()
            ->with(['station', 'officer'])
            ->whereIn('station_id', $stationIds)
            ->latest('date_filed')
            ->limit(5)
            ->get();

        $recentComplaints = CitizenComplaint::query()
            ->with('station')
            ->whereIn('station_id', $stationIds)
            ->latest('submitted_date')
            ->limit(5)
            ->get();

        return view('command.dashboard', compact(
            'headquarters',
            'stats',
            'recentCases',
            'recentComplaints',
        ));
    }
}
