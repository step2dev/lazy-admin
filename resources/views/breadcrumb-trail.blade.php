@use('Step2Dev\LazyBreadcrumb\Breadcrumbs')
@php($items = $items ?? Breadcrumbs::generate(Route::current()?->getName(), Route::current()?->parameters() ?? []))

@if ($items)
    <nav aria-label="{{ __('Breadcrumb') }}">
        <x-lazy-breadcrumbs class="breadcrumbs text-sm">
            <ul>
                @foreach ($items as $item)
                    <li>
                        @if ($loop->last)
                            <span aria-current="page">{{ $item['title'] }}</span>
                        @else
                            <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-lazy-breadcrumbs>
    </nav>
@endif
