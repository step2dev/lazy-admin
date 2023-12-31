@props([
    'header' => '',
    'wrapper' => '',
    'title' => '',
    'action' => '',
    'breadcrumb' => '',
    'footer' => '',
])

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} | {{ config('app.name', 'Laravel') }}</title>

    {{ $styles ?? '' }}
</head>
<body class="min-h-screen bg-gray-100 font-sans antialiased">
<div class="flex flex-col">
    @if ($wrapper)
        {{ $wrapper }}
    @else
        @if($header)
            <header class="navbar bg-base-200">
                {{ $header }}
            </header>
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
    @endif
</div>
{{ $scripts ?? '' }}
</body>
</html>
