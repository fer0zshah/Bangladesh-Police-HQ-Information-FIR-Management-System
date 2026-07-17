<x-admin-layout pageTitle="{{ $officer->name }} Team">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-gold-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gold-500"><span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>Officer-in-Charge</div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">{{ $officer->name }}</h2>
                    <p class="mt-2 text-sm text-gray-400">{{ $officer->station?->name ?? 'No station' }} — officers under this OC.</p>
                </div>
                <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center rounded-lg border border-hq-600 bg-transparent px-4 py-2.5 text-sm font-bold text-gray-300 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-white">Back</a>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-indigo-500/5"></div><p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">Team size</p><p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($officers->count()) }}</p></div>
            <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-amber-500/5"></div><p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">Total cases</p><p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($caseStats['total_cases']) }}</p></div>
            <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-sky-500/5"></div><p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">Active cases</p><p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($caseStats['active_cases']) }}</p></div>
            <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-emerald-500/5"></div><p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">Closed cases</p><p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($caseStats['closed_cases']) }}</p></div>
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <div class="border-b border-hq-700 bg-hq-900/35 px-5 py-4">
                <h3 class="text-base font-semibold text-white">Station personnel</h3>
                <p class="mt-1 text-xs text-gray-500">Includes OC, inspectors, constables, nayek and other ranks assigned to this thana.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-left whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-hq-700 bg-hq-900/60 text-xs font-bold uppercase tracking-widest text-gray-400">
                            <th class="px-5 py-4">Officer</th>
                            <th class="px-4 py-4">Rank</th>
                            <th class="px-4 py-4">Badge</th>
                            <th class="px-4 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hq-700/60 text-sm">
                        @foreach($officers as $teamOfficer)
                            <tr class="text-gray-400 transition-colors hover:bg-hq-700/20 hover:text-gray-200">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-200">{{ $teamOfficer->name }}</p>
                                    <p class="mt-0.5 text-xs text-gray-600">#{{ $teamOfficer->officer_id }}</p>
                                </td>
                                <td class="px-4 py-4">{{ $teamOfficer->rank }}</td>
                                <td class="px-4 py-4 text-gray-500">{{ $teamOfficer->badge_number }}</td>
                                <td class="px-4 py-4">{{ $teamOfficer->status }}</td>
                                <td class="px-5 py-4 text-right">
                                    @if($teamOfficer->is_oc)
                                        <span class="rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-emerald-400">OC</span>
                                    @else
                                        <span class="rounded-full border border-hq-600 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-gray-500">Officer</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-admin-layout>
