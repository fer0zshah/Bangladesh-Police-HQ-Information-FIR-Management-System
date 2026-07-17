<form method="GET" class="grid gap-3 border-b border-hq-700 p-4 lg:grid-cols-[1fr_13rem_16rem_auto_auto]">
    @if(request('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif
    <input name="case_search" value="{{ request('case_search') }}" placeholder="Search FIR title, ID, officer..." class="h-11 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white placeholder:text-gray-600 focus:border-gold-500 focus:ring-gold-500">
    <select name="status" class="h-11 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white focus:border-gold-500 focus:ring-gold-500">
        <option value="">All statuses</option>
        @foreach($statuses as $status)
            <option value="{{ $status->status }}" @selected(request('status') === $status->status)>{{ $status->status }}</option>
        @endforeach
    </select>
    @if($showStation ?? false)
        <select name="station_id" class="h-11 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white focus:border-gold-500 focus:ring-gold-500">
            <option value="">All thanas</option>
            @foreach($stations as $stationOption)
                <option value="{{ $stationOption->station_id }}" @selected((string) request('station_id') === (string) $stationOption->station_id)>{{ $stationOption->name }}</option>
            @endforeach
        </select>
    @else
        <div></div>
    @endif
    <button class="h-11 rounded-lg border border-gold-500/40 bg-transparent px-5 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10">Filter</button>
    <a href="{{ url()->current() }}" class="flex h-11 items-center justify-center rounded-lg border border-hq-600 px-5 text-sm font-bold text-gray-400 transition hover:border-hq-500 hover:bg-hq-700/30 hover:text-white">Reset</a>
</form>

@if ($cases->isEmpty())
    <div class="px-6 py-16 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-hq-700 bg-hq-900 text-hq-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
        </div>
        <h3 class="mt-4 text-base font-semibold text-white">No FIR records found</h3>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-5 text-gray-500">Try changing the search, status, or station filters.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1050px] text-left whitespace-nowrap">
            <thead>
                <tr class="border-b border-hq-700 bg-hq-900/60 text-xs font-bold uppercase tracking-widest text-gray-400">
                    <th class="px-5 py-4">FIR</th>
                    @if($showStation ?? false)
                        <th class="px-4 py-4">Thana</th>
                    @endif
                    <th class="px-4 py-4">Command HQ</th>
                    <th class="px-4 py-4">Investigator</th>
                    <th class="px-4 py-4">Filed</th>
                    <th class="px-4 py-4 text-center">Evidence</th>
                    <th class="px-4 py-4 text-center">Criminals</th>
                    <th class="px-5 py-4 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hq-700/60 text-sm">
                @foreach ($cases as $case)
                    <tr class="text-gray-400 transition-colors hover:bg-hq-700/20 hover:text-gray-200">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg border border-hq-600 bg-hq-900 font-mono text-xs font-bold text-hq-300">#{{ $case->case_id }}</div>
                                <div>
                                    <a href="{{ route('admin.cases.show', $case) }}" class="font-semibold text-gray-200 transition hover:text-gold-500">{{ $case->case_title }}</a>
                                    <p class="mt-0.5 text-xs text-gray-500">FIR reference #{{ $case->case_id }}</p>
                                </div>
                            </div>
                        </td>
                        @if($showStation ?? false)
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.cases.station', $case->station) }}" class="font-semibold text-gray-300 transition hover:text-gold-500">{{ $case->station?->name ?? 'Unknown thana' }}</a>
                            </td>
                        @endif
                        <td class="px-4 py-4 text-gray-500">{{ $case->station?->parent?->name ?? 'Not attached' }}</td>
                        <td class="px-4 py-4">{{ $case->officer?->name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-4 text-gray-500">{{ $case->date_filed ? \Carbon\Carbon::parse($case->date_filed)->format('d M Y') : 'N/A' }}</td>
                        <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($case->evidence_count) }}</td>
                        <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($case->criminals_count) }}</td>
                        <td class="px-5 py-4 text-right">
                            @php($status = strtolower($case->status))
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold uppercase tracking-wider {{ $status === 'closed' ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : ($status === 'transferred' ? 'border-indigo-500/20 bg-indigo-500/10 text-indigo-400' : 'border-amber-500/20 bg-amber-500/10 text-amber-400') }}">
                                {{ $case->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border-t border-hq-700 p-4">
        {{ $cases->links() }}
    </div>
@endif
