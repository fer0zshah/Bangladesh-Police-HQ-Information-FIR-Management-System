<x-admin-layout pageTitle="Criminal Registry">
    <div class="mx-auto max-w-[1440px] space-y-6">
        @if(session('success'))
            <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-300">{{ session('success') }}</div>
        @endif

        @if(isset($criminal))
            <section class="mx-auto max-w-3xl rounded-xl border border-hq-700 bg-hq-800 p-6">
                <h2 class="mb-5 text-lg font-bold text-white">Edit criminal record</h2>
                <form method="POST" action="{{ route('admin.criminals.update', $criminal->criminal_id) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PATCH')
                    <label class="text-sm">Name<input name="name" required value="{{ old('name', $criminal->name) }}" class="mt-2 w-full rounded-lg border border-hq-600 bg-hq-900 p-3"></label>
                    <label class="text-sm">Alias<input name="alias" value="{{ old('alias', $criminal->alias) }}" class="mt-2 w-full rounded-lg border border-hq-600 bg-hq-900 p-3"></label>
                    <label class="text-sm">NID<input name="nid_number" value="{{ old('nid_number', $criminal->nid_number) }}" class="mt-2 w-full rounded-lg border border-hq-600 bg-hq-900 p-3"></label>
                    <label class="text-sm">Date of birth<input type="date" name="date_of_birth" value="{{ old('date_of_birth', $criminal->date_of_birth) }}" class="mt-2 w-full rounded-lg border border-hq-600 bg-hq-900 p-3"></label>
                    <div class="sm:col-span-2 flex justify-end gap-3">
                        <a href="{{ route('admin.criminals.index') }}" class="px-4 py-2">Cancel</a>
                        <button class="rounded-lg border border-gold-500/40 bg-transparent px-5 py-2 font-bold text-gold-400 transition hover:bg-gold-500/10">Save</button>
                    </div>
                </form>
            </section>
        @else
            <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
                <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-rose-500/10 blur-3xl"></div>
                <div class="relative max-w-3xl">
                    <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-rose-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>
                        National criminal hierarchy
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Criminal registry by command</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-400">Browse linked criminals through the FIR hierarchy: command HQ, thana, then local criminal dictionary.</p>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['Registered criminals', $summary->total, 'All criminal records', 'rose'],
                    ['Wanted criminals', $summary->wanted ?? 0, 'Currently wanted', 'amber'],
                    ['Linked to FIRs', $summary->linked ?? 0, 'Attached to case files', 'sky'],
                    ['Command HQs', $summary->hq_units ?? 0, 'Metro and district units', 'indigo'],
                ] as [$label, $value, $note, $color])
                    <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-{{ $color }}-500/40">
                        <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-{{ $color }}-500/5 transition group-hover:bg-{{ $color }}-500/10"></div>
                        <p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">{{ $label }}</p>
                        <p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($value) }}</p>
                        <p class="relative mt-4 text-xs text-gray-500">{{ $note }}</p>
                    </div>
                @endforeach
            </section>

            <form method="GET" action="{{ route('admin.criminals.index') }}" class="rounded-xl border border-hq-700 bg-hq-800 p-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <input name="search" value="{{ request('search') }}" placeholder="Search metro/district HQ by name, district, division..." class="h-11 min-w-0 flex-1 rounded-lg border border-hq-600 bg-hq-900 px-4 text-sm text-white placeholder:text-gray-600">
                    <button class="h-11 rounded-lg border border-gold-500/40 bg-transparent px-5 text-sm font-bold text-gold-400 transition hover:bg-gold-500/10">Search HQs</button>
                    <a href="{{ route('admin.criminals.index') }}" class="flex h-11 items-center justify-center rounded-lg border border-hq-600 px-5 text-sm font-bold text-gray-400 transition hover:bg-hq-700/30">Reset</a>
                </div>
            </form>

            <section class="grid gap-5 xl:grid-cols-2">
                @foreach ([
                    ['title' => 'Metropolitan Criminal Links', 'subtitle' => 'Criminals linked to metro thana FIRs', 'type' => 'metro', 'items' => $metroHqs],
                    ['title' => 'District Criminal Links', 'subtitle' => 'Criminals linked to district thana FIRs', 'type' => 'district', 'items' => $districtHqs],
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
                                    <div class="relative">
                                        <div class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-gray-400">
                                            <span class="h-2 w-2 rounded-full {{ $hq->type === 'metropolitanHQ' ? 'bg-sky-400' : 'bg-indigo-400' }}"></span>
                                            {{ $hq->head_rank ?? 'Command HQ' }}
                                        </div>
                                        <h4 class="text-2xl font-bold tracking-tight text-white">{{ $hq->name }}</h4>
                                        <p class="mt-2 text-sm font-medium text-gray-500">{{ $hq->district ?: 'Metropolitan area' }} @if($hq->division)<span class="mx-1.5 text-gray-600">•</span>{{ $hq->division }}@endif</p>
                                    </div>
                                    <div class="relative mt-5 grid grid-cols-3 gap-4">
                                        <div class="rounded-xl border border-hq-700 bg-hq-800 p-4"><p class="text-xs font-bold uppercase tracking-widest text-gray-500">Thanas</p><p class="mt-1 text-2xl font-extrabold text-white">{{ number_format($hq->thanas_count) }}</p></div>
                                        <div class="rounded-xl border border-hq-700 bg-hq-800 p-4"><p class="text-xs font-bold uppercase tracking-widest text-gray-500">Criminals</p><p class="mt-1 text-2xl font-extrabold text-white">{{ number_format($hq->criminals_count) }}</p></div>
                                        <div class="rounded-xl border border-hq-700 bg-hq-800 p-4"><p class="text-xs font-bold uppercase tracking-widest text-gray-500">Wanted</p><p class="mt-1 text-2xl font-extrabold text-white">{{ number_format($hq->wanted_criminals_count) }}</p></div>
                                    </div>
                                    <div class="relative mt-auto flex items-center justify-between gap-3 pt-6">
                                        <p class="text-xs text-gray-500">Open this HQ to choose a thana and view linked criminals.</p>
                                        <a href="{{ route('admin.criminals.hq', $hq->station_id) }}" class="inline-flex items-center gap-2 rounded-lg border border-gold-500/40 bg-transparent px-4 py-2.5 text-sm font-bold text-gold-400 transition hover:bg-gold-500/10">View thana criminals</a>
                                    </div>
                                </div>
                            @empty
                                <div class="flex min-h-[280px] items-center justify-center rounded-xl border border-dashed border-hq-700 bg-hq-900/30 p-6 text-center text-sm text-gray-500">No HQ matched this search.</div>
                            @endforelse
                        </div>

                        @if ($browser['items']->isNotEmpty())
                            <div class="flex items-center justify-between border-t border-hq-700 bg-hq-900/35 px-5 py-4">
                                <button type="button" class="rounded-lg border border-hq-600 bg-transparent px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 transition hover:bg-hq-700/40" data-admin-prev>Previous</button>
                                <span class="text-xs font-semibold text-gray-500"><span data-admin-current>1</span> / <span>{{ $browser['items']->count() }}</span></span>
                                <button type="button" class="rounded-lg border border-hq-600 bg-transparent px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 transition hover:bg-hq-700/40" data-admin-next>Next</button>
                            </div>
                        @endif
                    </article>
                @endforeach
            </section>

            <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
                <div class="flex flex-col gap-3 border-b border-hq-700 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-white">National criminal dictionary</h3>
                        <p class="mt-1 text-xs text-gray-500">Search all criminal records directly, or use the cards above for command-wise browsing.</p>
                    </div>
                    <span class="w-fit rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $criminals->total() }} records</span>
                </div>
                @include('admin.criminals.partials.criminal-table', ['showStation' => true])
            </section>
        @endif
    </div>

    <script>
        document.querySelectorAll('[data-admin-browser]').forEach((browser) => {
            const cards = Array.from(browser.querySelectorAll('[data-admin-card]'));
            const current = browser.querySelector('[data-admin-current]');
            let index = 0;
            const show = () => {
                cards.forEach((card, i) => {
                    card.classList.toggle('hidden', i !== index);
                    card.classList.toggle('flex', i === index);
                });
                if (current) current.textContent = String(index + 1);
            };
            browser.querySelector('[data-admin-prev]')?.addEventListener('click', () => { index = (index - 1 + cards.length) % cards.length; show(); });
            browser.querySelector('[data-admin-next]')?.addEventListener('click', () => { index = (index + 1) % cards.length; show(); });
            show();
        });
    </script>
</x-admin-layout>
