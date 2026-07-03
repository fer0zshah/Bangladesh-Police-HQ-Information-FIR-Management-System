<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseFir;
use App\Models\Officer;
use App\Models\Station;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalCases = CaseFir::count();
        $closedCases = CaseFir::whereRaw('LOWER(status) = ?', ['closed'])->count();
        $districtCrime = Station::leftJoin('case_firs', 'case_firs.station_id', '=', 'stations.station_id')
            ->selectRaw('stations.district label, COUNT(case_firs.case_id) total')
            ->groupBy('stations.district')->orderByDesc('total')->get();
        $stationCrime = Station::withCount('cases')->orderByDesc('cases_count')->limit(10)->get()
            ->map(fn ($station) => (object) ['label' => $station->name, 'district' => $station->district, 'total' => $station->cases_count]);
        $officerWorkload = Officer::leftJoin('stations', 'stations.station_id', '=', 'officers.station_id')
            ->leftJoin('case_firs', 'case_firs.investigating_officer_id', '=', 'officers.officer_id')
            ->selectRaw("officers.name, officers.badge_number, stations.name station_name, COUNT(case_firs.case_id) total, SUM(CASE WHEN LOWER(case_firs.status) <> 'closed' THEN 1 ELSE 0 END) active, SUM(CASE WHEN LOWER(case_firs.status) = 'closed' THEN 1 ELSE 0 END) closed")
            ->groupBy('officers.officer_id', 'officers.name', 'officers.badge_number', 'stations.name')
            ->orderByDesc('total')->limit(10)->get();
        $monthlyClosure = collect(range(11, 0))->map(function ($months) {
            $start = Carbon::now()->startOfMonth()->subMonths($months);
            $cases = CaseFir::whereBetween('date_filed', [$start, $start->copy()->endOfMonth()]);
            $filed = (clone $cases)->count();
            $closed = (clone $cases)->whereRaw('LOWER(status) = ?', ['closed'])->count();
            return (object) ['label' => $start->format('M Y'), 'filed' => $filed, 'closed' => $closed, 'rate' => $filed ? round(($closed / $filed) * 100, 1) : 0];
        });
        $crimeTypes = CaseFir::selectRaw('LOWER(TRIM(case_title)) label, COUNT(*) total')
            ->groupByRaw('LOWER(TRIM(case_title))')->orderByDesc('total')->limit(8)->get();
        $cards = (object) [
            'total_cases' => $totalCases,
            'closure_rate' => $totalCases ? round(($closedCases / $totalCases) * 100, 1) : 0,
            'active_officers' => Officer::whereRaw('LOWER(status) = ?', ['active'])->count(),
            'top_district' => $districtCrime->first()?->label ?? 'No data',
        ];
        return view('admin.analytics', compact('cards', 'districtCrime', 'stationCrime', 'officerWorkload', 'monthlyClosure', 'crimeTypes'));
    }
}
