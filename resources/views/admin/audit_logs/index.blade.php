<x-admin.layout>
    <x-slot name="title">Audit Activity Logs</x-slot>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex items-center justify-between transition-colors">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">System Activity Stream</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 transition-colors">A chronological ledger tracking critical administrative modifications over the entire platform.</p>
            </div>
            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-[#1A1A1A] rounded-full transition-colors">{{ $logs->total() }} records</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10 transition-colors">
                <thead class="bg-gray-50 dark:bg-[#111] transition-colors">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Administrator Actor</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Action Triggered</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Target Element & Context</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#0A0A0A] divide-y divide-gray-100 dark:divide-white/5 transition-colors">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#111] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-black dark:bg-white text-white dark:text-black flex items-center justify-center text-xs font-bold uppercase shadow-sm flex-shrink-0 transition-colors">
                                        {{ substr($log->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white transition-colors">{{ $log->user->name ?? 'System/Deleted User' }}</div>
                                        <div class="text-[10px] text-gray-500 font-mono tracking-widest">UID: {{ $log->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $actionColors = [
                                        'approved_kyc' => 'text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/30 border border-green-200 dark:border-green-800/50',
                                        'rejected_kyc' => 'text-red-700 bg-red-100 dark:text-red-300 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50',
                                        'restored_event' => 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800/50',
                                        'soft_deleted_event' => 'text-orange-700 bg-orange-100 dark:text-orange-300 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-800/50',
                                        'force_deleted_event' => 'text-red-800 bg-red-100 dark:text-red-300 dark:bg-red-900/30 border border-red-200 dark:border-red-900/50',
                                        'granted_admin' => 'text-indigo-700 bg-indigo-100 dark:text-indigo-300 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800/50',
                                        'revoked_admin' => 'text-pink-700 bg-pink-100 dark:text-pink-300 dark:bg-pink-900/30 border border-pink-200 dark:border-pink-800/50',
                                        'deleted_user' => 'text-red-800 bg-red-100 dark:text-red-300 dark:bg-red-900/30 border border-red-200 dark:border-red-900/50',
                                        'updated_global_settings' => 'text-gray-800 bg-gray-100 dark:text-gray-300 dark:bg-white/10 border border-gray-200 dark:border-white/20',
                                    ];
                                    $colorClass = $actionColors[$log->action] ?? 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-white/10 border border-gray-200 dark:border-white/10';
                                @endphp
                                <span class="inline-flex px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider rounded-md transition-colors {{ $colorClass }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-200 font-medium transition-colors">
                                    {{ class_basename($log->model_type) ?: 'Platform Registry' }} @if($log->model_id) <span class="text-gray-400 text-xs ml-1">#{{ $log->model_id }}</span> @endif
                                </div>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($log->details ?? [] as $key => $value)
                                        @if(!is_array($value))
                                            <span class="inline-flex text-[10px] items-center px-1.5 py-0.5 rounded bg-gray-50 dark:bg-[#1A1A1A] text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-white/10 font-mono transition-colors">
                                                <span class="text-gray-400 dark:text-gray-500 mr-1">{{ $key }}:</span> {{ Str::limit((string)$value, 30) }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors">
                                {{ $log->created_at->diffForHumans() }}
                                <div class="text-[10px] uppercase font-bold tracking-widest mt-0.5">{{ $log->created_at->format('M j, Y h:i A') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors">
                                No administrative activity logs recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 transition-colors">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-admin.layout>
