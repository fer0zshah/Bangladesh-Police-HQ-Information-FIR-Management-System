<x-admin-layout pageTitle="{{ $station->name }}">
    <div class="mx-auto max-w-[1440px] space-y-6">
        
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-gold-500">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold-500 shadow-[0_0_8px_rgba(234,179,8,0.6)]"></span>
                        {{ $station->head_rank ?? 'Police command' }}
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">{{ $station->name }}</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-400">
                        {{ $station->address ?: 'HQ address will be updated soon.' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.stations.index') }}" class="inline-flex items-center justify-center rounded-lg border border-hq-600 bg-transparent px-4 py-2.5 text-sm font-bold text-gray-300 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-white">
                        Back to browser
                    </a>
                    <a href="{{ route('admin.stations.edit', $station) }}" class="inline-flex items-center justify-center rounded-lg border border-gold-500/40 bg-transparent px-5 py-2.5 text-sm font-bold text-gold-400 transition-all hover:-translate-y-0.5 hover:border-gold-500 hover:bg-gold-500/10 hover:text-gold-300">
                        Edit HQ
                    </a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-sky-500/40 hover:bg-hq-800/80">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-sky-500/5 transition group-hover:bg-sky-500/10"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Total thanas</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($hqSummary['thanas']) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-500/10 text-sky-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-3"/></svg>
                    </div>
                </div>
                <p class="relative mt-4 text-xs text-gray-500">Under this HQ</p>
            </div>

            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-emerald-500/40 hover:bg-hq-800/80">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-emerald-500/5 transition group-hover:bg-emerald-500/10"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Active thanas</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($hqSummary['active_thanas']) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="relative mt-4 text-xs text-gray-500">Operational stations</p>
            </div>

            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-indigo-500/40 hover:bg-hq-800/80">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-indigo-500/5 transition group-hover:bg-indigo-500/10"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Officers</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($hqSummary['officers']) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.4-1.8M7 20H2v-2a3 3 0 015.4-1.8M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <p class="relative mt-4 text-xs text-gray-500">Assigned personnel</p>
            </div>

            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-amber-500/40 hover:bg-hq-800/80">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-amber-500/5 transition group-hover:bg-amber-500/10"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Cases</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($hqSummary['cases']) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                </div>
                <p class="relative mt-4 text-xs text-gray-500">Case workload</p>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <div class="flex flex-col gap-3 border-b border-hq-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-white">Thana stations under this HQ</h3>
                    <p class="mt-1 text-xs text-gray-500">Open a thana to manage its officers, cases, and operational status.</p>
                </div>
                <span class="w-fit rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $thanas->count() }} records</span>
            </div>

            @if ($thanas->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-hq-700 bg-hq-900 text-hq-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m6-14h1m4 0h1m-6 4h1m4 0h1"/></svg>
                    </div>
                    <h3 class="mt-4 text-sm font-semibold text-white">No stations attached</h3>
                    <p class="mx-auto mt-2 max-w-sm text-xs leading-5 text-gray-500">No thana stations are currently attached to this headquarters command.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-hq-700 bg-hq-900/60 text-xs font-bold uppercase tracking-widest text-gray-400">
                                <th class="px-5 py-4">Thana</th>
                                <th class="px-4 py-4">District</th>
                                <th class="px-4 py-4">Contact</th>
                                <th class="px-4 py-4 text-center">Officers</th>
                                <th class="px-4 py-4 text-center">Cases</th>
                                <th class="px-4 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hq-700/60 text-sm">
                            @foreach ($thanas as $thana)
                                <tr class="group text-gray-400 transition-colors hover:bg-hq-700/20 hover:text-gray-200">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg border border-hq-600 bg-hq-900 font-mono text-xs font-bold text-hq-300">{{ str_pad($thana->station_id, 2, '0', STR_PAD_LEFT) }}</div>
                                            <div>
                                                <a href="{{ route('admin.stations.show', $thana) }}" class="font-semibold text-gray-200 transition hover:text-gold-500">{{ $thana->name }}</a>
                                                <p class="mt-0.5 text-xs text-gray-500">Registry #{{ $thana->station_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">{{ $thana->district ?: 'N/A' }}</td>
                                    <td class="px-4 py-4 text-gray-500">{{ $thana->contact_number ?? 'Not provided' }}</td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($thana->officers_count) }}</td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($thana->cases_count) }}</td>
                                    <td class="px-4 py-4">
                                        @if (strtolower($thana->status) === 'active')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Active</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-500/20 bg-rose-500/10 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-rose-400"><span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center gap-2 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100">
                                            <a href="{{ route('admin.stations.show', $thana) }}" class="rounded-md border border-sky-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-sky-400 transition hover:border-sky-500 hover:bg-sky-500/10">View</a>
                                            <a href="{{ route('admin.stations.edit', $thana) }}" class="rounded-md border border-amber-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-amber-400 transition hover:border-amber-500 hover:bg-amber-500/10">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-admin-layout>
