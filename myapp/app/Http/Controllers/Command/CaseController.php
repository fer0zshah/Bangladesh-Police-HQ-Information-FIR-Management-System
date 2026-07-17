<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use App\Models\CaseFir;
use App\Models\Station;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    public function show(CaseFir $case): View
    {
        $this->ensureStation($case->station);
        $case->load(['station.parent', 'officer', 'criminals', 'evidence.officer', 'auditLogs.user']);

        return view('records.show', [
            'layout' => 'command-layout', 'pageTitle' => 'FIR #'.$case->case_id, 'type' => 'case',
            'record' => (object) [
                'case_id' => $case->case_id, 'case_title' => $case->case_title, 'status' => $case->status,
                'date_filed' => $case->date_filed, 'station_name' => $case->station?->name,
                'command_name' => $case->station?->parent?->name, 'officer_name' => $case->officer?->name,
                'complaint_id' => $case->complaint_id,
            ],
            'criminals' => $case->criminals->map(fn ($criminal) => (object) ['name'=>$criminal->name,'alias'=>$criminal->alias,'involvement_type'=>$criminal->pivot->involvement_type]),
            'evidence' => $case->evidence->map(fn ($item) => (object) ['type'=>$item->type,'description'=>$item->description,'collected_date'=>$item->collected_date,'officer_name'=>$item->officer?->name]),
            'auditLogs' => $case->auditLogs->map(fn ($log) => (object) ['action'=>$log->action,'old_status'=>$log->old_status,'new_status'=>$log->new_status,'details'=>$log->details,'created_at'=>$log->created_at,'user_name'=>$log->user?->name]),
            'relatedCases'=>collect(),'linkedCase'=>null,'backUrl'=>route('command.cases.index'),'backLabel'=>'Back to FIR directory','editUrl'=>null,
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
        $query = CaseFir::query()
            ->with(['station', 'officer'])
            ->withCount(['criminals', 'evidence'])
            ->whereIn('station_id', $stationIds)
            ->when($selectedStation, fn ($query) => $query->where('station_id', $selectedStation->station_id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->toString();
                $query->where(function ($query) use ($term) {
                    $query->where('case_title', 'like', "%{$term}%")
                        ->orWhere('case_id', 'like', "%{$term}%")
                        ->orWhereHas('officer', fn ($query) => $query->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('station_id'), function ($query) use ($request, $stationIds) {
                $stationId = $request->integer('station_id');
                abort_unless($stationIds->contains($stationId), 403);
                $query->where('station_id', $stationId);
            })
            ->when($request->filled('status'), fn ($query) => $query->whereRaw('LOWER(status) = ?', [strtolower($request->string('status')->toString())]))
            ->latest('date_filed')
            ->latest('case_id');

        $records = $query->paginate(15)->withQueryString();
        $all = CaseFir::query()->whereIn('station_id', $stationIds);
        $summary = [
            'total' => (clone $all)->count(),
            'primary' => (clone $all)->whereRaw("LOWER(status) NOT IN ('closed','transferred')")->count(),
            'secondary' => (clone $all)->whereRaw("LOWER(status) = 'closed'")->count(),
            'tertiary' => (clone $all)->whereRaw("LOWER(status) = 'transferred'")->count(),
        ];
        $thanas = Station::query()
            ->whereIn('station_id', $stationIds)
            ->withCount([
                'cases as records_count',
                'cases as primary_count' => fn (Builder $query) => $query->whereRaw("LOWER(status) NOT IN ('closed','transferred')"),
            ])
            ->orderBy('name')
            ->get();

        return view('command.records.index', [
            'module' => 'cases',
            'title' => 'Case FIRs',
            'headquarters' => $headquarters,
            'selectedStation' => $selectedStation,
            'records' => $records,
            'thanas' => $thanas,
            'summary' => $summary,
            'stations' => Station::query()->whereIn('station_id', $stationIds)->orderBy('name')->get(),
            'statuses' => CaseFir::query()->whereIn('station_id', $stationIds)->distinct()->orderBy('status')->pluck('status'),
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
