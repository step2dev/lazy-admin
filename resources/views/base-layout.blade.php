@props([
    'title' => '',
    'action' => '',
    'meta' => '',
    'styles' => '',
    'scripts' => '',
    'noscript' => '',
])
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' | '.config('app.name', 'Laravel'): config('app.name', 'Laravel') }} | Admin
        Panel</title>
    {{ $meta ?? '' }}
    @if($styles)
        {{ $styles }}
    @else
        @vite(config('lazy.admin.styles'))
    @endif
    @livewireStyles

    @if(config('lazy.admin.trendcharts.enabled', true))
        <script
            type="module"
            src="{{ config('lazy.admin.trendcharts.src', 'https://cdn.jsdelivr.net/npm/@weblogin/trendchart-elements@1.1.0/dist/index.js/+esm') }}"
        ></script>
    @endif
</head>
<body class="min-h-screen bg-base-200 font-sans antialiased">
{{ $noscript ?? ''}}
<div class="flex min-h-screen flex-col">
    {{ $slot }}
</div>
@livewireScriptConfig
@if($scripts)
    {{ $scripts }}
@else
    @vite(config('lazy.admin.scripts'))
@endif
<x-lazy-toast/>
</body>
</html>
