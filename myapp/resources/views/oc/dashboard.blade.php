<x-oc-layout pageTitle="Station Overview">
    <div class="mx-auto max-w-7xl space-y-6">
            <section class="relative overflow-hidden rounded-xl border border-[#243447] bg-[#1a252f] p-6 shadow-xl">
                <div class="absolute right-0 top-0 h-full w-80 bg-gradient-to-l from-blue-500/[0.08] to-transparent"></div>
                <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-blue-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Own station overview
                        </div>
                        <h2 class="text-2xl font-bold text-white">{{ $station->name }}</h2>
                        <p class="mt-2 text-sm text-gray-400">{{ $station->district }} @if($station->contact_number)<span class="mx-2 text-gray-600">&bull;</span>{{ $station->contact_number }}@endif</p>
                    </div>
                    <div class="rounded-lg border border-[#34495e] bg-[#0f1923]/70 px-4 py-3">
                        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-600">Officer in Charge</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $oc->name }}</p>
                        <p class="mt-0.5 text-[11px] text-gray-500">{{ $oc->badge_number }} &middot; {{ $oc->rank }}</p>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-2 gap-4 lg:grid-cols-5">
                @foreach([
                    ['Active Cases', $stats['active_cases'], 'amber'],
                    ['Pending Complaints', $stats['pending_complaints'], 'yellow'],
                    ['Officers', $stats['officers'], 'indigo'],
                    ['Evidence Items', $stats['evidence'], 'sky'],
                    ['Closed This Month', $stats['closed_this_month'], 'emerald'],
                ] as [$label, $value, $tone])
                    <article class="rounded-xl border border-[#243447] bg-[#1a252f] p-4 shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:border-[#34495e]">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500">{{ $label }}</p>
                                <p class="mt-2 text-2xl font-extrabold text-white">{{ number_format($value) }}</p>
                            </div>
                            <span class="h-2.5 w-2.5 rounded-full bg-{{ $tone }}-400 ring-4 ring-{{ $tone }}-400/10"></span>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <article class="overflow-hidden rounded-xl border border-[#243447] bg-[#1a252f] shadow-xl shadow-black/10">
                    <header class="flex items-center justify-between border-b border-[#243447] px-5 py-4">
                        <div><h3 class="text-sm font-semibold text-white">Recent FIR cases</h3><p class="mt-1 text-[11px] text-gray-500">Latest cases from {{ $station->name }}</p></div>
                        <span class="rounded bg-blue-500/10 px-2 py-1 text-[10px] font-bold text-blue-400">OWN STATION</span>
                    </header>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead><tr class="bg-[#0f1923]/50 text-[9px] font-bold uppercase tracking-widest text-gray-600"><th class="px-5 py-3">Case</th><th class="px-4 py-3">Officer</th><th class="px-4 py-3">Filed</th><th class="px-5 py-3 text-right">Status</th></tr></thead>
                            <tbody class="divide-y divide-[#243447]">
                                @forelse($recentCases as $case)
                                    <tr class="text-xs"><td class="px-5 py-3"><p class="max-w-[180px] truncate font-semibold text-gray-200">{{ $case->case_title }}</p><p class="mt-0.5 text-[10px] text-gray-600">FIR #{{ $case->case_id }}</p></td><td class="px-4 py-3 text-gray-500">{{ $case->officer?->name ?? 'Unassigned' }}</td><td class="px-4 py-3 text-gray-500">{{ date('d M Y', strtotime($case->date_filed)) }}</td><td class="px-5 py-3 text-right"><span class="rounded-full bg-sky-500/10 px-2 py-1 text-[10px] font-semibold text-sky-400">{{ $case->status }}</span></td></tr>
                                @empty
                                    <tr><td colspan="4" class="p-10 text-center text-sm text-gray-600">No cases at this station yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="overflow-hidden rounded-xl border border-[#243447] bg-[#1a252f] shadow-xl shadow-black/10">
                    <header class="flex items-center justify-between border-b border-[#243447] px-5 py-4">
                        <div><h3 class="text-sm font-semibold text-white">Incoming complaints</h3><p class="mt-1 text-[11px] text-gray-500">Recent citizen submissions to this station</p></div>
                        <span class="rounded bg-amber-500/10 px-2 py-1 text-[10px] font-bold text-amber-400">{{ $stats['pending_complaints'] }} PENDING</span>
                    </header>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead><tr class="bg-[#0f1923]/50 text-[9px] font-bold uppercase tracking-widest text-gray-600"><th class="px-5 py-3">Complainant</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Submitted</th><th class="px-5 py-3 text-right">Status</th></tr></thead>
                            <tbody class="divide-y divide-[#243447]">
                                @forelse($recentComplaints as $complaint)
                                    <tr class="text-xs"><td class="px-5 py-3"><p class="font-semibold text-gray-200">{{ $complaint->complainant_name }}</p><p class="mt-0.5 text-[10px] text-gray-600">{{ $complaint->complainant_nid }}</p></td><td class="max-w-[190px] px-4 py-3"><p class="truncate text-gray-500">{{ $complaint->description }}</p></td><td class="px-4 py-3 text-gray-500">{{ date('d M Y', strtotime($complaint->submitted_date)) }}</td><td class="px-5 py-3 text-right"><span class="rounded-full bg-amber-500/10 px-2 py-1 text-[10px] font-semibold text-amber-400">{{ $complaint->status }}</span></td></tr>
                                @empty
                                    <tr><td colspan="4" class="p-10 text-center text-sm text-gray-600">No complaints at this station yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
    </div>
</x-oc-layout>



