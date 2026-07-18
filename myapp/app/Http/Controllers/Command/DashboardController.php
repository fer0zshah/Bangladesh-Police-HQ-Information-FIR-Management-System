<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use App\Models\CaseFir;
use App\Models\CitizenComplaint;
use App\Models\Officer;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $headquarters = Station::findOrFail($request->user()->station_id);
        $stationIds = $headquarters->children()->where('type', 'thana')->pluck('station_id');

        $stats = [
            'stations' => $stationIds->count(),
            'officers' => Officer::whereIn('station_id', $stationIds)->whereRaw("LOWER(status) = 'active'")->count(),
            'active_cases' => CaseFir::whereIn('station_id', $stationIds)->whereNotIn('status', ['Closed', 'Transferred'])->count(),
            'pending_complaints' => CitizenComplaint::whereIn('station_id', $stationIds)->where('status', 'Pending')->count(),
        ];

        $thanas = $headquarters->children()
            ->where('type', 'thana')
            ->withCount(['officers', 'cases', 'complaints'])
            ->orderBy('name')
            ->get();

        return view('command.dashboard', compact('headquarters', 'stats', 'thanas'));
    }
}
