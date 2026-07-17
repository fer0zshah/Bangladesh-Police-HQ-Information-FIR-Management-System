<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Command' }} | Bangladesh Police</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'Arial', 'sans-serif'] },
                    colors: {
                        hq: { 900:'#0f1923',800:'#1a252f',700:'#243447',600:'#2c3e50',500:'#34495e',400:'#4a6582',300:'#6b8caf' },
                        gold: { 500:'#f1c40f',600:'#d4ac0d' }
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar{width:6px;height:6px}::-webkit-scrollbar-track{background:#1a252f}::-webkit-scrollbar-thumb{background:#34495e;border-radius:3px}
        #commandSidebar{transform:translateX(-100%);transition:transform .25s ease}#commandSidebar.open{transform:translateX(0)}
        #commandOverlay{opacity:0;pointer-events:none;transition:opacity .25s ease}#commandOverlay.open{opacity:1;pointer-events:auto}
        .hq-table tbody tr:hover{background:rgba(52,73,94,.3)}
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-hq-900 font-sans text-gray-200 antialiased">
    <nav class="sticky top-0 z-40 flex h-14 items-center border-b border-hq-700 bg-hq-800 px-4 lg:px-6">
        <div class="flex flex-1 items-center gap-4">
            <button id="commandMenuBtn" class="rounded p-1 text-gray-400 transition hover:text-gold-500" aria-label="Open navigation">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <a href="{{ route('command.dashboard') }}" class="text-base font-bold tracking-tight text-gold-500">
                BD Police <span class="font-medium text-gray-400">Command Portal</span>
            </a>
        </div>
        <div class="relative" id="commandUserDropdown">
            <button type="button" onclick="document.getElementById('commandUserMenu').classList.toggle('hidden')" class="flex items-center gap-2 rounded px-2 py-1 text-gray-400 transition hover:bg-hq-700 hover:text-white">
                <span class="flex h-7 w-7 items-center justify-center rounded-full border border-hq-500 bg-hq-600 text-xs font-semibold">{{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}</span>
                <span class="hidden text-sm font-medium sm:block">{{ auth()->user()->name }}</span>
            </button>
            <div id="commandUserMenu" class="absolute right-0 z-50 mt-2 hidden w-56 rounded-lg border border-hq-700 bg-hq-800 py-1 shadow-xl">
                <div class="border-b border-hq-700 px-4 py-3">
                    <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ auth()->user()->role === 'metro_head' ? 'Police Commissioner' : 'Superintendent of Police' }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-400 hover:bg-hq-700 hover:text-white">Profile settings</a>
                <form method="POST" action="{{ route('logout') }}" class="border-t border-hq-700">@csrf
                    <button class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-hq-700">Sign out</button>
                </form>
            </div>
        </div>
    </nav>

    <aside id="commandSidebar" class="fixed bottom-0 left-0 top-14 z-30 w-60 overflow-y-auto border-r border-hq-700 bg-hq-800">
        <div class="py-4">
            <p class="mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-600">Jurisdiction command</p>
            @foreach ([
                ['route' => 'command.dashboard', 'match' => 'command.dashboard', 'label' => 'Overview', 'icon' => 'M4 6h6v6H4V6zm10 0h6v6h-6V6zM4 16h6v4H4v-4zm10 0h6v4h-6v-4z'],
                ['route' => 'command.stations.index', 'match' => 'command.stations.*', 'label' => 'Stations', 'icon' => 'M19 21V5H5v16m-2 0h18M9 9h1m4 0h1M9 13h1m4 0h1'],
                ['route' => 'command.officers.index', 'match' => 'command.officers.*', 'label' => 'Officers', 'icon' => 'M17 20h5v-2a3 3 0 00-5-2M7 20H2v-2a3 3 0 015-2m10 4H7m10 0v-2a5 5 0 00-10 0v2m8-13a3 3 0 11-6 0 3 3 0 016 0z'],
            ] as $item)
                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 border-r-2 px-4 py-2.5 text-sm transition {{ request()->routeIs($item['match']) ? 'border-gold-500 bg-hq-700/50 text-gold-500' : 'border-transparent text-gray-400 hover:bg-hq-700/40 hover:text-white' }}">
                    <svg class="h-4 w-4 flex-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
            <p class="mb-2 mt-6 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-600">Operations oversight</p>
            @foreach ([
                ['route' => 'command.cases.index', 'match' => 'command.cases.*', 'label' => 'Case FIRs'],
                ['route' => 'command.complaints.index', 'match' => 'command.complaints.*', 'label' => 'Complaints'],
                ['route' => 'command.criminals.index', 'match' => 'command.criminals.*', 'label' => 'Criminals'],
                ['route' => 'command.evidence.index', 'match' => 'command.evidence.*', 'label' => 'Evidence'],
                ['route' => 'command.analytics', 'match' => 'command.analytics', 'label' => 'Analytics'],
            ] as $item)
                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 border-r-2 px-4 py-2.5 text-sm transition {{ request()->routeIs($item['match']) ? 'border-gold-500 bg-hq-700/50 text-gold-500' : 'border-transparent text-gray-400 hover:bg-hq-700/40 hover:text-white' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $item['label'] }}
                </a>
            @endforeach
            <p class="mb-2 mt-6 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-600">Account</p>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:bg-hq-700/40 hover:text-white">Profile settings</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="w-full px-4 py-2.5 text-left text-sm text-gray-500 hover:bg-hq-700/40 hover:text-red-400">Sign out</button>
            </form>
        </div>
    </aside>
    <div id="commandOverlay" class="fixed inset-0 top-14 z-20 bg-black/50"></div>

    <main class="min-h-screen px-4 pb-12 pt-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-xl font-bold tracking-tight text-white">{{ $pageTitle }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ auth()->user()->role === 'metro_head' ? 'Metropolitan Police jurisdiction' : 'District Police jurisdiction' }}</p>
        </div>
        {{ $slot }}
    </main>

    <script>
        const sidebar=document.getElementById('commandSidebar'),overlay=document.getElementById('commandOverlay');
        const toggle=()=>{sidebar.classList.toggle('open');overlay.classList.toggle('open')};
        document.getElementById('commandMenuBtn').addEventListener('click',toggle);
        overlay.addEventListener('click',toggle);
        document.addEventListener('click',e=>{const menu=document.getElementById('commandUserMenu'),wrap=document.getElementById('commandUserDropdown');if(!wrap.contains(e.target))menu.classList.add('hidden')});
    </script>
    @stack('scripts')
</body>
</html>
