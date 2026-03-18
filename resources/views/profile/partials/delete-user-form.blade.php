<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-red-600 dark:text-red-500 tracking-tight transition-colors">
            Delete Account
        </h2>
        <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors">
            Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
        </p>
    </header>

    <button type="button" class="btn-vercel-secondary text-red-600 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 hover:border-red-200 dark:hover:border-red-800 hover:text-red-700 dark:hover:text-red-400 font-semibold px-4 py-2"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Delete Account
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white dark:bg-[#111111]">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight transition-colors">
                Are you sure you want to delete your account?
            </h2>

            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400 mb-6 transition-colors">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <div>
                <label for="password" class="sr-only">Password</label>
                <input id="password" name="password" type="password" placeholder="Password"
                       class="w-3/4 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn-vercel-secondary px-4 py-2" x-on:click="$dispatch('close')">
                    Cancel
                </button>

                <button type="submit" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-[#111] transition-colors">
                    Delete Account
                </button>
            </div>
        </form>
    </x-modal>
</section>
