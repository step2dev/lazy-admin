@php
    $errors = [
        401 => ['Authentication required', 'Sign in to continue to the admin area.'],
        403 => ['Access denied', 'You do not have permission to view this page.'],
        404 => ['Page not found', 'This admin page may have been moved, deleted, or never existed.'],
        419 => ['Session expired', 'Your session has expired. Refresh the page to continue.'],
        429 => ['Too many requests', 'Please wait a moment before trying again.'],
    ];
    [$errorTitle, $errorMessage] = $errors[$status] ?? $errors[404];
@endphp
<x-lazy-layout :title="$errorTitle" :routes="['create' => null, 'show' => null, 'edit' => null, 'destroy' => null]">
    {{-- Keep the central error illustration independent from host Vite builds. --}}
    <style>
        .lazy-admin-error { display: flex; align-items: center; justify-content: center; min-height: min(65vh, 640px); padding: 3rem 1rem 4rem; }
        .lazy-admin-error__inner { width: 100%; max-width: 560px; text-align: center; }
        .lazy-admin-error__icon { width: 72px; height: 72px; margin: 0 auto 1.75rem; display: grid; place-items: center; border: 1px solid var(--color-base-300, currentColor); border-radius: 20px; background: var(--color-base-200, transparent); color: var(--color-primary, #605dff); }
        .lazy-admin-error__icon svg { width: 34px; height: 34px; }
        .lazy-admin-error__code { margin: 0; font-size: clamp(5.5rem, 15vw, 9rem); line-height: .95; font-weight: 800; letter-spacing: -.085em; color: var(--color-primary, #605dff); font-variant-numeric: tabular-nums; }
        .lazy-admin-error__title { margin: 1.5rem 0 .75rem; font-size: clamp(1.5rem, 4vw, 2rem); font-weight: 700; letter-spacing: -.035em; line-height: 1.25; }
        .lazy-admin-error__description { max-width: 420px; margin: 0 auto; color: color-mix(in oklab, currentColor 63%, transparent); font-size: .95rem; line-height: 1.7; }
        .lazy-admin-error__actions { display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: .75rem; margin-top: 2rem; }
        .lazy-admin-error__footnote { margin: 2.75rem auto 0; padding-top: 1rem; border-top: 1px solid var(--color-base-300, #343b49); max-width: 320px; color: color-mix(in oklab, currentColor 48%, transparent); font-size: .75rem; }
        @media (max-width: 640px) { .lazy-admin-error { min-height: 55vh; padding: 2rem 0; } .lazy-admin-error__icon { width: 60px; height: 60px; } }
    </style>
    <section class="lazy-admin-error" aria-labelledby="lazy-admin-error-title">
        <div class="lazy-admin-error__inner">
            <div class="lazy-admin-error__icon" aria-hidden="true">
                @if($status === 404)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5M8.5 10.5h4.5"/></svg>
                @elseif($status === 403 || $status === 401)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 20 6v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6l8-4Z"/><path d="M12 8v5M12 17h.01"/></svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                @endif
            </div>
            <p class="lazy-admin-error__code" aria-hidden="true">{{ $status }}</p>
            <h2 id="lazy-admin-error-title" class="lazy-admin-error__title">{{ $errorTitle }}</h2>
            <p class="lazy-admin-error__description">{{ $errorMessage }}</p>
            <div class="lazy-admin-error__actions">
                <x-lazy-btn :href="$homeUrl" primary :label="__('Back to dashboard')" />
                <x-lazy-btn type="button" outline :label="__('Try again')" onclick="window.location.reload()" />
            </div>
            <p class="lazy-admin-error__footnote">HTTP {{ $status }} · {{ config('app.name', 'Lazy Admin') }}</p>
        </div>
    </section>
</x-lazy-layout>
