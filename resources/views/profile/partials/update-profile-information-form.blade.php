<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight transition-colors">
            Profile Information
        </h2>
        <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400 transition-colors">
            Update your account's profile information and email address.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors">
            <x-input-error class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                   class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors">
            <x-input-error class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-3 font-semibold text-gray-800 dark:text-gray-300 transition-colors">
                        Your email address is unverified.
                        <button form="send-verification" class="underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Click here to re-send the verification email.
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400 transition-colors">
                            A new verification link has been sent to your email address.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Bio --}}
        <div>
            <label for="bio" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Bio</label>
            <textarea id="bio" name="bio" rows="3"
                class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#1A1A1A] px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white focus:bg-white dark:focus:bg-[#222] transition-colors">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" :messages="$errors->get('bio')" />
        </div>

        {{-- Profile Picture --}}
        <div>
            <label for="profile_picture" class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 transition-colors">Profile Picture</label>
            @if ($user->profile_picture)
                <div class="mt-2 mb-3">
                    <img src="{{ Storage::url($user->profile_picture) }}" alt="Profile" class="h-20 w-20 rounded-full object-cover border border-gray-200 dark:border-white/10 shadow-sm transition-colors">
                </div>
            @endif
            <input id="profile_picture" name="profile_picture" type="file" accept="image/*"
                class="block w-full text-sm font-medium text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:uppercase file:tracking-widest file:bg-gray-100 dark:file:bg-[#222] file:text-gray-600 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-[#333] hover:file:text-black dark:hover:file:text-white transition-colors cursor-pointer">
            <x-input-error class="mt-2 text-xs font-semibold text-red-500 dark:text-red-400" :messages="$errors->get('profile_picture')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-vercel px-6 py-2.5">Save</button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest transition-colors">Saved.</p>
            @endif
        </div>
    </form>
</section>
