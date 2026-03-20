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

        <!-- Organizer Status -->
        <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-xl border border-gray-200/60 dark:border-white/10 bg-white/50 dark:bg-white/5 transition-colors">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Organizer Account</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                   @if(!$user->is_organizer)
                      Host your own events, scan tickets, and access analytics.
                   @elseif($user->has_unlimited_events)
                      You are currently on the Pro Plan (Unlimited Free Events).
                   @else
                      You are on the Basic Pay-As-You-Go Plan.
                   @endif
                </p>
            </div>
            
            <div class="flex items-center gap-4">
                @if (!$user->is_organizer)
                    <a href="{{ route('upgrade.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-xs font-bold rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:text-indigo-300 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Upgrade Now
                    </a>
                @else
                    @if ($user->has_unlimited_events)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 shadow-sm border border-green-200/50 dark:border-green-800/50">
                            <svg class="mr-1.5 h-3 w-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                            Pro Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 shadow-sm border border-indigo-200/50 dark:border-indigo-800/50">
                            <svg class="mr-1.5 h-3 w-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                            Basic Active
                        </span>
                        <a href="{{ route('upgrade.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors underline decoration-2 underline-offset-4">
                            Go Pro
                        </a>
                    @endif
                    
                    <label class="flex items-center gap-2 cursor-pointer ml-2 group">
                        <input type="checkbox" name="cancel_organizer" value="1" class="w-4 h-4 rounded border-red-300 dark:border-red-500/30 text-red-600 focus:ring-red-500/50 dark:bg-black/50 transition-colors">
                        <span class="text-xs font-bold text-red-600 group-hover:text-red-800 dark:text-red-400 dark:group-hover:text-red-300 transition-colors">Cancel Plan on Save</span>
                    </label>
                @endif
            </div>
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
