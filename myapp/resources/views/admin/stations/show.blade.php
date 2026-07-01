<x-admin-layout pageTitle="Station Profile">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <a href="{{ route('admin.stations.index') }}" class="inline-flex items-center gap-2 text-xs font-medium text-hq-300 transition hover:text-gold-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to station registry
        </a>

        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-7">
            <div class="absolute -right-16 -top-24 h-64 w-64 rounded-full bg-sky-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 items-start gap-4 sm:items-center sm:gap-5">
                    <div class="flex h-14 w-14 flex-none items-center justify-center rounded-2xl border border-sky-500/20 bg-sky-500/10 text-sky-400 sm:h-16 sm:w-16">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m6-14h1m4 0h1m-6 4h1m4 0h1m-5 10v-5h4v5"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-gray-500">Registry #{{ $station->station_id }}</span>
                            @if (strtolower($station->status) === 'active')
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Operational</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-500/20 bg-rose-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-rose-400"><span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>Inactive</span>
                            @endif
                        </div>
                        <h2 class="truncate text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ $station->name }}</h2>
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs text-gray-400">
                            <span class="inline-flex items-center gap-1.5"><svg class="h-3.5 w-3.5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.7 16.7L13.4 21a2 2 0 01-2.8 0l-4.3-4.3a8 8 0 1111.4 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>{{ $station->district }}</span>
                            <span class="inline-flex items-center gap-1.5"><svg class="h-3.5 w-3.5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3l2 5-2 1a11 11 0 006 6l1-2 5 2v3a2 2 0 01-2 2h-1C9.3 20 4 14.7 4 8V5z"/></svg>{{ $station->contact_number ?? 'No contact number' }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.stations.edit', $station) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-hq-600 bg-hq-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:border-hq-500 hover:bg-hq-600 lg:w-auto">
                    <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.2 5.2l3.6 3.6M16.7 3.7a2.5 2.5 0 113.6 3.6L6.5 21H3v-3.6L16.7 3.7z"/></svg>
                    Edit station
                </a>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            <div class="rounded-xl border border-hq-700 bg-hq-800 p-4 sm:p-5"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 sm:text-[10px]">Assigned officers</p><div class="mt-2 flex items-end justify-between"><p class="text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($officers->count()) }}</p><span class="mb-1 text-[10px] text-indigo-400">Personnel</span></div></div>
            <div class="rounded-xl border border-hq-700 bg-hq-800 p-4 sm:p-5"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 sm:text-[10px]">Total cases</p><div class="mt-2 flex items-end justify-between"><p class="text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($caseStats->total_cases) }}</p><span class="mb-1 text-[10px] text-sky-400">Registered</span></div></div>
            <div class="rounded-xl border border-hq-700 bg-hq-800 p-4 sm:p-5"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 sm:text-[10px]">Active cases</p><div class="mt-2 flex items-end justify-between"><p class="text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($caseStats->active_cases) }}</p><span class="mb-1 text-[10px] text-amber-400">In progress</span></div></div>
            <div class="rounded-xl border border-hq-700 bg-hq-800 p-4 sm:p-5"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 sm:text-[10px]">Closed cases</p><div class="mt-2 flex items-end justify-between"><p class="text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($caseStats->closed_cases) }}</p><span class="mb-1 text-[10px] text-emerald-400">Resolved</span></div></div>
        </section>

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_1.6fr]">
            <div class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
                <div class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-semibold text-white">Registry details</h3><p class="mt-1 text-[11px] text-gray-600">Official station information</p></div>
                <dl class="divide-y divide-hq-700/60 px-5">
                    <div class="grid grid-cols-[7rem_1fr] gap-4 py-4"><dt class="text-[10px] font-bold uppercase tracking-wider text-gray-600">District</dt><dd class="text-sm text-gray-300">{{ $station->district }}</dd></div>
                    <div class="grid grid-cols-[7rem_1fr] gap-4 py-4"><dt class="text-[10px] font-bold uppercase tracking-wider text-gray-600">Contact</dt><dd class="text-sm text-gray-300">{{ $station->contact_number ?? 'Not provided' }}</dd></div>
                    <div class="grid grid-cols-1 gap-2 py-4 sm:grid-cols-[7rem_1fr] sm:gap-4"><dt class="text-[10px] font-bold uppercase tracking-wider text-gray-600">Address</dt><dd class="text-sm leading-6 text-gray-400">{{ $station->address ?? 'No physical address has been added.' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-xl border border-hq-700 bg-hq-800 p-5 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div><h3 class="text-sm font-semibold text-white">Case load distribution</h3><p class="mt-1 text-[11px] text-gray-600">Active compared with closed station cases</p></div>
                    @if ($caseStats->total_cases > 0)
                        <span class="w-fit rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-400">{{ number_format(($caseStats->active_cases / $caseStats->total_cases) * 100) }}% active</span>
                    @endif
                </div>
                @php $activeWidth = $caseStats->total_cases > 0 ? ($caseStats->active_cases / $caseStats->total_cases) * 100 : 0; @endphp
                <div class="mt-8">
                    <div class="flex items-center justify-between text-[11px]"><span class="font-medium text-amber-400">Active {{ number_format($caseStats->active_cases) }}</span><span class="font-medium text-emerald-400">Closed {{ number_format($caseStats->closed_cases) }}</span></div>
                    <div class="mt-3 flex h-3 overflow-hidden rounded-full border border-hq-700 bg-hq-900">
                        @if ($caseStats->total_cases > 0)
                            <div class="h-full bg-amber-500 transition-all duration-300" style="width: {{ $activeWidth }}%"></div>
                            <div class="h-full flex-1 bg-emerald-500/80"></div>
                        @endif
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-lg border border-hq-700 bg-hq-900/50 p-3"><p class="text-[10px] uppercase tracking-wider text-gray-600">Resolution rate</p><p class="mt-1 text-lg font-bold text-white">{{ $caseStats->total_cases > 0 ? number_format(($caseStats->closed_cases / $caseStats->total_cases) * 100) : 0 }}%</p></div>
                        <div class="rounded-lg border border-hq-700 bg-hq-900/50 p-3"><p class="text-[10px] uppercase tracking-wider text-gray-600">Recent files</p><p class="mt-1 text-lg font-bold text-white">{{ $recentCases->count() }}</p></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
                <div class="flex items-center justify-between border-b border-hq-700 px-5 py-4"><div><h3 class="text-sm font-semibold text-white">Assigned officers</h3><p class="mt-1 text-[11px] text-gray-600">Personnel currently attached to this station</p></div><span class="rounded-full bg-indigo-500/10 px-2.5 py-1 text-[10px] font-bold text-indigo-400">{{ $officers->count() }}</span></div>
                @if ($officers->isEmpty())
                    <div class="px-5 py-12 text-center text-sm text-gray-600">No officers are assigned to this station.</div>
                @else
                    <div class="overflow-x-auto"><table class="hq-table w-full min-w-[570px] text-left"><thead><tr class="border-b border-hq-700 bg-hq-900/60 text-[10px] font-bold uppercase tracking-widest text-gray-500"><th class="px-5 py-3">Officer</th><th class="px-4 py-3">Badge</th><th class="px-4 py-3">Rank</th><th class="px-5 py-3 text-right">Status</th></tr></thead><tbody class="divide-y divide-hq-700/60 text-[13px]">@foreach ($officers as $officer)<tr class="text-gray-400"><td class="px-5 py-3.5 font-semibold text-gray-300">{{ $officer->name }}</td><td class="px-4 py-3.5 font-mono text-gray-500">{{ $officer->badge_number }}</td><td class="px-4 py-3.5">{{ $officer->rank }}</td><td class="px-5 py-3.5 text-right"><span class="rounded-full px-2 py-1 text-[10px] font-bold uppercase {{ strtolower($officer->status) === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">{{ $officer->status }}</span></td></tr>@endforeach</tbody></table></div>
                @endif
            </div>

            <div class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
                <div class="flex items-center justify-between border-b border-hq-700 px-5 py-4"><div><h3 class="text-sm font-semibold text-white">Recent case files</h3><p class="mt-1 text-[11px] text-gray-600">Latest FIRs registered at this station</p></div><span class="rounded-full bg-amber-500/10 px-2.5 py-1 text-[10px] font-bold text-amber-400">Latest 5</span></div>
                @if ($recentCases->isEmpty())
                    <div class="px-5 py-12 text-center text-sm text-gray-600">No cases are registered at this station.</div>
                @else
                    <div class="overflow-x-auto"><table class="hq-table w-full min-w-[650px] text-left"><thead><tr class="border-b border-hq-700 bg-hq-900/60 text-[10px] font-bold uppercase tracking-widest text-gray-500"><th class="px-5 py-3">Case</th><th class="px-4 py-3">Investigator</th><th class="px-4 py-3">Filed</th><th class="px-5 py-3 text-right">Status</th></tr></thead><tbody class="divide-y divide-hq-700/60 text-[13px]">@foreach ($recentCases as $case)<tr class="text-gray-400"><td class="px-5 py-3.5 font-semibold text-gray-300">{{ $case->case_title }}</td><td class="px-4 py-3.5 text-gray-500">{{ $case->officer?->name ?? 'Unassigned' }}</td><td class="px-4 py-3.5 whitespace-nowrap text-gray-600">{{ \Carbon\Carbon::parse($case->date_filed)->format('d M Y') }}</td><td class="px-5 py-3.5 text-right"><span class="rounded-full px-2 py-1 text-[10px] font-bold uppercase {{ strtolower($case->status) === 'closed' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">{{ $case->status }}</span></td></tr>@endforeach</tbody></table></div>
                @endif
            </div>
        </section>
    </div>
</x-admin-layout>
