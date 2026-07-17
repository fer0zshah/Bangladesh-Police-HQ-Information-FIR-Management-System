<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Submit a Complaint</h2>
            <p class="mt-1 text-sm text-gray-500">Send a preliminary complaint directly to the responsible police thana.</p>
        </div>
    </x-slot>

    <div class="bg-slate-50 py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-900 px-6 py-5 text-white">
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-sky-400">Citizen complaint form</p>
                    <h3 class="mt-2 text-xl font-bold">Tell the station what happened</h3>
                    <p class="mt-2 text-sm text-slate-400">Your registered name and NID will be attached automatically.</p>
                </div>

                <form method="POST" action="{{ route('citizen.complaints.store') }}" class="space-y-6 p-6">
                    @csrf

                    <div class="grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2">
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Complainant</p><p class="mt-1 font-semibold text-slate-800">{{ auth()->user()->name }}</p></div>
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Registered NID</p><p class="mt-1 font-semibold text-slate-800">{{ auth()->user()->nid_number }}</p></div>
                    </div>

                    <div>
                        <label for="station_id" class="mb-2 block text-sm font-semibold text-slate-700">Police thana</label>
                        <select id="station_id" name="station_id" required class="h-12 w-full rounded-xl border-slate-300 bg-white text-sm text-slate-800 focus:border-sky-500 focus:ring-sky-500">
                            <option value="">Select the thana handling this incident</option>
                            @foreach($stations as $station)
                                <option value="{{ $station->station_id }}" @selected((string) old('station_id') === (string) $station->station_id)>
                                    {{ $station->name }}{{ $station->district ? ' — '.$station->district : '' }}{{ $station->parent_name ? ' ('.$station->parent_name.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('station_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between"><label for="description" class="text-sm font-semibold text-slate-700">Incident description</label><span id="description-count" class="text-xs text-slate-400">0 / 255</span></div>
                        <textarea id="description" name="description" rows="7" minlength="15" maxlength="255" required placeholder="Describe what happened, when and where it happened, and any important identifying details." class="w-full rounded-xl border-slate-300 text-sm leading-6 text-slate-800 focus:border-sky-500 focus:ring-sky-500">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                        <a href="{{ route('citizen.dashboard') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
                        <button class="h-11 rounded-xl bg-slate-900 px-6 text-sm font-bold text-white hover:bg-slate-800">Submit complaint</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const description = document.getElementById('description');
        const counter = document.getElementById('description-count');
        const updateCount = () => counter.textContent = `${description.value.length} / 255`;
        description.addEventListener('input', updateCount);
        updateCount();
    </script>
</x-app-layout>
