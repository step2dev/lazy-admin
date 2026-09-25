<x-lazy-layout>
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">{{ __('Pages') }}</h1>
                <p class="text-sm opacity-70">{{ __('Manage CMS pages, translations, publishing and hierarchy.') }}</p>
            </div>

            @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.create'))
                <a class="btn btn-primary" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.create') }}">
                    {{ __('Create page') }}
                </a>
            @endif
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="GET" class="grid gap-3 md:grid-cols-4">
            <input class="input input-bordered" type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search pages') }}">

            <select class="select select-bordered" name="status">
                <option value="">{{ __('All statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                        {{ ucfirst($status->value) }}
                    </option>
                @endforeach
            </select>

            <label class="flex items-center gap-2">
                <input class="checkbox" type="checkbox" name="trashed" value="1" @checked(request()->boolean('trashed'))>
                <span>{{ __('Trash') }}</span>
            </label>

            <button class="btn btn-outline" type="submit">{{ __('Filter') }}</button>
        </form>

        <div class="overflow-x-auto rounded-box border border-base-300">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Updated') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pages as $page)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $page->title ?: __('Untitled') }}</div>
                                @if ($page->key)
                                    <div class="text-xs opacity-60">{{ $page->key }}</div>
                                @endif
                            </td>
                            <td><code>{{ $page->path() }}</code></td>
                            <td><span class="badge badge-outline">{{ $page->status->value }}</span></td>
                            <td>{{ $page->updated_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    @if ($page->trashed())
                                        @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.restore'))
                                            <form method="POST" action="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.restore', $page->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline" type="submit">{{ __('Restore') }}</button>
                                            </form>
                                        @endif
                                    @else
                                        @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.preview'))
                                            <a class="btn btn-sm btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.preview', $page) }}">
                                                {{ __('Preview') }}
                                            </a>
                                        @endif
                                        @if (!config('lazy.admin.permissions.enforce', true) || auth(config('lazy.auth.guard', 'web'))->user()?->can('pages.edit'))
                                            <a class="btn btn-sm btn-outline" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.page.edit', $page) }}">
                                                {{ __('Edit') }}
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center opacity-60">{{ __('No pages found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $pages->links() }}
    </div>
</x-lazy-layout>
