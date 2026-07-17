<?php

namespace App\Http\Controllers\Oc;

use App\Http\Controllers\Controller;
use App\Models\CaseAuditLog;
use App\Models\CaseFir;
use App\Models\CitizenComplaint;
use App\Models\Officer;
use App\Traits\ScopedToJurisdiction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CaseController extends Controller
{
    use ScopedToJurisdiction;

    public function index(Request $request): View
    {
        $oc=$this->oc();
        $cases=CaseFir::with(['officer','criminals'])->where('station_id',$oc->station_id)
            ->when($request->filled('search'),fn($q)=>$q->where(fn($q)=>$q->where('case_title','like','%'.$request->search.'%')->orWhere('case_id','like','%'.$request->search.'%')))
            ->when($request->filled('status'),fn($q)=>$q->where('status',$request->status))
            ->orderByDesc('date_filed')->orderByDesc('case_id')->paginate(12)->withQueryString();
        $summary=['total'=>CaseFir::where('station_id',$oc->station_id)->count(),'active'=>CaseFir::where('station_id',$oc->station_id)->whereRaw("LOWER(status)<>'closed'")->count(),'closed'=>CaseFir::where('station_id',$oc->station_id)->whereRaw("LOWER(status)='closed'")->count()];
        return view('oc.cases.index',compact('oc','cases','summary'));
    }

    public function create(): View
    {
        $oc=$this->oc();
        return view('oc.cases.form',['case'=>null,'officers'=>$this->officers($oc),'complaints'=>$this->complaints($oc)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $oc=$this->oc();$data=$this->validated($request,$oc,true);
        $case=DB::transaction(function()use($data,$oc){
            $case=CaseFir::create([...$data,'station_id'=>$oc->station_id,'status'=>'Pending']);
            $this->audit($case,'FIR created',null,'Pending','FIR created directly by the Officer in Charge.');
            return $case;
        });
        return redirect()->route('oc.cases.show',$case)->with('success','FIR created successfully.');
    }

    public function show(CaseFir $case): View
    {
        $this->guard($case);
        $case->load(['officer','station','criminals','evidence.officer','auditLogs.user']);
        return view('oc.cases.show',compact('case'));
    }

    public function edit(CaseFir $case): View
    {
        $oc=$this->guard($case);
        return view('oc.cases.form',compact('case')+['officers'=>$this->officers($oc),'complaints'=>$this->complaints($oc,$case)]);
    }

    public function update(Request $request,CaseFir $case): RedirectResponse
    {
        $oc=$this->guard($case);$data=$this->validated($request,$oc,false);$oldStatus=$this->canonical($case->status);$newStatus=$data['status'];
        $allowed=['Pending'=>['Under Investigation'],'Under Investigation'=>['Closed'],'Closed'=>['Transferred'],'Transferred'=>[]];
        if($newStatus!==$oldStatus&&!in_array($newStatus,$allowed[$oldStatus]??[],true))throw ValidationException::withMessages(['status'=>"Case cannot move from {$oldStatus} to {$newStatus}."]);
        DB::transaction(function()use($case,$data,$oldStatus,$newStatus){
            $changes=[];if($case->investigating_officer_id!=(int)$data['investigating_officer_id'])$changes[]='Investigating officer reassigned.';
            if($case->case_title!==$data['case_title'])$changes[]='Case title updated.';
            $case->update($data);
            if($newStatus!==$oldStatus)$this->audit($case,'Status changed',$oldStatus,$newStatus,"Case status moved from {$oldStatus} to {$newStatus}.");
            if($changes)$this->audit($case,'Case details updated',$newStatus,$newStatus,implode(' ',$changes));
        });
        return redirect()->route('oc.cases.show',$case)->with('success','Case updated successfully.');
    }

    private function validated(Request $request,Officer $oc,bool $creating): array
    {
        $rules=['case_title'=>'required|string|max:255','date_filed'=>'required|date','investigating_officer_id'=>['required','integer',Rule::exists('officers','officer_id')->where(fn($q)=>$q->where('station_id',$oc->station_id)->whereRaw("LOWER(status)='active'"))]];
        if($creating)$rules['complaint_id']=['nullable','integer',Rule::exists('citizen_complaints','complaint_id')->where(fn($q)=>$q->where('station_id',$oc->station_id)),Rule::unique('case_firs','complaint_id')];
        else $rules['status']=['required',Rule::in(['Pending','Under Investigation','Closed','Transferred'])];
        return $request->validate($rules);
    }

    private function oc(): Officer{$oc=Officer::where('user_id',auth()->id())->where('is_oc',true)->firstOrFail();$this->ensureStationInJurisdiction($oc->station_id);return $oc;}
    private function guard(CaseFir $case): Officer{$oc=$this->oc();abort_unless((int)$case->station_id===(int)$oc->station_id,403);return $oc;}
    private function officers(Officer $oc){return Officer::where('station_id',$oc->station_id)->whereRaw("LOWER(status)='active'")->orderBy('name')->get();}
    private function complaints(Officer $oc,?CaseFir $case=null){return CitizenComplaint::where('station_id',$oc->station_id)->whereDoesntHave('caseFir',fn($q)=>$case?$q->where('case_id','!=',$case->case_id):$q)->orderByDesc('submitted_date')->get();}
    private function canonical(string $status): string{return match(strtolower($status)){'pending'=>'Pending','under investigation'=>'Under Investigation','closed'=>'Closed','transferred'=>'Transferred',default=>$status};}
    private function audit(CaseFir $case,string $action,?string $old,?string $new,string $details): void{CaseAuditLog::create(['case_id'=>$case->case_id,'user_id'=>auth()->id(),'action'=>$action,'old_status'=>$old,'new_status'=>$new,'details'=>$details]);}
}
