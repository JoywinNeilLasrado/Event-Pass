<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Become an Organizer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-black/40 backdrop-blur-xl border border-gray-200 dark:border-white/10 overflow-hidden shadow-sm sm:rounded-3xl p-10 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-transparent pointer-events-none"></div>
                
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4 relative z-10 tracking-tight">Unlock Your Creator Potential</h3>
                <p class="text-gray-600 dark:text-gray-300 text-lg mb-8 max-w-xl mx-auto relative z-10">
                    Host unlimited events, manage attendees, and access professional-grade analytics with a one-time upgrade to an Organizer Account.
                </p>

                <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto relative z-10">
                    
                    <!-- Card 1: Pay-As-You-Go -->
                    <div class="bg-white/50 dark:bg-black/20 backdrop-blur-sm rounded-[2rem] p-8 border border-gray-200/60 dark:border-white/5 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-500/30 transition-all text-left flex flex-col h-full">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Basic Organizer</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Start building your community with no upfront cost.</p>
                        
                        <div class="mb-6">
                            <span class="text-4xl font-black text-gray-900 dark:text-white">Free</span>
                        </div>

                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                <svg class="h-5 w-5 text-indigo-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Access to Organizer Dashboard
                            </li>
                            <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                <svg class="h-5 w-5 text-indigo-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Create Event Drafts
                            </li>
                            <li class="flex items-start text-sm text-indigo-700 dark:text-indigo-400 font-semibold bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded-xl border border-indigo-100 dark:border-indigo-500/20 mt-4 shadow-sm">
                                <svg class="h-5 w-5 text-indigo-500 mr-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Pay ₹{{ $eventFee }} per published event.</span>
                            </li>
                        </ul>

                        <form action="{{ route('upgrade.basic') }}" method="POST" class="mt-auto">
                            @csrf
                            <button type="submit" class="w-full py-4 px-4 font-bold rounded-xl text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:text-indigo-300 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 border border-indigo-200 dark:border-indigo-500/30 transition-colors flex justify-center items-center shadow-sm">
                                Get Started Free
                            </button>
                        </form>
                    </div>

                    <!-- Card 2: Pro -->
                    <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-indigo-600 rounded-[2rem] p-8 border border-white/20 shadow-2xl shadow-indigo-500/20 text-left flex flex-col h-full relative overflow-hidden text-white transform md:-translate-y-4">
                        <!-- Shine effect -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl transform translate-x-12 -translate-y-12 pointer-events-none"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full blur-2xl transform -translate-x-16 translate-y-16 pointer-events-none"></div>
                        
                        <div class="inline-flex max-w-max items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-white/20 text-white mb-4 border border-white/20 shadow-sm backdrop-blur-md">
                            Recommended
                        </div>

                        <h3 class="text-xl font-bold mb-2">Pro Organizer</h3>
                        <p class="text-sm text-indigo-100 mb-6">Unlock true freedom and eliminate publishing fees.</p>
                        
                        <div class="mb-6">
                            <span class="text-4xl font-black">₹{{ $fee }}</span>
                            <span class="text-lg font-medium text-indigo-200">/one-time</span>
                        </div>

                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start text-sm text-white">
                                <svg class="h-5 w-5 text-indigo-200 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Everything in Basic
                            </li>
                            <li class="flex items-start text-sm text-white">
                                <svg class="h-5 w-5 text-indigo-200 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Advanced Admin Dashboard
                            </li>
                            <li class="flex items-start text-sm text-white font-bold bg-white/10 p-3 rounded-xl border border-white/20 mt-4 backdrop-blur-md shadow-sm">
                                <svg class="h-5 w-5 text-green-300 mr-3 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Publish UNLIMITED events for FREE!</span>
                            </li>
                        </ul>

                        <form action="{{ route('upgrade.pro') }}" method="POST" class="mt-auto relative z-10">
                            @csrf
                            <button type="submit" class="w-full py-4 px-4 font-black tracking-wide rounded-xl text-indigo-900 bg-white hover:bg-gray-50 transition-colors flex justify-center items-center shadow-xl">
                                Upgrade to Pro
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
