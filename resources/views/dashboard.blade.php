<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight transition-colors">
                Welcome, {{ explode(' ', Auth::user()->name)[0] }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.edit') }}" class="btn-vercel-secondary text-sm hidden sm:block">Edit Profile</a>
                <a href="{{ route('events.index') }}" class="btn-vercel text-sm hidden sm:block">Browse Events</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm transition-colors">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg px-4 py-3 text-sm font-semibold shadow-sm transition-colors">{{ session('error') }}</div>
            @endif

            {{-- Connect Stripe Banner --}}
            @if(Auth::user()->is_organizer && !Auth::user()->stripe_onboarding_completed)
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 border border-indigo-200 dark:border-indigo-800/30 rounded-xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-sm transition-colors">
                    <div>
                        <h3 class="text-lg font-extrabold text-indigo-900 dark:text-indigo-100 flex items-center gap-2">
                            <span class="flex h-3 w-3 relative">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                            </span>
                            Action Required: Connect Payouts
                        </h3>
                        <p class="text-indigo-700 dark:text-indigo-300 text-sm mt-1.5 font-medium">To start selling tickets and receiving automated payouts directly to your bank, please connect with Stripe.</p>
                    </div>
                    <a href="{{ route('stripe.connect') }}" class="inline-flex items-center justify-center px-6 py-3 bg-[#635BFF] hover:bg-[#5851E3] text-white font-bold text-sm rounded-lg transition-all shadow hover:shadow-lg whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#635BFF]">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Connect with Stripe
                    </a>
                </div>
            @endif

            {{-- ── ORGANIZER ANALYTICS ── --}}
            <div class="mb-8">
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors mb-4">Organizer Overview</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Revenue -->
                    <div class="card p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center justify-between text-gray-500 dark:text-gray-400">
                            <span class="text-xs font-bold uppercase tracking-widest">Total Revenue</span>
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">${{ number_format($stats['total_revenue'], 2) }}</p>
                        </div>
                    </div>
                    <!-- Total Sales (Attendees) -->
                    <div class="card p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center justify-between text-gray-500 dark:text-gray-400">
                            <span class="text-xs font-bold uppercase tracking-widest">Tickets Sold</span>
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        </div>
                        <div>
                            <p class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $stats['total_attendees'] }}</p>
                        </div>
                    </div>
                    <!-- Page Views -->
                    <div class="card p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center justify-between text-gray-500 dark:text-gray-400">
                            <span class="text-xs font-bold uppercase tracking-widest">Page Views</span>
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $stats['total_views'] }}</p>
                        </div>
                    </div>
                    <!-- Conversion Rate -->
                    <div class="card p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center justify-between text-gray-500 dark:text-gray-400">
                            <span class="text-xs font-bold uppercase tracking-widest">Conversion</span>
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <div>
                            <p class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $conversionRate }}%</p>
                        </div>
                    </div>
                </div>

                {{-- Chart Area --}}
                <div class="mt-6 card p-6">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white tracking-tight mb-4 uppercase">Ticket Sales Over Time</h4>
                    <div class="w-full h-80 relative">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ── MY EVENTS ── --}}
            <div class="card overflow-hidden" x-data="{ searchEvents: '' }">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 transition-colors">
                    <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors whitespace-nowrap">My Events</h3>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto sm:justify-end">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="searchEvents" class="form-input text-sm w-full pl-9 py-2 rounded-full border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-black/20 focus:bg-white dark:focus:bg-black/50 focus:ring-black/20 dark:focus:ring-white/20 transition-all shadow-sm" placeholder="Filter events...">
                        </div>
                        <a href="{{ route('events.create') }}" class="btn-vercel text-sm px-4 py-2 text-center whitespace-nowrap">+ New Event</a>
                    </div>
                </div>

                @if($myEvents->isEmpty())
                    <div class="text-center py-16">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-4 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors">You haven't created any events yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                        @foreach($myEvents as $event)
                            <div class="p-6 transition-colors hover:bg-white/30 dark:hover:bg-black/30 @if($event->deleted_at) opacity-60 grayscale @endif"
                                 x-show="searchEvents === '' || {{ json_encode(strtolower($event->title)) }}.includes(searchEvents.toLowerCase())"
                                 x-transition.opacity.duration.300ms>
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 flex-wrap mb-1">
                                            <a href="{{ $event->deleted_at ? '#' : route('events.show', $event->id) }}"
                                               class="font-bold text-lg text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors tracking-tight">
                                                {{ $event->title }}
                                            </a>
                                            @if($event->deleted_at)
                                                <span class="text-[10px] bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">Deleted</span>
                                            @endif
                                            <span class="text-[10px] bg-white/50 dark:bg-black/50 backdrop-blur-sm border border-white/50 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-md px-2 py-0.5 font-bold uppercase tracking-widest shadow-sm transition-colors">{{ $event->category->name }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-4 text-sm font-medium text-gray-500 dark:text-gray-400 mt-2 transition-colors">
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ $event->date->format('M j, Y') }}</span>
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $event->location }}</span>
                                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg> {{ $event->remaining }} seats left</span>
                                        </div>

                                        {{-- Event Analytics Row --}}
                                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/10 transition-colors">
                                            @php 
                                                $eventRevenue = $event->bookings->sum('amount_paid');
                                                $eventConversion = $event->views > 0 ? round(($event->bookings_count / $event->views) * 100, 1) : 0;
                                                $checkedInCount = $event->bookings->where('is_checked_in', true)->count();
                                            @endphp
                                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                                                <div>
                                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Views</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $event->views }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Conversion</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $eventConversion }}%</p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Tickets Sold</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $event->bookings_count }} <span class="text-xs text-gray-400">/ {{ $event->bookings_count + $event->remaining }}</span></p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Revenue</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format($eventRevenue, 2) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-bold {{ $checkedInCount > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }} uppercase tracking-widest mb-1">Checked In</p>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $checkedInCount }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if(!$event->deleted_at)
                                        <div class="flex sm:flex-col gap-3 flex-shrink-0 mt-4 sm:mt-0 items-stretch">
                                            <a href="{{ route('events.attendees', $event->id) }}"
                                               class="btn-vercel text-xs px-4 py-2 text-center flex-1 sm:flex-none">Manage Attendees</a>
                                            <a href="{{ route('events.export', $event->id) }}"
                                               class="inline-flex items-center justify-center bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 hover:border-black/20 dark:hover:border-white/20 text-gray-900 dark:text-white px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm flex-1 sm:flex-none">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>Export CSV
                                            </a>
                                            <a href="{{ route('promo_codes.index', $event->id) }}"
                                               class="inline-flex items-center justify-center bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm flex-1 sm:flex-none">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>Promo Codes
                                            </a>
                                            <div class="flex gap-2 w-full mt-2">
                                                <a href="{{ route('events.edit', $event->id) }}"
                                                   class="btn-vercel-secondary text-xs px-3 py-1.5 text-center flex-1">Edit</a>
                                                <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                                      onsubmit="return confirm('Soft-delete this event? It can be restored later.')" class="flex-1 flex">
                                                    @csrf @method('DELETE')
                                                    <button class="w-full text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 border border-transparent hover:border-red-200 dark:hover:border-red-900/50 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-colors py-1.5 focus:outline-none">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>



        </div>
    </div>
    
    {{-- Chart.js Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart');
            
            // Checking system dark mode for label colors
            const isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const textColor = isDark ? '#9ca3af' : '#6b7280';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Tickets Sold',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#6366f1', // Indigo-500
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? 'rgba(0,0,0,0.8)' : 'rgba(255,255,255,0.9)',
                            titleColor: isDark ? '#fff' : '#000',
                            bodyColor: isDark ? '#d1d5db' : '#374151',
                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' tickets';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { color: textColor, font: { family: "'Inter', sans-serif", size: 11 } }
                        },
                        y: {
                            grid: { color: gridColor, drawBorder: false },
                            ticks: { 
                                color: textColor, 
                                font: { family: "'Inter', sans-serif", size: 11 },
                                stepSize: 1,
                                beginAtZero: true
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        });
    </script>
</x-app-layout>
