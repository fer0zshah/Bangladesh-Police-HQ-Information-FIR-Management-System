<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseFir;
use App\Models\CitizenComplaint;
use App\Models\Officer;
use App\Models\Station;
use App\Models\User;
use App\Traits\ScopedToJurisdiction;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HqMemberController extends Controller
{
    use ScopedToJurisdiction;

    public function index(Request $request): View
    {
        $members = User::query()
            ->with('station')
            ->whereIn('role', ['metro_head', 'district_head'])
            ->orderBy('role', 'desc')
            ->orderBy('name')
            ->get()
            ->map(fn (User $member) => $this->withScopeData($member));

        $allOfficers = Officer::query()
            ->with(['station', 'user'])
            ->withCount(['cases', 'evidence'])
            ->get();

        $officers = Officer::query()
            ->with(['station', 'user'])
            ->withCount([
                'cases',
                'evidence',
            ])
            ->when($request->filled('officer_search'), function ($query) use ($request) {
                $search = $request->string('officer_search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('rank', 'like', "%{$search}%")
                        ->orWhere('badge_number', 'like', "%{$search}%")
                        ->orWhereHas('station', fn ($stationQuery) => $stationQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('rank'), fn ($query) => $query->where('rank', $request->string('rank')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('station_id'), fn ($query) => $query->where('station_id', $request->integer('station_id')))
            ->when($request->filled('oc'), function ($query) use ($request) {
                if ($request->string('oc')->toString() === 'yes') {
                    $query->where('is_oc', true);
                } elseif ($request->string('oc')->toString() === 'no') {
                    $query->where('is_oc', false);
                }
            })
            ->orderByRaw("CASE WHEN is_oc = 1 THEN 0 ELSE 1 END")
            ->orderBy('rank')
            ->orderBy('name')
            ->get();

        $summary = [
            'metro_heads' => $members->where('role', 'metro_head')->count(),
            'district_heads' => $members->where('role', 'district_head')->count(),
            'total_officers' => $allOfficers->count(),
            'station_ocs' => $allOfficers->where('is_oc', true)->count(),
            'active_cases' => $members->sum(fn (User $member) => $member->scope_stats['active_cases']),
            'pending_complaints' => $members->sum(fn (User $member) => $member->scope_stats['pending_complaints']),
        ];

        $ranks = $allOfficers->pluck('rank')->filter()->unique()->sort()->values();
        $stations = Station::query()
            ->whereIn('station_id', $allOfficers->pluck('station_id')->filter()->unique())
            ->orderBy('name')
            ->get(['station_id', 'name', 'type']);

        return view('admin.hq-members', compact('members', 'officers', 'summary', 'ranks', 'stations'));
    }

    public function show(User $member): View
    {
        abort_unless(in_array($member->role, ['metro_head', 'district_head'], true), 404);

        $member = $this->withScopeData($member->load('station'));
        $thanaIds = $member->scope_stations
            ->where('type', 'thana')
            ->pluck('station_id')
            ->map(fn ($id) => (int) $id);

        $ocs = Officer::query()
            ->with(['station', 'user'])
            ->withCount([
                'cases',
                'cases as active_cases_count' => fn ($query) => $query->whereNotIn('status', ['Closed', 'closed', 'Transferred', 'transferred']),
            ])
            ->whereIn('station_id', $thanaIds)
            ->where('is_oc', true)
            ->orderBy('name')
            ->get();

        $summary = [
            'thanas' => $thanaIds->count(),
            'ocs' => $ocs->count(),
            'officers' => $this->officerCount($thanaIds),
            'active_cases' => $this->activeCaseCount($thanaIds),
        ];

        return view('admin.hq-members-show', compact('member', 'ocs', 'summary'));
    }

    public function oc(Officer $officer): View
    {
        abort_unless($officer->is_oc, 404);

        $officer->load(['station', 'user']);

        $officers = Officer::query()
            ->where('station_id', $officer->station_id)
            ->orderByRaw("CASE WHEN officer_id = ? THEN 0 ELSE 1 END", [$officer->officer_id])
            ->orderBy('rank')
            ->orderBy('name')
            ->get();

        $caseStats = [
            'total_cases' => CaseFir::query()->where('station_id', $officer->station_id)->count(),
            'active_cases' => CaseFir::query()
                ->where('station_id', $officer->station_id)
                ->whereNotIn('status', ['Closed', 'closed', 'Transferred', 'transferred'])
                ->count(),
            'closed_cases' => CaseFir::query()
                ->where('station_id', $officer->station_id)
                ->whereIn('status', ['Closed', 'closed'])
                ->count(),
        ];

        return view('admin.hq-members-oc-show', compact('officer', 'officers', 'caseStats'));
    }

    private function withScopeData(User $member): User
    {
        $stationIds = $this->jurisdictionStationIds($member);

        $member->role_label = $member->role === 'metro_head'
            ? 'Police Commissioner'
            : 'Superintendent of Police';

        $member->scope_label = $member->role === 'metro_head'
            ? 'Metropolitan command'
            : 'District command';

        $member->scope_stations = $this->scopeStations($stationIds);
        $member->scope_stats = [
            'stations' => $member->scope_stations->count(),
            'thanas' => $member->scope_stations->where('type', 'thana')->count(),
            'active_cases' => $this->activeCaseCount($stationIds),
            'officers' => $this->officerCount($stationIds),
            'pending_complaints' => $this->pendingComplaintCount($stationIds),
        ];

        return $member;
    }

    private function scopeStations(Collection $stationIds): Collection
    {
        if ($stationIds->isEmpty()) {
            return collect();
        }

        return Station::query()
            ->whereIn('station_id', $stationIds)
            ->orderByRaw("CASE type WHEN 'metropolitanHQ' THEN 1 WHEN 'districtHQ' THEN 1 WHEN 'thana' THEN 2 ELSE 3 END")
            ->orderBy('name')
            ->get();
    }

    private function activeCaseCount(Collection $stationIds): int
    {
        if ($stationIds->isEmpty()) {
            return 0;
        }

        return CaseFir::query()
            ->whereIn('station_id', $stationIds)
            ->whereNotIn('status', ['Closed', 'closed', 'Transferred', 'transferred'])
            ->count();
    }

    private function officerCount(Collection $stationIds): int
    {
        if ($stationIds->isEmpty()) {
            return 0;
        }

        return Officer::query()
            ->whereIn('station_id', $stationIds)
            ->count();
    }

    private function pendingComplaintCount(Collection $stationIds): int
    {
        if ($stationIds->isEmpty()) {
            return 0;
        }

        return CitizenComplaint::query()
            ->whereIn('station_id', $stationIds)
            ->whereIn('status', ['Pending', 'pending'])
            ->count();
    }
}
