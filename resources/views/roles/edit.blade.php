<x-lazy-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">{{ __('Edit role') }}</h1>
            <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.role.index') }}">{{ __('Back') }}</a>
        </div>
        @if (session('status'))
            <div class="alert alert-success mb-6">{{ session('status') }}</div>
        @endif
        @include('lazy::roles.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.role.update', $role),
            'method' => 'PUT',
            'submitLabel' => __('Save role'),
        ])
    </div>
</x-lazy-layout>
