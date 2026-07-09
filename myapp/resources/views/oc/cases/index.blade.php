<x-oc-layout pageTitle="Case / FIR Management">
<div class="mx-auto max-w-7xl space-y-6">
    @if(session('success'))<div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{session('success')}}</div>@endif
    <section class="grid grid-cols-3 gap-4">
        @foreach([['All Cases',$summary['total'],'sky'],['Active',$summary['active'],'amber'],['Closed',$summary['closed'],'emerald']] as [$label,$value,$tone])
        <div class="rounded-xl border border-hq-700 bg-hq-800 p-4"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500">{{$label}}</p><div class="mt-2 flex items-end justify-between"><p class="text-2xl font-extrabold text-white">{{$value}}</p><span class="h-2.5 w-2.5 rounded-full bg-{{$tone}}-400"></span></div></div>
        @endforeach
    </section>
    <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/10">
        <header class="flex flex-col gap-3 border-b border-hq-700 p-4 sm:flex-row sm:items-center sm:justify-between">
            <form class="flex flex-1 gap-3"><input name="search" value="{{request('search')}}" placeholder="Search title or FIR number" class="h-10 min-w-0 flex-1 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white"><select name="status" class="h-10 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-gray-300"><option value="">All statuses</option>@foreach(['Pending','Under Investigation','Closed','Transferred'] as $status)<option @selected(request('status')===$status)>{{$status}}</option>@endforeach</select><button class="rounded-lg bg-hq-600 px-4 text-xs font-bold text-white">Filter</button></form>
            <a href="{{route('oc.cases.create')}}" class="inline-flex h-10 items-center justify-center rounded-lg bg-gold-500 px-4 text-xs font-bold text-hq-900 hover:bg-gold-600">Create FIR</a>
        </header>
        <div class="overflow-x-auto"><table class="w-full min-w-[760px] text-left"><thead><tr class="bg-hq-900/50 text-[9px] font-bold uppercase tracking-widest text-gray-600"><th class="px-5 py-3">Case</th><th class="px-4 py-3">Investigator</th><th class="px-4 py-3">Filed</th><th class="px-4 py-3">Criminals</th><th class="px-4 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead><tbody class="divide-y divide-hq-700">
        @forelse($cases as $case)<tr class="text-xs"><td class="px-5 py-3"><p class="font-semibold text-gray-200">{{$case->case_title}}</p><p class="mt-0.5 text-[10px] text-gray-600">FIR #{{$case->case_id}}</p></td><td class="px-4 py-3 text-gray-500">{{$case->officer?->name??'Unassigned'}}</td><td class="px-4 py-3 text-gray-500">{{date('d M Y',strtotime($case->date_filed))}}</td><td class="px-4 py-3 text-gray-500">{{$case->criminals->count()}}</td><td class="px-4 py-3"><span class="rounded-full bg-sky-500/10 px-2 py-1 text-[10px] font-semibold text-sky-400">{{$case->status}}</span></td><td class="px-5 py-3 text-right"><a href="{{route('oc.cases.show',$case)}}" class="mr-2 text-blue-400 hover:text-blue-300">View</a><a href="{{route('oc.cases.edit',$case)}}" class="text-amber-400 hover:text-amber-300">Edit</a></td></tr>
        @empty<tr><td colspan="6" class="p-12 text-center text-sm text-gray-600">No cases found.</td></tr>@endforelse
        </tbody></table></div><div class="border-t border-hq-700 p-4">{{$cases->links()}}</div>
    </section>
</div>
</x-oc-layout>
