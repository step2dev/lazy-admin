@props([
    'header' => '',
    'wrapper' => '',
    'title' => '',
    'action' => '',
    'breadcrumb' => '',
    'footer' => '',
    'meta' => '',
    'styles' => '',
    'scripts' => '',
    'noscript' => '',
])

<x-lazy-base-layout>
    @if($header)
        <header class="navbar bg-base-200">
            {{ $header }}
        </header>
    @else
        <x-lazy-header/>
    @endif
    {{ $slot }}
    @if($footer)
        <footer
            class="footer bg-base-200 items-center p-4">
            {{ $footer }}
        </footer>
    @else
        <x-lazy-footer/>
    @endif
</x-lazy-base-layout>
