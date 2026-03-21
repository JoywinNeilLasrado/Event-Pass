<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FAFAFA] dark:bg-[#111] overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-white/10">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight mb-2">Organizer Verification</h2>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-8 transition-colors">Please perfectly provide your active business details sequentially to verify your identity strictly protecting our community ecosystem.</p>
                    
                    <form action="{{ route('kyc.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="business_details" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 transition-colors">Business Details / Portfolio Summary</label>
                            <textarea id="business_details" name="business_details" rows="4" class="form-input-vercel w-full rounded-md shadow-sm border border-gray-200 dark:border-white/10 bg-transparent text-gray-900 dark:text-white focus:ring-1 focus:ring-black dark:focus:ring-white sm:text-sm transition-colors" required placeholder="Describe your event company prominently..."></textarea>
                        </div>
                        
                        <div>
                            <label for="social_links" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 transition-colors">Social Media Anchor (Optional)</label>
                            <input type="text" id="social_links" name="social_links" class="form-input-vercel w-full rounded-md shadow-sm border border-gray-200 dark:border-white/10 bg-transparent text-gray-900 dark:text-white focus:ring-1 focus:ring-black dark:focus:ring-white sm:text-sm transition-colors" placeholder="https://instagram.com/yourbrand">
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <button type="submit" class="btn-vercel px-6 py-2 shadow-sm font-bold">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
