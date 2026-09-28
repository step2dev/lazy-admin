<x-lazy-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ __('Page preview') }}</h1>
                <p class="text-sm opacity-70">{{ $page->path() }} · {{ $page->status->value }}</p>
            </div>
            <x-lazy-btn
                ghost
                :href="route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.edit', $page)"
                :label="__('Back to edit')"
            />
        </div>

        @foreach ($translationItems as $translationItem)
            <section class="rounded-box border border-base-300 p-6">
                <x-lazy-badge outline class="mb-4">{{ strtoupper($translationItem['locale']) }}</x-lazy-badge>
                <h2 class="text-3xl font-semibold">{{ $translationItem['translation']?->title ?: __('Untitled') }}</h2>
                @if ($translationItem['translation']?->description)
                    <p class="mt-3 opacity-70">{{ $translationItem['translation']->description }}</p>
                @endif
                <div class="prose mt-6 max-w-none">{!! $translationItem['translation']?->content !!}</div>
            </section>
        @endforeach
    </div>
</x-lazy-layout>
