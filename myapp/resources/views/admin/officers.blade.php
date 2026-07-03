<x-admin-layout pageTitle="Officer Management">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-indigo-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-400"></span>
                        Personnel command
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Officer registry</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-400">Manage personnel records, station assignments, service status, and explicit Officer-in-Charge access.</p>
                </div>
                <a href="{{ route('admin.officers.create') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gold-500 px-5 py-3 text-sm font-bold text-hq-900 shadow-lg shadow-gold-500/10 transition hover:-translate-y-0.5 hover:bg-gold-600 sm:w-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add officer
                </a>
            </div>
        </section>

        @if (session('success'))
            <div class="flex items-center gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                <svg class="h-5 w-5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                <svg class="h-5 w-5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.1 19h13.8a2 2 0 001.7-3L13.7 4a2 2 0 00-3.4 0L3.4 16a2 2 0 001.7 3z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                <p class="font-semibold">Please review the highlighted information.</p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-xs text-rose-300/80">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            <div class="rounded-xl border border-hq-700 bg-hq-800 p-4 sm:p-5"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 sm:text-[10px]">Registered</p><p class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($summary['total']) }}</p><p class="mt-3 text-[10px] text-gray-600">All officer records</p></div>
            <div class="rounded-xl border border-hq-700 bg-hq-800 p-4 sm:p-5"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 sm:text-[10px]">Active duty</p><p class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($summary['active']) }}</p><p class="mt-3 text-[10px] text-emerald-400">Operational personnel</p></div>
            <div class="rounded-xl border border-hq-700 bg-hq-800 p-4 sm:p-5"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 sm:text-[10px]">Station OCs</p><p class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($summary['oc']) }}</p><p class="mt-3 text-[10px] text-gold-500">Account access enabled</p></div>
            <div class="rounded-xl border border-hq-700 bg-hq-800 p-4 sm:p-5"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 sm:text-[10px]">Unassigned</p><p class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">{{ number_format($summary['unassigned']) }}</p><p class="mt-3 text-[10px] text-amber-400">Awaiting station</p></div>
        </section>

        @if ($formMode)
            @php
                $isEditing = $formMode === 'edit';
                $formOfficer = $editingOfficer;
            @endphp
            <section class="mx-auto max-w-5xl overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/10">
                <div class="flex items-center justify-between border-b border-hq-700 bg-hq-900/35 px-5 py-4 sm:px-7">
                    <div><h3 class="text-sm font-semibold text-white">{{ $isEditing ? 'Edit officer record' : 'Register an officer' }}</h3><p class="mt-1 text-[11px] text-gray-500">This form manages the personnel record only. It never creates login credentials.</p></div>
                    <a href="{{ route('admin.officers.index') }}" class="text-xs font-medium text-gray-500 transition hover:text-white">Close</a>
                </div>
                <form action="{{ $isEditing ? route('admin.officers.update', $formOfficer) : route('admin.officers.store') }}" method="POST">
                    @csrf
                    @if ($isEditing) @method('PUT') @endif
                    <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-7">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-2 block text-xs font-semibold text-gray-300">Full name <span class="text-rose-400">*</span></label>
                            <input id="name" name="name" type="text" required maxlength="100" value="{{ old('name', $formOfficer?->name) }}" placeholder="Official officer name" class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none transition placeholder:text-gray-600 focus:border-indigo-500/70 focus:ring-2 focus:ring-indigo-500/10">
                        </div>
                        <div>
                            <label for="badge_number" class="mb-2 block text-xs font-semibold text-gray-300">Badge number <span class="text-rose-400">*</span></label>
                            <input id="badge_number" name="badge_number" type="text" required maxlength="20" value="{{ old('badge_number', $formOfficer?->badge_number) }}" placeholder="e.g. BP-10234" class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none transition placeholder:text-gray-600 focus:border-indigo-500/70">
                        </div>
                        <div>
                            <label for="rank" class="mb-2 block text-xs font-semibold text-gray-300">Rank <span class="text-rose-400">*</span></label>
                            <input id="rank" name="rank" type="text" required maxlength="50" value="{{ old('rank', $formOfficer?->rank) }}" placeholder="e.g. Inspector" class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none transition placeholder:text-gray-600 focus:border-indigo-500/70">
                        </div>
                        <div>
                            <label for="station_id" class="mb-2 block text-xs font-semibold text-gray-300">Station assignment</label>
                            <select id="station_id" name="station_id" class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none transition focus:border-indigo-500/70">
                                <option value="">Unassigned</option>
                                @foreach ($stations as $station)
                                    <option value="{{ $station->station_id }}" @selected((string) old('station_id', $formOfficer?->station_id) === (string) $station->station_id)>{{ $station->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="mb-2 block text-xs font-semibold text-gray-300">Service status <span class="text-rose-400">*</span></label>
                            <select id="status" name="status" required class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none transition focus:border-indigo-500/70">
                                <option value="Active" @selected(old('status', $formOfficer?->status ?? 'Active') === 'Active')>Active</option>
                                <option value="Inactive" @selected(old('status', $formOfficer?->status) === 'Inactive')>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse gap-3 border-t border-hq-700 bg-hq-900/35 px-5 py-4 sm:flex-row sm:justify-end sm:px-7">
                        <a href="{{ route('admin.officers.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg px-5 text-sm font-semibold text-gray-400 hover:bg-hq-700 hover:text-white">Cancel</a>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-gold-500 px-5 text-sm font-bold text-hq-900 transition hover:bg-gold-600">{{ $isEditing ? 'Save changes' : 'Add officer' }}</button>
                    </div>
                </form>
            </section>
        @endif

        @if ($selectedOfficer)
            @php
                $ocEligible = $selectedOfficer->station
                    && strtolower($selectedOfficer->station->status) === 'active'
                    && strtolower($selectedOfficer->status) === 'active';
            @endphp
            <section class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(22rem,0.75fr)]">
                <div class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
                    <div class="flex flex-col gap-4 border-b border-hq-700 bg-gradient-to-r from-hq-800 to-hq-700/40 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 font-bold text-indigo-400">{{ strtoupper(substr($selectedOfficer->name, 0, 1)) }}</div>
                            <div><div class="flex flex-wrap items-center gap-2"><h3 class="text-lg font-bold text-white">{{ $selectedOfficer->name }}</h3>@if ($selectedOfficer->is_oc)<span class="rounded-full border border-gold-500/20 bg-gold-500/10 px-2 py-1 text-[9px] font-bold uppercase tracking-widest text-gold-500">Officer in Charge</span>@endif</div><p class="mt-1 text-xs text-gray-500">{{ $selectedOfficer->rank }} · {{ $selectedOfficer->badge_number }}</p></div>
                        </div>
                        <a href="{{ route('admin.officers.edit', $selectedOfficer) }}" class="inline-flex items-center justify-center rounded-lg border border-hq-600 bg-hq-700 px-4 py-2 text-xs font-semibold text-white hover:bg-hq-600">Edit record</a>
                    </div>
                    <dl class="grid grid-cols-1 divide-y divide-hq-700/60 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                        <div class="p-5"><dt class="text-[10px] font-bold uppercase tracking-wider text-gray-600">Station</dt><dd class="mt-2 text-sm text-gray-300">{{ $selectedOfficer->station?->name ?? 'Unassigned' }}</dd></div>
                        <div class="p-5"><dt class="text-[10px] font-bold uppercase tracking-wider text-gray-600">Service status</dt><dd class="mt-2"><span class="rounded-full px-2 py-1 text-[10px] font-bold uppercase {{ strtolower($selectedOfficer->status) === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">{{ $selectedOfficer->status }}</span></dd></div>
                        <div class="border-t border-hq-700/60 p-5 sm:border-l-0"><dt class="text-[10px] font-bold uppercase tracking-wider text-gray-600">Assigned cases</dt><dd class="mt-2 text-xl font-bold text-white">{{ number_format($selectedOfficer->cases_count) }}</dd></div>
                        <div class="border-t border-hq-700/60 p-5"><dt class="text-[10px] font-bold uppercase tracking-wider text-gray-600">Evidence records</dt><dd class="mt-2 text-xl font-bold text-white">{{ number_format($selectedOfficer->evidence_count) }}</dd></div>
                    </dl>
                </div>

                <div class="overflow-hidden rounded-xl border {{ $selectedOfficer->is_oc ? 'border-gold-500/20' : 'border-hq-700' }} bg-hq-800">
                    <div class="border-b border-hq-700 px-5 py-4"><h3 class="text-sm font-semibold text-white">OC account access</h3><p class="mt-1 text-[11px] text-gray-500">Manual admin action only</p></div>
                    @if ($selectedOfficer->is_oc)
                        <div class="p-5">
                            <div class="rounded-lg border border-gold-500/15 bg-gold-500/5 p-4"><p class="text-xs font-semibold text-gold-500">OC access is active</p><p class="mt-2 text-xs text-gray-400">{{ $selectedOfficer->user?->email ?? 'Linked account missing' }}</p></div>
                            <p class="mt-4 text-[11px] leading-5 text-gray-500">Removing access preserves the user and officer records, but changes the linked account to citizen access.</p>
                            <form action="{{ route('admin.officers.toggleOc', $selectedOfficer) }}" method="POST" class="mt-5">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full rounded-lg border border-rose-500/20 bg-rose-500/10 px-4 py-2.5 text-xs font-bold text-rose-400 transition hover:bg-rose-500/20">Remove OC access</button>
                            </form>
                        </div>
                    @elseif (! $ocEligible)
                        <div class="p-5"><div class="rounded-lg border border-amber-500/20 bg-amber-500/10 p-4"><p class="text-xs font-semibold text-amber-400">Not eligible for OC access</p><p class="mt-2 text-[11px] leading-5 text-gray-500">The officer and assigned station must both be active.</p></div><a href="{{ route('admin.officers.edit', $selectedOfficer) }}" class="mt-4 inline-flex text-xs font-semibold text-hq-300 hover:text-gold-500">Update officer assignment →</a></div>
                    @else
                        <form action="{{ route('admin.officers.toggleOc', $selectedOfficer) }}" method="POST" class="space-y-4 p-5">
                            @csrf @method('PATCH')
                            <p class="text-[11px] leading-5 text-gray-500">Enter credentials deliberately. The system will create an account only when you submit this form.</p>
                            <div><label for="email" class="mb-2 block text-xs font-semibold text-gray-300">Login email <span class="text-rose-400">*</span></label><input id="email" name="email" type="email" required value="{{ old('email', $selectedOfficer->user?->email) }}" class="h-10 w-full rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-white outline-none focus:border-gold-500/60"></div>
                            <div><label for="phone" class="mb-2 block text-xs font-semibold text-gray-300">Phone</label><input id="phone" name="phone" type="text" maxlength="15" value="{{ old('phone', $selectedOfficer->user?->phone) }}" class="h-10 w-full rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-white outline-none focus:border-gold-500/60"></div>
                            <div><label for="password" class="mb-2 block text-xs font-semibold text-gray-300">Initial password {{ $selectedOfficer->user ? '(optional)' : '*' }}</label><input id="password" name="password" type="password" {{ $selectedOfficer->user ? '' : 'required' }} class="h-10 w-full rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-white outline-none focus:border-gold-500/60"></div>
                            <div><label for="password_confirmation" class="mb-2 block text-xs font-semibold text-gray-300">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" class="h-10 w-full rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-white outline-none focus:border-gold-500/60"></div>
                            <button type="submit" class="w-full rounded-lg bg-gold-500 px-4 py-2.5 text-xs font-bold text-hq-900 transition hover:bg-gold-600">Assign as OC and enable account</button>
                        </form>
                    @endif
                </div>
            </section>
        @endif

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <form method="GET" action="{{ route('admin.officers.index') }}" class="grid grid-cols-1 gap-3 border-b border-hq-700 p-4 sm:grid-cols-2 xl:grid-cols-[minmax(16rem,1fr)_14rem_11rem_10rem_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="Search name, badge, or rank" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-white outline-none placeholder:text-gray-600 focus:border-indigo-500/60">
                <select name="station_id" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300 outline-none"><option value="">All stations</option>@foreach ($stations as $station)<option value="{{ $station->station_id }}" @selected((string) request('station_id') === (string) $station->station_id)>{{ $station->name }}</option>@endforeach</select>
                <select name="status" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300 outline-none"><option value="">All statuses</option><option value="Active" @selected(request('status') === 'Active')>Active</option><option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option></select>
                <select name="oc" class="h-10 rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-gray-300 outline-none"><option value="">All roles</option><option value="yes" @selected(request('oc') === 'yes')>OCs only</option><option value="no" @selected(request('oc') === 'no')>Non-OCs</option></select>
                <div class="flex gap-2"><button class="h-10 flex-1 rounded-lg bg-hq-600 px-4 text-xs font-bold text-white hover:bg-hq-500">Filter</button><a href="{{ route('admin.officers.index') }}" class="inline-flex h-10 items-center rounded-lg px-3 text-xs text-gray-500 hover:text-white">Reset</a></div>
            </form>

            <div class="flex items-center justify-between border-b border-hq-700 px-5 py-4"><div><h3 class="text-sm font-semibold text-white">Personnel directory</h3><p class="mt-1 text-[11px] text-gray-600">Officer records and station assignments</p></div><span class="rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ $officers->total() }} records</span></div>

            @if ($officers->isEmpty())
                <div class="px-6 py-16 text-center"><h3 class="text-sm font-semibold text-white">No officers found</h3><p class="mt-2 text-xs text-gray-500">Adjust the filters or add a new officer record.</p></div>
            @else
                <div class="overflow-x-auto">
                    <table class="hq-table w-full min-w-[1050px] text-left">
                        <thead><tr class="border-b border-hq-700 bg-hq-900/60 text-[10px] font-bold uppercase tracking-widest text-gray-500"><th class="px-5 py-3.5">Officer</th><th class="px-4 py-3.5">Badge</th><th class="px-4 py-3.5">Rank</th><th class="px-4 py-3.5">Station</th><th class="px-4 py-3.5 text-center">Cases</th><th class="px-4 py-3.5">Status</th><th class="px-5 py-3.5 text-right">Actions</th></tr></thead>
                        <tbody class="divide-y divide-hq-700/60 text-[13px]">
                            @foreach ($officers as $officer)
                                <tr class="text-gray-400 transition hover:text-gray-200">
                                    <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500/10 text-xs font-bold text-indigo-400">{{ strtoupper(substr($officer->name, 0, 1)) }}</div><div><a href="{{ route('admin.officers.show', $officer) }}" class="font-semibold text-gray-200 hover:text-gold-500">{{ $officer->name }}</a>@if ($officer->is_oc)<p class="mt-0.5 text-[9px] font-bold uppercase tracking-wider text-gold-500">Officer in Charge</p>@else<p class="mt-0.5 text-[10px] text-gray-600">Officer #{{ $officer->officer_id }}</p>@endif</div></div></td>
                                    <td class="px-4 py-4 font-mono text-gray-500">{{ $officer->badge_number }}</td>
                                    <td class="px-4 py-4">{{ $officer->rank }}</td>
                                    <td class="px-4 py-4">{{ $officer->station?->name ?? 'Unassigned' }}</td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-300">{{ number_format($officer->cases_count) }}</td>
                                    <td class="px-4 py-4"><span class="rounded-full px-2 py-1 text-[10px] font-bold uppercase {{ strtolower($officer->status) === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">{{ $officer->status }}</span></td>
                                    <td class="px-5 py-4 text-right"><div class="inline-flex items-center gap-1.5"><a href="{{ route('admin.officers.show', $officer) }}" class="rounded-md bg-sky-500/10 px-2.5 py-1.5 text-[11px] font-semibold text-sky-400 hover:bg-sky-500/20">Manage</a><a href="{{ route('admin.officers.edit', $officer) }}" class="rounded-md bg-amber-500/10 px-2.5 py-1.5 text-[11px] font-semibold text-amber-400 hover:bg-amber-500/20">Edit</a><form action="{{ route('admin.officers.destroy', $officer) }}" method="POST" onsubmit="return confirm('Delete this officer record?');">@csrf @method('DELETE')<button class="rounded-md bg-rose-500/10 px-2.5 py-1.5 text-[11px] font-semibold text-rose-400 hover:bg-rose-500/20">Delete</button></form></div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-hq-700 px-5 py-4">{{ $officers->links() }}</div>
            @endif
        </section>
    </div>
</x-admin-layout>
