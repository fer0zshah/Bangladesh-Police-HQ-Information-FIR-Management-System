<x-command-layout pageTitle="Reports & Analytics">
@php
    $maxStation=max(1,(int)($stationCrime->max('total')??1));
    $maxOfficer=max(1,(int)($officerWorkload->max('total')??1));
@endphp
<div class="mx-auto max-w-[1500px] space-y-6">
    <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 to-hq-700/70 p-6">
        <div class="absolute right-0 top-0 h-40 w-40 rounded-bl-full bg-blue-500/5"></div>
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-400">Jurisdiction analytics</p><h2 class="mt-2 text-2xl font-bold text-white">{{$headquarters->name}}</h2><p class="mt-2 text-sm text-gray-400">Crime trends, case outcomes, and personnel workload across your child thanas only.</p></div>
            <div class="rounded-lg border border-hq-600 bg-hq-900/40 px-4 py-2.5"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-600">Report period</p><p class="mt-1 text-xs font-semibold text-gray-300">Rolling 12 months</p></div>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        @foreach([
            ['Total cases',number_format($cards->total_cases),'FIRs in this command','blue'],
            ['Closure rate',$cards->closure_rate.'%','Overall resolution','emerald'],
            ['Active officers',number_format($cards->active_officers),'All active ranks','indigo'],
            ['Highest case load',$cards->top_station,'Busiest thana','amber'],
        ] as [$label,$value,$note,$tone])
        <article class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5">
            <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-{{$tone}}-500/5"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">{{$label}}</p><p class="mt-2 truncate text-2xl font-extrabold text-white">{{$value}}</p><p class="mt-3 text-[10px] text-{{$tone}}-400">{{$note}}</p>
        </article>
        @endforeach
    </section>

    <section class="grid gap-6 xl:grid-cols-12">
        <article class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 xl:col-span-8">
            <header class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-bold text-white">Crime rate by thana</h3><p class="mt-1 text-xs text-gray-500">Registered FIR volume for each thana under this command</p></header>
            <div class="h-[310px] p-5"><canvas id="stationCrimeChart"></canvas></div>
        </article>
        <article class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 xl:col-span-4">
            <header class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-bold text-white">Case status distribution</h3><p class="mt-1 text-xs text-gray-500">Current outcome and investigation state</p></header>
            <div class="h-[310px] p-5"><canvas id="statusChart"></canvas></div>
        </article>
    </section>

    <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
        <header class="flex flex-col gap-2 border-b border-hq-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"><div><h3 class="text-sm font-bold text-white">Officer workload report</h3><p class="mt-1 text-xs text-gray-500">The twelve busiest personnel records across your thanas</p></div><a href="{{route('command.officers.index')}}" class="text-xs font-semibold text-blue-400 hover:text-blue-300">Manage officers &rarr;</a></header>
        <div class="overflow-x-auto"><table class="w-full min-w-[800px] text-left"><thead class="bg-hq-900/50 text-[10px] uppercase tracking-widest text-gray-600"><tr><th class="px-5 py-3">Officer</th><th class="px-4 py-3">Thana</th><th class="px-4 py-3">Workload</th><th class="px-4 py-3 text-center">Active</th><th class="px-4 py-3 text-center">Closed</th><th class="px-5 py-3 text-right">Total</th></tr></thead><tbody class="divide-y divide-hq-700/60">
        @forelse($officerWorkload as $officer)
            <tr class="text-sm hover:bg-hq-700/20"><td class="px-5 py-3"><p class="font-semibold text-gray-200">{{$officer->name}}</p><p class="mt-1 text-[10px] text-gray-600">{{$officer->rank}} · {{$officer->badge_number}}</p></td><td class="px-4 py-3 text-xs text-gray-500">{{$officer->station_name}}</td><td class="w-56 px-4 py-3"><div class="h-2 rounded-full bg-hq-900"><div class="h-full rounded-full bg-indigo-500" style="width:{{round(((int)$officer->total/$maxOfficer)*100)}}%"></div></div></td><td class="px-4 py-3 text-center"><span class="rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-bold text-amber-400">{{$officer->active}}</span></td><td class="px-4 py-3 text-center"><span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-bold text-emerald-400">{{$officer->closed}}</span></td><td class="px-5 py-3 text-right font-bold text-white">{{$officer->total}}</td></tr>
        @empty<tr><td colspan="6" class="p-12 text-center text-sm text-gray-500">No personnel workload data.</td></tr>@endforelse
        </tbody></table></div>
    </section>

    <section class="grid gap-6 xl:grid-cols-12">
        <article class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 xl:col-span-8"><header class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-bold text-white">Monthly case closure rate</h3><p class="mt-1 text-xs text-gray-500">Closed cases as a percentage of FIRs filed in each month</p></header><div class="h-[310px] p-5"><canvas id="closureChart"></canvas></div></article>
        <article class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 xl:col-span-4"><header class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-bold text-white">Most common crime types</h3><p class="mt-1 text-xs text-gray-500">Categories inferred from FIR titles</p></header><div class="h-[270px] p-5"><canvas id="crimeTypeChart"></canvas></div><div class="border-t border-blue-500/20 bg-blue-500/10 px-4 py-3 text-[10px] leading-4 text-blue-300">Crime categories are derived from title keywords because the current schema has no dedicated crime-type column.</div></article>
    </section>

    <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
        <header class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-bold text-white">Thana ranking</h3><p class="mt-1 text-xs text-gray-500">Case volume comparison across the command</p></header>
        <div class="divide-y divide-hq-700/60">@forelse($stationCrime as $index=>$station)<div class="grid grid-cols-[2rem_minmax(0,1fr)_3rem] items-center gap-3 px-5 py-3"><span class="text-[10px] font-bold text-gray-600">{{str_pad($index+1,2,'0',STR_PAD_LEFT)}}</span><div><div class="mb-2 flex justify-between"><p class="text-xs font-semibold text-gray-300">{{$station->label}}</p><span class="text-[10px] text-gray-600">{{$station->district}}</span></div><div class="h-1.5 rounded-full bg-hq-900"><div class="h-full rounded-full bg-blue-500" style="width:{{round(((int)$station->total/$maxStation)*100)}}%"></div></div></div><span class="text-right font-bold text-white">{{$station->total}}</span></div>@empty<div class="p-10 text-center text-gray-500">No thana data.</div>@endforelse</div>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const grid='rgba(148,163,184,.1)',ink='#94a3b8',palette=['#3b82f6','#6366f1','#10b981','#f59e0b','#f43f5e','#8b5cf6','#14b8a6','#64748b'];
Chart.defaults.color=ink;Chart.defaults.font.family='Inter, Arial, sans-serif';
new Chart(document.getElementById('stationCrimeChart'),{type:'bar',data:{labels:@json($stationCrime->pluck('label')),datasets:[{data:@json($stationCrime->pluck('total')),backgroundColor:'#3b82f6',borderRadius:6,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:grid},ticks:{precision:0}}}}});
new Chart(document.getElementById('statusChart'),{type:'doughnut',data:{labels:@json($statusBreakdown->pluck('label')),datasets:[{data:@json($statusBreakdown->pluck('total')),backgroundColor:palette,borderColor:'#1a252f',borderWidth:3}]},options:{responsive:true,maintainAspectRatio:false,cutout:'64%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,boxWidth:7,padding:12,font:{size:10}}}}}});
new Chart(document.getElementById('closureChart'),{type:'line',data:{labels:@json($monthlyClosure->pluck('label')),datasets:[{label:'Closure rate',data:@json($monthlyClosure->pluck('rate')),borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.1)',fill:true,tension:.35,borderWidth:2.5},{label:'Cases filed',data:@json($monthlyClosure->pluck('filed')),borderColor:'#94a3b8',borderDash:[5,5],tension:.3,yAxisID:'y1'}]},options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},scales:{x:{grid:{display:false}},y:{beginAtZero:true,max:100,grid:{color:grid},ticks:{callback:v=>v+'%'}},y1:{beginAtZero:true,position:'right',grid:{display:false},ticks:{precision:0}}}}});
new Chart(document.getElementById('crimeTypeChart'),{type:'doughnut',data:{labels:@json($crimeTypes->pluck('label')),datasets:[{data:@json($crimeTypes->pluck('total')),backgroundColor:palette,borderColor:'#1a252f',borderWidth:3}]},options:{responsive:true,maintainAspectRatio:false,cutout:'62%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,boxWidth:7,padding:10,font:{size:10}}}}}});
</script>
@endpush
</x-command-layout>
