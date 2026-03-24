<x-admin.layout>
    <x-slot name="title">KYC Approvals Queue</x-slot>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-white/10 flex flex-col sm:flex-row justify-between sm:items-center gap-4 transition-colors">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-gray-900 dark:text-white tracking-tight transition-colors">Organizer Applications</h3>
                <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-[#222] border border-gray-200 dark:border-white/10 px-2 py-0.5 rounded-md shadow-sm transition-colors">{{ $users->total() }}</span>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.kyc.index', ['status' => 'pending']) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ $status === 'pending' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Pending</a>
                <a href="{{ route('admin.kyc.index', ['status' => 'approved']) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ $status === 'approved' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Approved</a>
                <a href="{{ route('admin.kyc.index', ['status' => 'rejected']) }}" class="text-xs px-3 py-1.5 rounded-md font-semibold transition-colors {{ $status === 'rejected' ? 'bg-[#111] dark:bg-white text-white dark:text-black' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10' }}">Rejected</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-white/10 text-sm transition-colors">
                <thead class="bg-[#FAFAFA] dark:bg-[#111] transition-colors">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Organizer Info</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Business Details</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Social Links</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/5 transition-colors">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 transition-colors">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300 text-xs font-medium transition-colors max-w-sm whitespace-normal break-words leading-relaxed">{{ $user->business_details }}</td>
                            <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300 font-medium transition-colors">
                                @if($user->social_links)
                                    <a href="{{ $user->social_links }}" target="_blank" class="text-blue-500 hover:underline inline-flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Link
                                    </a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if($status === 'pending')
                                        <form action="{{ route('admin.kyc.approve', $user) }}" method="POST" onsubmit="return confirm('Approve this firm as an Organizer?')">
                                            @csrf
                                            <button class="text-[11px] font-black text-green-600 dark:text-green-500 uppercase tracking-widest focus:outline-none hover:opacity-80 transition-opacity">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.kyc.reject', $user) }}" method="POST" onsubmit="return confirm('Reject and lock out this application?')">
                                            @csrf
                                            <button class="text-[11px] font-black text-red-600 dark:text-red-500 uppercase tracking-widest focus:outline-none hover:opacity-80 transition-opacity">Reject</button>
                                        </form>
                                    @elseif($status === 'approved')
                                        <form action="{{ route('admin.kyc.reject', $user) }}" method="POST" onsubmit="return confirm('Revoke Organizer access and downgrade to standard user?')">
                                            @csrf
                                            <button class="text-[11px] font-black text-red-600 dark:text-red-500 uppercase tracking-widest focus:outline-none hover:opacity-80 transition-opacity">Revoke Organizer</button>
                                        </form>
                                    @elseif($status === 'rejected')
                                        <form action="{{ route('admin.kyc.approve', $user) }}" method="POST" onsubmit="return confirm('Approve this firm as an Organizer?')">
                                            @csrf
                                            <button class="text-[11px] font-black text-green-600 dark:text-green-500 uppercase tracking-widest focus:outline-none hover:opacity-80 transition-opacity">Re-Approve</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 font-medium transition-colors">No {{ $status }} organizer applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 transition-colors">{{ $users->links() }}</div>
        @endif
    </div>
</x-admin.layout>
