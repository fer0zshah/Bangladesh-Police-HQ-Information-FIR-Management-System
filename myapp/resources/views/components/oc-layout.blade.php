<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Station Command' }} | Bangladesh Police</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { sans: ['Inter', 'Arial', 'sans-serif'] },
                colors: {
                    hq: { 900:'#0f1923',800:'#1a252f',700:'#243447',600:'#2c3e50',500:'#34495e',400:'#4a6582',300:'#6b8caf',200:'#94b8d9',100:'#c5d9ed',50:'#e8f0f8' },
                    gold: { 500:'#f1c40f',600:'#d4ac0d',700:'#b7950b' }
                }
            }}
        }
    </script>
    <style>
        ::-webkit-scrollbar{width:6px;height:6px}::-webkit-scrollbar-track{background:#1a252f}::-webkit-scrollbar-thumb{background:#34495e;border-radius:3px}
        #ocSidebar{transform:translateX(-100%);transition:transform .25s ease}#ocSidebar.open{transform:translateX(0)}
        #ocOverlay{opacity:0;pointer-events:none;transition:opacity .25s ease}#ocOverlay.open{opacity:1;pointer-events:auto}
        .nav-transition{transition:all .2s ease}
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-hq-900 font-sans text-gray-200 antialiased">
    <nav class="sticky top-0 z-40 flex h-14 items-center border-b border-hq-700 bg-hq-800 px-4 lg:px-6">
        <div class="flex flex-1 items-center gap-4">
            <button id="ocMenuBtn" class="rounded p-1 text-gray-400 transition hover:text-gold-500" aria-label="Toggle navigation">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <a href="{{ route('oc.dashboard') }}" class="text-base font-bold tracking-tight text-gold-500">BD Police <span class="font-medium text-gray-400">Station Command</span></a>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider text-emerald-400 sm:inline-flex">OC Access</span>
            <div class="relative" id="ocUserDropdown">
                <button id="ocUserButton" class="flex items-center gap-2 rounded px-2 py-1 text-gray-400 transition hover:bg-hq-700 hover:text-white">
                    <div class="flex h-7 w-7 items-center justify-center rounded-full border border-hq-500 bg-hq-600 text-xs font-semibold text-gray-300">{{ strtoupper(substr(auth()->user()->name ?? 'O', 0, 1)) }}</div>
                    <span class="hidden text-sm font-medium sm:block">{{ auth()->user()->name ?? 'Officer' }}</span>
                    <svg class="h-3.5 w-3.5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="ocUserMenu" class="absolute right-0 z-50 mt-2 hidden w-52 rounded-lg border border-hq-700 bg-hq-800 py-1 shadow-xl">
                    <div class="border-b border-hq-700 px-4 py-2"><p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p><p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p></div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-400 hover:bg-hq-700 hover:text-white">Profile Settings</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-hq-700">@csrf<button class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-hq-700 hover:text-red-300">Sign Out</button></form>
                </div>
            </div>
        </div>
    </nav>

    <aside id="ocSidebar" class="fixed bottom-0 left-0 top-14 z-30 w-60 overflow-y-auto border-r border-hq-700 bg-hq-800">
        <div class="py-4">
            <div class="mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-600">Station Command</div>
            <a href="{{ route('oc.dashboard') }}" class="nav-transition flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('oc.dashboard') ? 'border-r-2 border-gold-500 bg-hq-700/50 text-gold-500' : 'text-gray-400 hover:bg-hq-700/40 hover:text-white' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h4v6H4V6zm10-2h4a2 2 0 012 2v4h-6V4zM4 14h6v6H6a2 2 0 01-2-2v-4zm10 0h6v4a2 2 0 01-2 2h-4v-6z"/></svg>
                Overview
            </a>
            <a href="{{ route('oc.complaints.index') }}" class="nav-transition flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('oc.complaints.*') ? 'border-r-2 border-gold-500 bg-hq-700/50 text-gold-500' : 'text-gray-400 hover:bg-hq-700/40 hover:text-white' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/></svg>
                Complaints
            </a>

            <div class="mb-2 mt-5 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-600">Operations</div>
            <a href="{{ route('oc.cases.index') }}" class="nav-transition flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('oc.cases.*') ? 'border-r-2 border-gold-500 bg-hq-700/50 text-gold-500' : 'text-gray-400 hover:bg-hq-700/40 hover:text-white' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                Case / FIR Management
            </a>
            <a href="{{ route('oc.criminals.index') }}" class="nav-transition flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('oc.criminals.*') ? 'border-r-2 border-gold-500 bg-hq-700/50 text-gold-500' : 'text-gray-400 hover:bg-hq-700/40 hover:text-white' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.5-1 6.8-2.75 9.57M8 11a4 4 0 118 0c0 1-.07 2-.2 3"/></svg>
                Criminal Management
            </a>
            <a href="{{ route('oc.evidence.index') }}" class="nav-transition flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('oc.evidence.*') ? 'border-r-2 border-gold-500 bg-hq-700/50 text-gold-500' : 'text-gray-400 hover:bg-hq-700/40 hover:text-white' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12h14V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 016 0"/></svg>
                Evidence Management
            </a>

            <div class="mb-2 mt-5 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-600">Personnel</div>
            <a href="{{ route('oc.officers.index') }}" class="nav-transition flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('oc.officers.*') ? 'border-r-2 border-gold-500 bg-hq-700/50 text-gold-500' : 'text-gray-400 hover:bg-hq-700/40 hover:text-white' }}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20H7v-2a5 5 0 0110 0v2zM15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Officers
            </a>

            <div class="mb-2 mt-5 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-600">Account</div>
            <a href="{{ route('profile.edit') }}" class="nav-transition flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:bg-hq-700/40 hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7zM19.4 15a1.7 1.7 0 00.34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0015 19.4"/></svg>
                Profile Settings
            </a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-transition flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-500 hover:bg-hq-700/40 hover:text-red-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Sign Out</button></form>
        </div>
    </aside>

    <div id="ocOverlay" class="fixed inset-0 top-14 z-20 bg-black/50"></div>
    <main class="min-h-screen px-4 pb-12 pt-6 lg:px-8">
        @if(isset($pageTitle))<div class="mb-6"><h1 class="text-xl font-bold tracking-tight text-white">{{ $pageTitle }}</h1><p class="mt-1 text-sm text-gray-500">Bangladesh Police Station Operations</p></div>@endif
        {{ $slot }}
    </main>

    <script>
        const sidebar=document.getElementById('ocSidebar'),overlay=document.getElementById('ocOverlay'),menu=document.getElementById('ocMenuBtn');
        let sidebarOpen=false;
        function toggleOcSidebar(){sidebarOpen=!sidebarOpen;sidebar.classList.toggle('open',sidebarOpen);overlay.classList.toggle('open',sidebarOpen)}
        menu.addEventListener('click',toggleOcSidebar);overlay.addEventListener('click',toggleOcSidebar);
        sidebar.querySelectorAll('a').forEach(link=>link.addEventListener('click',()=>{if(window.innerWidth<1024&&sidebarOpen)toggleOcSidebar()}));
        const userButton=document.getElementById('ocUserButton'),userMenu=document.getElementById('ocUserMenu');
        userButton.addEventListener('click',event=>{event.stopPropagation();userMenu.classList.toggle('hidden')});
        document.addEventListener('click',event=>{if(!userMenu.contains(event.target)&&!userButton.contains(event.target))userMenu.classList.add('hidden')});
    </script>
    @stack('scripts')
</body>
</html>
