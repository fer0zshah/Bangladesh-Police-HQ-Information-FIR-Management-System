<x-admin-layout pageTitle="{{ $station->name }} Criminal Links">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-rose-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-rose-400"><span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>{{ $station->head_rank ?? 'Police command' }}</div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">{{ $station->name }} criminal links</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-400">Choose a thana to view criminals linked through that station's FIRs.</p>
                </div>
                <a href="{{ route('admin.criminals.index') }}" class="inline-flex items-center justify-center rounded-lg border border-hq-600 bg-transparent px-4 py-2.5 text-sm font-bold text-gray-300 transition hover:bg-gold-500/10">Back to criminal browser</a>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            @foreach ([['Thanas',$hqSummary['thanas'],'Under this HQ','sky'],['Criminals',$hqSummary['criminals'],'Linked through FIRs','rose'],['Wanted',$hqSummary['wanted'],'Active wanted status','amber'],['FIRs',$hqSummary['cases'],'Local case files','indigo']] as [$label,$value,$note,$color])
                <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-{{ $color }}-500/40"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-{{ $color }}-500/5"></div><p class="relative text-xs font-bold uppercase tracking-widest text-gray-500">{{ $label }}</p><p class="relative mt-2 text-3xl font-extrabold text-white">{{ number_format($value) }}</p><p class="relative mt-4 text-xs text-gray-500">{{ $note }}</p></div>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <div class="flex flex-col gap-3 border-b border-hq-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div><h3 class="text-base font-semibold text-white">Thana criminal dictionary selector</h3><p class="mt-1 text-xs text-gray-500">Click a thana to see criminals linked with its FIRs.</p></div>
                <span class="w-fit rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $thanas->count() }} records</span>
            </div>
            @if ($thanas->isEmpty())
                <div class="px-6 py-16 text-center text-sm text-gray-500">No thana stations are currently attached to this command.</div>
            @else
                <div class="overflow-x-auto"><table class="w-full min-w-[900px] text-left whitespace-nowrap"><thead><tr class="border-b border-hq-700 bg-hq-900/60 text-xs font-bold uppercase tracking-widest text-gray-400"><th class="px-5 py-4">Thana</th><th class="px-4 py-4">District</th><th class="px-4 py-4 text-center">FIRs</th><th class="px-4 py-4 text-center">Criminals</th><th class="px-4 py-4 text-center">Wanted</th><th class="px-5 py-4 text-right">Action</th></tr></thead><tbody class="divide-y divide-hq-700/60 text-sm">
                @foreach ($thanas as $thana)<tr class="text-gray-400 transition-colors hover:bg-hq-700/20 hover:text-gray-200"><td class="px-5 py-4"><div class="flex items-center gap-4"><div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg border border-hq-600 bg-hq-900 font-mono text-xs font-bold text-hq-300">{{ str_pad($thana->station_id, 2, '0', STR_PAD_LEFT) }}</div><div><a href="{{ route('admin.criminals.station', $thana->station_id) }}" class="font-semibold text-gray-200 transition hover:text-gold-500">{{ $thana->name }}</a><p class="mt-0.5 text-xs text-gray-500">Registry #{{ $thana->station_id }}</p></div></div></td><td class="px-4 py-4">{{ $thana->district ?: 'N/A' }}</td><td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($thana->cases_count) }}</td><td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($thana->criminals_count) }}</td><td class="px-4 py-4 text-center font-semibold text-rose-400">{{ number_format($thana->wanted_criminals_count) }}</td><td class="px-5 py-4 text-right"><a href="{{ route('admin.criminals.station', $thana->station_id) }}" class="rounded-md border border-gold-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-gold-400 transition hover:bg-gold-500/10">View criminals</a></td></tr>@endforeach
                </tbody></table></div>
            @endif
        </section>
    </div>
</x-admin-layout>
