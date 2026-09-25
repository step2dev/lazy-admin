<x-lazy-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ __('Page preview') }}</h1>
                <p class="text-sm opacity-70">{{ $page->path() }} · {{ $page->status->value }}</p>
            </div>
            <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.edit', $page) }}">{{ __('Back to edit') }}</a>
        </div>

        @foreach ($locales as $locale)
            @php($translation = $page->translate($locale))
            <section class="rounded-box border border-base-300 p-6">
                <div class="mb-4 badge badge-outline">{{ strtoupper($locale) }}</div>
                <h2 class="text-3xl font-semibold">{{ $translation?->title ?: __('Untitled') }}</h2>
                @if ($translation?->description)
                    <p class="mt-3 opacity-70">{{ $translation->description }}</p>
                @endif
                <div class="prose mt-6 max-w-none">{!! $translation?->content !!}</div>
            </section>
        @endforeach
    </div>
</x-lazy-layout>
