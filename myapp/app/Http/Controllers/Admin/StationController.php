<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StationController extends Controller
{
    /**
     * Display a listing of stations.
     */
    public function index(Request $request): View
    {
        $allStations = Station::query()
            ->withCount(['officers', 'cases'])
            ->latest('station_id')
            ->get();

        $stations = Station::query()
            ->with(['parent'])
            ->withCount(['officers', 'cases'])
            ->when($request->filled('directory_search'), function ($query) use ($request) {
                $search = $request->string('directory_search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('division', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('parent_id'), fn ($query) => $query->where('parent_id', $request->integer('parent_id')))
            ->orderByRaw("CASE type WHEN 'hq' THEN 1 WHEN 'metropolitanHQ' THEN 2 WHEN 'districtHQ' THEN 3 WHEN 'thana' THEN 4 ELSE 5 END")
            ->orderBy('division')
            ->orderBy('district')
            ->orderBy('name')
            ->get();

        $search = $request->string('search')->toString();

        $metroHqs = $this->headquartersQuery('metropolitanHQ', $search)
            ->orderBy('name')
            ->get();

        $districtHqs = $this->headquartersQuery('districtHQ', $search)
            ->orderBy('division')
            ->orderBy('district')
            ->orderBy('name')
            ->get();

        $summary = [
            'total_stations' => $allStations->count(),
            'active_stations' => $allStations
                ->filter(fn (Station $station) => strtolower($station->status) === 'active')
                ->count(),
            'total_officers' => (int) $allStations->sum('officers_count'),
            'total_cases' => (int) $allStations->sum('cases_count'),
        ];

        $parentStations = Station::query()
            ->whereIn('type', ['hq', 'metropolitanHQ', 'districtHQ'])
            ->orderByRaw("CASE type WHEN 'hq' THEN 1 WHEN 'metropolitanHQ' THEN 2 WHEN 'districtHQ' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->get(['station_id', 'name', 'type']);

        return view('admin.stations.index', compact('stations', 'summary', 'metroHqs', 'districtHqs', 'parentStations'));
    }

    /**
     * Show the form for creating a new station.
     */
    public function create(): View
    {
        return view('admin.stations.create');
    }

    /**
     * Store a newly created station.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['status'] = 'Active';

        Station::create($data);

        return redirect()
            ->route('admin.stations.index')
            ->with('success', 'Station created successfully.');
    }

    /**
     * Display a station and its current workload.
     */
    public function show(Station $station): View
    {
        if (in_array($station->type, ['metropolitanHQ', 'districtHQ'], true)) {
            $thanas = $station->children()
                ->where('type', 'thana')
                ->withCount(['officers', 'cases'])
                ->orderBy('name')
                ->get();

            $hqSummary = [
                'thanas' => $thanas->count(),
                'officers' => (int) $thanas->sum('officers_count'),
                'cases' => (int) $thanas->sum('cases_count'),
                'active_thanas' => $thanas->filter(fn (Station $thana) => strtolower($thana->status) === 'active')->count(),
            ];

            return view('admin.stations.hq-show', compact('station', 'thanas', 'hqSummary'));
        }

        $station->load([
            'officers' => fn ($query) => $query->orderBy('name'),
            'cases' => fn ($query) => $query
                ->with('officer')
                ->latest('date_filed')
                ->limit(5),
        ]);

        $totalCases = $station->cases()->count();
        $closedCases = $station->cases()
            ->whereRaw('LOWER(status) = ?', ['closed'])
            ->count();

        $caseStats = (object) [
            'total_cases' => $totalCases,
            'active_cases' => $totalCases - $closedCases,
            'closed_cases' => $closedCases,
        ];
        $officers = $station->officers;
        $recentCases = $station->cases;

        return view('admin.stations.show', compact(
            'station',
            'officers',
            'caseStats',
            'recentCases',
        ));
    }

    /**
     * Show the form for editing a station.
     */
    public function edit(Station $station): View
    {
        return view('admin.stations.edit', compact('station'));
    }

    /**
     * Update a station.
     */
    public function update(Request $request, Station $station): RedirectResponse
    {
        $station->update($this->validatedData($request, includeStatus: true));

        return redirect()
            ->route('admin.stations.index')
            ->with('success', 'Station updated successfully.');
    }

    /**
     * Activate or deactivate a station without deleting its records.
     */
    public function toggleStatus(Station $station): RedirectResponse
    {
        $station->update([
            'status' => strtolower($station->status) === 'active' ? 'Inactive' : 'Active',
        ]);

        $message = $station->status === 'Inactive'
            ? 'Station deactivated successfully.'
            : 'Station activated successfully.';

        return redirect()
            ->route('admin.stations.index')
            ->with('success', $message);
    }

    /**
     * Validate station form data.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, bool $includeStatus = false): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'contact_number' => ['nullable', 'string', 'max:15'],
        ];

        if ($includeStatus) {
            $rules['status'] = ['required', 'string', 'in:Active,Inactive'];
        }

        return $request->validate($rules);
    }

    private function headquartersQuery(string $type, string $search)
    {
        return Station::query()
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
            });
    }
}
