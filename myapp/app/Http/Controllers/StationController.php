<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Station;

class StationController extends Controller
{
    public function index(Request $request): View
    {
        $headquarters = fn (string $type) => Station::query()
            ->where('type', $type)
            ->where(fn ($query) => $query->where('is_active', true)->orWhereRaw("LOWER(status) = 'active'"))
            ->withCount(['children as thanas_count' => fn ($query) => $query->where('type', 'thana')])
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search')->toString().'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $term)
                    ->orWhere('district', 'like', $term)
                    ->orWhere('division', 'like', $term)
                    ->orWhere('address', 'like', $term));
            })
            ->orderBy('name')
            ->get();

        $metroHqs = $headquarters('metropolitanHQ');
        $districtHqs = $headquarters('districtHQ');

        return view('stations.index', compact('metroHqs', 'districtHqs'));
    }

    public function show(Station $station): View
    {
        abort_unless(strtolower($station->status) === 'active', 404);

        if (in_array($station->type, ['metropolitanHQ', 'districtHQ'], true)) {
            $thanas = $station->children()
                ->where('type', 'thana')
                ->whereRaw("LOWER(status) = 'active'")
                ->withCount(['cases', 'officers'])
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

        $officers = $station->officers()
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->orderBy('rank')
            ->orderBy('name')
            ->get(['officer_id', 'name', 'rank']);

        return view('stations.show', compact('station', 'stats', 'officers'));
    }
}
