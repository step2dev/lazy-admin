<x-lazy-layout>
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">{{ __('Create page') }}</h1>
            <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.index') }}">{{ __('Back') }}</a>
        </div>

        @include('lazy::pages.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.store'),
            'method' => 'POST',
            'submitLabel' => __('Create page'),
        ])
    </div>
</x-lazy-layout>
