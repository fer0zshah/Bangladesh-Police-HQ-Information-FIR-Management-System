<?php

namespace App\Http\Controllers\Oc;

use App\Http\Controllers\Controller;
use App\Models\CitizenComplaint;
use App\Models\CaseAuditLog;
use App\Models\CaseFir;
use App\Models\Officer;
use App\Traits\ScopedToJurisdiction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    use ScopedToJurisdiction;

    public function index(Request $request): View
    {
        $oc = $this->oc();
        $complaints = CitizenComplaint::query()
            ->with('caseFir')
            ->where('station_id', $oc->station_id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search')->toString().'%';
                $query->where(fn ($query) => $query
                    ->where('complainant_name', 'like', $term)
                    ->orWhere('complainant_nid', 'like', $term)
                    ->orWhere('description', 'like', $term));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->orderByDesc('submitted_date')
            ->orderByDesc('complaint_id')
            ->paginate(12)
            ->withQueryString();

        $summary = [
            'total' => CitizenComplaint::where('station_id', $oc->station_id)->count(),
            'pending' => CitizenComplaint::where('station_id', $oc->station_id)->where('status', 'Pending')->count(),
            'review' => CitizenComplaint::where('station_id', $oc->station_id)->where('status', 'Under Review')->count(),
            'escalated' => CitizenComplaint::where('station_id', $oc->station_id)->where('status', 'Escalated')->count(),
        ];
        $officers = Officer::where('station_id', $oc->station_id)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->orderBy('name')
            ->get();

        return view('oc.complaints.index', compact('oc', 'complaints', 'summary', 'officers'));
    }

    public function updateStatus(Request $request, CitizenComplaint $complaint): RedirectResponse
    {
        $this->guardStation($complaint);
        $data = $request->validate(['status' => ['required', Rule::in(['Under Review', 'Dismissed'])]]);
        $allowed = [
            'Pending' => ['Under Review'],
            'Under Review' => ['Dismissed'],
        ];

        if (! in_array($data['status'], $allowed[$complaint->status] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => "Complaint cannot move from {$complaint->status} to {$data['status']}.",
            ]);
        }

        $complaint->update(['status' => $data['status']]);
        return back()->with('success', "Complaint marked {$data['status']}.");
    }

    public function escalate(Request $request, CitizenComplaint $complaint): RedirectResponse
    {
        $oc = $this->guardStation($complaint);
        $data = $request->validate([
            'case_title' => ['required', 'string', 'max:255'],
            'investigating_officer_id' => [
                'required', 'integer',
                Rule::exists('officers', 'officer_id')->where(fn ($query) => $query
                    ->where('station_id', $oc->station_id)
                    ->whereRaw('LOWER(status) = ?', ['active'])),
            ],
        ]);

        DB::transaction(function () use ($complaint, $oc, $data) {
            DB::statement('CALL escalate_complaint_to_fir(?, ?, ?, ?)', [
                $complaint->complaint_id, $oc->station_id,
                $data['investigating_officer_id'], $data['case_title'],
            ]);
            $case = CaseFir::where('complaint_id', $complaint->complaint_id)->firstOrFail();
            CaseAuditLog::create([
                'case_id' => $case->case_id,
                'user_id' => auth()->id(),
                'action' => 'FIR created from complaint',
                'old_status' => null,
                'new_status' => 'Pending',
                'details' => "Complaint #{$complaint->complaint_id} was escalated and converted to an FIR.",
            ]);
        });

        return redirect()->route('oc.complaints.index')
            ->with('success', 'Complaint escalated and FIR created successfully.');
    }

    private function oc(): Officer
    {
        $oc = Officer::where('user_id', auth()->id())->where('is_oc', true)->firstOrFail();
        $this->ensureStationInJurisdiction($oc->station_id);

        return $oc;
    }

    private function guardStation(CitizenComplaint $complaint): Officer
    {
        $oc = $this->oc();
        abort_unless((int) $complaint->station_id === (int) $oc->station_id, 403);
        return $oc;
    }
}
