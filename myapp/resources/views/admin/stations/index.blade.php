<x-admin-layout pageTitle="Station Management">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-sky-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-sky-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                        National station registry
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Police station network</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-400">Monitor every registered station, assigned personnel, and local case workload from one command view.</p>
                </div>
                <a href="{{ route('admin.stations.create') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gold-500 px-5 py-3 text-sm font-bold text-hq-900 shadow-lg shadow-gold-500/10 transition hover:-translate-y-0.5 hover:bg-gold-600 sm:w-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Add police station
                </a>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-sky-500/40">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-sky-500/5"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Registered stations</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['total_stations']) }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-500/10 text-sky-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m6-14h1m4 0h1M9 11h1m4 0h1m-5 10v-5h4v5"/></svg>
                    </div>
                </div>
                <p class="mt-4 text-[11px] text-gray-500">Across the HQ registry</p>
            </div>

            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-emerald-500/40">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-emerald-500/5"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Operational</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['active_stations']) }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="mt-4 text-[11px] text-gray-500">Currently active stations</p>
            </div>

            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-indigo-500/40">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-indigo-500/5"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Assigned officers</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['total_officers']) }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.4-1.8M17 20H7m10 0v-2a5 5 0 00-10 0v2m0 0H2v-2a3 3 0 015.4-1.8M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <p class="mt-4 text-[11px] text-gray-500">Personnel across stations</p>
            </div>

            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-amber-500/40">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-amber-500/5"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total case load</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['total_cases']) }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                </div>
                <p class="mt-4 text-[11px] text-gray-500">All registered case files</p>
            </div>
        </section>

        @if (session('success'))
            <div class="flex items-center gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                <svg class="h-5 w-5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <div class="flex flex-col gap-3 border-b border-hq-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-white">Station directory</h3>
                    <p class="mt-1 text-[11px] text-gray-500">Registry details and live workload by station</p>
                </div>
                <span class="w-fit rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ $stations->count() }} records</span>
            </div>

            @if ($stations->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-hq-700 bg-hq-900 text-hq-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m6-14h1m4 0h1m-6 4h1m4 0h1"/></svg>
                    </div>
                    <h3 class="mt-4 text-sm font-semibold text-white">No stations registered</h3>
                    <p class="mx-auto mt-2 max-w-sm text-xs leading-5 text-gray-500">Create the first station record to start assigning officers and tracking local cases.</p>
                    <a href="{{ route('admin.stations.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-gold-500 px-4 py-2.5 text-xs font-bold text-hq-900 hover:bg-gold-600">Add first station</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="hq-table w-full min-w-[980px] text-left">
                        <thead>
                            <tr class="border-b border-hq-700 bg-hq-900/60 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                <th class="px-5 py-3.5">Station</th>
                                <th class="px-4 py-3.5">District</th>
                                <th class="px-4 py-3.5">Contact</th>
                                <th class="px-4 py-3.5 text-center">Officers</th>
                                <th class="px-4 py-3.5 text-center">Cases</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hq-700/60 text-[13px]">
                            @foreach ($stations as $station)
                                <tr class="text-gray-400 transition-colors hover:text-gray-200">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 flex-none items-center justify-center rounded-lg border border-hq-600 bg-hq-900 font-mono text-[10px] font-bold text-hq-300">{{ str_pad($station->station_id, 2, '0', STR_PAD_LEFT) }}</div>
                                            <div>
                                                <a href="{{ route('admin.stations.show', $station) }}" class="font-semibold text-gray-200 transition hover:text-gold-500">{{ $station->name }}</a>
                                                <p class="mt-0.5 text-[10px] text-gray-600">Registry #{{ $station->station_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">{{ $station->district }}</td>
                                    <td class="px-4 py-4 text-gray-500">{{ $station->contact_number ?? 'Not provided' }}</td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($station->officers_count) }}</td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($station->cases_count) }}</td>
                                    <td class="px-4 py-4">
                                        @if (strtolower($station->status) === 'active')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Active</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-500/20 bg-rose-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-rose-400"><span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('admin.stations.show', $station) }}" class="rounded-md bg-sky-500/10 px-2.5 py-1.5 text-[11px] font-semibold text-sky-400 transition hover:bg-sky-500/20">View</a>
                                            <a href="{{ route('admin.stations.edit', $station) }}" class="rounded-md bg-amber-500/10 px-2.5 py-1.5 text-[11px] font-semibold text-amber-400 transition hover:bg-amber-500/20">Edit</a>
                                            <form action="{{ route('admin.stations.toggle-status', $station) }}" method="POST" class="inline" onsubmit="return confirm('Change the operational status of this station?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-md px-2.5 py-1.5 text-[11px] font-semibold transition {{ strtolower($station->status) === 'active' ? 'bg-rose-500/10 text-rose-400 hover:bg-rose-500/20' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20' }}">{{ strtolower($station->status) === 'active' ? 'Deactivate' : 'Activate' }}</button>
                                            </form>
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
