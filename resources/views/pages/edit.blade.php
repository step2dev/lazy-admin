<x-lazy-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-semibold">{{ __('Edit page') }}</h1>
            <div class="flex gap-2">
                @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.preview'))
                    <a class="btn btn-outline" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.preview', $page) }}">{{ __('Preview') }}</a>
                @endif
                <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.index') }}">{{ __('Back') }}</a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @include('lazy::pages.partials.form', [
            'action' => route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.update', $page),
            'method' => 'PUT',
            'submitLabel' => __('Save changes'),
        ])

        @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.delete'))
            <div class="rounded-box border border-error/30 p-6">
                <h2 class="text-lg font-semibold text-error">{{ __('Delete page') }}</h2>
                <p class="mt-2 text-sm opacity-70">{{ __('The page will be moved to trash and can be restored later.') }}</p>
                <form method="POST" action="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.destroy', $page) }}" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-error" type="submit">{{ __('Move to trash') }}</button>
                </form>
            </div>
        @endif
    </div>
</x-lazy-layout>
