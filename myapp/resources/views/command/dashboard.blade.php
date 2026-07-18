<x-app-layout>
    <x-slot name="header"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-sky-600">Command overview</p><h2 class="mt-1 text-xl font-bold text-slate-900">{{$headquarters->name}}</h2></div></x-slot>
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6">
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([['Thana stations',$stats['stations']],['Active officers',$stats['officers']],['Active FIRs',$stats['active_cases']],['Pending complaints',$stats['pending_complaints']]] as [$label,$value])
                    <article class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="absolute right-0 top-0 h-20 w-20 rounded-bl-full bg-sky-50"></div><p class="relative text-xs font-bold uppercase tracking-wider text-slate-500">{{$label}}</p><p class="relative mt-4 text-3xl font-black text-slate-900">{{$value}}</p></article>
                @endforeach
            </section>
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><header class="border-b border-slate-200 p-5"><h3 class="font-bold text-slate-900">Thanas under this command</h3></header><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="p-4">Thana</th><th class="p-4">Officers</th><th class="p-4">FIRs</th><th class="p-4">Complaints</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($thanas as $thana)<tr><td class="p-4 font-semibold">{{$thana->name}}</td><td class="p-4">{{$thana->officers_count}}</td><td class="p-4">{{$thana->cases_count}}</td><td class="p-4">{{$thana->complaints_count}}</td></tr>@endforeach</tbody></table></div></section>
        </div>
    </div>
</x-app-layout>
