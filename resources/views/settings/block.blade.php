@props([
    'type' => 'text',
])
@switch($type)
    @case('text')
        <x-lazy-input {{ $attributes }} />
        @break
    @case('textarea')
        <x-dynamic-component :component="'lazy-'.$type" {{ $attributes }} />
        @break
    @default
        <x-lazy-input {{ $attributes }} />
        @break
@endswitch

