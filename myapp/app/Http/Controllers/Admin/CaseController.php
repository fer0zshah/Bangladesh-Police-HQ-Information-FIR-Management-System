<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseFir;
use App\Models\Station;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    public function index(Request $request): View
    {
        $allCases = CaseFir::query()->get(['case_id', 'status']);

        $search = $request->string('search')->toString();

        $metroHqs = $this->headquarters('metropolitanHQ', $search);
        $districtHqs = $this->headquarters('districtHQ', $search);

        $cases = $this->caseDictionaryQuery($request)
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total_cases' => $allCases->count(),
            'active_cases' => $allCases
                ->reject(fn (CaseFir $case) => in_array(strtolower($case->status), ['closed', 'transferred'], true))
                ->count(),
            'closed_cases' => $allCases
                ->filter(fn (CaseFir $case) => strtolower($case->status) === 'closed')
                ->count(),
            'hq_units' => $metroHqs->count() + $districtHqs->count(),
        ];

        $stations = Station::query()
            ->where('type', 'thana')
            ->orderBy('name')
            ->get(['station_id', 'name']);

        $statuses = CaseFir::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->get();

        return view('admin.cases', compact(
            'cases',
            'districtHqs',
            'metroHqs',
            'stations',
            'statuses',
            'summary',
        ));
    }

    public function hq(Request $request, Station $station): View
    {
        abort_unless(in_array($station->type, ['metropolitanHQ', 'districtHQ'], true), 404);

        $thanas = $station->children()
            ->where('type', 'thana')
            ->withCount([
                'cases',
                'cases as active_cases_count' => fn (Builder $query) => $query->whereRaw("LOWER(status) NOT IN ('closed', 'transferred')"),
                'cases as closed_cases_count' => fn (Builder $query) => $query->whereRaw("LOWER(status) = 'closed'"),
                'officers',
            ])
            ->orderBy('name')
            ->get();

        $hqSummary = [
            'thanas' => $thanas->count(),
            'cases' => (int) $thanas->sum('cases_count'),
            'active_cases' => (int) $thanas->sum('active_cases_count'),
            'closed_cases' => (int) $thanas->sum('closed_cases_count'),
        ];

        return view('admin.cases.hq-show', compact('hqSummary', 'station', 'thanas'));
    }

    public function station(Request $request, Station $station): View
    {
        abort_unless($station->type === 'thana', 404);

        $cases = $this->caseDictionaryQuery($request)
            ->where('station_id', $station->station_id)
            ->paginate(15)
            ->withQueryString();

        $statuses = CaseFir::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->get();

        $totalCases = $station->cases()->count();
        $closedCases = $station->cases()->whereRaw("LOWER(status) = 'closed'")->count();

        $summary = [
            'total_cases' => $totalCases,
            'active_cases' => $totalCases - $closedCases,
            'closed_cases' => $closedCases,
            'officers' => $station->officers()->count(),
        ];

        return view('admin.cases.station-show', compact('cases', 'station', 'statuses', 'summary'));
    }

    private function caseDictionaryQuery(Request $request)
    {
        return CaseFir::query()
            ->with(['station.parent', 'officer'])
            ->withCount(['criminals', 'evidence'])
            ->when($request->filled('case_search'), function ($query) use ($request) {
                $search = $request->string('case_search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('case_title', 'like', "%{$search}%")
                        ->orWhere('case_id', 'like', "%{$search}%")
                        ->orWhereHas('station', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('officer', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->whereRaw('LOWER(status) = ?', [strtolower($request->string('status')->toString())]))
            ->when($request->filled('station_id'), fn ($query) => $query->where('station_id', $request->integer('station_id')))
            ->latest('date_filed')
            ->latest('case_id');
    }

    private function headquarters(string $type, string $search)
    {
        $hqs = Station::query()
            ->where('type', $type)
            ->withCount([
                'children as thanas_count' => fn ($query) => $query->where('type', 'thana'),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('division', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->orderBy($type === 'metropolitanHQ' ? 'name' : 'division')
            ->orderBy('district')
            ->orderBy('name')
            ->get();

        $hqs->each(function (Station $hq): void {
            $thanaIds = $hq->children()->where('type', 'thana')->pluck('station_id');

            $hq->cases_count = CaseFir::query()
                ->whereIn('station_id', $thanaIds)
                ->count();

            $hq->active_cases_count = CaseFir::query()
                ->whereIn('station_id', $thanaIds)
                ->whereRaw("LOWER(status) NOT IN ('closed', 'transferred')")
                ->count();
        });

        return $hqs;
    }
}
