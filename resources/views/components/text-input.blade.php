@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-slate-200 bg-white shadow-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100']) }}>
