@use('Step2Dev\LazyBreadcrumb\Breadcrumbs')
@php($items = $items ?? Breadcrumbs::generate(Route::current()?->getName(), Route::current()?->parameters() ?? []))

@if ($items)
    <nav aria-label="{{ __('Breadcrumb') }}">
        <x-lazy-breadcrumbs :items="collect($items)->map(fn (array $item) => [
            'label' => $item['title'],
            'href' => $item['url'],
        ])->all()" />
    </nav>
@endif
