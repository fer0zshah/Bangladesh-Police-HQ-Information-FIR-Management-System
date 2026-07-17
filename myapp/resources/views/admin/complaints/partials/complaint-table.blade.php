<form method="GET" class="grid gap-3 border-b border-hq-700 p-4 lg:grid-cols-[1fr_13rem_16rem_auto_auto]">
    @if(request('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif
    <input name="complaint_search" value="{{ request('complaint_search') }}" placeholder="Search name, NID, complaint ID or description..." class="h-11 rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white placeholder:text-gray-600 focus:border-gold-500 focus:ring-gold-500">
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

@if ($complaints->isEmpty())
    <div class="px-6 py-16 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-hq-700 bg-hq-900 text-hq-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8m-8 4h5m-9 6h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <h3 class="mt-4 text-base font-semibold text-white">No complaints found</h3>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-5 text-gray-500">Try changing the search, status, or station filters.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1050px] text-left whitespace-nowrap">
            <thead>
                <tr class="border-b border-hq-700 bg-hq-900/60 text-xs font-bold uppercase tracking-widest text-gray-400">
                    <th class="px-5 py-4">Complainant</th>
                    <th class="px-4 py-4">Description</th>
                    @if($showStation ?? false)
                        <th class="px-4 py-4">Thana</th>
                    @endif
                    <th class="px-4 py-4">Command HQ</th>
                    <th class="px-4 py-4">Submitted</th>
                    <th class="px-4 py-4">Linked FIR</th>
                    <th class="px-5 py-4 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hq-700/60 text-sm">
                @foreach ($complaints as $complaint)
                    <tr class="text-gray-400 transition-colors hover:bg-hq-700/20 hover:text-gray-200">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg border border-hq-600 bg-hq-900 font-mono text-xs font-bold text-hq-300">#{{ $complaint->complaint_id }}</div>
                                <div>
                                    <a href="{{ route('admin.complaints.show', $complaint) }}" class="font-semibold text-gray-200 transition hover:text-gold-500">{{ $complaint->complainant_name }}</a>
                                    <p class="mt-0.5 text-xs text-gray-500">NID {{ $complaint->complainant_nid }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="max-w-sm px-4 py-4">
                            <p class="truncate text-gray-400">{{ $complaint->description }}</p>
                        </td>
                        @if($showStation ?? false)
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.complaints.station', $complaint->station) }}" class="font-semibold text-gray-300 transition hover:text-gold-500">{{ $complaint->station?->name ?? 'Unknown thana' }}</a>
                            </td>
                        @endif
                        <td class="px-4 py-4 text-gray-500">{{ $complaint->station?->parent?->name ?? 'Not attached' }}</td>
                        <td class="px-4 py-4 text-gray-500">{{ $complaint->submitted_date ? \Carbon\Carbon::parse($complaint->submitted_date)->format('d M Y') : 'N/A' }}</td>
                        <td class="px-4 py-4">
                            @if($complaint->caseFir)
                                <a href="{{ route('admin.cases.show', $complaint->caseFir) }}" class="rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-bold text-emerald-400">FIR #{{ $complaint->caseFir->case_id }}</a>
                            @else
                                <span class="text-xs text-gray-600">Not linked</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @php($status = strtolower($complaint->status))
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold uppercase tracking-wider {{ $status === 'escalated' ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : ($status === 'dismissed' ? 'border-rose-500/20 bg-rose-500/10 text-rose-400' : ($status === 'under review' ? 'border-sky-500/20 bg-sky-500/10 text-sky-400' : 'border-amber-500/20 bg-amber-500/10 text-amber-400')) }}">
                                {{ $complaint->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border-t border-hq-700 p-4">
        {{ $complaints->links() }}
    </div>
@endif
