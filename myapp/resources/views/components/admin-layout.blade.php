<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle ?? 'HQ Admin' }} | Bangladesh Police HQ</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CDN (replace with your build in production) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'Arial', 'sans-serif'],
                    },
                    colors: {
                        hq: {
                            900: '#0f1923',   /* deepest navy */
                            800: '#1a252f',   /* nav bg */
                            700: '#243447',   /* card bg */
                            600: '#2c3e50',   /* hero bg */
                            500: '#34495e',   /* lighter navy */
                            400: '#4a6582',   /* muted blue-gray */
                            300: '#6b8caf',   /* accent blue-gray */
                            200: '#94b8d9',   /* light accent */
                            100: '#c5d9ed',   /* very light */
                            50:  '#e8f0f8',   /* barely there */
                        },
                        gold: {
                            500: '#f1c40f',
                            600: '#d4ac0d',
                            700: '#b7950b',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #1a252f; }
        ::-webkit-scrollbar-thumb { background: #34495e; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #4a6582; }

        /* Smooth transitions */
        .transition-all-200 { transition: all 0.2s ease; }

        /* Sidebar slide */
        #sidebar {
            transform: translateX(-100%);
            transition: transform 0.25s ease;
        }
        #sidebar.open { transform: translateX(0); }

        /* Overlay */
        #overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        #overlay.open { opacity: 1; pointer-events: auto; }

        /* Table */
        .hq-table th { letter-spacing: 0.03em; }
        .hq-table tbody tr:hover { background-color: rgba(52, 73, 94, 0.3); }

        /* Badge */
        .badge { @apply inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold uppercase tracking-wider; }
    </style>

    @stack('styles')
</head>
<body class="bg-hq-900 text-gray-200 font-sans antialiased min-h-screen">

    <!-- ========== TOP NAVIGATION ========== -->
    <nav class="bg-hq-800 border-b border-hq-700 sticky top-0 z-40 h-14 flex items-center px-4 lg:px-6">
        <div class="flex items-center gap-4 flex-1">
            <!-- Hamburger -->
            <button id="menuBtn" class="text-gray-400 hover:text-gold-500 transition-colors p-1 rounded focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Brand -->
            <a href="{{ route('admin.dashboard') }}" class="text-gold-500 font-bold text-base tracking-tight">
                BD Police <span class="text-gray-400 font-medium">HQ Admin</span>
            </a>
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-3">
            <!-- Notification bell -->
            <button class="text-gray-500 hover:text-gray-300 transition-colors p-1.5 rounded relative">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-0.5 right-0.5 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User dropdown trigger -->
            <div class="relative" id="userDropdown">
                <button onclick="document.getElementById('userMenu').classList.toggle('hidden')" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors py-1 px-2 rounded hover:bg-hq-700">
                    <div class="w-7 h-7 rounded-full bg-hq-600 border border-hq-500 flex items-center justify-center text-xs font-semibold text-gray-300">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <span class="text-sm font-medium hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown -->
                <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-hq-800 border border-hq-700 rounded-lg shadow-xl py-1 z-50">
                    <div class="px-4 py-2 border-b border-hq-700">
                        <p class="text-sm font-semibold text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-hq-700 transition-colors">
                        Profile Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-hq-700 mt-1">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-hq-700 transition-colors">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== SIDEBAR (Collapsible) ========== -->
    <aside id="sidebar" class="fixed top-14 left-0 bottom-0 w-60 bg-hq-800 border-r border-hq-700 z-30 overflow-y-auto">
        <div class="py-4">
            <!-- Section: Main -->
            <div class="px-4 mb-2 text-[10px] font-bold text-gray-600 uppercase tracking-widest">Main</div>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.dashboard') ? 'text-gold-500 bg-hq-700/50 border-r-2 border-gold-500' : 'text-gray-400 hover:text-white hover:bg-hq-700/40' }} transition-all-200">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.stations.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.stations.*') ? 'text-gold-500 bg-hq-700/50 border-r-2 border-gold-500' : 'text-gray-400 hover:text-white hover:bg-hq-700/40' }} transition-all-200">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Stations
            </a>

            <a href="{{ route('admin.hq-members.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.hq-members.*') ? 'text-gold-500 bg-hq-700/50 border-r-2 border-gold-500' : 'text-gray-400 hover:text-white hover:bg-hq-700/40' }} transition-all-200">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Officers
            </a>

            <!-- Section: Operations -->
            <div class="px-4 mt-5 mb-2 text-[10px] font-bold text-gray-600 uppercase tracking-widest">Operations</div>

            <a href="{{ route('admin.cases.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.cases.*') ? 'text-gold-500 bg-hq-700/50 border-r-2 border-gold-500' : 'text-gray-400 hover:text-white hover:bg-hq-700/40' }} transition-all-200">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                Case FIRs
                @if($stats['active_cases'] ?? 0 > 0)
                    <span class="ml-auto bg-amber-500/20 text-amber-400 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stats['active_cases'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.complaints.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.complaints.*') ? 'text-gold-500 bg-hq-700/50 border-r-2 border-gold-500' : 'text-gray-400 hover:text-white hover:bg-hq-700/40' }} transition-all-200">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/>
                </svg>
                Complaints
                @if($stats['pending_complaints'] ?? 0 > 0)
                    <span class="ml-auto bg-red-500/20 text-red-400 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $stats['pending_complaints'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.criminals.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.criminals.*') ? 'text-gold-500 bg-hq-700/50 border-r-2 border-gold-500' : 'text-gray-400 hover:text-white hover:bg-hq-700/40' }} transition-all-200">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.131A8 8 0 008 3.239c-4.828 2.957-7.142 8.81-5.21 14.213"/>
                </svg>
                Criminals
            </a>

            <a href="{{ route('admin.analytics') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm {{ request()->routeIs('admin.analytics') ? 'text-gold-500 bg-hq-700/50 border-r-2 border-gold-500' : 'text-gray-400 hover:text-white hover:bg-hq-700/40' }} transition-all-200">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 16l4-5 4 3 5-7"/></svg>
                Analytics
            </a>

            <!-- Section: System -->
            <div class="px-4 mt-5 mb-2 text-[10px] font-bold text-gray-600 uppercase tracking-widest">System</div>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-hq-700/40 transition-all-200">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-500 hover:text-red-400 hover:bg-hq-700/40 transition-all-200">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- ========== OVERLAY ========== -->
    <div id="overlay" class="fixed inset-0 bg-black/50 z-20 top-14" onclick="toggleSidebar()"></div>

    <!-- ========== MAIN CONTENT ========== -->
    <main id="mainContent" class="pt-6 px-4 lg:px-8 pb-12 min-h-screen transition-all duration-250">
        <!-- Page Header -->
        @if(isset($pageTitle))
            <div class="mb-6">
                <h1 class="text-xl font-bold text-white tracking-tight">{{ $pageTitle }}</h1>
                <p class="text-sm text-gray-500 mt-1">Bangladesh Police Headquarters Administration</p>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- ========== SCRIPTS ========== -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuBtn = document.getElementById('menuBtn');
        const mainContent = document.getElementById('mainContent');
        let sidebarOpen = false;

        function toggleSidebar() {
            sidebarOpen = !sidebarOpen;
            sidebar.classList.toggle('open', sidebarOpen);
            overlay.classList.toggle('open', sidebarOpen);
        }

        menuBtn.addEventListener('click', toggleSidebar);

        // Close sidebar when clicking a link (mobile)
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) toggleSidebar();
            });
        });

        // Close user dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userMenu');
            const trigger = document.querySelector('#userDropdown button');
            if (!dropdown.contains(e.target) && !trigger.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
