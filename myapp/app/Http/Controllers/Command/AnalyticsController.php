<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use App\Models\CaseFir;
use App\Models\Officer;
use App\Models\Station;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $headquarters = Station::query()->findOrFail(auth()->user()->station_id);
        $stationIds = Station::query()
            ->where('parent_id', $headquarters->station_id)
            ->where('type', 'thana')
            ->pluck('station_id');

        $totalCases = CaseFir::query()->whereIn('station_id', $stationIds)->count();
        $closedCases = CaseFir::query()
            ->whereIn('station_id', $stationIds)
            ->whereRaw("LOWER(status) = 'closed'")
            ->count();

        $stationCrime = Station::query()
            ->whereIn('station_id', $stationIds)
            ->withCount('cases')
            ->orderByDesc('cases_count')
            ->orderBy('name')
            ->get()
            ->map(fn (Station $station) => (object) [
                'label' => $station->name,
                'district' => $station->district,
                'total' => $station->cases_count,
            ]);

        $statusBreakdown = CaseFir::query()
            ->whereIn('station_id', $stationIds)
            ->selectRaw('status label, COUNT(*) total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $officerWorkload = Officer::query()
            ->join('stations', 'stations.station_id', '=', 'officers.station_id')
            ->leftJoin('case_firs', 'case_firs.investigating_officer_id', '=', 'officers.officer_id')
            ->whereIn('officers.station_id', $stationIds)
            ->selectRaw("officers.name, officers.badge_number, officers.rank, stations.name station_name,
                COUNT(case_firs.case_id) total,
                SUM(CASE WHEN case_firs.case_id IS NOT NULL AND LOWER(case_firs.status) NOT IN ('closed','transferred') THEN 1 ELSE 0 END) active,
                SUM(CASE WHEN case_firs.case_id IS NOT NULL AND LOWER(case_firs.status) = 'closed' THEN 1 ELSE 0 END) closed")
            ->groupBy('officers.officer_id', 'officers.name', 'officers.badge_number', 'officers.rank', 'stations.name')
            ->orderByDesc('total')
            ->orderBy('officers.name')
            ->limit(12)
            ->get();

        $monthlyClosure = collect(range(11, 0))->map(function (int $months) use ($stationIds) {
            $start = Carbon::now()->startOfMonth()->subMonths($months);
            $cases = CaseFir::query()
                ->whereIn('station_id', $stationIds)
                ->whereBetween('date_filed', [$start, $start->copy()->endOfMonth()]);
            $filed = (clone $cases)->count();
            $closed = (clone $cases)->whereRaw("LOWER(status) = 'closed'")->count();

            return (object) [
                'label' => $start->format('M Y'),
                'filed' => $filed,
                'closed' => $closed,
                'rate' => $filed ? round(($closed / $filed) * 100, 1) : 0,
            ];
        });

        $typeExpression = $this->crimeTypeExpression();
        $crimeTypes = CaseFir::query()
            ->whereIn('station_id', $stationIds)
            ->selectRaw("{$typeExpression} label, COUNT(*) total")
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        $cards = (object) [
            'total_cases' => $totalCases,
            'closure_rate' => $totalCases ? round(($closedCases / $totalCases) * 100, 1) : 0,
            'active_officers' => Officer::query()
                ->whereIn('station_id', $stationIds)
                ->whereRaw("LOWER(status) = 'active'")
                ->count(),
            'top_station' => $stationCrime->first()?->label ?? 'No data',
        ];

        return view('command.analytics', compact(
            'headquarters',
            'cards',
            'stationCrime',
            'statusBreakdown',
            'officerWorkload',
            'monthlyClosure',
            'crimeTypes',
        ));
    }

    private function crimeTypeExpression(): string
    {
        return "CASE
            WHEN LOWER(case_title) LIKE '%robbery%' OR LOWER(case_title) LIKE '%mugging%' THEN 'Robbery'
            WHEN LOWER(case_title) LIKE '%theft%' OR LOWER(case_title) LIKE '%snatching%' OR LOWER(case_title) LIKE '%burglary%' OR LOWER(case_title) LIKE '%pickpocket%' THEN 'Theft'
            WHEN LOWER(case_title) LIKE '%fraud%' OR LOWER(case_title) LIKE '%cyber%' OR LOWER(case_title) LIKE '%blackmail%' THEN 'Fraud and Cybercrime'
            WHEN LOWER(case_title) LIKE '%assault%' OR LOWER(case_title) LIKE '%clash%' THEN 'Assault'
            WHEN LOWER(case_title) LIKE '%extortion%' THEN 'Extortion'
            WHEN LOWER(case_title) LIKE '%forgery%' THEN 'Forgery'
            WHEN LOWER(case_title) LIKE '%smuggling%' THEN 'Smuggling'
            ELSE 'Other'
        END";
    }
}
