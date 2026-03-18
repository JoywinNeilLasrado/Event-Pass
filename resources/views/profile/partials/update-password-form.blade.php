<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight transition-colors">
            Update Password
        </h2>
        <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors">
            Ensure your account is using a long, random password to stay secure.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Current Password</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                   class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">New Password</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                   class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Confirm Password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-vercel px-6 py-2.5">Save</button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Saved.</p>
            @endif
        </div>
    </form>
</section>
