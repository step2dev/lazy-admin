<x-lazy-layout :title="__('Create page')">
    <div class="mx-auto max-w-7xl">
        @include('lazy::pages.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.store'),
            'method' => 'POST',
            'submitLabel' => __('Create page'),
        ])
    </div>
</x-lazy-layout>
