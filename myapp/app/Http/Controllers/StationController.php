<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\CaseFir;
use App\Models\Station;

class StationController extends Controller
{
    public function index(Request $request): View
    {
        $stations = Station::query()
            ->withCount([
                'cases',
                'officers' => fn ($query) => $query->whereRaw('LOWER(status) = ?', ['active']),
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('district'), fn ($query) => $query->where('district', $request->string('district')->toString()))
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->orderBy('district')
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $districts = Station::query()
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->select('district')
            ->distinct()
            ->orderBy('district')
            ->pluck('district');

        return view('stations.index', compact('stations', 'districts'));
    }

    public function show(Station $station): View
    {
        abort_unless(strtolower($station->status) === 'active', 404);

        $station->loadCount(['cases', 'officers']);

        $stats = [
            'total_cases' => $station->cases()->count(),
            'active_cases' => $station->cases()->whereRaw("LOWER(status) NOT IN ('closed', 'transferred')")->count(),
            'closed_cases' => $station->cases()->whereRaw("LOWER(status) = 'closed'")->count(),
            'evidence_items' => $station->cases()->join('evidence', 'evidence.case_id', '=', 'case_firs.case_id')->count(),
        ];

        $cases = $station->cases()
            ->with('officer')
            ->withCount(['evidence', 'criminals'])
            ->whereRaw("LOWER(status) NOT IN ('closed', 'transferred')")
            ->orderByDesc('date_filed')
            ->orderByDesc('case_id')
            ->get();

        $officers = $station->officers()
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->orderBy('rank')
            ->orderBy('name')
            ->get(['officer_id', 'name', 'rank']);

        return view('stations.show', compact('station', 'stats', 'cases', 'officers'));
    }

    public function caseShow(Station $station, CaseFir $case): View
    {
        abort_unless(strtolower($station->status) === 'active', 404);
        abort_unless((int) $case->station_id === (int) $station->station_id, 404);

        $case->load(['officer', 'criminals' => fn ($query) => $query->select('criminals.criminal_id', 'name', 'alias', 'wanted_status')])
            ->loadCount(['evidence', 'criminals']);

        return view('stations.case-show', [
            'station' => $station,
            'case' => $case,
        ]);
    }
}
