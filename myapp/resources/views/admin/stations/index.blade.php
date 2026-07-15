<x-admin-layout pageTitle="Station Management">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-sky-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-sky-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                        National station registry
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Police station network</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-400">Monitor every registered station, assigned personnel, and local case workload from one command view.</p>
                </div>
                <a href="{{ route('admin.stations.create') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gold-500/40 bg-transparent px-5 py-3 text-sm font-bold text-gold-400 transition-all hover:-translate-y-0.5 hover:border-gold-500 hover:bg-gold-500/10 hover:text-gold-300 sm:w-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Add police station
                </a>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-sky-500/40 hover:bg-hq-800/80">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-sky-500/5 transition group-hover:bg-sky-500/10"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Registered stations</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['total_stations']) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-500/10 text-sky-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m6-14h1m4 0h1M9 11h1m4 0h1m-5 10v-5h4v5"/></svg>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-500">Across the HQ registry</p>
            </div>

            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-emerald-500/40 hover:bg-hq-800/80">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-emerald-500/5 transition group-hover:bg-emerald-500/10"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Operational</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['active_stations']) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-500">Currently active stations</p>
            </div>

            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-indigo-500/40 hover:bg-hq-800/80">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-indigo-500/5 transition group-hover:bg-indigo-500/10"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Assigned officers</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['total_officers']) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.4-1.8M17 20H7m10 0v-2a5 5 0 00-10 0v2m0 0H2v-2a3 3 0 015.4-1.8M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-500">Personnel across stations</p>
            </div>

            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-amber-500/40 hover:bg-hq-800/80">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-amber-500/5 transition group-hover:bg-amber-500/10"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Total case load</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['total_cases']) }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-500">All registered case files</p>
            </div>
        </section>

        @if (session('success'))
            <div class="flex items-center gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                <svg class="h-5 w-5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <section class="grid gap-5 xl:grid-cols-2">
            @foreach ([
                ['title' => 'Metropolitan Police HQs', 'subtitle' => 'Commissioner-led city commands', 'type' => 'metro', 'items' => $metroHqs],
                ['title' => 'District Police HQs', 'subtitle' => 'SP-led district commands', 'type' => 'district', 'items' => $districtHqs],
            ] as $browser)
                <article class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5" data-admin-browser="{{ $browser['type'] }}">
                    <div class="flex items-center justify-between gap-4 border-b border-hq-700 bg-hq-900/35 px-5 py-4">
                        <div>
                            <h3 class="text-base font-semibold text-white">{{ $browser['title'] }}</h3>
                            <p class="mt-1 text-xs text-gray-500">{{ $browser['subtitle'] }}</p>
                        </div>
                        <span class="rounded-full border border-hq-600 bg-hq-900/70 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $browser['items']->count() }} HQs</span>
                    </div>

                    <div class="min-h-[320px] p-5">
                        @forelse ($browser['items'] as $hq)
                            <div class="hidden flex-col relative min-h-[280px] overflow-hidden rounded-xl border border-hq-700 bg-hq-900/30 p-5 transition hover:border-gold-500/40" data-admin-card>
                                <div class="absolute right-0 top-0 h-28 w-28 rounded-bl-full {{ $hq->type === 'metropolitanHQ' ? 'bg-sky-500/5' : 'bg-indigo-500/5' }}"></div>
                                
                                <div class="relative flex items-start justify-between gap-5">
                                    <div>
                                        <div class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-gray-400">
                                            <span class="h-2 w-2 rounded-full {{ $hq->type === 'metropolitanHQ' ? 'bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.6)]' : 'bg-indigo-400 shadow-[0_0_8px_rgba(129,140,248,0.6)]' }}"></span>
                                            {{ $hq->head_rank ?? ($hq->type === 'metropolitanHQ' ? 'Commissioner' : 'SP') }}
                                        </div>
                                        <h4 class="text-2xl font-bold tracking-tight text-white">{{ $hq->name }}</h4>
                                        <p class="mt-2 text-sm font-medium text-gray-500">
                                            {{ $hq->district ?: ($hq->type === 'metropolitanHQ' ? 'Metropolitan area' : 'District area') }}
                                            @if($hq->division)
                                                <span class="mx-1.5 text-gray-600">•</span> {{ $hq->division }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="relative z-10 flex h-12 w-12 flex-none items-center justify-center rounded-xl {{ $hq->type === 'metropolitanHQ' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' }}">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-3M9 9h1m-1 4h1m0 4h1m5-4h1m-1 4h1"/></svg>
                                    </div>
                                </div>
                                <p class="relative mt-3 text-sm leading-6 text-gray-400">{{ $hq->address ?: 'HQ address will be updated soon.' }}</p>

                                <div class="relative mt-5 grid grid-cols-2 gap-4">
                                    <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-4">
                                        <div class="absolute right-0 top-0 h-16 w-16 rounded-bl-full bg-gold-500/5"></div>
                                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Thanas</p>
                                        <p class="mt-1 text-2xl font-extrabold text-white">{{ number_format($hq->thanas_count) }}</p>
                                    </div>
                                    <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-4">
                                        <div class="absolute right-0 top-0 h-16 w-16 rounded-bl-full {{ strtolower($hq->status) === 'active' ? 'bg-emerald-500/5' : 'bg-rose-500/5' }}"></div>
                                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Status</p>
                                        <p class="mt-1 text-sm font-bold uppercase tracking-wide {{ strtolower($hq->status) === 'active' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $hq->status }}</p>
                                    </div>
                                </div>

                                <div class="relative mt-auto flex flex-col gap-4 pt-6 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-2">
                                        <button type="button" data-admin-prev class="flex h-10 w-10 items-center justify-center rounded-full border border-hq-600 bg-transparent text-gray-400 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-gold-300 focus:ring-2 focus:ring-gold-500/40 outline-none">‹</button>
                                        <span class="min-w-12 text-center text-sm font-bold text-gray-500" data-admin-current>1/{{ $browser['items']->count() }}</span>
                                        <button type="button" data-admin-next class="flex h-10 w-10 items-center justify-center rounded-full border border-hq-600 bg-transparent text-gray-400 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-gold-300 focus:ring-2 focus:ring-gold-500/40 outline-none">›</button>
                                    </div>
                                    <a href="{{ route('admin.stations.show', $hq) }}" class="inline-flex items-center justify-center rounded-lg border border-gold-500/40 bg-transparent px-4 py-2.5 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10 hover:text-gold-300 focus:ring-2 focus:ring-gold-500/40">View Thanas</a>
                                </div>
                            </div>
                        @empty
                            <div class="flex h-full flex-col items-center justify-center rounded-xl border border-dashed border-hq-700 bg-hq-900/30 p-8 text-center text-sm text-gray-500">
                                <svg class="mb-3 h-8 w-8 text-hq-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                No HQ records found.
                            </div>
                        @endforelse
                    </div>
                </article>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <div class="flex flex-col gap-3 border-b border-hq-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-white">Station directory</h3>
                    <p class="mt-1 text-xs text-gray-500">Registry details and live workload by station</p>
                </div>
                <span class="w-fit rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $stations->count() }} records</span>
            </div>

            <form method="GET" action="{{ route('admin.stations.index') }}" class="grid gap-3 border-b border-hq-700 bg-hq-900/20 p-5 md:grid-cols-2 xl:grid-cols-6">
                @if(request()->filled('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <input name="directory_search" value="{{ request('directory_search') }}" placeholder="Search station, district, contact..." class="h-11 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none transition placeholder:text-gray-600 focus:border-gold-500/60 xl:col-span-2">
                <select name="type" class="h-11 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-gray-300 outline-none transition focus:border-gold-500/60">
                    <option value="">All types</option>
                    <option value="hq" @selected(request('type') === 'hq')>HQ</option>
                    <option value="metropolitanHQ" @selected(request('type') === 'metropolitanHQ')>Metropolitan HQ</option>
                    <option value="districtHQ" @selected(request('type') === 'districtHQ')>District HQ</option>
                    <option value="thana" @selected(request('type') === 'thana')>Thana</option>
                </select>
                <select name="status" class="h-11 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-gray-300 outline-none transition focus:border-gold-500/60">
                    <option value="">All status</option>
                    <option value="Active" @selected(request('status') === 'Active')>Active</option>
                    <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
                </select>
                <select name="parent_id" class="h-11 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-gray-300 outline-none transition focus:border-gold-500/60">
                    <option value="">All parent HQs</option>
                    @foreach($parentStations as $parentStation)
                        <option value="{{ $parentStation->station_id }}" @selected((string) request('parent_id') === (string) $parentStation->station_id)>{{ $parentStation->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="h-11 flex-1 rounded-lg border border-gold-500/40 bg-transparent px-4 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10">Filter</button>
                    <a href="{{ route('admin.stations.index', request()->filled('search') ? ['search' => request('search')] : []) }}" class="flex h-11 items-center justify-center rounded-lg border border-hq-600 px-4 text-sm font-bold text-gray-400 transition hover:border-hq-500 hover:bg-hq-700/30 hover:text-white">Reset</a>
                </div>
            </form>

            @if ($stations->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-hq-700 bg-hq-900 text-hq-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m6-14h1m4 0h1m-6 4h1m4 0h1"/></svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-white">No stations registered</h3>
                    <p class="mx-auto mt-2 max-w-sm text-sm leading-5 text-gray-500">Create the first station record to start assigning officers and tracking local cases.</p>
                    <a href="{{ route('admin.stations.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg border border-gold-500/40 bg-transparent px-4 py-2.5 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10 hover:text-gold-300">Add first station</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px] text-left whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-hq-700 bg-hq-900/60 text-xs font-bold uppercase tracking-widest text-gray-400">
                                <th class="px-5 py-4">Station</th>
                                <th class="px-4 py-4">District</th>
                                <th class="px-4 py-4">Contact</th>
                                <th class="px-4 py-4 text-center">Officers</th>
                                <th class="px-4 py-4 text-center">Cases</th>
                                <th class="px-4 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hq-700/60 text-sm">
                            @foreach ($stations as $station)
                                <tr class="text-gray-400 transition-colors hover:bg-hq-700/20 hover:text-gray-200 group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg border border-hq-600 bg-hq-900 font-mono text-xs font-bold text-hq-300">{{ str_pad($station->station_id, 2, '0', STR_PAD_LEFT) }}</div>
                                            <div>
                                                <a href="{{ route('admin.stations.show', $station) }}" class="font-semibold text-gray-200 transition hover:text-gold-500">{{ $station->name }}</a>
                                                <p class="mt-0.5 text-xs text-gray-500">Registry #{{ $station->station_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">{{ $station->district }}</td>
                                    <td class="px-4 py-4 text-gray-500">{{ $station->contact_number ?? 'Not provided' }}</td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($station->officers_count) }}</td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($station->cases_count) }}</td>
                                    <td class="px-4 py-4">
                                        @if (strtolower($station->status) === 'active')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Active</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-500/20 bg-rose-500/10 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-rose-400"><span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                      <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.stations.show', $station) }}" class="rounded-md border border-sky-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-sky-400 transition hover:border-sky-500 hover:bg-sky-500/10">View</a>
                                            <a href="{{ route('admin.stations.edit', $station) }}" class="rounded-md border border-amber-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-amber-400 transition hover:border-amber-500 hover:bg-amber-500/10">Edit</a>
                                            <form action="{{ route('admin.stations.toggle-status', $station) }}" method="POST" class="inline" onsubmit="return confirm('Change the operational status of this station?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-md border bg-transparent px-3 py-1.5 text-xs font-semibold transition {{ strtolower($station->status) === 'active' ? 'border-rose-500/30 text-rose-400 hover:border-rose-500 hover:bg-rose-500/10' : 'border-emerald-500/30 text-emerald-400 hover:border-emerald-500 hover:bg-emerald-500/10' }}">{{ strtolower($station->status) === 'active' ? 'Deactivate' : 'Activate' }}</button>
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

    <script>
        document.querySelectorAll('[data-admin-browser]').forEach((browser) => {
            const cards = [...browser.querySelectorAll('[data-admin-card]')];
            const current = browser.querySelector('[data-admin-current]');
            let index = 0;

            const render = () => {
                cards.forEach((card, cardIndex) => {
                    // Toggles between purely hidden and our base flexbox configuration
                    card.classList.toggle('hidden', cardIndex !== index);
                    card.classList.toggle('flex', cardIndex === index);
                });

                if (current) current.textContent = cards.length ? `${index + 1}/${cards.length}` : '0/0';
            };

            browser.querySelectorAll('[data-admin-prev]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!cards.length) return;
                    index = (index - 1 + cards.length) % cards.length;
                    render();
                });
            });

            browser.querySelectorAll('[data-admin-next]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!cards.length) return;
                    index = (index + 1) % cards.length;
                    render();
                });
            });

            render();
        });
    </script>
</x-admin-layout>
