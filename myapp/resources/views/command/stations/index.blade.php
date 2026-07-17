<x-command-layout pageTitle="Stations">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-6">
            <div class="absolute right-0 top-0 h-32 w-32 rounded-bl-full bg-sky-500/5"></div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-sky-400">Command headquarters</p>
            <h2 class="mt-2 text-2xl font-bold text-white">{{ $headquarters->name }}</h2>
            <p class="mt-2 text-sm text-gray-400">Browse only the thanas directly assigned to this command.</p>
        </section>

        <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach([['Thanas',$summary['total'],'Registered'],['Operational',$summary['active'],'Active stations'],['Personnel',$summary['officers'],'All ranks'],['Case load',$summary['cases'],'Registered FIRs']] as $card)
                <div class="relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-sky-500/5"></div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">{{ $card[0] }}</p><p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($card[1]) }}</p><p class="mt-3 text-[10px] text-gray-500">{{ $card[2] }}</p></div>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
            <form method="GET" action="{{ route('command.stations.index') }}" class="grid gap-3 border-b border-hq-700 p-4 sm:grid-cols-[1fr_12rem_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="Search thana, district, address, contact..." class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none placeholder:text-gray-600 focus:border-sky-500/60">
                <select name="status" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300"><option value="">All statuses</option><option value="Active" @selected(request('status')==='Active')>Active</option><option value="Inactive" @selected(request('status')==='Inactive')>Inactive</option></select>
                <div class="flex gap-2"><button class="rounded-lg border border-sky-500/40 px-4 text-xs font-bold text-sky-400 hover:bg-sky-500/10">Filter</button><a href="{{ route('command.stations.index') }}" class="flex items-center px-3 text-xs text-gray-500 hover:text-white">Reset</a></div>
            </form>
            <div class="overflow-x-auto">
                <table class="hq-table w-full min-w-[900px] text-left text-sm">
                    <thead class="border-b border-hq-700 bg-hq-900/60 text-[10px] uppercase tracking-widest text-gray-500"><tr><th class="px-5 py-4">Thana</th><th class="px-4 py-4">District</th><th class="px-4 py-4">OC rank</th><th class="px-4 py-4 text-center">Officers</th><th class="px-4 py-4 text-center">Cases</th><th class="px-4 py-4 text-center">Complaints</th><th class="px-5 py-4 text-right">Action</th></tr></thead>
                    <tbody class="divide-y divide-hq-700/60">
                        @forelse($stations as $station)
                            <tr><td class="px-5 py-4"><p class="font-semibold text-gray-200">{{ $station->name }}</p><p class="mt-1 text-xs text-gray-600">{{ $station->contact_number ?: 'No contact' }}</p></td><td class="px-4 py-4 text-gray-500">{{ $station->district }}</td><td class="px-4 py-4 text-gray-400">{{ $station->head_rank ?: 'OC' }}</td><td class="px-4 py-4 text-center font-semibold">{{ $station->officers_count }}</td><td class="px-4 py-4 text-center font-semibold">{{ $station->cases_count }}</td><td class="px-4 py-4 text-center font-semibold">{{ $station->complaints_count }}</td><td class="px-5 py-4 text-right"><a href="{{ route('command.stations.show',$station) }}" class="rounded-lg border border-sky-500/30 px-3 py-2 text-xs font-semibold text-sky-400 hover:bg-sky-500/10">View thana</a></td></tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-14 text-center text-gray-500">No matching thanas in your jurisdiction.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-command-layout>
