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

    public function show(Officer $officer): View
    {
        $oc = $this->oc();
        abort_unless((int) $officer->station_id === (int) $oc->station_id, 404);
        $officer->load(['station', 'cases', 'evidence.case']);

        return view('records.show', [
            'layout'=>'oc-layout','pageTitle'=>$officer->name,'type'=>'officer',
            'record'=>(object)['officer_id'=>$officer->officer_id,'name'=>$officer->name,'rank'=>$officer->rank,'badge_number'=>$officer->badge_number,'status'=>$officer->status,'is_oc'=>$officer->is_oc,'station_name'=>$officer->station?->name],
            'relatedCases'=>$officer->cases->map(fn($case)=>(object)['case_id'=>$case->case_id,'case_title'=>$case->case_title,'status'=>$case->status,'date_filed'=>$case->date_filed]),
            'evidence'=>$officer->evidence->map(fn($item)=>(object)['type'=>$item->type,'case_id'=>$item->case_id,'collected_date'=>$item->collected_date]),
            'criminals'=>collect(),'auditLogs'=>collect(),'linkedCase'=>null,
            'backUrl'=>route('oc.officers.index'),'backLabel'=>'Back to station officers','editUrl'=>null,
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
