<x-app-layout>
    <x-slot name="header">
        @if($user->role === 'citizen')
            <div class="flex items-center justify-between gap-4">
                <div><p class="text-xs font-bold uppercase tracking-[.18em] text-sky-600">Citizen account</p><h2 class="mt-1 text-xl font-bold text-slate-900">Profile and complaint history</h2></div>
                <a href="{{ url('/') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Citizen home</a>
            </div>
        @else
            <h2 class="text-xl font-semibold text-gray-800">{{ __('Profile') }}</h2>
        @endif
    </x-slot>

    @if($user->role === 'citizen')
        <style>
            .citizen-profile-grid{width:min(1400px,calc(100% - 32px));margin:0 auto;display:grid;grid-template-columns:minmax(0,1fr);gap:24px}
            .citizen-profile-main{min-width:0;grid-row:1}.citizen-profile-side{min-width:0;grid-row:2}
            .citizen-summary-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
            .citizen-summary-card{position:relative;min-height:126px;overflow:hidden;border:1px solid #e2e8f0;border-radius:16px;background:#fff;padding:18px;box-shadow:0 1px 3px rgba(15,23,42,.06)}
            .citizen-summary-card::after{content:"";position:absolute;top:0;right:0;width:82px;height:82px;border-bottom-left-radius:82px;background:var(--accent)}
            .citizen-summary-card>*{position:relative;z-index:1}
            .citizen-filter{display:grid;grid-template-columns:1fr;gap:12px}
            .record-browsers{display:grid;grid-template-columns:1fr;gap:18px;padding:20px;background:#f8fafc}
            .record-browser{min-width:0;overflow:hidden;border:1px solid #e2e8f0;border-radius:16px;background:#fff}.record-slide{display:none}.record-slide.is-active{display:block}
            .record-arrow{width:36px;height:36px;border:1px solid #cbd5e1;border-radius:999px;background:#fff;color:#334155;font-size:18px;cursor:pointer}.record-arrow:disabled{opacity:.35}
            .citizen-profile-side .bg-gray-800{border:1px solid #bae6fd!important;background:#f0f9ff!important;color:#0369a1!important;box-shadow:none!important}
            @media(min-width:760px){.citizen-filter{grid-template-columns:minmax(0,1fr) 190px auto;align-items:center}.record-browsers{grid-template-columns:repeat(2,minmax(0,1fr))}}
            @media(min-width:1024px){.citizen-profile-grid{grid-template-columns:minmax(0,1fr) minmax(320px,380px);align-items:start}.citizen-profile-main{grid-column:1;grid-row:1}.citizen-profile-side{grid-column:2;grid-row:1}.citizen-summary-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
        </style>

        <div class="min-h-screen bg-slate-50 py-8">
            <div class="citizen-profile-grid">
                <main class="citizen-profile-main space-y-6">
                    <section class="citizen-summary-grid">
                        @foreach([
                            ['All complaints',$complaintSummary['total'],'text-sky-700','bg-sky-50','rgba(14,165,233,.09)'],
                            ['Open review',$complaintSummary['open'],'text-amber-700','bg-amber-50','rgba(245,158,11,.10)'],
                            ['Escalated to FIR',$complaintSummary['escalated'],'text-emerald-700','bg-emerald-50','rgba(16,185,129,.09)'],
                            ['Dismissed',$complaintSummary['dismissed'],'text-rose-700','bg-rose-50','rgba(244,63,94,.09)'],
                        ] as [$label,$value,$color,$background,$accent])
                            <article class="citizen-summary-card" style="--accent:{{$accent}}"><span class="inline-flex rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{$color}} {{$background}}">{{$label}}</span><p class="mt-4 text-3xl font-black text-slate-900">{{number_format($value)}}</p></article>
                        @endforeach
                    </section>

                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <header class="flex flex-col gap-4 border-b border-slate-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div><h3 class="font-bold text-slate-900">My submitted complaints and FIRs</h3><p class="mt-1 text-sm text-slate-500">Browse one complaint and linked FIR at a time.</p></div>
                            <div class="flex items-center gap-3"><span class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold text-sky-700">{{$complaints->total()}} complaint(s)</span><a href="{{route('citizen.complaints.create')}}" class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-xs font-bold text-sky-700">New complaint</a></div>
                        </header>
                        <form method="GET" action="{{route('profile.edit')}}" class="citizen-filter border-b border-slate-200 bg-slate-50 p-4">
                            <input name="search" value="{{request('search')}}" placeholder="Search title, reference or station" class="h-11 rounded-xl border-slate-300 text-sm">
                            <select name="status" class="h-11 rounded-xl border-slate-300 bg-white text-sm"><option value="">All statuses</option>@foreach(['Pending','Under Review','Escalated','Dismissed'] as $status)<option value="{{$status}}" @selected(request('status')===$status)>{{$status}}</option>@endforeach</select>
                            <div class="flex gap-2"><button class="h-11 rounded-xl border border-sky-200 bg-sky-50 px-5 text-sm font-bold text-sky-700">Filter</button>@if(request()->filled('search')||request()->filled('status'))<a href="{{route('profile.edit')}}" class="inline-flex h-11 items-center text-sm font-semibold text-slate-500">Reset</a>@endif</div>
                        </form>

                        @php($linkedFirs=$complaints->filter(fn($item)=>!empty($item->case_id))->values())
                        <div class="record-browsers">
                            <section class="record-browser" data-record-browser>
                                <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4"><div><p class="text-[10px] font-bold uppercase tracking-[.18em] text-sky-600">Complaint browser</p><h4 class="mt-1 font-bold">My complaints</h4></div><span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">{{$complaints->count()}}</span></header>
                                <div class="min-h-[250px]">
                                    @forelse($complaints as $complaint)
                                        <a href="{{route('citizen.complaints.show',$complaint)}}" class="record-slide min-h-[250px] p-5 hover:bg-slate-50" data-record-slide><div class="flex justify-between gap-3"><span class="font-bold text-sky-700">{{$complaint->reference}}</span><span class="rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-[10px] font-bold text-sky-700">{{$complaint->status}}</span></div><h5 class="mt-5 text-lg font-bold text-slate-900">{{$complaint->complaint_title}}</h5><p class="mt-3 text-sm leading-6 text-slate-500">{{$complaint->description}}</p><div class="mt-6 border-t border-slate-100 pt-4 text-xs text-slate-400"><p class="font-semibold text-slate-600">{{$complaint->station_name}}</p><p class="mt-1">{{$complaint->submitted_date->format('d M Y')}}</p></div></a>
                                    @empty
                                        <div class="flex min-h-[250px] items-center justify-center p-8 text-center text-slate-500">No complaints found.</div>
                                    @endforelse
                                </div>
                                <footer class="flex items-center justify-between border-t border-slate-200 px-5 py-3"><button type="button" class="record-arrow" data-record-prev>&lsaquo;</button><span class="text-xs font-bold text-slate-400" data-record-position>0 / 0</span><button type="button" class="record-arrow" data-record-next>&rsaquo;</button></footer>
                            </section>
                            <section class="record-browser" data-record-browser>
                                <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4"><div><p class="text-[10px] font-bold uppercase tracking-[.18em] text-emerald-600">FIR browser</p><h4 class="mt-1 font-bold">Linked FIRs</h4></div><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{$linkedFirs->count()}}</span></header>
                                <div class="min-h-[250px]">
                                    @forelse($linkedFirs as $fir)
                                        <a href="{{route('citizen.complaints.show',$fir)}}" class="record-slide min-h-[250px] p-5 hover:bg-slate-50" data-record-slide><div class="flex justify-between gap-3"><span class="font-bold text-emerald-700">FIR #{{$fir->case_id}}</span><span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">{{$fir->case_status}}</span></div><h5 class="mt-5 text-lg font-bold">{{$fir->case_title?:$fir->complaint_title}}</h5><p class="mt-3 text-sm leading-6 text-slate-500">Created from {{$fir->reference}} and handled by {{$fir->station_name}}.</p></a>
                                    @empty
                                        <div class="flex min-h-[250px] items-center justify-center p-8 text-center"><div><p class="font-semibold text-slate-600">No linked FIR yet.</p><p class="mt-2 text-sm text-slate-400">It will appear after station escalation.</p></div></div>
                                    @endforelse
                                </div>
                                <footer class="flex items-center justify-between border-t border-slate-200 px-5 py-3"><button type="button" class="record-arrow" data-record-prev>&lsaquo;</button><span class="text-xs font-bold text-slate-400" data-record-position>0 / 0</span><button type="button" class="record-arrow" data-record-next>&rsaquo;</button></footer>
                            </section>
                        </div>
                        @if($complaints->hasPages())<div class="border-t border-slate-200 px-5 py-4">{{$complaints->links()}}</div>@endif
                    </section>
                </main>

                <aside class="citizen-profile-side space-y-6">
                    <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="absolute right-0 top-0 h-32 w-32 rounded-bl-full bg-sky-50"></div><div class="relative"><div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-sky-100 bg-sky-50 text-xl font-black text-sky-700">{{strtoupper(substr($user->name,0,1))}}</div><h3 class="mt-4 text-lg font-bold">{{$user->name}}</h3><p class="mt-1 text-sm text-slate-500">{{$user->email}}</p><div class="mt-5 border-t border-slate-100 pt-4"><p class="text-[10px] font-bold uppercase tracking-[.18em] text-slate-400">Registered NID</p><p class="mt-1 text-sm font-semibold text-slate-700">{{$user->nid_number?:'Not recorded'}}</p></div></div></section>
                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">@include('profile.partials.update-profile-information-form')</section>
                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">@include('profile.partials.update-password-form')</section>
                    <section class="rounded-2xl border border-rose-200 bg-white p-6 shadow-sm">@include('profile.partials.delete-user-form')</section>
                </aside>
            </div>
        </div>
        <script>document.querySelectorAll('[data-record-browser]').forEach(browser=>{const slides=[...browser.querySelectorAll('[data-record-slide]')],prev=browser.querySelector('[data-record-prev]'),next=browser.querySelector('[data-record-next]'),position=browser.querySelector('[data-record-position]');let current=0;const render=()=>{slides.forEach((slide,index)=>slide.classList.toggle('is-active',index===current));position.textContent=slides.length?`${current+1} / ${slides.length}`:'0 / 0';prev.disabled=next.disabled=slides.length<2};prev.addEventListener('click',()=>{if(slides.length>1){current=(current-1+slides.length)%slides.length;render()}});next.addEventListener('click',()=>{if(slides.length>1){current=(current+1)%slides.length;render()}});render()})</script>
    @else
        <div class="py-12"><div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8"><div class="bg-white p-8 shadow sm:rounded-lg"><div class="max-w-xl">@include('profile.partials.update-profile-information-form')</div></div><div class="bg-white p-8 shadow sm:rounded-lg"><div class="max-w-xl">@include('profile.partials.update-password-form')</div></div><div class="bg-white p-8 shadow sm:rounded-lg"><div class="max-w-xl">@include('profile.partials.delete-user-form')</div></div></div></div>
    @endif
</x-app-layout>
