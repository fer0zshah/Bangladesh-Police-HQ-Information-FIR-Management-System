<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StationController extends Controller
{
    public function index(Request $request): View
    {
        $headquarters = Station::query()->findOrFail(auth()->user()->station_id);

        $query = Station::query()
            ->where('parent_id', $headquarters->station_id)
            ->where('type', 'thana')
            ->withCount(['officers', 'cases', 'complaints']);

        $query
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->toString();
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('district', 'like', "%{$term}%")
                        ->orWhere('address', 'like', "%{$term}%")
                        ->orWhere('contact_number', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()));

        $stations = $query->orderBy('name')->get();
        $allStations = Station::query()
            ->where('parent_id', $headquarters->station_id)
            ->where('type', 'thana')
            ->withCount(['officers', 'cases'])
            ->get();

        $summary = [
            'total' => $allStations->count(),
            'active' => $allStations->filter(fn (Station $station) => strtolower($station->status) === 'active')->count(),
            'officers' => (int) $allStations->sum('officers_count'),
            'cases' => (int) $allStations->sum('cases_count'),
        ];

        return view('command.stations.index', compact('headquarters', 'stations', 'summary'));
    }

    public function show(Station $station): View
    {
        $headquarters = Station::query()->findOrFail(auth()->user()->station_id);
        abort_unless(
            $station->type === 'thana' && (int) $station->parent_id === (int) $headquarters->station_id,
            403,
            'This thana is outside your command.'
        );

        $station->load([
            'officers' => fn ($query) => $query->orderByDesc('is_oc')->orderBy('rank')->orderBy('name'),
            'cases' => fn ($query) => $query->with('officer')->latest('date_filed')->limit(8),
        ])->loadCount(['officers', 'cases', 'complaints']);

        $closedCases = $station->cases()
            ->whereRaw('LOWER(status) = ?', ['closed'])
            ->count();

        return view('command.stations.show', [
            'headquarters' => $headquarters,
            'station' => $station,
            'officers' => $station->officers,
            'recentCases' => $station->cases,
            'activeCases' => $station->cases_count - $closedCases,
            'closedCases' => $closedCases,
        ]);
    }
}
