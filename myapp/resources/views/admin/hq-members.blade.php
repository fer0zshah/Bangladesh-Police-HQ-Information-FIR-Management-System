<x-admin-layout pageTitle="Officer Command Hierarchy">
    @php
        $metroHeads = $members->where('role', 'metro_head')->values();
        $districtHeads = $members->where('role', 'district_head')->values();
    @endphp

    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gold-500">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold-500 shadow-[0_0_8px_rgba(234,179,8,0.6)]"></span>
                        IGP officer command
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Metropolitan & District Command Heads</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-400">Browse Commissioners and SPs first. Open a command head to see all OCs under their metro/district area.</p>
                </div>
                <div class="rounded-xl border border-gold-500/20 bg-gold-500/10 px-4 py-3 text-sm font-medium text-gold-200">
                    Head → OC → Station officers
                </div>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-sky-500/40">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-sky-500/5 transition group-hover:bg-sky-500/10"></div>
                <div class="relative flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-widest text-gray-500">Commissioners</p><p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['metro_heads']) }}</p></div><div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-500/10 text-sky-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18"/></svg></div></div>
                <p class="relative mt-4 text-xs text-sky-400">Metropolitan heads</p>
            </div>
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-indigo-500/40">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-indigo-500/5 transition group-hover:bg-indigo-500/10"></div>
                <div class="relative flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-widest text-gray-500">District SPs</p><p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['district_heads']) }}</p></div><div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg></div></div>
                <p class="relative mt-4 text-xs text-indigo-400">District heads</p>
            </div>
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-emerald-500/40">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-emerald-500/5 transition group-hover:bg-emerald-500/10"></div>
                <div class="relative flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-widest text-gray-500">Station OCs</p><p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['station_ocs']) }}</p></div><div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div></div>
                <p class="relative mt-4 text-xs text-emerald-400">Thana command</p>
            </div>
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-amber-500/40">
                <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-amber-500/5 transition group-hover:bg-amber-500/10"></div>
                <div class="relative flex items-start justify-between"><div><p class="text-xs font-bold uppercase tracking-widest text-gray-500">All officers</p><p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['total_officers']) }}</p></div><div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.4-1.8M7 20H2v-2a3 3 0 015.4-1.8M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div></div>
                <p class="relative mt-4 text-xs text-amber-400">Full directory</p>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-2">
            @foreach ([
                ['title' => 'Metropolitan HQ Heads', 'subtitle' => 'Police Commissioners', 'type' => 'metro', 'items' => $metroHeads],
                ['title' => 'District HQ Heads', 'subtitle' => 'Superintendents of Police', 'type' => 'district', 'items' => $districtHeads],
            ] as $browser)
                <article class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5" data-head-browser="{{ $browser['type'] }}">
                    <div class="flex items-center justify-between gap-4 border-b border-hq-700 bg-hq-900/35 px-5 py-4">
                        <div>
                            <h3 class="text-base font-semibold text-white">{{ $browser['title'] }}</h3>
                            <p class="mt-1 text-xs text-gray-500">{{ $browser['subtitle'] }}</p>
                        </div>
                        <span class="rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $browser['items']->count() }} heads</span>
                    </div>

                    <div class="min-h-[330px] p-5">
                        @forelse ($browser['items'] as $member)
                            <div class="relative hidden min-h-[290px] overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-gold-500/40" data-head-card>
                                <div class="absolute right-0 top-0 h-28 w-28 rounded-bl-full {{ $member->role === 'metro_head' ? 'bg-sky-500/5' : 'bg-indigo-500/5' }}"></div>
                                <div class="relative flex items-start justify-between gap-5">
                                    <div>
                                        <div class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-500">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $member->role === 'metro_head' ? 'bg-sky-400' : 'bg-indigo-400' }}"></span>
                                            {{ $member->role_label }}
                                        </div>
                                        <h4 class="text-2xl font-bold tracking-tight text-white">{{ $member->name }}</h4>
                                        <p class="mt-1 text-sm text-gray-500">{{ $member->email }}</p>
                                    </div>
                                    <div class="relative z-10 flex h-12 w-12 flex-none items-center justify-center rounded-xl {{ $member->role === 'metro_head' ? 'border border-sky-500/20 bg-sky-500/10 text-sky-400' : 'border border-indigo-500/20 bg-indigo-500/10 text-indigo-400' }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM4 20a8 8 0 1116 0"/></svg>
                                    </div>
                                </div>

                                <p class="relative mt-4 text-sm text-gray-400">{{ $member->station?->name ?? 'No headquarters assigned' }}</p>
                                <div class="relative mt-5 grid grid-cols-3 gap-3">
                                    <div class="rounded-xl border border-hq-700 bg-hq-800 p-3 text-center"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Thanas</p><p class="mt-1 text-xl font-bold text-white">{{ number_format($member->scope_stats['thanas']) }}</p></div>
                                    <div class="rounded-xl border border-hq-700 bg-hq-800 p-3 text-center"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Officers</p><p class="mt-1 text-xl font-bold text-white">{{ number_format($member->scope_stats['officers']) }}</p></div>
                                    <div class="rounded-xl border border-hq-700 bg-hq-800 p-3 text-center"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Cases</p><p class="mt-1 text-xl font-bold text-white">{{ number_format($member->scope_stats['active_cases']) }}</p></div>
                                </div>

                                <div class="relative mt-auto flex flex-col gap-3 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center gap-2">
                                        <button type="button" data-head-prev class="flex h-10 w-10 items-center justify-center rounded-full border border-hq-600 bg-transparent text-gray-400 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-gold-300">‹</button>
                                        <span class="min-w-12 text-center text-xs font-bold text-gray-500" data-head-current>1/{{ $browser['items']->count() }}</span>
                                        <button type="button" data-head-next class="flex h-10 w-10 items-center justify-center rounded-full border border-hq-600 bg-transparent text-gray-400 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-gold-300">›</button>
                                    </div>
                                    <a href="{{ route('admin.hq-members.show', $member) }}" class="inline-flex items-center justify-center rounded-lg border border-gold-500/40 bg-transparent px-4 py-2.5 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10 hover:text-gold-300">View OCs</a>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-hq-700 bg-hq-900/30 p-8 text-center text-sm text-gray-500">No command heads found.</div>
                        @endforelse
                    </div>
                </article>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <div class="flex flex-col gap-3 border-b border-hq-700 bg-hq-900/35 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-white">Officer directory</h3>
                    <p class="mt-1 text-xs text-gray-500">All officers across HQ, metropolitan, district, and thana command levels.</p>
                </div>
                <span class="w-fit rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $officers->count() }} records</span>
            </div>

            <form method="GET" action="{{ route('admin.hq-members.index') }}" class="grid gap-3 border-b border-hq-700 bg-hq-900/20 p-5 md:grid-cols-2 xl:grid-cols-7">
                <input name="officer_search" value="{{ request('officer_search') }}" placeholder="Search name, rank, badge, station..." class="h-11 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none transition placeholder:text-gray-600 focus:border-gold-500/60 xl:col-span-2">
                <select name="rank" class="h-11 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-gray-300 outline-none transition focus:border-gold-500/60">
                    <option value="">All ranks</option>
                    @foreach($ranks as $rank)
                        <option value="{{ $rank }}" @selected(request('rank') === $rank)>{{ $rank }}</option>
                    @endforeach
                </select>
                <select name="status" class="h-11 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-gray-300 outline-none transition focus:border-gold-500/60">
                    <option value="">All status</option>
                    <option value="Active" @selected(request('status') === 'Active')>Active</option>
                    <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
                </select>
                <select name="oc" class="h-11 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-gray-300 outline-none transition focus:border-gold-500/60">
                    <option value="">All roles</option>
                    <option value="yes" @selected(request('oc') === 'yes')>OCs only</option>
                    <option value="no" @selected(request('oc') === 'no')>Non-OCs</option>
                </select>
                <select name="station_id" class="h-11 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-gray-300 outline-none transition focus:border-gold-500/60">
                    <option value="">All stations</option>
                    @foreach($stations as $station)
                        <option value="{{ $station->station_id }}" @selected((string) request('station_id') === (string) $station->station_id)>{{ $station->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="h-11 flex-1 rounded-lg border border-gold-500/40 bg-transparent px-4 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10">Filter</button>
                    <a href="{{ route('admin.hq-members.index') }}" class="flex h-11 items-center justify-center rounded-lg border border-hq-600 px-4 text-sm font-bold text-gray-400 transition hover:border-hq-500 hover:bg-hq-700/30 hover:text-white">Reset</a>
                </div>
            </form>

            @if($officers->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-hq-700 bg-hq-900 text-hq-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </div>
                    <h3 class="mt-4 text-sm font-semibold text-white">No officers registered</h3>
                    <p class="mx-auto mt-2 max-w-sm text-xs leading-5 text-gray-500">Add officers from the officer management form to populate this directory.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1040px] text-left whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-hq-700 bg-hq-900/60 text-xs font-bold uppercase tracking-widest text-gray-400">
                                <th class="px-5 py-4">Officer</th>
                                <th class="px-4 py-4">Rank</th>
                                <th class="px-4 py-4">Station / HQ</th>
                                <th class="px-4 py-4">Badge</th>
                                <th class="px-4 py-4 text-center">Cases</th>
                                <th class="px-4 py-4 text-center">Evidence</th>
                                <th class="px-4 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hq-700/60 text-sm">
                            @foreach($officers as $officer)
                                <tr class="group text-gray-400 transition-colors hover:bg-hq-700/20 hover:text-gray-200">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg border border-hq-600 bg-hq-900 font-mono text-xs font-bold text-hq-300">{{ str_pad($officer->officer_id, 2, '0', STR_PAD_LEFT) }}</div>
                                            <div>
                                                <a href="{{ route('admin.officers.show', $officer) }}" class="font-semibold text-gray-200 transition hover:text-gold-500">{{ $officer->name }}</a>
                                                <div class="mt-1 flex items-center gap-2">
                                                    @if($officer->is_oc)
                                                        <span class="rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400">OC</span>
                                                    @endif
                                                    @if($officer->user)
                                                        <span class="rounded-full border border-sky-500/20 bg-sky-500/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-sky-400">Login</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">{{ $officer->rank }}</td>
                                    <td class="px-4 py-4">
                                        <p class="font-medium text-gray-300">{{ $officer->station?->name ?? 'Unassigned' }}</p>
                                        <p class="mt-0.5 text-xs text-gray-600">{{ $officer->station?->type ?? 'No station type' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-gray-500">{{ $officer->badge_number }}</td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($officer->cases_count) }}</td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($officer->evidence_count) }}</td>
                                    <td class="px-4 py-4">
                                        @if(strtolower($officer->status) === 'active')
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Active</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-rose-500/20 bg-rose-500/10 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-rose-400"><span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>{{ $officer->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center gap-2 opacity-0 transition-opacity group-hover:opacity-100 focus-within:opacity-100">
                                            <a href="{{ route('admin.officers.show', $officer) }}" class="rounded-md border border-sky-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-sky-400 transition hover:border-sky-500 hover:bg-sky-500/10">View</a>
                                            <a href="{{ route('admin.officers.edit', $officer) }}" class="rounded-md border border-amber-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-amber-400 transition hover:border-amber-500 hover:bg-amber-500/10">Edit</a>
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
        document.querySelectorAll('[data-head-browser]').forEach((browser) => {
            const cards = [...browser.querySelectorAll('[data-head-card]')];
            const current = browser.querySelector('[data-head-current]');
            let index = 0;

            const render = () => {
                cards.forEach((card, cardIndex) => {
                    card.classList.toggle('hidden', cardIndex !== index);
                    card.classList.toggle('flex', cardIndex === index);
                    card.classList.toggle('flex-col', cardIndex === index);
                });
                if (current) current.textContent = cards.length ? `${index + 1}/${cards.length}` : '0/0';
            };

            browser.querySelectorAll('[data-head-prev]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!cards.length) return;
                    index = (index - 1 + cards.length) % cards.length;
                    render();
                });
            });

            browser.querySelectorAll('[data-head-next]').forEach((button) => {
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
