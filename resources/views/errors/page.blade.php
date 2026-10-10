{{-- Only authenticated 403/404 responses may render the full admin shell. --}}
@if(! $useAdminLayout)
@php
    $pages = [
        401 => ['title' => 'Authentication required', 'text' => 'Sign in to continue to the admin area.', 'icon' => 'lock'],
        403 => ['title' => 'Access denied', 'text' => 'You do not have permission to view this page.', 'icon' => 'shield'],
        404 => ['title' => 'Page not found', 'text' => 'This admin page may have moved, or the address may be incorrect.', 'icon' => 'search'],
        419 => ['title' => 'Session expired', 'text' => 'Your session has expired. Refresh the page and try again.', 'icon' => 'clock'],
        429 => ['title' => 'Too many requests', 'text' => 'You have made too many requests. Please try again shortly.', 'icon' => 'clock'],
        500 => ['title' => 'Something went wrong', 'text' => 'An unexpected error occurred. Please try again later.', 'icon' => 'alert'],
        503 => ['title' => 'Temporarily unavailable', 'text' => 'The admin area is temporarily unavailable. Please try again soon.', 'icon' => 'alert'],
    ];
    $page = $pages[$status] ?? $pages[500];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $status }} · {{ $page['title'] }} · {{ config('app.name', 'Lazy Admin') }}</title>
    <style>
        /* Self-contained DaisyUI-inspired palette for error handling without assets. */
        :root { color-scheme: light; font-family: ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;
            --base-100:#fff;--base-200:#f5f6f9;--base-300:#e3e6ed;--content:#222737;--muted:#687385;--primary:#4f46e5;--primary-content:#fff; }
        @media(prefers-color-scheme:dark){:root{color-scheme:dark;--base-100:#1a2030;--base-200:#121827;--base-300:#30394b;--content:#eef0f6;--muted:#a6adbd;--primary:#a5b4fc;--primary-content:#161b30}}
        *{box-sizing:border-box}body{min-height:100vh;min-height:100dvh;margin:0;display:flex;flex-direction:column;background:var(--base-200);color:var(--content)}
        .topbar{height:4rem;display:flex;align-items:center;gap:.7rem;padding:0 clamp(1rem,3vw,2rem);border-bottom:1px solid var(--base-300);font-size:.875rem;font-weight:600}
        .brand{display:grid;place-items:center;width:2.3rem;height:2.3rem;border-radius:.65rem;background:var(--primary);color:var(--primary-content)}
        .brand svg{width:1.4rem;height:1.4rem}.subtle{color:var(--muted);font-weight:400}
        main{flex:1;display:grid;place-items:center;padding:2rem 1rem}
        .card{width:min(100%,37rem);padding:clamp(1.5rem,6vw,3.25rem);background:var(--base-100);border:1px solid var(--base-300);border-radius:1rem;text-align:center;box-shadow:0 8px 30px #00000008}
        .symbol{width:4.5rem;height:4.5rem;margin:0 auto 1.5rem;border-radius:1rem;display:grid;place-items:center;background:var(--base-200);border:1px solid var(--base-300);color:var(--primary)}
        .symbol svg{width:2rem;height:2rem;stroke:currentColor;fill:none;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
        .status{font-size:clamp(4.25rem,14vw,6rem);font-weight:800;letter-spacing:-.075em;line-height:1;color:var(--primary)}
        h1{font-size:clamp(1.4rem,4vw,1.875rem);letter-spacing:-.04em;margin:1.25rem 0 .75rem}
        p{color:var(--muted);line-height:1.65;max-width:26rem;margin:0 auto 1.75rem;font-size:.925rem}
        .actions{display:flex;flex-wrap:wrap;justify-content:center;gap:.75rem}
        .btn{appearance:none;display:inline-flex;align-items:center;justify-content:center;min-height:2.75rem;padding:.65rem 1.1rem;border:1px solid var(--base-300);border-radius:.5rem;background:var(--base-100);color:var(--content);text-decoration:none;font:inherit;font-size:.875rem;font-weight:600;cursor:pointer}
        .btn:hover{background:var(--base-200)}.btn-primary{background:var(--primary);border-color:var(--primary);color:var(--primary-content)}.btn-primary:hover{filter:brightness(.92);background:var(--primary)}
        .btn:focus-visible{outline:2px solid var(--primary);outline-offset:3px}
        footer{text-align:center;color:var(--muted);padding:1rem;font-size:.75rem}
    </style>
</head>
<body>
<header class="topbar"><div class="brand" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 21 7v10l-9 5-9-5V7l9-5Z"/><path d="m8 15 4-8 4 8M9.5 12h5"/></svg></div><span>{{ config('app.name', 'Lazy Admin') }}</span><span class="subtle">/ Admin</span></header>
<main><section class="card" aria-labelledby="error-title">
        <div class="symbol" aria-hidden="true">
            @if($page['icon'] === 'search')
                <svg viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5M8 9h.01"/></svg>
            @elseif($page['icon'] === 'lock')
                <svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
            @elseif($page['icon'] === 'shield')
                <svg viewBox="0 0 24 24"><path d="M12 2 20 6v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6l8-4Z"/><path d="m9 12 2 2 4-4"/></svg>
            @elseif($page['icon'] === 'clock')
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            @else
                <svg viewBox="0 0 24 24"><path d="M12 3 2 21h20L12 3Z"/><path d="M12 9v5M12 18h.01"/></svg>
            @endif
        </div>
        <div class="status">{{ $status }}</div>
        <h1 id="error-title">{{ $page['title'] }}</h1>
        <p>{{ $page['text'] }}</p>
        <div class="actions">
            <a class="btn btn-primary" href="{{ $homeUrl }}">Back to dashboard</a>
            <button class="btn" type="button" onclick="window.location.reload()">Try again</button>
        </div>
    </section>
</main>
<footer>Lazy Admin · {{ $status }}</footer>
</body>
</html>

@else
    @include('lazy::errors.admin-shell')
@endif
