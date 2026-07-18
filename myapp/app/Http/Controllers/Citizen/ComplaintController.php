<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\CitizenComplaint;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function create(): View
    {
        $divisions = Station::query()
            ->where('type', 'thana')
            ->whereNotNull('division')
            ->where(fn ($query) => $query->where('is_active', true)->orWhereRaw("LOWER(status) = 'active'"))
            ->distinct()
            ->orderBy('division')
            ->pluck('division');

        $mapboxToken = config('services.mapbox.public_token');

        return view('citizen.complaints.create', compact('divisions', 'mapboxToken'));
    }

    public function districts(Request $request): JsonResponse
    {
        $division = trim($request->string('division')->toString());

        if ($division === '') {
            return response()->json([]);
        }

        $districts = Station::query()
            ->where('type', 'thana')
            ->where('division', $division)
            ->whereNotNull('district')
            ->where(fn ($query) => $query->where('is_active', true)->orWhereRaw("LOWER(status) = 'active'"))
            ->distinct()
            ->orderBy('district')
            ->pluck('district')
            ->map(fn ($district) => ['district' => $district])
            ->values();

        return response()->json($districts);
    }

    public function thanas(Request $request): JsonResponse
    {
        $division = trim($request->string('division')->toString());
        $district = trim($request->string('district')->toString());

        if ($division === '' || $district === '') {
            return response()->json([]);
        }

        $thanas = Station::query()
            ->with('parent:station_id,name')
            ->where('type', 'thana')
            ->where('division', $division)
            ->where('district', $district)
            ->where(fn ($query) => $query->where('is_active', true)->orWhereRaw("LOWER(status) = 'active'"))
            ->orderBy('name')
            ->get(['station_id', 'parent_id', 'name'])
            ->map(fn (Station $station) => [
                'station_id' => $station->station_id,
                'name' => $station->name,
                'command_name' => $station->parent?->name,
            ]);

        return response()->json($thanas);
    }

    public function mapStations(): JsonResponse
    {
        $stations = Station::query()
            ->with('parent:station_id,name')
            ->where('type', 'thana')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(fn ($query) => $query->where('is_active', true)->orWhereRaw("LOWER(status) = 'active'"))
            ->orderBy('division')
            ->orderBy('district')
            ->orderBy('name')
            ->get([
                'station_id',
                'parent_id',
                'name',
                'division',
                'district',
                'address',
                'contact_number',
                'latitude',
                'longitude',
            ])
            ->map(fn (Station $station) => [
                'station_id' => $station->station_id,
                'name' => $station->name,
                'command_name' => $station->parent?->name,
                'division' => $station->division,
                'district' => $station->district,
                'address' => $station->address,
                'contact_number' => $station->contact_number,
                'latitude' => (float) $station->latitude,
                'longitude' => (float) $station->longitude,
            ])
            ->values();

        return response()->json(['stations' => $stations]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'division' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'station_id' => [
                'required',
                'integer',
                Rule::exists('stations', 'station_id')->where(fn ($query) => $query
                    ->where('type', 'thana')
                    ->where('division', $request->string('division')->toString())
                    ->where('district', $request->string('district')->toString())
                    ->where('is_active', true)),
            ],
            'complaint_title' => ['required', 'string', 'min:5', 'max:150'],
            'description' => ['required', 'string', 'min:15', 'max:255'],
        ]);

        $complaint = CitizenComplaint::create([
            'station_id' => $data['station_id'],
            'complainant_name' => $request->user()->name,
            'complainant_nid' => $request->user()->nid_number,
            'complaint_title' => $data['complaint_title'],
            'description' => $data['description'],
            'submitted_date' => now()->toDateString(),
            'status' => 'Pending',
        ]);

        return redirect()
            ->route('citizen.complaints.show', $complaint)
            ->with('success', 'Complaint submitted successfully. Reference: '.$this->reference($complaint));
    }

    public function show(Request $request, CitizenComplaint $complaint): View
    {
        abort_unless($complaint->complainant_nid === $request->user()->nid_number, 404);

        $complaint->load([
            'station.parent',
            'caseFir.officer',
        ]);
        $complaint->reference = $this->reference($complaint);

        return view('citizen.complaints.show', compact('complaint'));
    }

    private function reference(CitizenComplaint $complaint): string
    {
        $year = optional($complaint->submitted_date)->format('Y')
            ?? date('Y', strtotime((string) $complaint->submitted_date));

        return 'CMP-'.$year.'-'.str_pad((string) $complaint->complaint_id, 6, '0', STR_PAD_LEFT);
    }
}
