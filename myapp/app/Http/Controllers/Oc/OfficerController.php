<?php

namespace App\Http\Controllers\Oc;

use App\Http\Controllers\Controller;
use App\Models\Officer;
use App\Traits\ScopedToJurisdiction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficerController extends Controller
{
    use ScopedToJurisdiction;

    public function index(Request $request): View
    {
        $oc = $this->oc();

        $officers = Officer::query()
            ->with('user')
            ->withCount(['cases', 'evidence'])
            ->where('station_id', $oc->station_id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('badge_number', 'like', "%{$search}%")
                        ->orWhere('rank', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->get('oc') === 'yes', fn ($query) => $query->where('is_oc', true))
            ->when($request->get('oc') === 'no', fn ($query) => $query->where('is_oc', false))
            ->orderByDesc('is_oc')
            ->orderBy('rank')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $summary = [
            'total' => Officer::where('station_id', $oc->station_id)->count(),
            'active' => Officer::where('station_id', $oc->station_id)->whereRaw('LOWER(status) = ?', ['active'])->count(),
            'oc' => Officer::where('station_id', $oc->station_id)->where('is_oc', true)->count(),
            'inactive' => Officer::where('station_id', $oc->station_id)->whereRaw('LOWER(status) <> ?', ['active'])->count(),
        ];

        return view('oc.officers.index', [
            'officers' => $officers,
            'summary' => $summary,
            'station' => $oc->station,
        ]);
    }

    private function oc(): Officer
    {
        $oc = Officer::with('station')
            ->where('user_id', auth()->id())
            ->where('is_oc', true)
            ->firstOrFail();

        $this->ensureStationInJurisdiction($oc->station_id);

        return $oc;
    }
}
