<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div><p class="text-xs font-bold uppercase tracking-[.18em] text-sky-600">Complaint details</p><h2 class="mt-1 text-xl font-bold text-slate-900">{{ $complaint->reference }}</h2></div>
            <a href="{{ route('profile.edit') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Back to my complaints</a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6">
            @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>@endif
            <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="absolute right-0 top-0 h-40 w-40 rounded-bl-full bg-sky-50"></div>
                <div class="relative flex justify-between gap-5">
                    <div><p class="text-xs font-bold uppercase tracking-[.2em] text-sky-600">Citizen complaint</p><h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $complaint->complaint_title }}</h3><p class="mt-2 text-sm text-slate-500">Submitted {{ $complaint->submitted_date->format('d M Y') }}</p></div>
                    <span class="h-fit rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-bold text-sky-700">{{ $complaint->status }}</span>
                </div>
            </section>
            <section class="grid gap-6 lg:grid-cols-[1.35fr_.65fr]">
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h3 class="font-bold text-slate-900">Incident description</h3><p class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $complaint->description }}</p></article>
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="font-bold text-slate-900">Handling station</h3>
                    <dl class="mt-4 space-y-4 text-sm"><div><dt class="text-xs uppercase text-slate-400">Thana</dt><dd class="mt-1 font-semibold">{{ $complaint->station->name }}</dd></div><div><dt class="text-xs uppercase text-slate-400">Command</dt><dd class="mt-1">{{ $complaint->station->parent?->name ?: 'Not available' }}</dd></div><div><dt class="text-xs uppercase text-slate-400">Contact</dt><dd class="mt-1">{{ $complaint->station->contact_number ?: 'Not available' }}</dd></div></dl>
                    <a href="{{ route('stations.show',$complaint->station) }}" class="mt-5 inline-flex rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700">View station profile</a>
                </article>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-bold text-slate-900">Linked FIR</h3>
                @if($complaint->caseFir)
                    <div class="mt-5 grid gap-4 sm:grid-cols-3"><div><p class="text-xs uppercase text-slate-400">Reference</p><p class="mt-1 font-bold">FIR #{{ $complaint->caseFir->case_id }}</p></div><div><p class="text-xs uppercase text-slate-400">Title</p><p class="mt-1">{{ $complaint->caseFir->case_title }}</p></div><div><p class="text-xs uppercase text-slate-400">Status</p><p class="mt-1 font-semibold text-emerald-700">{{ $complaint->caseFir->status }}</p></div></div>
                    <p class="mt-5 text-sm text-slate-600">Investigating officer: {{ $complaint->caseFir->officer?->name ?: 'Not assigned' }}</p>
                @else
                    <div class="mt-5 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center"><p class="font-semibold text-slate-600">No FIR has been created.</p><p class="mt-2 text-sm text-slate-400">It will appear here after the station escalates this complaint.</p></div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
