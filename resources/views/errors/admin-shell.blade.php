@php
    $pages = [
        403 => [
            'title' => 'Access denied',
            'eyebrow' => 'RESTRICTED ACCESS',
            'message' => 'You do not have permission to access this section.',
            'hint' => 'If you need access, contact an administrator.',
        ],
        404 => [
            'title' => 'Page not found',
            'eyebrow' => 'PAGE UNAVAILABLE',
            'message' => 'We could not find the admin page you requested.',
            'hint' => 'The address may be incorrect, or the page may have been moved.',
        ],
    ];
    $page = $pages[$status] ?? $pages[404];
@endphp
<x-lazy-layout :title="$page['title']" :routes="['create' => null, 'show' => null, 'edit' => null, 'destroy' => null]">
    <section class="mx-auto w-full max-w-5xl py-6 sm:py-12" aria-labelledby="admin-error-title">
        <div class="grid overflow-hidden rounded-xl border border-base-300 bg-base-100 shadow-sm lg:grid-cols-[minmax(0,1fr)_minmax(0,0.8fr)]">
            <div class="flex flex-col justify-center p-7 sm:p-10 lg:p-14">
                <div class="mb-7 flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        @if($status === 404)
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5M8 11h6"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 2 20 6v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6l8-4Z"/><path d="M12 8v5m0 4h.01"/></svg>
                        @endif
                    </span>
                    <span class="text-xs font-bold tracking-widest text-base-content/60">{{ $page['eyebrow'] }}</span>
                </div>
                <h2 id="admin-error-title" class="text-3xl font-bold tracking-tight text-base-content sm:text-4xl">{{ $page['title'] }}</h2>
                <p class="mt-4 text-base leading-7 text-base-content/70">{{ $page['message'] }}</p>
                <p class="mt-2 text-sm leading-6 text-base-content/50">{{ $page['hint'] }}</p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <x-lazy-btn :href="$homeUrl" primary :label="__('Back to dashboard')" />
                    <x-lazy-btn type="button" outline :label="__('Try again')" onclick="window.location.reload()" />
                </div>
            </div>
            <div class="flex min-h-56 flex-col items-center justify-center border-t border-base-300 bg-base-200/60 px-6 py-10 lg:min-h-96 lg:border-l lg:border-t-0">
                <div class="flex size-20 items-center justify-center rounded-2xl border border-base-300 bg-base-100 text-primary shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16v16H4zM4 9h16M9 9v11"/><path d="m12 13 2 2 4-4" opacity=".45"/></svg>
                </div>
                <span class="mt-5 text-7xl font-extrabold tracking-tight text-primary sm:text-8xl" aria-label="HTTP {{ $status }}">{{ $status }}</span>
                <span class="mt-3 rounded-lg border border-base-300 bg-base-100 px-3 py-1.5 text-xs font-medium text-base-content/60">HTTP {{ $status }}</span>
            </div>
        </div>
    </section>
</x-lazy-layout>
