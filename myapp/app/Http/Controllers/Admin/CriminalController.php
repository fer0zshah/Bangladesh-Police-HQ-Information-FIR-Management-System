<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criminal;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CriminalController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $metroHqs = $this->headquarters('metropolitanHQ', $search);
        $districtHqs = $this->headquarters('districtHQ', $search);

        $criminals = $this->criminalDictionaryQuery($request)
            ->paginate(15)
            ->withQueryString();

        $summary = (object) [
            'total' => Criminal::count(),
            'wanted' => Criminal::where('wanted_status', true)->count(),
            'linked' => Criminal::whereHas('cases')->count(),
            'hq_units' => $metroHqs->count() + $districtHqs->count(),
        ];

        $stations = Station::query()
            ->where('type', 'thana')
            ->orderBy('name')
            ->get(['station_id', 'name']);

        return view('admin.criminals', compact('criminals', 'districtHqs', 'metroHqs', 'stations', 'summary'));
    }

    public function hq(Station $station): View
    {
        abort_unless(in_array($station->type, ['metropolitanHQ', 'districtHQ'], true), 404);

        $thanas = $station->children()
            ->where('type', 'thana')
            ->orderBy('name')
            ->get();

        $thanas->each(function (Station $thana): void {
            $thana->criminals_count = $this->criminalCountForStations([$thana->station_id]);
            $thana->wanted_criminals_count = $this->criminalCountForStations([$thana->station_id], wanted: true);
            $thana->cases_count = $thana->cases()->count();
        });

        $hqSummary = [
            'thanas' => $thanas->count(),
            'criminals' => (int) $thanas->sum('criminals_count'),
            'wanted' => (int) $thanas->sum('wanted_criminals_count'),
            'cases' => (int) $thanas->sum('cases_count'),
        ];

        return view('admin.criminals.hq-show', compact('hqSummary', 'station', 'thanas'));
    }

    public function station(Request $request, Station $station): View
    {
        abort_unless($station->type === 'thana', 404);

        $criminals = $this->criminalDictionaryQuery($request)
            ->whereHas('cases', fn ($query) => $query->where('case_firs.station_id', $station->station_id))
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => $this->criminalCountForStations([$station->station_id]),
            'wanted' => $this->criminalCountForStations([$station->station_id], wanted: true),
            'cases' => $station->cases()->count(),
            'linked_cases' => $station->cases()->whereHas('criminals')->count(),
        ];

        return view('admin.criminals.station-show', compact('criminals', 'station', 'summary'));
    }

    public function edit(Criminal $criminal): View
    {
        return view('admin.criminals', compact('criminal'));
    }

    public function update(Request $request, Criminal $criminal)
    {
        $criminal->update($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'alias' => ['nullable', 'string', 'max:100'],
            'nid_number' => ['nullable', 'string', 'max:20', Rule::unique('criminals', 'nid_number')->ignore($criminal->criminal_id, 'criminal_id')],
            'date_of_birth' => ['nullable', 'date'],
        ]));

        return redirect()->route('admin.criminals.index')->with('success', 'Criminal record updated.');
    }

    public function toggleWanted(Criminal $criminal)
    {
        $criminal->update(['wanted_status' => ! $criminal->wanted_status]);

        return back()->with('success', 'Wanted status updated.');
    }

    private function criminalDictionaryQuery(Request $request)
    {
        return Criminal::query()
            ->with(['cases' => fn ($query) => $query->with(['station.parent'])->latest('date_filed')])
            ->withCount('cases')
            ->when($request->filled('criminal_search'), function ($query) use ($request) {
                $search = $request->string('criminal_search')->toString();
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('alias', 'like', "%{$search}%")
                        ->orWhere('nid_number', 'like', "%{$search}%")
                        ->orWhereHas('cases', fn ($query) => $query->where('case_title', 'like', "%{$search}%"));
                });
            })
            ->when($request->wanted === 'yes', fn ($query) => $query->where('wanted_status', true))
            ->when($request->wanted === 'no', fn ($query) => $query->where('wanted_status', false))
            ->when($request->filled('station_id'), fn ($query) => $query->whereHas('cases', fn ($query) => $query->where('case_firs.station_id', $request->integer('station_id'))))
            ->latest('criminal_id');
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
                        ->orWhere('division', 'like', "%{$search}%");
                });
            })
            ->orderBy($type === 'metropolitanHQ' ? 'name' : 'division')
            ->orderBy('district')
            ->orderBy('name')
            ->get();

        $hqs->each(function (Station $hq): void {
            $thanaIds = $hq->children()->where('type', 'thana')->pluck('station_id')->all();
            $hq->criminals_count = $this->criminalCountForStations($thanaIds);
            $hq->wanted_criminals_count = $this->criminalCountForStations($thanaIds, wanted: true);
        });

        return $hqs;
    }

    private function criminalCountForStations(array $stationIds, bool $wanted = false): int
    {
        if ($stationIds === []) {
            return 0;
        }

        return Criminal::query()
            ->when($wanted, fn ($query) => $query->where('wanted_status', true))
            ->whereHas('cases', fn ($query) => $query->whereIn('case_firs.station_id', $stationIds))
            ->distinct('criminals.criminal_id')
            ->count('criminals.criminal_id');
    }
}
