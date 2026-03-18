@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border border-white/40 dark:border-white/10 bg-white/30 dark:bg-black/30 backdrop-blur-md text-gray-900 dark:text-white shadow-sm focus:border-black/50 dark:focus:border-white/50 focus:ring-black/20 dark:focus:ring-white/20 focus:bg-white/50 dark:focus:bg-black/50 rounded-md transition-all duration-200']) }}>
