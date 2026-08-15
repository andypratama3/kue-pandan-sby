@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand text-start text-base font-medium text-brand-deep bg-brand-light/20 focus:outline-none focus:text-brand-deep focus:bg-brand-light/30 focus:border-brand transition duration-150 ease-in-out dark:text-brand-light dark:bg-brand-deep/40 dark:focus:text-brand-light dark:focus:bg-brand-deep/50'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out dark:text-gray-300 dark:hover:text-white dark:hover:bg-slate-800 dark:hover:border-gray-600';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
