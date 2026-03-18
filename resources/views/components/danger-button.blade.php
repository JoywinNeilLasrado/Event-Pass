<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-600/80 backdrop-blur-md border border-red-500/50 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500/80 active:bg-red-700/80 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
