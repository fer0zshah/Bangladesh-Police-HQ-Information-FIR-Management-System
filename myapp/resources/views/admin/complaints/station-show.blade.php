<x-admin-layout pageTitle="{{ $station->name }} Complaint Dictionary">
    <div class="mx-auto max-w-[1440px] space-y-6">
        <section class="relative overflow-hidden rounded-xl border border-hq-700 bg-gradient-to-br from-hq-800 via-hq-800 to-hq-700/70 p-5 shadow-xl shadow-black/10 sm:p-6">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-rose-500/10 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-rose-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-400 shadow-[0_0_8px_rgba(251,113,133,0.6)]"></span>
                        {{ $station->parent?->name ?? 'Station command' }}
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">{{ $station->name }} complaint dictionary</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-400">Citizen submissions registered under this thana only.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if($station->parent)
                        <a href="{{ route('admin.complaints.hq', $station->parent) }}" class="inline-flex items-center justify-center rounded-lg border border-hq-600 bg-transparent px-4 py-2.5 text-sm font-bold text-gray-300 transition hover:border-gold-500/50 hover:bg-gold-500/10 hover:text-white">
                            Back to {{ $station->parent->name }}
                        </a>
                    @endif
                    <a href="{{ route('admin.complaints.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gold-500/40 bg-transparent px-4 py-2.5 text-sm font-bold text-gold-400 transition hover:border-gold-500 hover:bg-gold-500/10">
                        Complaint browser
                    </a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Complaints', 'value' => $summary['total_complaints'], 'note' => 'Registered here', 'color' => 'rose'],
                ['label' => 'Pending', 'value' => $summary['pending_complaints'], 'note' => 'Awaiting OC review', 'color' => 'amber'],
                ['label' => 'Under Review', 'value' => $summary['review_complaints'], 'note' => 'Currently checked', 'color' => 'sky'],
                ['label' => 'Escalated', 'value' => $summary['escalated_complaints'], 'note' => 'Moved toward FIR', 'color' => 'emerald'],
            ] as $card)
                <div class="group relative overflow-hidden rounded-xl border border-hq-700 bg-hq-800 p-5 transition hover:border-{{ $card['color'] }}-500/40 hover:bg-hq-800/80">
                    <div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-{{ $card['color'] }}-500/5 transition group-hover:bg-{{ $card['color'] }}-500/10"></div>
                    <div class="relative">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-extrabold text-white">{{ number_format($card['value']) }}</p>
                        <p class="mt-4 text-xs text-gray-500">{{ $card['note'] }}</p>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800 shadow-xl shadow-black/5">
            <div class="flex flex-col gap-3 border-b border-hq-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-white">Complaint dictionary</h3>
                    <p class="mt-1 text-xs text-gray-500">Search and filter citizen submissions for this station.</p>
                </div>
                <span class="w-fit rounded-full border border-hq-600 bg-hq-900/60 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gray-400">{{ $complaints->total() }} records</span>
            </div>

            @include('admin.complaints.partials.complaint-table', ['showStation' => false])
        </section>
    </div>
</x-admin-layout>
