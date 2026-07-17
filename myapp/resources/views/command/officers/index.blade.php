<x-command-layout pageTitle="Officer Management">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 to-hq-700/70 p-6">
            <div class="absolute right-0 top-0 h-40 w-40 rounded-bl-full bg-indigo-500/5"></div>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-400">{{ $headquarters->name }}</p><h2 class="mt-2 text-2xl font-bold text-white">Personnel directory</h2><p class="mt-2 text-sm text-gray-400">Inspectors, Sub-Inspectors, Nayeks and Constables across your thanas.</p></div>
                <a href="{{ route('command.officers.create') }}" class="inline-flex items-center justify-center rounded-lg border border-gold-500/40 px-5 py-3 text-sm font-bold text-gold-400 hover:bg-gold-500/10">Add officer</a>
            </div>
        </section>

        @if(session('success'))<div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('success') }}</div>@endif
        <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach([['Personnel',$summary['total']],['Active duty',$summary['active']],['Station OCs',$summary['oc']],['Constables',$summary['constables']]] as $card)
                <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-indigo-500/5"></div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">{{ $card[0] }}</p><p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($card[1]) }}</p></div>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
            <form method="GET" action="{{ route('command.officers.index') }}" class="grid gap-3 border-b border-hq-700 p-4 md:grid-cols-2 xl:grid-cols-[1fr_14rem_12rem_11rem_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="Search name, badge or rank..." class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none placeholder:text-gray-600">
                <select name="station_id" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300"><option value="">All thanas</option>@foreach($stations as $station)<option value="{{ $station->station_id }}" @selected((string)request('station_id')===(string)$station->station_id)>{{ $station->name }}</option>@endforeach</select>
                <select name="rank" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300"><option value="">All ranks</option>@foreach($ranks as $rank)<option value="{{ $rank }}" @selected(request('rank')===$rank)>{{ $rank }}</option>@endforeach</select>
                <select name="status" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300"><option value="">All statuses</option><option value="Active" @selected(request('status')==='Active')>Active</option><option value="Inactive" @selected(request('status')==='Inactive')>Inactive</option></select>
                <div class="flex gap-2"><button class="rounded-lg border border-indigo-500/40 px-4 text-xs font-bold text-indigo-400 hover:bg-indigo-500/10">Filter</button><a href="{{ route('command.officers.index') }}" class="flex items-center px-3 text-xs text-gray-500 hover:text-white">Reset</a></div>
            </form>
            <div class="overflow-x-auto">
                <table class="hq-table w-full min-w-[960px] text-left text-sm">
                    <thead class="border-b border-hq-700 bg-hq-900/60 text-[10px] uppercase tracking-widest text-gray-500"><tr><th class="px-5 py-4">Officer</th><th class="px-4 py-4">Rank</th><th class="px-4 py-4">Thana</th><th class="px-4 py-4 text-center">Cases</th><th class="px-4 py-4">Status</th><th class="px-5 py-4 text-right">Action</th></tr></thead>
                    <tbody class="divide-y divide-hq-700/60">
                        @forelse($officers as $officer)
                            <tr><td class="px-5 py-4"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-lg border border-hq-600 bg-hq-900 text-xs font-bold text-indigo-400">{{ strtoupper(substr($officer->name,0,1)) }}</span><div><p class="font-semibold text-gray-200">{{ $officer->name }}</p><p class="mt-1 text-xs text-gray-600">{{ $officer->badge_number }}</p></div>@if($officer->is_oc)<span class="rounded-full bg-gold-500/10 px-2 py-1 text-[9px] font-bold uppercase text-gold-500">OC</span>@endif</div></td><td class="px-4 py-4 text-gray-400">{{ $officer->rank }}</td><td class="px-4 py-4 text-gray-500">{{ $officer->station?->name }}</td><td class="px-4 py-4 text-center font-semibold">{{ $officer->cases_count }}</td><td class="px-4 py-4"><span class="{{ strtolower($officer->status)==='active'?'text-emerald-400':'text-rose-400' }}">{{ $officer->status }}</span></td><td class="px-5 py-4 text-right"><a href="{{ route('command.officers.show',$officer) }}" class="rounded-lg border border-indigo-500/30 px-3 py-2 text-xs font-semibold text-indigo-400 hover:bg-indigo-500/10">Manage</a></td></tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-14 text-center text-gray-500">No officers match these filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($officers->hasPages())<div class="border-t border-hq-700 px-5 py-4">{{ $officers->links() }}</div>@endif
        </section>
    </div>
</x-command-layout>
