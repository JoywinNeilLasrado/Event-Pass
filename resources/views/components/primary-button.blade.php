<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-900/90 dark:bg-white/90 backdrop-blur-md text-white dark:text-gray-900 hover:bg-black dark:hover:bg-white transition-all duration-200 ease-in-out rounded-lg font-medium shadow-[0_4px_16px_0_rgba(31,38,135,0.1)] hover:-translate-y-[1px] active:translate-y-[1px] active:shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black dark:focus:ring-white uppercase tracking-widest text-xs']) }}>
    {{ $slot }}
</button>
