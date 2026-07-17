<?php

namespace App\Http\Controllers\Oc;

use App\Http\Controllers\Controller;
use App\Models\CaseAuditLog;
use App\Models\CaseFir;
use App\Models\Criminal;
use App\Models\Officer;
use App\Traits\ScopedToJurisdiction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CriminalController extends Controller
{
    use ScopedToJurisdiction;

    public function index(Request $request): View
    {
        $oc=$this->oc();
        $criminals=Criminal::withCount('cases')
            ->when($request->filled('search'),fn($q)=>$q->where(fn($q)=>$q->where('name','like','%'.$request->search.'%')->orWhere('alias','like','%'.$request->search.'%')->orWhere('nid_number','like','%'.$request->search.'%')))
            ->when($request->wanted==='yes',fn($q)=>$q->where('wanted_status',true))
            ->when($request->wanted==='no',fn($q)=>$q->where('wanted_status',false))
            ->orderByDesc('criminal_id')->paginate(12)->withQueryString();
        $summary=['total'=>Criminal::count(),'wanted'=>Criminal::where('wanted_status',true)->count(),'linked'=>Criminal::whereHas('cases',fn($q)=>$q->where('station_id',$oc->station_id))->count()];
        return view('oc.criminals.index',compact('criminals','summary'));
    }

    public function create(): View{return view('oc.criminals.form',['criminal'=>null]);}

    public function store(Request $request): RedirectResponse
    {
        $criminal=Criminal::create($this->validated($request));
        return redirect()->route('oc.criminals.show',$criminal)->with('success','Criminal added to the registry.');
    }

    public function show(Criminal $criminal): View
    {
        $oc=$this->oc();$criminal->load(['cases'=>fn($q)=>$q->where('station_id',$oc->station_id)->with('officer')]);
        $cases=CaseFir::where('station_id',$oc->station_id)->orderByDesc('date_filed')->get();
        return view('oc.criminals.show',compact('criminal','cases'));
    }

    public function edit(Criminal $criminal): View{return view('oc.criminals.form',compact('criminal'));}

    public function update(Request $request,Criminal $criminal): RedirectResponse
    {
        $criminal->update($this->validated($request,$criminal));
        return redirect()->route('oc.criminals.show',$criminal)->with('success','Criminal profile updated.');
    }

    public function toggleWanted(Criminal $criminal): RedirectResponse
    {
        $criminal->update(['wanted_status'=>!$criminal->wanted_status]);
        return back()->with('success','Wanted status updated.');
    }

    public function linkCase(Request $request,Criminal $criminal): RedirectResponse
    {
        $oc=$this->oc();$data=$request->validate([
            'case_id'=>['required','integer',Rule::exists('case_firs','case_id')->where(fn($q)=>$q->where('station_id',$oc->station_id))],
            'involvement_type'=>['required',Rule::in(['Prime Suspect','Accomplice','Witness','Person of Interest'])],
        ]);
        DB::transaction(function()use($criminal,$data){
            $criminal->cases()->syncWithoutDetaching([$data['case_id']=>['involvement_type'=>$data['involvement_type']]]);
            CaseAuditLog::create(['case_id'=>$data['case_id'],'user_id'=>auth()->id(),'action'=>'Criminal linked','details'=>"{$criminal->name} linked as {$data['involvement_type']}."]);
        });
        return back()->with('success','Criminal linked to the FIR.');
    }

    private function validated(Request $request,?Criminal $criminal=null): array
    {
        return $request->validate([
            'name'=>'required|string|max:100','alias'=>'nullable|string|max:100','date_of_birth'=>'nullable|date',
            'nid_number'=>['nullable','string','max:20',Rule::unique('criminals','nid_number')->ignore($criminal?->criminal_id,'criminal_id')],
        ]);
    }
    private function oc(): Officer{$oc=Officer::where('user_id',auth()->id())->where('is_oc',true)->firstOrFail();$this->ensureStationInJurisdiction($oc->station_id);return $oc;}
}
