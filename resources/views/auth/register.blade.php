<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="form-input-vercel px-4 py-3">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="form-input-vercel px-4 py-3">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="form-input-vercel px-4 py-3">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="form-input-vercel px-4 py-3">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" />
        </div>

        <button type="submit" class="btn-vercel w-full py-3 mt-6">
            Register
        </button>

        <p class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 mt-6">
            Already registered? 
            <a href="{{ route('login') }}" class="text-black dark:text-white font-bold hover:underline">Log in</a>
        </p>
    </form>
</x-guest-layout>
