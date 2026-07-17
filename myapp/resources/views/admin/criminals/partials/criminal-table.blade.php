<form method="GET" class="grid gap-3 border-b border-hq-700 p-4 lg:grid-cols-[1fr_12rem_16rem_auto_auto]">
    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
    <input name="criminal_search" value="{{ request('criminal_search') }}" placeholder="Search name, alias, NID or case title..." class="h-11 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white placeholder:text-gray-600">
    <select name="wanted" class="h-11 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white">
        <option value="">All statuses</option>
        <option value="yes" @selected(request('wanted') === 'yes')>Wanted</option>
        <option value="no" @selected(request('wanted') === 'no')>Not wanted</option>
    </select>
    @if($showStation ?? false)
        <select name="station_id" class="h-11 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white">
            <option value="">All thanas</option>
            @foreach($stations as $stationOption)
                <option value="{{ $stationOption->station_id }}" @selected((string) request('station_id') === (string) $stationOption->station_id)>{{ $stationOption->name }}</option>
            @endforeach
        </select>
    @else
        <div></div>
    @endif
    <button class="h-11 rounded-lg border border-gold-500/40 bg-transparent px-5 text-sm font-bold text-gold-400 transition hover:bg-gold-500/10">Filter</button>
    <a href="{{ url()->current() }}" class="flex h-11 items-center justify-center rounded-lg border border-hq-600 px-5 text-sm font-bold text-gray-400 transition hover:bg-hq-700/30">Reset</a>
</form>

@if ($criminals->isEmpty())
    <div class="px-6 py-16 text-center text-sm text-gray-500">No criminal records found.</div>
@else
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1050px] text-left whitespace-nowrap">
            <thead>
                <tr class="border-b border-hq-700 bg-hq-900/60 text-xs font-bold uppercase tracking-widest text-gray-400">
                    <th class="px-5 py-4">Person</th>
                    <th class="px-4 py-4">NID / DOB</th>
                    @if($showStation ?? false)<th class="px-4 py-4">Latest thana link</th>@endif
                    <th class="px-4 py-4">Latest FIR</th>
                    <th class="px-4 py-4 text-center">Cases</th>
                    <th class="px-4 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hq-700/60 text-sm">
                @foreach ($criminals as $criminal)
                    @php($latestCase = $criminal->cases->first())
                    <tr class="text-gray-400 transition-colors hover:bg-hq-700/20 hover:text-gray-200">
                        <td class="px-5 py-4"><a href="{{ route('admin.criminals.show', $criminal) }}" class="font-semibold text-gray-200 transition hover:text-gold-500">{{ $criminal->name }}</a><p class="mt-0.5 text-xs text-gray-500">{{ $criminal->alias ?: 'No alias' }}</p></td>
                        <td class="px-4 py-4">{{ $criminal->nid_number ?: '—' }}<div class="text-xs text-gray-500">{{ $criminal->date_of_birth ?: 'DOB unavailable' }}</div></td>
                        @if($showStation ?? false)
                            <td class="px-4 py-4">
                                @if($latestCase?->station)
                                    <a href="{{ route('admin.criminals.station', $latestCase->station->station_id) }}" class="font-semibold text-gray-300 transition hover:text-gold-500">{{ $latestCase->station->name }}</a>
                                    <div class="text-xs text-gray-500">{{ $latestCase->station->parent?->name }}</div>
                                @else
                                    <span class="text-gray-600">Not linked</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-4 py-4">{{ $latestCase?->case_title ?? 'No linked FIR' }}</td>
                        <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($criminal->cases_count) }}</td>
                        <td class="px-4 py-4"><span class="{{ $criminal->wanted_status ? 'text-rose-400' : 'text-emerald-400' }}">{{ $criminal->wanted_status ? 'Wanted' : 'Not wanted' }}</span></td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.criminals.show', $criminal) }}" class="rounded-md border border-sky-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-sky-400 transition hover:bg-sky-500/10">View</a>
                                <a href="{{ route('admin.criminals.edit', $criminal->criminal_id) }}" class="rounded-md border border-amber-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-amber-400 transition hover:bg-amber-500/10">Edit</a>
                                <form method="POST" action="{{ route('admin.criminals.toggleWanted', $criminal->criminal_id) }}">@csrf @method('PATCH')<button class="rounded-md border border-rose-500/30 bg-transparent px-3 py-1.5 text-xs font-semibold text-rose-400 transition hover:bg-rose-500/10">Toggle wanted</button></form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border-t border-hq-700 p-4">{{ $criminals->links() }}</div>
@endif
