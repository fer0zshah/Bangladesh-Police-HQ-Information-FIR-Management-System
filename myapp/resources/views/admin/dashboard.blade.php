<x-admin-layout pageTitle="Overview">
    <div class="space-y-6 max-w-[1440px]">

        <!-- ========== STAT CARDS GRID ========== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

            <!-- Total Stations -->
            <div class="bg-hq-800 border border-hq-700 rounded-lg p-5 relative overflow-hidden group hover:border-hq-500 transition-all-200">
                <div class="absolute top-0 right-0 w-16 h-16 bg-sky-500/5 rounded-bl-full"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Stations</p>
                        <p class="text-2xl font-bold text-white mt-1.5">{{ number_format($stats['total_stations']) }}</p>
                    </div>
                    <div class="w-8 h-8 rounded bg-sky-500/10 text-sky-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-gray-500">
                    <span class="text-emerald-400 font-medium">Active</span> across districts
                </div>
            </div>

            <!-- Total Officers -->
            <div class="bg-hq-800 border border-hq-700 rounded-lg p-5 relative overflow-hidden group hover:border-hq-500 transition-all-200">
                <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-500/5 rounded-bl-full"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Officers</p>
                        <p class="text-2xl font-bold text-white mt-1.5">{{ number_format($stats['total_officers']) }}</p>
                    </div>
                    <div class="w-8 h-8 rounded bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-gray-500">
                    <span class="text-indigo-400 font-medium">HQ Registered</span> personnel
                </div>
            </div>

            <!-- Active Cases -->
            <div class="bg-hq-800 border border-hq-700 rounded-lg p-5 relative overflow-hidden group hover:border-hq-500 transition-all-200">
                <div class="absolute top-0 right-0 w-16 h-16 bg-amber-500/5 rounded-bl-full"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Active Cases</p>
                        <p class="text-2xl font-bold text-white mt-1.5">{{ number_format($stats['active_cases']) }}</p>
                    </div>
                    <div class="w-8 h-8 rounded bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-gray-500">
                    <span class="text-amber-400 font-medium">Under Investigation</span>
                </div>
            </div>

            <!-- Pending Complaints -->
            <div class="bg-hq-800 border border-hq-700 rounded-lg p-5 relative overflow-hidden group hover:border-hq-500 transition-all-200">
                <div class="absolute top-0 right-0 w-16 h-16 bg-yellow-500/5 rounded-bl-full"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Pending</p>
                        <p class="text-2xl font-bold text-white mt-1.5">{{ number_format($stats['pending_complaints']) }}</p>
                    </div>
                    <div class="w-8 h-8 rounded bg-yellow-500/10 text-yellow-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-gray-500">
                    <span class="text-yellow-400 font-medium">Awaiting Review</span>
                </div>
            </div>

            <!-- Criminal Registry -->
            <div class="bg-hq-800 border border-hq-700 rounded-lg p-5 relative overflow-hidden group hover:border-hq-500 transition-all-200">
                <div class="absolute top-0 right-0 w-16 h-16 bg-rose-500/5 rounded-bl-full"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Criminals</p>
                        <p class="text-2xl font-bold text-white mt-1.5">{{ number_format($stats['total_criminals']) }}</p>
                    </div>
                    <div class="w-8 h-8 rounded bg-rose-500/10 text-rose-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-gray-500">
                    <span class="text-rose-400 font-medium">Identified</span> profiles
                </div>
            </div>

            <!-- Closed This Month -->
            <div class="bg-hq-800 border border-hq-700 rounded-lg p-5 relative overflow-hidden group hover:border-hq-500 transition-all-200">
                <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-500/5 rounded-bl-full"></div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Closed (Mo)</p>
                        <p class="text-2xl font-bold text-white mt-1.5">{{ number_format($stats['closed_this_month']) }}</p>
                    </div>
                    <div class="w-8 h-8 rounded bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-gray-500">
                    <span class="text-emerald-400 font-medium">Resolution</span> this month
                </div>
            </div>

        </div>

        <!-- ========== DATA TABLES GRID ========== -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- Recent FIRs -->
            <div class="bg-hq-800 border border-hq-700 rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="px-5 py-4 border-b border-hq-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded bg-hq-700 text-gray-400 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-white">Recent FIRs</h3>
                            <p class="text-[11px] text-gray-600">Latest case filings</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.cases.index') }}" class="text-[11px] font-medium text-hq-300 hover:text-gold-500 transition-colors">
                        View All &rarr;
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    @if ($recentCases->isEmpty())
                        <div class="py-10 text-center text-gray-600 text-sm">
                            <svg class="w-8 h-8 mx-auto text-hq-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            No recent cases found.
                        </div>
                    @else
                        <table class="w-full text-left hq-table">
                            <thead>
                                <tr class="bg-hq-900/60 text-gray-500 text-[10px] font-bold uppercase tracking-widest border-b border-hq-700">
                                    <th class="py-3 px-5">Case Title</th>
                                    <th class="py-3 px-4">Station</th>
                                    <th class="py-3 px-4 hidden md:table-cell">Officer</th>
                                    <th class="py-3 px-4 hidden sm:table-cell">Filed</th>
                                    <th class="py-3 px-5 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-hq-700/60 text-[13px]">
                                @foreach ($recentCases as $case)
                                    <tr class="text-gray-400 hover:text-gray-200 transition-colors">
                                        <td class="py-3 px-5">
                                            <span class="font-medium text-gray-300 truncate max-w-[160px] block" title="{{ $case->case_title }}">
                                                {{ $case->case_title }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-500 truncate max-w-[120px]">
                                            {{ $case->station->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-gray-500 truncate max-w-[120px] hidden md:table-cell">
                                            {{ $case->officer->name ?? 'Unassigned' }}
                                        </td>
                                        <td class="py-3 px-4 text-gray-600 whitespace-nowrap hidden sm:table-cell">
                                            {{ \Carbon\Carbon::parse($case->date_filed)->format('d M Y') }}
                                        </td>
                                        <td class="py-3 px-5 text-right">
                                            @php
                                                $statusClass = match (strtolower($case->status)) {
                                                    'closed' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                                    'under investigation', 'active' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                                    default => 'bg-hq-700 text-gray-400 border border-hq-600',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ $case->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- Recent Complaints -->
            <div class="bg-hq-800 border border-hq-700 rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="px-5 py-4 border-b border-hq-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded bg-hq-700 text-gray-400 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-white">Recent Complaints</h3>
                            <p class="text-[11px] text-gray-600">Citizen submissions</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.complaints.index') }}" class="text-[11px] font-medium text-hq-300 hover:text-gold-500 transition-colors">
                        View All &rarr;
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    @if ($recentComplaints->isEmpty())
                        <div class="py-10 text-center text-gray-600 text-sm">
                            <svg class="w-8 h-8 mx-auto text-hq-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            No recent complaints found.
                        </div>
                    @else
                        <table class="w-full text-left hq-table">
                            <thead>
                                <tr class="bg-hq-900/60 text-gray-500 text-[10px] font-bold uppercase tracking-widest border-b border-hq-700">
                                    <th class="py-3 px-5">Complainant</th>
                                    <th class="py-3 px-4">Station</th>
                                    <th class="py-3 px-4 hidden sm:table-cell">Submitted</th>
                                    <th class="py-3 px-5 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-hq-700/60 text-[13px]">
                                @foreach ($recentComplaints as $complaint)
                                    <tr class="text-gray-400 hover:text-gray-200 transition-colors">
                                        <td class="py-3 px-5">
                                            <span class="font-medium text-gray-300 truncate max-w-[160px] block">
                                                {{ $complaint->complainant_name }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-500 truncate max-w-[120px]">
                                            {{ $complaint->station->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-gray-600 whitespace-nowrap hidden sm:table-cell">
                                            {{ \Carbon\Carbon::parse($complaint->submitted_date)->format('d M Y') }}
                                        </td>
                                        <td class="py-3 px-5 text-right">
                                            @php
                                                $statusClass = match (strtolower($complaint->status)) {
                                                    'approved', 'resolved' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                                    'pending' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                                                    'rejected' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                                                    default => 'bg-hq-700 text-gray-400 border border-hq-600',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ $complaint->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
