<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use App\Models\Evidence;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvidenceController extends Controller
{
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
        $query = Evidence::query()
            ->with(['case.station', 'officer'])
            ->whereHas('case', fn ($query) => $query->whereIn('station_id', $effectiveIds))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->toString();
                $query->where(function ($query) use ($term) {
                    $query->where('type', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhereHas('case', fn ($query) => $query->where('case_title', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('station_id'), function ($query) use ($request, $stationIds) {
                $stationId = $request->integer('station_id');
                abort_unless($stationIds->contains($stationId), 403);
                $query->whereHas('case', fn ($query) => $query->where('station_id', $stationId));
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->toString()))
            ->latest('collected_date')
            ->latest('evidence_id');

        $records = $query->paginate(15)->withQueryString();
        $base = Evidence::query()->whereHas('case', fn ($query) => $query->whereIn('station_id', $stationIds));
        $summary = [
            'total' => (clone $base)->count(),
            'primary' => (clone $base)->distinct('case_id')->count('case_id'),
            'secondary' => (clone $base)->distinct('type')->count('type'),
            'tertiary' => (clone $base)->whereDate('collected_date', '>=', now()->subDays(30))->count(),
        ];
        $thanas = Station::query()->whereIn('station_id', $stationIds)->orderBy('name')->get();
        $thanas->each(function (Station $station): void {
            $station->records_count = Evidence::query()->whereHas('case', fn ($query) => $query->where('station_id', $station->station_id))->count();
            $station->primary_count = Evidence::query()->whereHas('case', fn ($query) => $query->where('station_id', $station->station_id))->distinct('case_id')->count('case_id');
        });

        return view('command.records.index', [
            'module' => 'evidence',
            'title' => 'Evidence Registry',
            'headquarters' => $headquarters,
            'selectedStation' => $selectedStation,
            'records' => $records,
            'thanas' => $thanas,
            'summary' => $summary,
            'stations' => Station::query()->whereIn('station_id', $stationIds)->orderBy('name')->get(),
            'statuses' => Evidence::query()->whereHas('case', fn ($query) => $query->whereIn('station_id', $stationIds))->distinct()->orderBy('type')->pluck('type'),
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
