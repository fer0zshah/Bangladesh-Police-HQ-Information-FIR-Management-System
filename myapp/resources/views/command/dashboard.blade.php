<x-command-layout pageTitle="Command Overview">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 to-hq-700/70 p-6">
            <div class="absolute right-0 top-0 h-40 w-40 rounded-bl-full bg-gold-500/5"></div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gold-500">{{ auth()->user()->role === 'metro_head' ? 'Metropolitan command' : 'District command' }}</p>
            <h2 class="mt-2 text-2xl font-bold text-white">{{ $headquarters->name }}</h2>
            <p class="mt-2 text-sm text-gray-400">{{ $headquarters->division }}{{ $headquarters->district ? ' · '.$headquarters->district : '' }} · Only child thanas are included in every figure below.</p>
        </section>

        <section class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
            @foreach ([
                ['label'=>'Thanas','value'=>$stats['total_stations'],'note'=>'Under your command','color'=>'sky'],
                ['label'=>'Personnel','value'=>$stats['total_officers'],'note'=>'All ranks included','color'=>'indigo'],
                ['label'=>'Station OCs','value'=>$stats['station_ocs'],'note'=>'Commanding thanas','color'=>'violet'],
                ['label'=>'Active cases','value'=>$stats['active_cases'],'note'=>'Open investigations','color'=>'amber'],
                ['label'=>'Pending complaints','value'=>$stats['pending_complaints'],'note'=>'Awaiting action','color'=>'yellow'],
                ['label'=>'Closed this month','value'=>$stats['closed_this_month'],'note'=>'Monthly resolution','color'=>'emerald'],
            ] as $card)
                <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5">
                    <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-{{ $card['color'] }}-500/5"></div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">{{ $card['label'] }}</p>
                    <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($card['value']) }}</p>
                    <p class="mt-3 text-[10px] text-{{ $card['color'] }}-400">{{ $card['note'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <article class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
                <div class="flex items-center justify-between border-b border-hq-700 px-5 py-4"><div><h3 class="text-sm font-semibold text-white">Recent FIRs</h3><p class="mt-1 text-[11px] text-gray-500">Latest filings in your thanas</p></div></div>
                <div class="overflow-x-auto">
                    <table class="hq-table w-full text-left text-sm">
                        <thead class="bg-hq-900/60 text-[10px] uppercase tracking-widest text-gray-500"><tr><th class="px-5 py-3">Case</th><th class="px-4 py-3">Thana</th><th class="px-4 py-3">Status</th></tr></thead>
                        <tbody class="divide-y divide-hq-700/60">
                            @forelse($recentCases as $case)
                                <tr><td class="px-5 py-4 font-semibold text-gray-200">{{ $case->case_title }}</td><td class="px-4 py-4 text-gray-500">{{ $case->station?->name }}</td><td class="px-4 py-4 text-amber-400">{{ $case->status }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-10 text-center text-gray-500">No FIRs in this jurisdiction.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
                <div class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-semibold text-white">Recent complaints</h3><p class="mt-1 text-[11px] text-gray-500">Citizen submissions to your thanas</p></div>
                <div class="overflow-x-auto">
                    <table class="hq-table w-full text-left text-sm">
                        <thead class="bg-hq-900/60 text-[10px] uppercase tracking-widest text-gray-500"><tr><th class="px-5 py-3">Complainant</th><th class="px-4 py-3">Thana</th><th class="px-4 py-3">Status</th></tr></thead>
                        <tbody class="divide-y divide-hq-700/60">
                            @forelse($recentComplaints as $complaint)
                                <tr><td class="px-5 py-4 font-semibold text-gray-200">{{ $complaint->complainant_name }}</td><td class="px-4 py-4 text-gray-500">{{ $complaint->station?->name }}</td><td class="px-4 py-4 text-yellow-400">{{ $complaint->status }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-10 text-center text-gray-500">No complaints in this jurisdiction.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </div>
</x-command-layout>
