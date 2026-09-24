<x-lazy-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">{{ __('Edit permission') }}</h1>
            <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.permission.index') }}">{{ __('Back') }}</a>
        </div>
        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif
        @include('lazy::permissions.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.permission.update', $permission),
            'method' => 'PUT',
            'submitLabel' => __('Save permission'),
        ])
    </div>
</x-lazy-layout>
