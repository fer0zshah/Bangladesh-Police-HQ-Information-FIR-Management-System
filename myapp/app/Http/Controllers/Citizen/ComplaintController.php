<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\CitizenComplaint;
use App\Models\Station;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function create(): View
    {
        $stations = Station::query()
            ->where('type', 'thana')
            ->where(fn ($query) => $query->where('is_active', true)->orWhereRaw("LOWER(status) = 'active'"))
            ->with('parent')
            ->orderBy('district')
            ->orderBy('name')
            ->get()
            ->map(fn (Station $station) => (object) [
                'station_id' => $station->station_id,
                'name' => $station->name,
                'district' => $station->district,
                'parent_name' => $station->parent?->name,
            ]);

        return view('citizen.complaints.create', compact('stations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'station_id' => [
                'required',
                'integer',
                Rule::exists('stations', 'station_id')->where(fn ($query) => $query
                    ->where('type', 'thana')
                    ->where(fn ($query) => $query->where('is_active', true)->orWhereRaw("LOWER(status) = 'active'"))),
            ],
            'description' => ['required', 'string', 'min:15', 'max:255'],
        ]);

        $complaint = CitizenComplaint::create([
            'station_id' => $data['station_id'],
            'complainant_name' => $request->user()->name,
            'complainant_nid' => $request->user()->nid_number,
            'description' => $data['description'],
            'submitted_date' => now()->toDateString(),
            'status' => 'Pending',
        ]);

        return redirect()->route('citizen.dashboard')->with(
            'success',
            'Complaint submitted successfully. Reference: '.$this->reference($complaint->complaint_id)
        );
    }

    private function reference(int $id): string
    {
        return 'CMP-'.now()->format('Y').'-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }
}
