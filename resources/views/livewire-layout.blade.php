@props([
    'title' => '',
])

<x-lazy-layout :title="__($title)">
    {{ $slot }}
</x-lazy-layout>
