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
    public function index(): View
    {
        $stations = Station::query()
            ->withCount(['officers', 'cases'])
            ->latest('station_id')
            ->get();

        $summary = [
            'total_stations' => $stations->count(),
            'active_stations' => $stations
                ->filter(fn (Station $station) => strtolower($station->status) === 'active')
                ->count(),
            'total_officers' => (int) $stations->sum('officers_count'),
            'total_cases' => (int) $stations->sum('cases_count'),
        ];

        return view('admin.stations.index', compact('stations', 'summary'));
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
}
