<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use App\Models\Criminal;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CriminalController extends Controller
{
    public function show(Criminal $criminal): View
    {
        [, $stationIds] = $this->scope();
        abort_unless($criminal->cases()->whereIn('case_firs.station_id', $stationIds)->exists(), 404);
        $criminal->load(['cases' => fn ($query) => $query->whereIn('case_firs.station_id', $stationIds)->with(['station','officer'])->latest('date_filed')]);

        return view('records.show', [
            'layout'=>'command-layout','pageTitle'=>$criminal->name,'type'=>'criminal','record'=>$criminal,
            'relatedCases'=>$criminal->cases->map(fn($case)=>(object)['case_id'=>$case->case_id,'case_title'=>$case->case_title,'station_name'=>$case->station?->name,'involvement_type'=>$case->pivot->involvement_type,'officer_name'=>$case->officer?->name,'date_filed'=>$case->date_filed,'status'=>$case->status]),
            'criminals'=>collect(),'evidence'=>collect(),'auditLogs'=>collect(),'linkedCase'=>null,
            'backUrl'=>route('command.criminals.index'),'backLabel'=>'Back to criminal directory','editUrl'=>null,
        ]);
    }

    public function index(Request $request): View
    {
        return $this->render($request);
    }

    public function station(Request $request, Station $station): View
    {
        $this->ensureStation($station);
        return $this->render($request, $station);
    }

    private function render(Request $request, ?Station $selectedStation = null): View
    {
        [$headquarters, $stationIds] = $this->scope();
        $effectiveIds = $selectedStation ? collect([(int) $selectedStation->station_id]) : $stationIds;

        $query = Criminal::query()
            ->whereHas('cases', fn ($query) => $query->whereIn('case_firs.station_id', $effectiveIds))
            ->with(['cases' => fn ($query) => $query
                ->whereIn('case_firs.station_id', $effectiveIds)
                ->with('station')
                ->latest('date_filed')])
            ->withCount(['cases' => fn ($query) => $query->whereIn('case_firs.station_id', $effectiveIds)])
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->toString();
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('alias', 'like', "%{$term}%")
                        ->orWhereHas('cases', fn ($query) => $query->where('case_title', 'like', "%{$term}%"));
                });
            })
            ->when($request->wanted === 'yes', fn ($query) => $query->where('wanted_status', true))
            ->when($request->wanted === 'no', fn ($query) => $query->where('wanted_status', false))
            ->when($request->filled('station_id'), function ($query) use ($request, $stationIds) {
                $stationId = $request->integer('station_id');
                abort_unless($stationIds->contains($stationId), 403);
                $query->whereHas('cases', fn ($query) => $query->where('case_firs.station_id', $stationId));
            })
            ->latest('criminal_id');

        $records = $query->paginate(15)->withQueryString();
        $base = Criminal::query()->whereHas('cases', fn ($query) => $query->whereIn('case_firs.station_id', $stationIds));
        $summary = [
            'total' => (clone $base)->count(),
            'primary' => (clone $base)->where('wanted_status', true)->count(),
            'secondary' => (clone $base)->where('wanted_status', false)->count(),
            'tertiary' => (clone $base)->whereHas('cases', fn ($query) => $query->whereIn('case_firs.station_id', $stationIds)->whereRaw("LOWER(status) NOT IN ('closed','transferred')"))->count(),
        ];

        $thanas = Station::query()->whereIn('station_id', $stationIds)->orderBy('name')->get();
        $thanas->each(function (Station $station): void {
            $station->records_count = Criminal::query()
                ->whereHas('cases', fn ($query) => $query->where('case_firs.station_id', $station->station_id))
                ->count();
            $station->primary_count = Criminal::query()
                ->where('wanted_status', true)
                ->whereHas('cases', fn ($query) => $query->where('case_firs.station_id', $station->station_id))
                ->count();
        });

        return view('command.records.index', [
            'module' => 'criminals',
            'title' => 'Criminal Registry',
            'headquarters' => $headquarters,
            'selectedStation' => $selectedStation,
            'records' => $records,
            'thanas' => $thanas,
            'summary' => $summary,
            'stations' => Station::query()->whereIn('station_id', $stationIds)->orderBy('name')->get(),
            'statuses' => collect(),
        ]);
    }

    private function scope(): array
    {
        $headquarters = Station::query()->findOrFail(auth()->user()->station_id);
        $ids = Station::query()->where('parent_id', $headquarters->station_id)->where('type', 'thana')->pluck('station_id')->map(fn ($id) => (int) $id);
        return [$headquarters, $ids];
    }

    private function ensureStation(Station $station): void
    {
        abort_unless($station->type === 'thana' && (int) $station->parent_id === (int) auth()->user()->station_id, 403, 'This thana is outside your command.');
    }
}
