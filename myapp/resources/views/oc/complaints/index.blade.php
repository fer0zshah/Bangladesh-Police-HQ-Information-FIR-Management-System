<x-oc-layout pageTitle="Complaint Management">
    <div class="mx-auto max-w-7xl space-y-6">
            @if(session('success'))<div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300"><p class="font-semibold">Please correct the following:</p><ul class="mt-2 list-inside list-disc text-xs">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach([['All Complaints',$summary['total'],'sky'],['Pending',$summary['pending'],'amber'],['Under Review',$summary['review'],'indigo'],['Escalated',$summary['escalated'],'emerald']] as [$label,$value,$tone])
                    <div class="rounded-xl border border-[#243447] bg-[#1a252f] p-4 shadow-lg shadow-black/10"><p class="text-[9px] font-bold uppercase tracking-widest text-gray-500">{{ $label }}</p><div class="mt-2 flex items-end justify-between"><p class="text-2xl font-extrabold text-white">{{ number_format($value) }}</p><span class="h-2.5 w-2.5 rounded-full bg-{{ $tone }}-400"></span></div></div>
                @endforeach
            </section>

            <section class="overflow-hidden rounded-xl border border-[#243447] bg-[#1a252f] shadow-xl shadow-black/10">
                <form method="GET" class="grid gap-3 border-b border-[#243447] p-4 sm:grid-cols-[1fr_13rem_auto]">
                    <input name="search" value="{{ request('search') }}" placeholder="Search title, name, NID or complaint text" class="h-10 rounded-lg border border-[#34495e] bg-[#0f1923] px-3 text-sm text-white placeholder:text-gray-600 focus:border-blue-500 focus:ring-blue-500">
                    <select name="status" class="h-10 rounded-lg border border-[#34495e] bg-[#0f1923] px-3 text-sm text-gray-300 focus:border-blue-500 focus:ring-blue-500"><option value="">All statuses</option>@foreach(['Pending','Under Review','Escalated','Dismissed'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
                    <button class="rounded-lg bg-blue-600 px-5 text-sm font-semibold text-white hover:bg-blue-500">Filter</button>
                </form>

                <div class="divide-y divide-[#243447]">
                    @forelse($complaints as $complaint)
                        @php
                            $statusClass = match($complaint->status) {
                                'Pending' => 'bg-amber-500/10 text-amber-400',
                                'Under Review' => 'bg-indigo-500/10 text-indigo-400',
                                'Escalated' => 'bg-emerald-500/10 text-emerald-400',
                                'Dismissed' => 'bg-gray-500/10 text-gray-400',
                                default => 'bg-sky-500/10 text-sky-400',
                            };
                        @endphp
                        <article class="p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2"><h3 class="font-semibold text-white">{{ $complaint->complaint_title }}</h3><span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusClass }}">{{ $complaint->status }}</span>@if($complaint->caseFir)<span class="rounded-full bg-blue-500/10 px-2.5 py-1 text-[10px] font-bold text-blue-400">FIR #{{ $complaint->caseFir->case_id }}</span>@endif</div>
                                    <p class="mt-1 text-[11px] text-gray-600">Complaint #{{ $complaint->complaint_id }} &middot; {{ $complaint->complainant_name }} &middot; NID {{ $complaint->complainant_nid }} &middot; {{ date('d M Y', strtotime($complaint->submitted_date)) }}</p>
                                    <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-400">{{ $complaint->description }}</p>
                                </div>
                                <div class="flex shrink-0 flex-wrap gap-2">
                                    @if($complaint->status === 'Pending')
                                        <form method="POST" action="{{ route('oc.complaints.status', $complaint) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Under Review"><button class="rounded-lg bg-indigo-500/10 px-4 py-2 text-xs font-semibold text-indigo-400 hover:bg-indigo-500/20">Start review</button></form>
                                    @elseif($complaint->status === 'Under Review')
                                        <form method="POST" action="{{ route('oc.complaints.status', $complaint) }}" onsubmit="return confirm('Dismiss this complaint?')">@csrf @method('PATCH')<input type="hidden" name="status" value="Dismissed"><button class="rounded-lg bg-rose-500/10 px-4 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-500/20">Dismiss</button></form>
                                    @endif
                                </div>
                            </div>

                            @if($complaint->status === 'Under Review')
                                <details class="mt-4 rounded-lg border border-emerald-500/20 bg-emerald-500/[0.04]">
                                    <summary class="cursor-pointer px-4 py-3 text-xs font-semibold text-emerald-400">Escalate and create FIR</summary>
                                    <form method="POST" action="{{ route('oc.complaints.escalate', $complaint) }}" class="grid gap-4 border-t border-emerald-500/10 p-4 md:grid-cols-2">@csrf
                                        <label class="text-xs font-semibold text-gray-400 md:col-span-2">FIR title<input name="case_title" required maxlength="255" value="{{ old('case_title', $complaint->complaint_title) }}" class="mt-2 h-10 w-full rounded-lg border border-[#34495e] bg-[#0f1923] px-3 text-sm text-white focus:border-emerald-500 focus:ring-emerald-500"></label>
                                        <label class="text-xs font-semibold text-gray-400">Investigating officer<select name="investigating_officer_id" required class="mt-2 h-10 w-full rounded-lg border border-[#34495e] bg-[#0f1923] px-3 text-sm text-white focus:border-emerald-500 focus:ring-emerald-500"><option value="">Select officer</option>@foreach($officers as $officer)<option value="{{ $officer->officer_id }}" @selected((string)old('investigating_officer_id')===(string)$officer->officer_id)>{{ $officer->name }} &middot; {{ $officer->badge_number }}</option>@endforeach</select></label>
                                        <div class="flex items-end"><button class="h-10 w-full rounded-lg bg-emerald-600 px-5 text-sm font-bold text-white hover:bg-emerald-500" onclick="return confirm('Escalate this complaint and create an FIR?')">Create FIR</button></div>
                                    </form>
                                </details>
                            @endif
                        </article>
                    @empty
                        <div class="p-12 text-center text-sm text-gray-600">No complaints match your filters.</div>
                    @endforelse
                </div>
                <div class="border-t border-[#243447] p-4">{{ $complaints->links() }}</div>
            </section>
    </div>
</x-oc-layout>


