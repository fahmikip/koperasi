<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-xl border border-transparent bg-blue-800 px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white shadow-md shadow-blue-900/15 transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200 disabled:pointer-events-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
