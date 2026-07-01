<x-admin-layout pageTitle="Add Police Station">
    <div class="mx-auto max-w-6xl space-y-5">
        <a href="{{ route('admin.stations.index') }}" class="inline-flex items-center gap-2 text-xs font-medium text-hq-300 transition hover:text-gold-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to station registry
        </a>

        @if ($errors->any())
            <div class="flex items-start gap-3 rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                <svg class="mt-0.5 h-5 w-5 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.1 19h13.8a2 2 0 001.7-3L13.7 4a2 2 0 00-3.4 0L3.4 16a2 2 0 001.7 3z"/></svg>
                <div><p class="font-semibold">Please review the highlighted fields.</p><p class="mt-0.5 text-xs text-rose-300/70">The station has not been registered yet.</p></div>
            </div>
        @endif

        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <form action="{{ route('admin.stations.store') }}" method="POST" class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/10">
                @csrf

                <div class="flex items-center gap-4 border-b border-hq-700 bg-gradient-to-r from-hq-800 to-hq-700/40 px-5 py-5 sm:px-7">
                    <div class="flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-sky-500/10 text-sky-400 ring-1 ring-inset ring-sky-500/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m6-14h1m4 0h1m-6 4h1m4 0h1m-5 10v-5h4v5"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-white">Station information</h2>
                        <p class="mt-1 text-xs text-gray-500">Enter the official registry and contact details.</p>
                    </div>
                </div>

                <div class="space-y-6 p-5 sm:p-7">
                    <div>
                        <label for="name" class="mb-2 block text-xs font-semibold text-gray-300">Station name <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" id="name" required maxlength="100" value="{{ old('name') }}" placeholder="e.g. Uttara East Police Station"
                               class="h-11 w-full rounded-lg border bg-hq-900 px-4 text-sm text-white outline-none transition placeholder:text-gray-600 focus:border-sky-500/70 focus:ring-2 focus:ring-sky-500/10 {{ $errors->has('name') ? 'border-rose-500/60' : 'border-hq-700' }}">
                        @error('name')<p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="district" class="mb-2 block text-xs font-semibold text-gray-300">District <span class="text-rose-400">*</span></label>
                            <input type="text" name="district" id="district" required maxlength="50" value="{{ old('district') }}" placeholder="e.g. Dhaka"
                                   class="h-11 w-full rounded-lg border bg-hq-900 px-4 text-sm text-white outline-none transition placeholder:text-gray-600 focus:border-sky-500/70 focus:ring-2 focus:ring-sky-500/10 {{ $errors->has('district') ? 'border-rose-500/60' : 'border-hq-700' }}">
                            @error('district')<p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="contact_number" class="mb-2 block text-xs font-semibold text-gray-300">Contact number <span class="font-normal text-gray-600">(optional)</span></label>
                            <input type="tel" name="contact_number" id="contact_number" maxlength="15" autocomplete="tel" value="{{ old('contact_number') }}" placeholder="e.g. +8801711000000"
                                   class="h-11 w-full rounded-lg border bg-hq-900 px-4 text-sm text-white outline-none transition placeholder:text-gray-600 focus:border-sky-500/70 focus:ring-2 focus:ring-sky-500/10 {{ $errors->has('contact_number') ? 'border-rose-500/60' : 'border-hq-700' }}">
                            @error('contact_number')<p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="address" class="mb-2 block text-xs font-semibold text-gray-300">Full address <span class="font-normal text-gray-600">(optional)</span></label>
                        <textarea name="address" id="address" rows="4" placeholder="Street, area, postal code, district"
                                  class="w-full resize-y rounded-lg border bg-hq-900 px-4 py-3 text-sm leading-6 text-white outline-none transition placeholder:text-gray-600 focus:border-sky-500/70 focus:ring-2 focus:ring-sky-500/10 {{ $errors->has('address') ? 'border-rose-500/60' : 'border-hq-700' }}">{{ old('address') }}</textarea>
                        @error('address')<p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>@enderror
                        <p class="mt-2 text-[11px] text-gray-600">Use the address officers and citizens will recognize.</p>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-hq-700 bg-hq-900/35 px-5 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-7">
                    <a href="{{ route('admin.stations.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg px-5 text-sm font-semibold text-gray-400 transition hover:bg-hq-700 hover:text-white">Cancel</a>
                    <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-gold-500 px-5 text-sm font-bold text-hq-900 shadow-lg shadow-gold-500/10 transition hover:-translate-y-0.5 hover:bg-gold-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Register station
                    </button>
                </div>
            </form>

            <aside class="space-y-4 lg:sticky lg:top-20">
                <div class="rounded-xl border border-hq-700 bg-hq-800 p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gold-500">Registry guide</p>
                    <h3 class="mt-2 text-sm font-semibold text-white">Before you register</h3>
                    <div class="mt-5 space-y-4">
                        <div class="flex gap-3"><span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-sky-500/10 text-[10px] font-bold text-sky-400">1</span><p class="text-xs leading-5 text-gray-400">Use the station's complete official name.</p></div>
                        <div class="flex gap-3"><span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-sky-500/10 text-[10px] font-bold text-sky-400">2</span><p class="text-xs leading-5 text-gray-400">Confirm its district and public contact number.</p></div>
                        <div class="flex gap-3"><span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-sky-500/10 text-[10px] font-bold text-sky-400">3</span><p class="text-xs leading-5 text-gray-400">Add enough address detail to identify the location.</p></div>
                    </div>
                </div>

                <div class="rounded-xl border border-emerald-500/15 bg-emerald-500/5 p-5">
                    <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-400"></span><p class="text-xs font-semibold text-emerald-300">Starts as operational</p></div>
                    <p class="mt-2 text-[11px] leading-5 text-gray-500">New stations are activated automatically. You can change this later from the registry or edit page.</p>
                </div>
            </aside>
        </div>
    </div>
</x-admin-layout>
