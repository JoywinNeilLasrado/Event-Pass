<x-admin.layout>
    <x-slot name="title">System Health &amp; Queues</x-slot>

    <!-- Global Node Counters -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 mt-2">
        <div class="card p-6 flex flex-col justify-between border-l-4 border-l-blue-500 border border-gray-100 dark:border-white/10">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-gray-500 dark:text-gray-400 transition-colors">Active Queue</p>
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                    </span>
                </div>
                <h3 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight transition-colors">{{ count($pendingJobs) }}</h3>
            </div>
            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400 font-medium">
                Pending payloads executing chronologically via background listeners.
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between bg-red-50 dark:bg-red-900/10 border-l-4 border-l-red-500 border border-red-100 dark:border-red-800/30">
            <div>
                <p class="text-[10px] uppercase font-bold tracking-widest text-red-600 dark:text-red-500 mb-1 transition-colors">Dead Letter Errors</p>
                <h3 class="text-3xl font-extrabold text-red-600 dark:text-red-500 tracking-tight transition-colors">{{ count($failedJobs) }}</h3>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-xs text-red-600/70 dark:text-red-500/70 font-medium">Permanently halted processing executions.</span>
                @if(count($failedJobs) > 0)
                    <form action="{{ route('admin.system.flush') }}" method="POST" onsubmit="return confirm('Are you strictly sure you wish to explicitly flush all exceptions permanently without execution?')">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-red-700 bg-red-100 dark:text-red-400 dark:bg-red-900/50 hover:bg-red-200 dark:hover:bg-red-900 px-3 py-1.5 rounded transition">Flush Cache</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Stream Queue -->
    <div class="card overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex items-center justify-between transition-colors bg-blue-50/50 dark:bg-[#111] backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">Pending In-Flight Workers</h3>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10 transition-colors">
                <thead class="bg-gray-50 dark:bg-[#111] transition-colors">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">ID</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Queue Connection Architecture</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Retries</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Created At</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#0A0A0A] divide-y divide-gray-100 dark:divide-white/5 transition-colors">
                    @forelse($pendingJobs as $job)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#111] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-400">#{{ $job->id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-gray-200 transition-colors">{{ $job->queue }}</div>
                                @php
                                    $payload = json_decode($job->payload);
                                @endphp
                                <div class="text-[10px] text-gray-500 font-mono mt-1 w-96 truncate">{{ $payload->displayName ?? 'Generic Command' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900 dark:text-white">{{ $job->attempts }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::createFromTimestamp($job->created_at)->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors">
                                Active node queues are zeroed. Wait for a background listener intercept to fire organically.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dead Letter Queue -->
    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex items-center justify-between transition-colors bg-red-50/50 dark:bg-red-900/10 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="font-bold text-red-900 dark:text-red-400 tracking-tight transition-colors">Exception Dump (Failed Logs)</h3>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10 transition-colors table-fixed">
                <thead class="bg-gray-50 dark:bg-[#111] transition-colors">
                    <tr>
                        <th scope="col" class="w-16 px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">ID</th>
                        <th scope="col" class="w-1/2 px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Exception Stack Trace</th>
                        <th scope="col" class="w-1/4 px-6 py-3.5 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Timestamp</th>
                        <th scope="col" class="w-32 px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Manual Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#0A0A0A] divide-y divide-gray-100 dark:divide-white/5 transition-colors">
                    @forelse($failedJobs as $job)
                        <tr class="hover:bg-red-50/30 dark:hover:bg-red-900/10 transition-colors">
                            <td class="px-6 py-4 text-sm font-mono text-gray-400 align-top pt-5">#{{ $job->id }}</td>
                            <td class="px-6 py-4 align-top overflow-hidden">
                                @php
                                    $payload = json_decode($job->payload);
                                @endphp
                                <div class="text-sm font-bold text-gray-900 dark:text-gray-200 mb-1">{{ $payload->displayName ?? 'Unknown Exception Entity' }}</div>
                                <div class="text-[10px] text-red-500 font-mono bg-red-50 dark:bg-black/50 border border-red-100 dark:border-red-900/30 p-2 rounded w-full overflow-x-auto whitespace-pre">
{{ explode("\n", $job->exception)[0] }}
</div>
                            </td>
                            <td class="px-6 py-4 text-center align-top pt-5 text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right align-top pt-4">
                                <div class="flex flex-col items-end gap-2">
                                    <form action="{{ route('admin.system.retry', $job->uuid) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-white/20 shadow-sm text-xs font-bold rounded text-gray-700 dark:text-gray-200 bg-white dark:bg-[#1A1A1A] hover:bg-gray-50 dark:hover:bg-black w-24 justify-center transition">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Retry Job
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.system.delete', $job->uuid) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 border border-transparent shadow-sm text-xs font-bold rounded text-red-700 bg-red-100 hover:bg-red-200 dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-900/60 w-24 justify-center transition">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Discard Drop
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">Crystal Clear Architecture</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Zero dead letters. Exception logs are functionally clean indefinitely.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin.layout>
