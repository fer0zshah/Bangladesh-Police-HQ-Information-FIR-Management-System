<x-command-layout :pageTitle="$title">
    @php
        $labels = match($module) {
            'cases' => ['All FIRs','Active','Closed','Transferred'],
            'complaints' => ['Complaints','Pending','Under review','Escalated'],
            'criminals' => ['Linked criminals','Wanted','Not wanted','Active-case links'],
            'evidence' => ['Evidence items','Linked FIRs','Evidence types','Last 30 days'],
        };
        $stationRoute = "command.{$module}.station";
        $indexRoute = "command.{$module}.index";
    @endphp
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 to-hq-700/70 p-6">
            <div class="absolute right-0 top-0 h-40 w-40 rounded-bl-full bg-gold-500/5"></div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gold-500">Jurisdiction oversight</p>
            <div class="mt-2 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div><h2 class="text-2xl font-bold text-white">{{ $headquarters->name }}</h2><p class="mt-2 text-sm text-gray-400">{{ $selectedStation ? $selectedStation->name.' dictionary' : 'All child thanas under this command' }}</p></div>
                @if($selectedStation)<a href="{{ route($indexRoute) }}" class="rounded-lg border border-hq-600 px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white">Clear thana selection</a>@endif
            </div>
        </section>

        <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach([[$labels[0],$summary['total']],[$labels[1],$summary['primary']],[$labels[2],$summary['secondary']],[$labels[3],$summary['tertiary']]] as $card)
                <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-gold-500/5"></div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">{{ $card[0] }}</p><p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($card[1]) }}</p></div>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
            <div class="border-b border-hq-700 px-5 py-4"><h3 class="font-semibold text-white">Thana workload</h3><p class="mt-1 text-xs text-gray-500">Select a thana to open its {{ strtolower($title) }} dictionary</p></div>
            <div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($thanas as $thana)
                    <a href="{{ route($stationRoute,$thana) }}" class="relative overflow-hidden rounded-xl border p-4 transition {{ $selectedStation && $selectedStation->is($thana) ? 'border-gold-500/50 bg-gold-500/5' : 'border-hq-700 bg-hq-900/30 hover:border-hq-500' }}">
                        <div class="absolute right-0 top-0 h-16 w-16 rounded-bl-full bg-indigo-500/5"></div><p class="relative font-semibold text-gray-200">{{ $thana->name }}</p><div class="relative mt-3 flex gap-4 text-xs"><span class="text-gray-500">Records <strong class="ml-1 text-white">{{ $thana->records_count }}</strong></span><span class="text-gray-500">{{ $module === 'criminals' ? 'Wanted' : ($module === 'evidence' ? 'FIRs' : 'Open') }} <strong class="ml-1 text-amber-400">{{ $thana->primary_count }}</strong></span></div>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
            <form method="GET" action="{{ $selectedStation ? route($stationRoute,$selectedStation) : route($indexRoute) }}" class="grid gap-3 border-b border-hq-700 p-4 md:grid-cols-2 xl:grid-cols-[1fr_15rem_13rem_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="Search this dictionary..." class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none placeholder:text-gray-600">
                @unless($selectedStation)<select name="station_id" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300"><option value="">All thanas</option>@foreach($stations as $station)<option value="{{ $station->station_id }}" @selected((string)request('station_id')===(string)$station->station_id)>{{ $station->name }}</option>@endforeach</select>@else<div></div>@endunless
                @if($module === 'criminals')
                    <select name="wanted" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300"><option value="">All wanted states</option><option value="yes" @selected(request('wanted')==='yes')>Wanted</option><option value="no" @selected(request('wanted')==='no')>Not wanted</option></select>
                @else
                    <select name="{{ $module === 'evidence' ? 'type' : 'status' }}" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300"><option value="">All {{ $module === 'evidence' ? 'types' : 'statuses' }}</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request($module === 'evidence' ? 'type' : 'status')===$status)>{{ $status }}</option>@endforeach</select>
                @endif
                <div class="flex gap-2"><button class="rounded-lg border border-gold-500/40 px-4 text-xs font-bold text-gold-400 hover:bg-gold-500/10">Filter</button><a href="{{ $selectedStation ? route($stationRoute,$selectedStation) : route($indexRoute) }}" class="flex items-center px-3 text-xs text-gray-500 hover:text-white">Reset</a></div>
            </form>
            <div class="overflow-x-auto">
                <table class="hq-table w-full min-w-[980px] text-left text-sm">
                    @if($module === 'cases')
                        <thead class="bg-hq-900/60 text-[10px] uppercase tracking-widest text-gray-500"><tr><th class="px-5 py-4">FIR</th><th class="px-4 py-4">Thana</th><th class="px-4 py-4">Investigator</th><th class="px-4 py-4">Filed</th><th class="px-4 py-4">Links</th><th class="px-5 py-4 text-right">Status</th></tr></thead><tbody class="divide-y divide-hq-700/60">@forelse($records as $record)<tr><td class="px-5 py-4"><a href="{{ route('command.cases.show', $record) }}" class="font-semibold text-gray-200 hover:text-gold-500">{{ $record->case_title }}</a><p class="mt-1 text-xs text-gray-600">FIR #{{ $record->case_id }}</p></td><td class="px-4 py-4 text-gray-500">{{ $record->station?->name }}</td><td class="px-4 py-4 text-gray-400">{{ $record->officer?->name ?? 'Unassigned' }}</td><td class="px-4 py-4 text-gray-500">{{ \Carbon\Carbon::parse($record->date_filed)->format('d M Y') }}</td><td class="px-4 py-4 text-xs text-gray-500">{{ $record->criminals_count }} criminals · {{ $record->evidence_count }} evidence</td><td class="px-5 py-4 text-right text-amber-400">{{ $record->status }}</td></tr>@empty<tr><td colspan="6" class="px-5 py-14 text-center text-gray-500">No FIRs found.</td></tr>@endforelse</tbody>
                    @elseif($module === 'complaints')
                        <thead class="bg-hq-900/60 text-[10px] uppercase tracking-widest text-gray-500"><tr><th class="px-5 py-4">Complaint</th><th class="px-4 py-4">Thana</th><th class="px-4 py-4">Description</th><th class="px-4 py-4">Submitted</th><th class="px-4 py-4">FIR</th><th class="px-5 py-4 text-right">Status</th></tr></thead><tbody class="divide-y divide-hq-700/60">@forelse($records as $record)<tr><td class="px-5 py-4"><a href="{{ route('command.complaints.show', $record) }}" class="font-semibold text-gray-200 hover:text-gold-500">{{ $record->complainant_name }}</a><p class="mt-1 text-xs text-gray-600">Ref #{{ $record->complaint_id }}</p></td><td class="px-4 py-4 text-gray-500">{{ $record->station?->name }}</td><td class="max-w-sm truncate px-4 py-4 text-gray-400">{{ $record->description }}</td><td class="px-4 py-4 text-gray-500">{{ \Carbon\Carbon::parse($record->submitted_date)->format('d M Y') }}</td><td class="px-4 py-4 text-gray-500">{{ $record->caseFir ? '#'.$record->caseFir->case_id : '—' }}</td><td class="px-5 py-4 text-right text-yellow-400">{{ $record->status }}</td></tr>@empty<tr><td colspan="6" class="px-5 py-14 text-center text-gray-500">No complaints found.</td></tr>@endforelse</tbody>
                    @elseif($module === 'criminals')
                        <thead class="bg-hq-900/60 text-[10px] uppercase tracking-widest text-gray-500"><tr><th class="px-5 py-4">Criminal</th><th class="px-4 py-4">Latest linked FIR</th><th class="px-4 py-4">Thana</th><th class="px-4 py-4 text-center">FIR links</th><th class="px-5 py-4 text-right">Wanted status</th></tr></thead><tbody class="divide-y divide-hq-700/60">@forelse($records as $record)@php($latest=$record->cases->first())<tr><td class="px-5 py-4"><a href="{{ route('command.criminals.show', $record) }}" class="font-semibold text-gray-200 hover:text-gold-500">{{ $record->name }}</a><p class="mt-1 text-xs text-gray-600">{{ $record->alias ?: 'No alias' }}</p></td><td class="px-4 py-4 text-gray-400">{{ $latest?->case_title ?? '—' }}</td><td class="px-4 py-4 text-gray-500">{{ $latest?->station?->name ?? '—' }}</td><td class="px-4 py-4 text-center font-semibold">{{ $record->cases_count }}</td><td class="px-5 py-4 text-right"><span class="{{ $record->wanted_status ? 'text-rose-400' : 'text-emerald-400' }}">{{ $record->wanted_status ? 'Wanted' : 'Not wanted' }}</span></td></tr>@empty<tr><td colspan="5" class="px-5 py-14 text-center text-gray-500">No linked criminals found.</td></tr>@endforelse</tbody>
                    @else
                        <thead class="bg-hq-900/60 text-[10px] uppercase tracking-widest text-gray-500"><tr><th class="px-5 py-4">Evidence</th><th class="px-4 py-4">FIR</th><th class="px-4 py-4">Thana</th><th class="px-4 py-4">Collector</th><th class="px-5 py-4 text-right">Collected</th></tr></thead><tbody class="divide-y divide-hq-700/60">@forelse($records as $record)<tr><td class="px-5 py-4"><p class="font-semibold text-gray-200">{{ $record->type }}</p><p class="mt-1 max-w-xs truncate text-xs text-gray-600">{{ $record->description ?: 'No description' }}</p></td><td class="px-4 py-4 text-gray-400">#{{ $record->case_id }} · {{ $record->case?->case_title }}</td><td class="px-4 py-4 text-gray-500">{{ $record->case?->station?->name }}</td><td class="px-4 py-4 text-gray-500">{{ $record->officer?->name }}</td><td class="px-5 py-4 text-right text-gray-500">{{ \Carbon\Carbon::parse($record->collected_date)->format('d M Y') }}</td></tr>@empty<tr><td colspan="5" class="px-5 py-14 text-center text-gray-500">No evidence found.</td></tr>@endforelse</tbody>
                    @endif
                </table>
            </div>
            @if($records->hasPages())<div class="border-t border-hq-700 px-5 py-4">{{ $records->links() }}</div>@endif
        </section>
    </div>
</x-command-layout>
