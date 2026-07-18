<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-xl border border-transparent bg-rose-700 px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white shadow-sm transition hover:bg-rose-600 focus:outline-none focus:ring-4 focus:ring-rose-200']) }}>
    {{ $slot }}
</button>
