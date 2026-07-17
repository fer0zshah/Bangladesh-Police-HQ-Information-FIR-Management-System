<?php

namespace App\Http\Controllers\Admin;

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
        $complaint->load(['station.parent', 'caseFir.officer']);
        $linked = $complaint->caseFir;

        return view('records.show', [
            'layout' => 'admin-layout', 'pageTitle' => 'Complaint #'.$complaint->complaint_id,
            'type' => 'complaint', 'record' => (object) [
                'complaint_id' => $complaint->complaint_id, 'complainant_name' => $complaint->complainant_name,
                'complainant_nid' => $complaint->complainant_nid, 'description' => $complaint->description,
                'submitted_date' => $complaint->submitted_date, 'status' => $complaint->status,
                'station_name' => $complaint->station?->name, 'command_name' => $complaint->station?->parent?->name,
            ],
            'linkedCase' => $linked ? (object) [
                'case_id' => $linked->case_id, 'case_title' => $linked->case_title,
                'officer_name' => $linked->officer?->name, 'status' => $linked->status,
            ] : null,
            'criminals' => collect(), 'evidence' => collect(), 'auditLogs' => collect(), 'relatedCases' => collect(),
            'backUrl' => route('admin.complaints.index'), 'backLabel' => 'Back to complaint directory', 'editUrl' => null,
        ]);
    }

    public function index(Request $request): View
    {
        $allComplaints = CitizenComplaint::query()->get(['complaint_id', 'status']);

        $search = $request->string('search')->toString();

        $metroHqs = $this->headquarters('metropolitanHQ', $search);
        $districtHqs = $this->headquarters('districtHQ', $search);

        $complaints = $this->complaintDictionaryQuery($request)
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total_complaints' => $allComplaints->count(),
            'pending_complaints' => $allComplaints
                ->filter(fn (CitizenComplaint $complaint) => strtolower($complaint->status) === 'pending')
                ->count(),
            'review_complaints' => $allComplaints
                ->filter(fn (CitizenComplaint $complaint) => strtolower($complaint->status) === 'under review')
                ->count(),
            'escalated_complaints' => $allComplaints
                ->filter(fn (CitizenComplaint $complaint) => strtolower($complaint->status) === 'escalated')
                ->count(),
        ];

        $stations = Station::query()
            ->where('type', 'thana')
            ->orderBy('name')
            ->get(['station_id', 'name']);

        $statuses = CitizenComplaint::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->get();

        return view('admin.complaints', compact(
            'complaints',
            'districtHqs',
            'metroHqs',
            'stations',
            'statuses',
            'summary',
        ));
    }

    public function hq(Station $station): View
    {
        abort_unless(in_array($station->type, ['metropolitanHQ', 'districtHQ'], true), 404);

        $thanas = $station->children()
            ->where('type', 'thana')
            ->withCount([
                'complaints',
                'complaints as pending_complaints_count' => fn (Builder $query) => $query->whereRaw("LOWER(status) = 'pending'"),
                'complaints as review_complaints_count' => fn (Builder $query) => $query->whereRaw("LOWER(status) = 'under review'"),
                'complaints as escalated_complaints_count' => fn (Builder $query) => $query->whereRaw("LOWER(status) = 'escalated'"),
            ])
            ->orderBy('name')
            ->get();

        $hqSummary = [
            'thanas' => $thanas->count(),
            'complaints' => (int) $thanas->sum('complaints_count'),
            'pending' => (int) $thanas->sum('pending_complaints_count'),
            'escalated' => (int) $thanas->sum('escalated_complaints_count'),
        ];

        return view('admin.complaints.hq-show', compact('hqSummary', 'station', 'thanas'));
    }

    public function station(Request $request, Station $station): View
    {
        abort_unless($station->type === 'thana', 404);

        $complaints = $this->complaintDictionaryQuery($request)
            ->where('station_id', $station->station_id)
            ->paginate(15)
            ->withQueryString();

        $statuses = CitizenComplaint::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->get();

        $summary = [
            'total_complaints' => $station->complaints()->count(),
            'pending_complaints' => $station->complaints()->whereRaw("LOWER(status) = 'pending'")->count(),
            'review_complaints' => $station->complaints()->whereRaw("LOWER(status) = 'under review'")->count(),
            'escalated_complaints' => $station->complaints()->whereRaw("LOWER(status) = 'escalated'")->count(),
        ];

        return view('admin.complaints.station-show', compact('complaints', 'station', 'statuses', 'summary'));
    }

    private function complaintDictionaryQuery(Request $request)
    {
        return CitizenComplaint::query()
            ->with(['station.parent', 'caseFir'])
            ->when($request->filled('complaint_search'), function ($query) use ($request) {
                $search = $request->string('complaint_search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('complainant_name', 'like', "%{$search}%")
                        ->orWhere('complainant_nid', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('complaint_id', 'like', "%{$search}%")
                        ->orWhereHas('station', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->whereRaw('LOWER(status) = ?', [strtolower($request->string('status')->toString())]))
            ->when($request->filled('station_id'), fn ($query) => $query->where('station_id', $request->integer('station_id')))
            ->latest('submitted_date')
            ->latest('complaint_id');
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
                        ->orWhere('division', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->orderBy($type === 'metropolitanHQ' ? 'name' : 'division')
            ->orderBy('district')
            ->orderBy('name')
            ->get();

        $hqs->each(function (Station $hq): void {
            $thanaIds = $hq->children()->where('type', 'thana')->pluck('station_id');

            $hq->complaints_count = CitizenComplaint::query()
                ->whereIn('station_id', $thanaIds)
                ->count();

            $hq->pending_complaints_count = CitizenComplaint::query()
                ->whereIn('station_id', $thanaIds)
                ->whereRaw("LOWER(status) = 'pending'")
                ->count();
        });

        return $hqs;
    }
}
