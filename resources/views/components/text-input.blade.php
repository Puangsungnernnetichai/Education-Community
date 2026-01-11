@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-md border border-slate-200 bg-white text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100']) !!}>
