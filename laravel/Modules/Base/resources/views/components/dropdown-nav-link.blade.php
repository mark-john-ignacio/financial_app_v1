@props(['active'])

@php
$classes = ($active ?? false)
    ? 'h-16 inline-flex items-center px-1 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none transition duration-150 ease-in-out'
    : 'h-16 inline-flex items-center px-1 text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 transition duration-150 ease-in-out';

$containerClasses = ($active ?? false)
    ? 'relative h-full border-b-2 border-indigo-400 dark:border-indigo-600'
    : 'relative h-full border-b-2 border-transparent hover:border-gray-300 dark:hover:border-gray-700';
@endphp

<div class="{{ $containerClasses }}" x-data="{ open: false }" @click.away="open = false">
    <button type="button" 
        {{ $attributes->merge(['class' => $classes]) }}
        @click="open = !open">
        {{ $trigger }}
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute left-0 z-50 mt-2 w-48 rounded-md shadow-lg origin-top-left"
         style="display: none;">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
            {{ $content }}
        </div>
    </div>
</div>