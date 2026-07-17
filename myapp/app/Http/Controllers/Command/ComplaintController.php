<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use App\Models\CitizenComplaint;
use App\Models\Station;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function show(CitizenComplaint $complaint): View
    {
        $this->ensureStation($complaint->station);
        $complaint->load(['station.parent', 'caseFir.officer']);
        $linked = $complaint->caseFir;

        return view('records.show', [
            'layout'=>'command-layout','pageTitle'=>'Complaint #'.$complaint->complaint_id,'type'=>'complaint',
            'record'=>(object)['complaint_id'=>$complaint->complaint_id,'complainant_name'=>$complaint->complainant_name,'complainant_nid'=>$complaint->complainant_nid,'description'=>$complaint->description,'submitted_date'=>$complaint->submitted_date,'status'=>$complaint->status,'station_name'=>$complaint->station?->name,'command_name'=>$complaint->station?->parent?->name],
            'linkedCase'=>$linked?(object)['case_id'=>$linked->case_id,'case_title'=>$linked->case_title,'officer_name'=>$linked->officer?->name,'status'=>$linked->status]:null,
            'criminals'=>collect(),'evidence'=>collect(),'auditLogs'=>collect(),'relatedCases'=>collect(),
            'backUrl'=>route('command.complaints.index'),'backLabel'=>'Back to complaint directory','editUrl'=>null,
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
        $query = CitizenComplaint::query()
            ->with(['station', 'caseFir'])
            ->whereIn('station_id', $stationIds)
            ->when($selectedStation, fn ($query) => $query->where('station_id', $selectedStation->station_id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->toString();
                $query->where(function ($query) use ($term) {
                    $query->where('complainant_name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('complaint_id', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('station_id'), function ($query) use ($request, $stationIds) {
                $stationId = $request->integer('station_id');
                abort_unless($stationIds->contains($stationId), 403);
                $query->where('station_id', $stationId);
            })
            ->when($request->filled('status'), fn ($query) => $query->whereRaw('LOWER(status) = ?', [strtolower($request->string('status')->toString())]))
            ->latest('submitted_date')
            ->latest('complaint_id');

        $records = $query->paginate(15)->withQueryString();
        $all = CitizenComplaint::query()->whereIn('station_id', $stationIds);
        $summary = [
            'total' => (clone $all)->count(),
            'primary' => (clone $all)->whereRaw("LOWER(status) = 'pending'")->count(),
            'secondary' => (clone $all)->whereRaw("LOWER(status) = 'under review'")->count(),
            'tertiary' => (clone $all)->whereRaw("LOWER(status) = 'escalated'")->count(),
        ];
        $thanas = Station::query()
            ->whereIn('station_id', $stationIds)
            ->withCount([
                'complaints as records_count',
                'complaints as primary_count' => fn (Builder $query) => $query->whereRaw("LOWER(status) IN ('pending','under review')"),
            ])
            ->orderBy('name')
            ->get();

        return view('command.records.index', [
            'module' => 'complaints',
            'title' => 'Citizen Complaints',
            'headquarters' => $headquarters,
            'selectedStation' => $selectedStation,
            'records' => $records,
            'thanas' => $thanas,
            'summary' => $summary,
            'stations' => Station::query()->whereIn('station_id', $stationIds)->orderBy('name')->get(),
            'statuses' => CitizenComplaint::query()->whereIn('station_id', $stationIds)->distinct()->orderBy('status')->pluck('status'),
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
