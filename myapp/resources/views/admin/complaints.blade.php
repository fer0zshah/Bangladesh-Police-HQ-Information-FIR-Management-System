<x-admin-layout pageTitle="Citizen Complaints">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-rose-500/10 blur-3xl"></div>
            <div class="relative max-w-3xl">
                <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-rose-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-400 shadow-[0_0_8px_rgba(251,113,133,0.6)]"></span>
                    National complaint hierarchy
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-white">Citizen complaint command registry</h2>
                <p class="mt-2 text-sm leading-6 text-gray-400">Browse citizen submissions by metropolitan or district command, then drill down into each thana complaint dictionary.</p>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total complaints', 'value' => $summary['total_complaints'], 'note' => 'All citizen submissions', 'color' => 'rose'],
                ['label' => 'Pending', 'value' => $summary['pending_complaints'], 'note' => 'Awaiting station review', 'color' => 'amber'],
                ['label' => 'Under review', 'value' => $summary['review_complaints'], 'note' => 'Being checked by OC', 'color' => 'sky'],
                ['label' => 'Escalated', 'value' => $summary['escalated_complaints'], 'note' => 'Promoted toward FIR', 'color' => 'emerald'],
            ] as $card)
                <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-{{ $card['color'] }}-500/40 hover:bg-hq-800/80">
                    <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-{{ $card['color'] }}-500/5 transition group-hover:bg-{{ $card['color'] }}-500/10"></div>
                    <div class="relative">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($card['value']) }}</p>
                        <p class="mt-4 text-xs text-gray-500">{{ $card['note'] }}</p>
                    </div>
                </div>
            @endforeach
        </section>

        <form method="GET" action="{{ route('admin.complaints.index') }}" class="rounded-xl border border-hq-700 bg-hq-800 p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input name="search" value="{{ request('search') }}" placeholder="Search metro/district HQ by name, district, division..." class="h-11 min-w-0 flex-1 rounded-lg border border-hq-600 bg-hq-900 px-4 text-sm text-white placeholder:text-gray-600 focus:border-gold-500 focus:ring-gold-500">
                <button class="h-11 rounded-lg border border-gold-500/40 bg-transparent px-5 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10">Search HQs</button>
                <a href="{{ route('admin.complaints.index') }}" class="flex h-11 items-center justify-center rounded-lg border border-hq-600 px-5 text-sm font-bold text-gray-400 transition hover:border-hq-500 hover:bg-hq-700/30 hover:text-white">Reset</a>
            </div>
        </form>

        <section class="grid gap-5 xl:grid-cols-2">
            @foreach ([
                ['title' => 'Metropolitan Police Complaints', 'subtitle' => 'Commissioner-led city commands', 'type' => 'metro', 'items' => $metroHqs],
                ['title' => 'District Police Complaints', 'subtitle' => 'SP-led district commands', 'type' => 'district', 'items' => $districtHqs],
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
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-3"/></svg>
                                    </div>
                                </div>

                                <div class="relative mt-5 grid grid-cols-3 gap-4">
                                    <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-4">
                                        <div class="absolute right-0 top-0 h-16 w-16 rounded-bl-full bg-gold-500/5"></div>
                                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Thanas</p>
                                        <p class="mt-1 text-2xl font-extrabold text-white">{{ number_format($hq->thanas_count) }}</p>
                                    </div>
                                    <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-4">
                                        <div class="absolute right-0 top-0 h-16 w-16 rounded-bl-full bg-rose-500/5"></div>
                                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Complaints</p>
                                        <p class="mt-1 text-2xl font-extrabold text-white">{{ number_format($hq->complaints_count) }}</p>
                                    </div>
                                    <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-4">
                                        <div class="absolute right-0 top-0 h-16 w-16 rounded-bl-full bg-amber-500/5"></div>
                                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Pending</p>
                                        <p class="mt-1 text-2xl font-extrabold text-white">{{ number_format($hq->pending_complaints_count) }}</p>
                                    </div>
                                </div>

                                <div class="relative mt-auto flex items-center justify-between gap-3 pt-6">
                                    <p class="text-xs text-gray-500">Open this HQ to choose a thana and view its complaint dictionary.</p>
                                    <a href="{{ route('admin.complaints.hq', $hq) }}" class="inline-flex items-center gap-2 rounded-lg border border-gold-500/40 bg-transparent px-4 py-2.5 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10 hover:text-gold-300">
                                        View thana complaints
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="flex min-h-[280px] items-center justify-center rounded-xl border border-dashed border-hq-700 bg-hq-900/30 p-6 text-center text-sm text-gray-500">No HQ matched this search.</div>
                        @endforelse
                    </div>

                    @if ($browser['items']->isNotEmpty())
                        <div class="flex items-center justify-between border-t border-hq-700 bg-hq-900/35 px-5 py-4">
                            <button type="button" class="rounded-lg border border-hq-600 bg-transparent px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 transition hover:border-hq-500 hover:bg-hq-700/40 hover:text-white" data-admin-prev>Previous</button>
                            <span class="text-xs font-semibold text-gray-500"><span data-admin-current>1</span> / <span>{{ $browser['items']->count() }}</span></span>
                            <button type="button" class="rounded-lg border border-hq-600 bg-transparent px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 transition hover:border-hq-500 hover:bg-hq-700/40 hover:text-white" data-admin-next>Next</button>
                        </div>
                    @endif
                </article>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <div class="flex flex-col gap-3 border-b border-hq-700 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-white">National complaint dictionary</h3>
                    <p class="mt-1 text-xs text-gray-500">Search all citizen complaints directly, or use the cards above for command-wise browsing.</p>
                </div>
                <span class="w-fit rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $complaints->total() }} records</span>
            </div>

            @include('admin.complaints.partials.complaint-table', ['showStation' => true])
        </section>
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

            browser.querySelector('[data-admin-prev]')?.addEventListener('click', () => {
                index = (index - 1 + cards.length) % cards.length;
                show();
            });

            browser.querySelector('[data-admin-next]')?.addEventListener('click', () => {
                index = (index + 1) % cards.length;
                show();
            });

            show();
        });
    </script>
</x-admin-layout>
