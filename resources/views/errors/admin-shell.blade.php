@php
    $titles = [
        401 => ['Authentication required', 'Sign in to continue to the admin area.'],
        403 => ['Access denied', 'You do not have permission to view this page.'],
        404 => ['Page not found', 'The requested admin page does not exist or may have moved.'],
        419 => ['Session expired', 'Refresh the page and try again.'],
        429 => ['Too many requests', 'Please wait a moment before trying again.'],
    ];
    [$errorTitle, $errorMessage] = $titles[$status] ?? $titles[404];
@endphp
<x-lazy-layout :title="$errorTitle" :routes="['create' => null, 'show' => null, 'edit' => null, 'destroy' => null]">
    <div class="mx-auto flex min-h-[calc(100dvh-15rem)] max-w-4xl items-center justify-center py-8 sm:py-12">
        <section class="w-full overflow-hidden rounded-xl border border-base-300 bg-base-100 shadow-sm" aria-labelledby="error-title">
            <div class="flex items-center gap-3 border-b border-base-300 bg-base-200/60 px-6 py-4 sm:px-8">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-base-300 bg-base-100 text-primary">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 9v4m0 4h.01M10.3 3.7 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-base-content">{{ $errorTitle }}</p>
                    <p class="text-xs text-base-content/60">Admin panel / HTTP {{ $status }}</p>
                </div>
            </div>
            <div class="px-6 py-12 text-center sm:px-12 sm:py-16">
                <div class="text-7xl font-extrabold tracking-tight text-primary sm:text-8xl">{{ $status }}</div>
                <h1 id="error-title" class="mt-5 text-2xl font-bold tracking-tight text-base-content sm:text-3xl">{{ $errorTitle }}</h1>
                <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-base-content/65 sm:text-base">{{ $errorMessage }}</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <x-lazy-btn :href="$homeUrl" primary :label="__('Back to dashboard')" />
                    <x-lazy-btn type="button" outline :label="__('Try again')" onclick="window.location.reload()" />
                </div>
            </div>
        </section>
    </div>
</x-lazy-layout>
