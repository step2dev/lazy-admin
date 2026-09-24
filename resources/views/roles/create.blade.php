<x-lazy-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">{{ __('Create role') }}</h1>
            <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.role.index') }}">{{ __('Back') }}</a>
        </div>
        @include('lazy::roles.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.role.store'),
            'method' => 'POST',
            'submitLabel' => __('Create role'),
        ])
    </div>
</x-lazy-layout>
