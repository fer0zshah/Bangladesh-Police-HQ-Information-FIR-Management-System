<x-admin-layout pageTitle="Edit Police Station">
    <div class="mx-auto max-w-6xl space-y-5">
        <a href="{{ route('admin.stations.show', $station) }}" class="inline-flex items-center gap-2 text-xs font-medium text-hq-300 transition hover:text-gold-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to station profile
        </a>

        @if ($errors->any())
            <div class="flex items-start gap-3 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                <svg class="mt-0.5 h-5 w-5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.1 19h13.8a2 2 0 001.7-3L13.7 4a2 2 0 00-3.4 0L3.4 16a2 2 0 001.7 3z"/></svg>
                <div><p class="font-semibold">Please review the highlighted fields.</p><p class="mt-0.5 text-xs text-rose-300/70">Your changes have not been saved.</p></div>
            </div>
        @endif

        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <form action="{{ route('admin.stations.update', $station) }}" method="POST" class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/10">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-4 border-b border-hq-700 bg-gradient-to-r from-hq-800 to-hq-700/40 px-5 py-5 sm:px-7">
                    <div class="flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 ring-1 ring-inset ring-amber-500/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.2 5.2l3.6 3.6M16.7 3.7a2.5 2.5 0 113.6 3.6L6.5 21H3v-3.6L16.7 3.7z"/></svg>
                    </div>
                    <div><h2 class="text-base font-semibold text-white">Station information</h2><p class="mt-1 text-xs text-gray-500">Editing registry #{{ $station->station_id }}</p></div>
                </div>

                <div class="space-y-6 p-5 sm:p-7">
                    <div>
                        <label for="name" class="mb-2 block text-xs font-semibold text-gray-300">Station name <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" id="name" required maxlength="100" value="{{ old('name', $station->name) }}"
                               class="h-11 w-full rounded-lg border bg-hq-900 px-4 text-sm text-white outline-none transition focus:border-sky-500/70 focus:ring-2 focus:ring-sky-500/10 {{ $errors->has('name') ? 'border-rose-500/60' : 'border-hq-700' }}">
                        @error('name')<p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="district" class="mb-2 block text-xs font-semibold text-gray-300">District <span class="text-rose-400">*</span></label>
                            <input type="text" name="district" id="district" required maxlength="50" value="{{ old('district', $station->district) }}"
                                   class="h-11 w-full rounded-lg border bg-hq-900 px-4 text-sm text-white outline-none transition focus:border-sky-500/70 focus:ring-2 focus:ring-sky-500/10 {{ $errors->has('district') ? 'border-rose-500/60' : 'border-hq-700' }}">
                            @error('district')<p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="contact_number" class="mb-2 block text-xs font-semibold text-gray-300">Contact number <span class="font-normal text-gray-600">(optional)</span></label>
                            <input type="tel" name="contact_number" id="contact_number" maxlength="15" autocomplete="tel" value="{{ old('contact_number', $station->contact_number) }}"
                                   class="h-11 w-full rounded-lg border bg-hq-900 px-4 text-sm text-white outline-none transition focus:border-sky-500/70 focus:ring-2 focus:ring-sky-500/10 {{ $errors->has('contact_number') ? 'border-rose-500/60' : 'border-hq-700' }}">
                            @error('contact_number')<p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-[minmax(0,1fr)_14rem]">
                        <div>
                            <label for="address" class="mb-2 block text-xs font-semibold text-gray-300">Full address <span class="font-normal text-gray-600">(optional)</span></label>
                            <textarea name="address" id="address" rows="4" class="w-full resize-y rounded-lg border bg-hq-900 px-4 py-3 text-sm leading-6 text-white outline-none transition focus:border-sky-500/70 focus:ring-2 focus:ring-sky-500/10 {{ $errors->has('address') ? 'border-rose-500/60' : 'border-hq-700' }}">{{ old('address', $station->address) }}</textarea>
                            @error('address')<p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="status" class="mb-2 block text-xs font-semibold text-gray-300">Operational status <span class="text-rose-400">*</span></label>
                            <select name="status" id="status" required class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none transition focus:border-sky-500/70 focus:ring-2 focus:ring-sky-500/10">
                                <option value="Active" {{ old('status', $station->status) === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status', $station->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>@enderror
                            <p class="mt-2 text-[11px] leading-5 text-gray-600">Inactive stations remain in the registry.</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-hq-700 bg-hq-900/35 px-5 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-7">
                    <a href="{{ route('admin.stations.show', $station) }}" class="inline-flex h-10 items-center justify-center rounded-lg px-5 text-sm font-semibold text-gray-400 transition hover:bg-hq-700 hover:text-white">Cancel</a>
                    <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-gold-500 px-5 text-sm font-bold text-hq-900 shadow-lg shadow-gold-500/10 transition hover:-translate-y-0.5 hover:bg-gold-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Save changes
                    </button>
                </div>
            </form>

            <aside class="space-y-4 lg:sticky lg:top-20">
                <div class="rounded-xl border border-hq-700 bg-hq-800 p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-hq-300">Current record</p>
                    <h3 class="mt-2 text-sm font-semibold text-white">{{ $station->name }}</h3>
                    <dl class="mt-5 space-y-4 text-xs">
                        <div><dt class="text-gray-600">Registry ID</dt><dd class="mt-1 font-mono text-gray-300">#{{ $station->station_id }}</dd></div>
                        <div><dt class="text-gray-600">District</dt><dd class="mt-1 text-gray-300">{{ $station->district }}</dd></div>
                        <div><dt class="text-gray-600">Current status</dt><dd class="mt-1"><span class="inline-flex rounded-full px-2 py-1 text-[10px] font-bold uppercase {{ strtolower($station->status) === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">{{ $station->status }}</span></dd></div>
                    </dl>
                </div>
                <div class="rounded-xl border border-amber-500/15 bg-amber-500/5 p-5"><p class="text-xs font-semibold text-amber-300">Safe status changes</p><p class="mt-2 text-[11px] leading-5 text-gray-500">Deactivating a station never removes its officers, complaints, or case history.</p></div>
            </aside>
        </div>
    </div>
</x-admin-layout>
