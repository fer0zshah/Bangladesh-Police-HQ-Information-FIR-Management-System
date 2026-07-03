<x-admin-layout pageTitle="Reports & Analytics">
@php
    $maxStation = max(1, (int) ($stationCrime->max('total') ?? 1));
    $maxOfficer = max(1, (int) ($officerWorkload->max('total') ?? 1));
@endphp
<div class="-mx-4 -mt-6 min-h-screen bg-hq-900 px-4 py-7 text-gray-200 lg:-mx-8 lg:px-8">
<div class="mx-auto max-w-[1500px] space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-blue-600">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>Headquarters reporting
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">Crime and performance reports</h2>
            <p class="mt-2 text-sm text-gray-500">Operational insights across districts, stations, officers, and case outcomes.</p>
        </div>
        <div class="rounded-lg border border-hq-700 bg-hq-800 px-4 py-2.5 shadow-sm">
            <p class="text-[9px] font-bold uppercase tracking-widest text-gray-600">Report period</p>
            <p class="mt-1 text-xs font-semibold text-gray-300">Rolling 12 months</p>
        </div>
    </header>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Total cases', number_format($cards->total_cases), 'All FIR records', 'blue'],
            ['Case closure rate', $cards->closure_rate.'%', 'Overall resolution', 'emerald'],
            ['Active officers', number_format($cards->active_officers), 'Available personnel', 'indigo'],
            ['Highest crime district', $cards->top_district, 'By reported case volume', 'amber']
        ] as [$label,$value,$note,$tone])
        <article class="rounded-xl border border-hq-700 bg-hq-800 p-4 shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-start justify-between">
                <div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-600">{{$label}}</p><p class="mt-2 truncate text-2xl font-bold text-white">{{$value}}</p><p class="mt-2 text-[11px] text-gray-500">{{$note}}</p></div>
                <span class="h-2.5 w-2.5 rounded-full bg-{{$tone}}-500 ring-4 ring-{{$tone}}-500/20"></span>
            </div>
        </article>
        @endforeach
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <article class="rounded-xl border border-hq-700 bg-hq-800 shadow-lg shadow-black/10 xl:col-span-7">
            <header class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-bold text-white">Crime rate by district</h3><p class="mt-1 text-xs text-gray-500">Number of registered FIR cases in each district</p></header>
            <div class="h-[290px] p-5"><canvas id="districtCrime"></canvas></div>
        </article>
        <article class="rounded-xl border border-hq-700 bg-hq-800 shadow-lg shadow-black/10 xl:col-span-5">
            <header class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-bold text-white">Crime rate by station</h3><p class="mt-1 text-xs text-gray-500">Top stations by reported case volume</p></header>
            <div class="divide-y divide-hq-700">
                @forelse($stationCrime as $index => $station)
                <div class="grid grid-cols-[1.5rem_minmax(0,1fr)_2.5rem] items-center gap-3 px-4 py-2.5">
                    <span class="text-[10px] font-bold text-gray-600">{{str_pad($index+1,2,'0',STR_PAD_LEFT)}}</span>
                    <div class="min-w-0"><div class="mb-1.5 flex items-center justify-between gap-2"><p class="truncate text-xs font-semibold text-gray-300">{{$station->label}}</p><span class="hidden text-[10px] text-gray-600 sm:block">{{$station->district}}</span></div><div class="h-1.5 rounded-full bg-hq-900"><div class="h-full rounded-full bg-blue-500" style="width:{{round(((int)$station->total/$maxStation)*100)}}%"></div></div></div>
                    <span class="text-right text-sm font-bold text-white">{{$station->total}}</span>
                </div>
                @empty<div class="p-10 text-center text-sm text-gray-600">No station data available.</div>@endforelse
            </div>
        </article>
    </section>

    <section class="rounded-xl border border-hq-700 bg-hq-800 shadow-sm">
        <header class="flex flex-col gap-2 border-b border-hq-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div><h3 class="text-sm font-bold text-white">Officer workload report</h3><p class="mt-1 text-xs text-gray-500">Assigned, active, and closed cases for the ten busiest officers</p></div>
            <a href="{{route('admin.officers.index')}}" class="text-xs font-semibold text-blue-600 hover:text-blue-300">Manage officers &rarr;</a>
        </header>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left">
                <thead><tr class="border-b border-hq-700 bg-hq-900/50 text-[10px] font-bold uppercase tracking-widest text-gray-600"><th class="px-4 py-2.5">Officer</th><th class="px-4 py-2.5">Station</th><th class="px-4 py-2.5">Workload</th><th class="px-4 py-2.5 text-center">Active</th><th class="px-4 py-2.5 text-center">Closed</th><th class="px-4 py-2.5 text-right">Total</th></tr></thead>
                <tbody class="divide-y divide-hq-700">
                @forelse($officerWorkload as $officer)
                    <tr class="text-sm hover:bg-hq-700/30"><td class="px-4 py-2.5"><p class="font-semibold text-gray-200">{{$officer->name}}</p><p class="mt-0.5 text-[10px] text-gray-600">{{$officer->badge_number}}</p></td><td class="px-4 py-2.5 text-xs text-gray-500">{{$officer->station_name??'Unassigned'}}</td><td class="w-56 px-4 py-2.5"><div class="h-2 rounded-full bg-hq-900"><div class="h-full rounded-full bg-indigo-500" style="width:{{round(((int)$officer->total/$maxOfficer)*100)}}%"></div></div></td><td class="px-4 py-2.5 text-center"><span class="rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-bold text-amber-400">{{$officer->active??0}}</span></td><td class="px-4 py-2.5 text-center"><span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-bold text-emerald-400">{{$officer->closed??0}}</span></td><td class="px-4 py-2.5 text-right font-bold text-white">{{$officer->total}}</td></tr>
                @empty<tr><td colspan="6" class="p-10 text-center text-sm text-gray-600">No officer workload data available.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <article class="rounded-xl border border-hq-700 bg-hq-800 shadow-lg shadow-black/10 xl:col-span-8">
            <header class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-bold text-white">Monthly case closure rate</h3><p class="mt-1 text-xs text-gray-500">Closed cases as a percentage of cases filed in each month</p></header>
            <div class="h-[290px] p-5"><canvas id="monthlyClosure"></canvas></div>
        </article>
        <article class="rounded-xl border border-hq-700 bg-hq-800 shadow-lg shadow-black/10 xl:col-span-4">
            <header class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-bold text-white">Most common crime types</h3><p class="mt-1 text-xs text-gray-500">Derived from matching case titles</p></header>
            <div class="h-[250px] p-5"><canvas id="crimeTypes"></canvas></div>
            <div class="border-t border-blue-500/20 bg-blue-500/10 px-4 py-2.5 text-[10px] leading-4 text-blue-300">A dedicated crime-type field is not present in the current schema, so matching case titles are grouped as the closest available category.</div>
        </article>
    </section>
</div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const grid='rgba(148,163,184,.1)', ink='#94a3b8', palette=['#2563eb','#4f46e5','#0ea5e9','#10b981','#f59e0b','#f43f5e','#8b5cf6','#14b8a6'];
Chart.defaults.color=ink;Chart.defaults.font.family='Inter, Arial, sans-serif';
Chart.defaults.plugins.tooltip.backgroundColor='#0f172a';Chart.defaults.plugins.tooltip.padding=11;Chart.defaults.plugins.tooltip.cornerRadius=8;
new Chart(document.getElementById('districtCrime'),{type:'bar',data:{labels:@json($districtCrime->pluck('label')),datasets:[{label:'Cases',data:@json($districtCrime->pluck('total')),backgroundColor:'#3b82f6',hoverBackgroundColor:'#2563eb',borderRadius:6,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},border:{display:false}},y:{beginAtZero:true,grid:{color:grid},border:{display:false},ticks:{precision:0}}}}});
new Chart(document.getElementById('monthlyClosure'),{type:'line',data:{labels:@json($monthlyClosure->pluck('label')),datasets:[{label:'Closure rate',data:@json($monthlyClosure->pluck('rate')),borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.1)',fill:true,tension:.35,borderWidth:2.5,pointRadius:3,pointBackgroundColor:'#fff',pointBorderColor:'#10b981',pointBorderWidth:2},{label:'Cases filed',data:@json($monthlyClosure->pluck('filed')),borderColor:'#94a3b8',borderDash:[5,5],tension:.3,borderWidth:1.5,pointRadius:0,yAxisID:'y1'}]},options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},plugins:{legend:{position:'top',align:'end',labels:{usePointStyle:true,boxWidth:7}}},scales:{x:{grid:{display:false},border:{display:false}},y:{beginAtZero:true,max:100,grid:{color:grid},border:{display:false},ticks:{callback:v=>v+'%'}},y1:{beginAtZero:true,position:'right',grid:{display:false},border:{display:false},ticks:{precision:0}}}}});
new Chart(document.getElementById('crimeTypes'),{type:'doughnut',data:{labels:@json($crimeTypes->pluck('label')->map(fn($v)=>ucwords($v))),datasets:[{data:@json($crimeTypes->pluck('total')),backgroundColor:palette,borderColor:'#1a252f',borderWidth:3,hoverOffset:5}]},options:{responsive:true,maintainAspectRatio:false,cutout:'64%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,boxWidth:7,padding:12,font:{size:10}}}}}});
</script>
@endpush
</x-admin-layout>




