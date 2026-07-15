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
        $search = $request->string('search')->toString();

        $metroHqs = $this->headquartersQuery('metropolitanHQ', $search)
            ->orderBy('name')
            ->get();

        $districtHqs = $this->headquartersQuery('districtHQ', $search)
            ->orderBy('division')
            ->orderBy('district')
            ->orderBy('name')
            ->get();

        return view('stations.index', compact('metroHqs', 'districtHqs'));
    }

    public function show(Station $station): View
    {
        abort_unless(strtolower($station->status) === 'active', 404);

        if (in_array($station->type, ['metropolitanHQ', 'districtHQ'], true)) {
            $thanas = $station->children()
                ->where('type', 'thana')
                ->whereRaw('LOWER(status) = ?', ['active'])
                ->withCount([
                    'cases',
                    'officers' => fn ($query) => $query->whereRaw('LOWER(status) = ?', ['active']),
                ])
                ->orderBy('name')
                ->get();

            return view('stations.hq-show', compact('station', 'thanas'));
        }

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

    private function headquartersQuery(string $type, string $search)
    {
        return Station::query()
            ->where('type', $type)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->withCount([
                'children as thanas_count' => fn ($query) => $query
                    ->where('type', 'thana')
                    ->whereRaw('LOWER(status) = ?', ['active']),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('division', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            });
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
