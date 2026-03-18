<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="form-input-vercel px-4 py-3">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="form-input-vercel px-4 py-3">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border border-white/40 dark:border-white/20 bg-white/30 dark:bg-black/30 backdrop-blur-md text-gray-900 dark:text-white shadow-sm focus:ring-black/20 dark:focus:ring-white/20 cursor-pointer transition-colors">
                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-vercel w-full py-3 mt-6">
            Log in
        </button>
        
        <p class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 mt-6">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-black dark:text-white font-bold hover:underline">Register</a>
        </p>
    </form>
</x-guest-layout>
