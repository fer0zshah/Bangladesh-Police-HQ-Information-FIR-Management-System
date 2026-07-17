<?php

namespace App\Http\Controllers\Oc;

use App\Http\Controllers\Controller;
use App\Models\CaseAuditLog;
use App\Models\CaseFir;
use App\Models\Evidence;
use App\Models\Officer;
use App\Traits\ScopedToJurisdiction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EvidenceController extends Controller
{
    use ScopedToJurisdiction;

    public function index(Request $request): View
    {
        $oc=$this->oc();
        $evidence=Evidence::with(['case','officer'])->whereHas('case',fn($q)=>$q->where('station_id',$oc->station_id))
            ->when($request->filled('search'),fn($q)=>$q->where(fn($q)=>$q->where('type','like','%'.$request->search.'%')->orWhere('description','like','%'.$request->search.'%')))
            ->when($request->filled('case_id'),fn($q)=>$q->where('case_id',$request->case_id))
            ->orderByDesc('collected_date')->orderByDesc('evidence_id')->paginate(12)->withQueryString();
        $cases=CaseFir::where('station_id',$oc->station_id)->orderByDesc('date_filed')->get();
        $summary=['total'=>Evidence::whereHas('case',fn($q)=>$q->where('station_id',$oc->station_id))->count(),'types'=>Evidence::whereHas('case',fn($q)=>$q->where('station_id',$oc->station_id))->distinct('type')->count('type'),'cases'=>Evidence::whereHas('case',fn($q)=>$q->where('station_id',$oc->station_id))->distinct('case_id')->count('case_id')];
        return view('oc.evidence.index',compact('evidence','cases','summary'));
    }

    public function create(Request $request): View
    {
        $oc=$this->oc();return view('oc.evidence.form',['evidence'=>null,'cases'=>$this->cases($oc),'officers'=>$this->officers($oc),'selectedCase'=>$request->integer('case_id')?:null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $oc=$this->oc();$data=$this->validated($request,$oc);
        $evidence=DB::transaction(function()use($data){$item=Evidence::create($data);$this->audit($item,'Evidence logged',"Evidence item '{$item->type}' was logged.");return $item;});
        return redirect()->route('oc.evidence.index')->with('success',"Evidence #{$evidence->evidence_id} logged successfully.");
    }

    public function edit(Evidence $evidence): View
    {
        $oc=$this->guard($evidence);return view('oc.evidence.form',compact('evidence')+['cases'=>$this->cases($oc),'officers'=>$this->officers($oc),'selectedCase'=>$evidence->case_id]);
    }

    public function update(Request $request,Evidence $evidence): RedirectResponse
    {
        $oc=$this->guard($evidence);$data=$this->validated($request,$oc);
        DB::transaction(function()use($evidence,$data){$oldCase=$evidence->case_id;$evidence->update($data);$this->audit($evidence,'Evidence updated',"Evidence item '{$evidence->type}' was updated.");if((int)$oldCase!==(int)$evidence->case_id)CaseAuditLog::create(['case_id'=>$oldCase,'user_id'=>auth()->id(),'action'=>'Evidence moved','details'=>"Evidence #{$evidence->evidence_id} was moved to another FIR."]);});
        return redirect()->route('oc.evidence.index')->with('success','Evidence details updated.');
    }

    private function validated(Request $request,Officer $oc): array
    {
        return $request->validate(['case_id'=>['required','integer',Rule::exists('case_firs','case_id')->where(fn($q)=>$q->where('station_id',$oc->station_id))],'officer_id'=>['required','integer',Rule::exists('officers','officer_id')->where(fn($q)=>$q->where('station_id',$oc->station_id))],'type'=>'required|string|max:100','description'=>'nullable|string|max:2000','collected_date'=>'required|date']);
    }
    private function oc(): Officer{$oc=Officer::where('user_id',auth()->id())->where('is_oc',true)->firstOrFail();$this->ensureStationInJurisdiction($oc->station_id);return $oc;}
    private function guard(Evidence $evidence): Officer{$oc=$this->oc();$evidence->loadMissing('case');abort_unless((int)$evidence->case->station_id===(int)$oc->station_id,403);return $oc;}
    private function cases(Officer $oc){return CaseFir::where('station_id',$oc->station_id)->orderByDesc('date_filed')->get();}
    private function officers(Officer $oc){return Officer::where('station_id',$oc->station_id)->whereRaw("LOWER(status)='active'")->orderBy('name')->get();}
    private function audit(Evidence $item,string $action,string $details): void{CaseAuditLog::create(['case_id'=>$item->case_id,'user_id'=>auth()->id(),'action'=>$action,'details'=>$details]);}
}
