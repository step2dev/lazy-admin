<x-lazy-layout>
    @php
        $routePrefix = trim((string) config('lazy.admin.route.name', 'admin.'), '.');
        $enforcePermissions = (bool) config('lazy.admin.permissions.enforce', true);
        $user = auth((string) config('lazy.auth.guard', 'web'))->user();

        $canCreate = ! $enforcePermissions || $user?->can('seo_redirects.create');
        $canEdit = ! $enforcePermissions || $user?->can('seo_redirects.edit');
        $canDelete = ! $enforcePermissions || $user?->can('seo_redirects.delete');
    @endphp

    <div class="mx-auto max-w-[1500px] space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-xs font-semibold uppercase tracking-[0.22em] opacity-50">
                    {{ __('SEO') }}
                </div>
                <h1 class="mt-2 text-2xl font-semibold">{{ __('Redirects') }}</h1>
                <p class="mt-1 text-sm opacity-60">
                    {{ __('Manage exact, wildcard and regex redirect rules provided by lazy-seo-redirects.') }}
                </p>
            </div>

            <form method="GET" action="{{ route($routePrefix.'.seo.redirects.index') }}" class="flex w-full gap-2 lg:max-w-md">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    class="input input-bordered min-w-0 flex-1"
                    placeholder="{{ __('Search old or new URL') }}"
                />
                <button type="submit" class="btn">{{ __('Search') }}</button>
            </form>
        </div>

        @if (session('status'))
            <div class="alert alert-success shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($canCreate)
            <section class="rounded-3xl border border-base-300 bg-base-100/50 p-5">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold">{{ __('Create redirect') }}</h2>
                    <p class="text-sm opacity-55">
                        {{ __('Use 410 for removed content; the destination is ignored for that status.') }}
                    </p>
                </div>

                <form method="POST" action="{{ route($routePrefix.'.seo.redirects.store') }}" class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_130px_auto]">
                    @csrf

                    <input
                        type="text"
                        name="old_url"
                        value="{{ old('old_url') }}"
                        class="input input-bordered w-full"
                        placeholder="/old-page or /blog/*"
                        required
                    />

                    <input
                        type="text"
                        name="new_url"
                        value="{{ old('new_url') }}"
                        class="input input-bordered w-full"
                        placeholder="/new-page"
                    />

                    <select name="status_code" class="select select-bordered w-full">
                        @foreach ($allowedStatusCodes as $statusCode)
                            <option value="{{ $statusCode }}" @selected((int) old('status_code', 301) === $statusCode)>
                                {{ $statusCode }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">
                        ＋ {{ __('Create') }}
                    </button>

                    <div class="flex flex-wrap items-center gap-5 xl:col-span-4">
                        <label class="label cursor-pointer gap-3">
                            <input type="checkbox" name="enabled" value="1" class="checkbox checkbox-primary" @checked(old('enabled', true)) />
                            <span class="label-text">{{ __('Enabled') }}</span>
                        </label>

                        <label class="label cursor-pointer gap-3">
                            <input type="checkbox" name="is_regex" value="1" class="checkbox checkbox-primary" @checked(old('is_regex')) />
                            <span class="label-text">{{ __('Regex') }}</span>
                        </label>
                    </div>
                </form>
            </section>
        @endif

        <section class="space-y-3">
            @forelse ($redirects as $redirect)
                <div class="rounded-3xl border border-base-300 bg-base-100/40 p-5">
                    <form
                        method="POST"
                        action="{{ route($routePrefix.'.seo.redirects.update', $redirect->getKey()) }}"
                        class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_130px_auto]"
                    >
                        @csrf
                        @method('PUT')

                        <input
                            type="text"
                            name="old_url"
                            value="{{ $redirect->old_url }}"
                            class="input input-bordered w-full"
                            @disabled(! $canEdit)
                            required
                        />

                        <input
                            type="text"
                            name="new_url"
                            value="{{ $redirect->new_url }}"
                            class="input input-bordered w-full"
                            placeholder="{{ __('Gone (410)') }}"
                            @disabled(! $canEdit)
                        />

                        <select name="status_code" class="select select-bordered w-full" @disabled(! $canEdit)>
                            @foreach ($allowedStatusCodes as $statusCode)
                                <option value="{{ $statusCode }}" @selected((int) $redirect->status_code === $statusCode)>
                                    {{ $statusCode }}
                                </option>
                            @endforeach
                        </select>

                        <div class="flex gap-2">
                            @if ($canEdit)
                                <button type="submit" class="btn btn-primary flex-1">
                                    {{ __('Save') }}
                                </button>
                            @endif

                            @if ($canDelete)
                                <button
                                    type="submit"
                                    form="delete-seo-redirect-{{ $redirect->getKey() }}"
                                    class="btn btn-ghost text-error"
                                    title="{{ __('Delete') }}"
                                >
                                    ×
                                </button>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 xl:col-span-4">
                            <label class="label gap-3">
                                <input
                                    type="checkbox"
                                    name="enabled"
                                    value="1"
                                    class="checkbox checkbox-primary checkbox-sm"
                                    @checked($redirect->enabled)
                                    @disabled(! $canEdit)
                                />
                                <span class="label-text">{{ __('Enabled') }}</span>
                            </label>

                            <label class="label gap-3">
                                <input
                                    type="checkbox"
                                    name="is_regex"
                                    value="1"
                                    class="checkbox checkbox-primary checkbox-sm"
                                    @checked($redirect->is_regex)
                                    @disabled(! $canEdit)
                                />
                                <span class="label-text">{{ __('Regex') }}</span>
                            </label>

                            <span class="text-xs opacity-50">
                                {{ __('Hits: :count', ['count' => $redirect->hits]) }}
                            </span>

                            @if ($redirect->last_hit_at)
                                <span class="text-xs opacity-50">
                                    {{ __('Last hit: :date', ['date' => $redirect->last_hit_at->format('Y-m-d H:i')]) }}
                                </span>
                            @endif
                        </div>
                    </form>

                    @if ($canDelete)
                        <form
                            id="delete-seo-redirect-{{ $redirect->getKey() }}"
                            method="POST"
                            action="{{ route($routePrefix.'.seo.redirects.destroy', $redirect->getKey()) }}"
                            onsubmit="return confirm('{{ __('Delete this redirect?') }}')"
                        >
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-base-300 p-10 text-center opacity-60">
                    {{ __('No redirects found.') }}
                </div>
            @endforelse
        </section>

        @if ($redirects->hasPages())
            <div>
                {{ $redirects->links() }}
            </div>
        @endif
    </div>
</x-lazy-layout>
