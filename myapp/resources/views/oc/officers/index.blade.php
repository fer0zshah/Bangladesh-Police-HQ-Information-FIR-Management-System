<x-oc-layout pageTitle="Officer View">
<div class="mx-auto max-w-7xl space-y-6">
    <section class="overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-r from-hq-800 to-hq-700 p-5 shadow-xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-blue-400">Own Station Personnel</p>
                <h2 class="mt-2 text-2xl font-extrabold text-white">{{ $station->name ?? 'Assigned Station' }}</h2>
               
            </div>
            <span class="rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-3 py-2 text-xs font-bold uppercase tracking-widest text-emerald-300">Read Only</span>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([['Total Officers',$summary['total'],'sky'],['Active',$summary['active'],'emerald'],['OC Assigned',$summary['oc'],'gold'],['Inactive',$summary['inactive'],'rose']] as [$label,$value,$tone])
            <div class="rounded-xl border border-hq-700 bg-hq-800 p-4">
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500">{{ $label }}</p>
                <div class="mt-2 flex items-end justify-between">
                    <p class="text-2xl font-extrabold text-white">{{ $value }}</p>
                    <span class="h-2.5 w-2.5 rounded-full bg-{{ $tone }}-400"></span>
                </div>
            </div>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/10">
        <header class="border-b border-hq-700 p-4">
            <form class="grid gap-3 md:grid-cols-[1fr_180px_160px_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="Search name, badge or rank" class="h-10 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white placeholder:text-gray-600">
                <select name="status" class="h-10 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-gray-300">
                    <option value="">All statuses</option>
                    @foreach(['Active','Inactive','Suspended'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select name="oc" class="h-10 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-gray-300">
                    <option value="">All roles</option>
                    <option value="yes" @selected(request('oc') === 'yes')>OC only</option>
                    <option value="no" @selected(request('oc') === 'no')>Non-OC</option>
                </select>
                <button class="rounded-lg bg-hq-600 px-4 text-xs font-bold text-white hover:bg-hq-500">Filter</button>
            </form>
        </header>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr class="bg-hq-900/50 text-[9px] font-bold uppercase tracking-widest text-gray-600">
                        <th class="px-5 py-3">Officer</th>
                        <th class="px-4 py-3">Rank</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Login Email</th>
                        <th class="px-4 py-3">Cases</th>
                        <th class="px-5 py-3">Evidence</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hq-700">
                    @forelse($officers as $officer)
                        <tr class="text-xs">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full border border-hq-600 bg-hq-700 text-sm font-bold text-gray-300">{{ strtoupper(substr($officer->name, 0, 1)) }}</div>
                                    <div>
                                        <p class="font-semibold text-gray-100">{{ $officer->name }}</p>
                                        <p class="mt-0.5 text-[10px] text-gray-600">{{ $officer->badge_number }}</p>
                                    </div>
                                    @if($officer->is_oc)
                                        <span class="rounded-full bg-gold-500/10 px-2 py-1 text-[9px] font-bold uppercase tracking-widest text-gold-500">OC</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $officer->rank }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ strtolower($officer->status) === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">{{ $officer->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $officer->user?->email ?? 'No login account' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $officer->cases_count }}</td>
                            <td class="px-5 py-3 text-gray-400">{{ $officer->evidence_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-12 text-center text-sm text-gray-600">No officers found for your station.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-hq-700 p-4">{{ $officers->links() }}</div>
    </section>
</div>
</x-oc-layout>
