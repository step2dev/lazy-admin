<x-lazy-layout :title="__('Edit page')">
    <x-slot name="action">
        @if (! config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.preview'))
            <x-lazy-btn
                :href="route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.preview', $page)"
                outline info sm
                :label="__('Preview')"
            />
        @endif
    </x-slot>

    <div class="mx-auto max-w-7xl">
        @if (session('status'))
            <div class="alert alert-success mb-4">{{ session('status') }}</div>
        @endif

        @include('lazy::pages.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.update', $page),
            'method' => 'PUT',
            'submitLabel' => __('Save changes'),
        ])
    </div>
</x-lazy-layout>
