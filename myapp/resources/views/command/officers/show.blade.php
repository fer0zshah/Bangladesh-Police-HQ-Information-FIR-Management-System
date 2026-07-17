<x-command-layout pageTitle="Officer Profile">
    <div class="mx-auto max-w-5xl space-y-6">
        @if(session('success'))<div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <section class="grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
            <article class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
                <div class="flex items-center justify-between border-b border-hq-700 bg-gradient-to-r from-hq-800 to-hq-700/40 p-6"><div class="flex items-center gap-4"><span class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-500/10 text-xl font-bold text-indigo-400">{{ strtoupper(substr($officer->name,0,1)) }}</span><div><div class="flex items-center gap-2"><h2 class="text-xl font-bold text-white">{{ $officer->name }}</h2>@if($officer->is_oc)<span class="rounded-full bg-gold-500/10 px-2 py-1 text-[9px] font-bold uppercase text-gold-500">OC</span>@endif</div><p class="mt-1 text-sm text-gray-500">{{ $officer->rank }} · {{ $officer->badge_number }}</p></div></div><a href="{{ route('command.officers.edit',$officer) }}" class="rounded-lg border border-hq-600 px-4 py-2 text-xs font-semibold text-gray-300 hover:bg-hq-700">Edit</a></div>
                <dl class="grid grid-cols-2 divide-x divide-y divide-hq-700/60"><div class="p-5"><dt class="text-[10px] uppercase tracking-widest text-gray-600">Thana</dt><dd class="mt-2 text-sm text-gray-200">{{ $officer->station?->name }}</dd></div><div class="p-5"><dt class="text-[10px] uppercase tracking-widest text-gray-600">Status</dt><dd class="mt-2 text-sm {{ strtolower($officer->status)==='active'?'text-emerald-400':'text-rose-400' }}">{{ $officer->status }}</dd></div><div class="p-5"><dt class="text-[10px] uppercase tracking-widest text-gray-600">Assigned FIRs</dt><dd class="mt-2 text-2xl font-bold text-white">{{ $officer->cases_count }}</dd></div><div class="p-5"><dt class="text-[10px] uppercase tracking-widest text-gray-600">Evidence logged</dt><dd class="mt-2 text-2xl font-bold text-white">{{ $officer->evidence_count }}</dd></div></dl>
            </article>
            <article class="overflow-hidden rounded-xl border {{ $officer->is_oc?'border-gold-500/20':'border-hq-700' }} bg-hq-800">
                <div class="border-b border-hq-700 px-5 py-4"><h3 class="font-semibold text-white">Officer-in-Charge access</h3><p class="mt-1 text-xs text-gray-500">One OC account is permitted per thana.</p></div>
                @if($officer->is_oc)
                    <div class="p-5"><div class="rounded-lg border border-gold-500/20 bg-gold-500/10 p-4"><p class="text-sm font-semibold text-gold-500">OC access active</p><p class="mt-2 text-xs text-gray-400">{{ $officer->user?->email ?: 'Linked account unavailable' }}</p></div><form method="POST" action="{{ route('command.officers.toggleOc',$officer) }}" class="mt-5">@csrf @method('PATCH')<button class="w-full rounded-lg border border-rose-500/30 px-4 py-2.5 text-xs font-bold text-rose-400 hover:bg-rose-500/10">Remove OC access</button></form></div>
                @elseif(strtolower($officer->status)!=='active')
                    <div class="p-5 text-sm text-amber-400">Activate this officer before assigning OC access.</div>
                @else
                    <form method="POST" action="{{ route('command.officers.toggleOc',$officer) }}" class="space-y-4 p-5">@csrf @method('PATCH')
                        <div><label class="mb-2 block text-xs font-semibold text-gray-300">Login email</label><input name="email" type="email" required value="{{ old('email',$officer->user?->email) }}" class="h-10 w-full rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-white"></div>
                        <div><label class="mb-2 block text-xs font-semibold text-gray-300">Phone</label><input name="phone" value="{{ old('phone',$officer->user?->phone) }}" class="h-10 w-full rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-white"></div>
                        <div><label class="mb-2 block text-xs font-semibold text-gray-300">Password {{ $officer->user?'(optional)':'*' }}</label><input name="password" type="password" {{ $officer->user?'':'required' }} class="h-10 w-full rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-white"></div>
                        <div><label class="mb-2 block text-xs font-semibold text-gray-300">Confirm password</label><input name="password_confirmation" type="password" class="h-10 w-full rounded-lg border border-hq-700 bg-hq-900 px-3 text-sm text-white"></div>
                        <button class="w-full rounded-lg border border-gold-500/40 px-4 py-2.5 text-xs font-bold text-gold-400 hover:bg-gold-500/10">Assign as OC</button>
                    </form>
                @endif
            </article>
        </section>
        <a href="{{ route('command.officers.index') }}" class="inline-flex text-sm text-gray-500 hover:text-white">← Back to personnel directory</a>
    </div>
</x-command-layout>
