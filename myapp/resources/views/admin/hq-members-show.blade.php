<x-admin-layout pageTitle="{{ $member->name }} OCs">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gold-500"><span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>{{ $member->role_label }}</div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">{{ $member->name }}</h2>
                    <p class="mt-2 text-sm text-gray-400">{{ $member->station?->name ?? 'No headquarters assigned' }} — OCs under this command area.</p>
                </div>
                <a href="{{ route('admin.hq-members.index') }}" class="inline-flex items-center justify-center rounded-lg border border-hq-600 bg-transparent px-4 py-2.5 text-sm font-bold text-gray-300 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-white">Back to heads</a>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-sky-500/5"></div><p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">Thanas</p><p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['thanas']) }}</p></div>
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-emerald-500/5"></div><p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">OCs</p><p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['ocs']) }}</p></div>
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-indigo-500/5"></div><p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">Officers</p><p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['officers']) }}</p></div>
            <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-amber-500/5"></div><p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">Active cases</p><p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($summary['active_cases']) }}</p></div>
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <div class="border-b border-hq-700 bg-hq-900/35 px-5 py-4">
                <h3 class="text-base font-semibold text-white">Officer-in-Charge list</h3>
                <p class="mt-1 text-xs text-gray-500">Click an OC to see all officers and constables under that thana.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse($ocs as $oc)
                    <article class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-gold-500/40">
                        <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-gold-500/5"></div>
                        <div class="relative flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ $oc->rank }}</p>
                                <h4 class="mt-2 text-lg font-bold text-white">{{ $oc->name }}</h4>
                                <p class="mt-1 text-xs text-gray-500">{{ $oc->badge_number }}</p>
                            </div>
                            <span class="rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400">OC</span>
                        </div>
                        <p class="relative mt-4 text-sm text-gray-400">{{ $oc->station?->name ?? 'No station' }}</p>
                        <div class="relative mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-lg border border-hq-700 p-3 text-center"><p class="text-[10px] uppercase tracking-widest text-gray-500">Cases</p><p class="mt-1 font-bold text-white">{{ number_format($oc->cases_count) }}</p></div>
                            <div class="rounded-lg border border-hq-700 p-3 text-center"><p class="text-[10px] uppercase tracking-widest text-gray-500">Active</p><p class="mt-1 font-bold text-white">{{ number_format($oc->active_cases_count) }}</p></div>
                        </div>
                        <a href="{{ route('admin.hq-members.ocs.show', $oc) }}" class="relative mt-5 inline-flex w-full items-center justify-center rounded-lg border border-gold-500/40 bg-transparent px-4 py-2.5 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10 hover:text-gold-300">View Officers</a>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-hq-700 bg-hq-900/30 p-8 text-center text-sm text-gray-500 md:col-span-2 xl:col-span-3">No OC records found under this command area.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-admin-layout>
