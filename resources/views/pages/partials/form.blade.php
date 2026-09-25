<form
    method="POST"
    action="{{ $action }}"
    class="space-y-4"
    x-data="{
        locale: '{{ old('original_locale', $page->original_locale ?: ($locales[0] ?? app()->getLocale())) }}',
        advanced: false,
    }"
    x-init="$nextTick(() => $dispatch('lazy-page-locale-changed', { locale }))"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <section class="rounded-box border border-base-300 bg-base-100">
            <div class="border-b border-base-300 px-4 py-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold">{{ __('Content') }}</h2>
                        <p class="text-xs opacity-60">{{ __('Edit translated page content.') }}</p>
                    </div>

                    <div class="join">
                        @foreach ($locales as $locale)
                            <button
                                type="button"
                                class="btn btn-sm join-item min-w-12"
                                :class="locale === '{{ $locale }}' ? 'btn-active btn-neutral' : 'btn-ghost border border-base-300'"
                                @click="locale = '{{ $locale }}'; $nextTick(() => $dispatch('lazy-page-locale-changed', { locale }))"
                            >
                                {{ strtoupper($locale) }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-4">
                @foreach ($locales as $locale)
                    @php($translation = $page->translate($locale))

                    <div
                        x-show="locale === '{{ $locale }}'"
                        x-cloak
                        class="space-y-4"
                        data-lazy-page-locale-panel="{{ $locale }}"
                    >
                        <label class="form-control">
                            <span class="label-text mb-1 text-sm">{{ __('Title') }} ({{ strtoupper($locale) }})</span>
                            <input
                                class="input input-bordered input-sm w-full @error("translations.$locale.title") input-error @enderror"
                                name="translations[{{ $locale }}][title]"
                                value="{{ old("translations.$locale.title", $translation?->title) }}"
                            >
                            @error("translations.$locale.title")
                                <span class="mt-1 text-xs text-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="form-control">
                            <span class="label-text mb-1 text-sm">{{ __('Description') }}</span>
                            <textarea
                                class="textarea textarea-bordered min-h-24 w-full"
                                rows="4"
                                id="lazy-page-description-{{ $locale }}"
                                name="translations[{{ $locale }}][description]"
                                data-lazy-page-editor="description"
                                data-locale="{{ $locale }}"
                            >{{ old("translations.$locale.description", $translation?->description) }}</textarea>
                        </label>

                        <label class="form-control">
                            <span class="label-text mb-1 text-sm">{{ __('Content') }}</span>
                            <textarea
                                class="textarea textarea-bordered min-h-[28rem] w-full font-mono"
                                rows="22"
                                id="lazy-page-content-{{ $locale }}"
                                name="translations[{{ $locale }}][content]"
                                data-lazy-page-editor="content"
                                data-locale="{{ $locale }}"
                            >{{ old("translations.$locale.content", $translation?->content) }}</textarea>
                        </label>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="space-y-4">
            <section class="rounded-box border border-base-300 bg-base-100 p-4">
                <h2 class="mb-4 font-semibold">{{ __('Page settings') }}</h2>

                <div class="space-y-3">
                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Slug') }}</span>
                        <input
                            class="input input-bordered input-sm w-full @error('slug') input-error @enderror"
                            name="slug"
                            value="{{ old('slug', $page->slug) }}"
                            required
                        >
                        @error('slug')
                            <span class="mt-1 text-xs text-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Status') }}</span>
                        <select class="select select-bordered select-sm w-full" name="status">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $page->status?->value ?? 'draft') === $status->value)>
                                    {{ ucfirst($status->value) }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Original locale') }}</span>
                        <select
                            class="select select-bordered select-sm w-full"
                            name="original_locale"
                            x-model="locale"
                            @change="$nextTick(() => $dispatch('lazy-page-locale-changed', { locale }))"
                        >
                            @foreach ($locales as $locale)
                                <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Parent page') }}</span>
                        <select class="select select-bordered select-sm w-full" name="parent_id">
                            <option value="">{{ __('No parent') }}</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((string) old('parent_id', $page->parent_id) === (string) $parent->id)>
                                    {{ $parent->path() }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <span class="mt-1 text-xs text-error">{{ $message }}</span>
                        @enderror
                    </label>
                </div>
            </section>

            <section class="rounded-box border border-base-300 bg-base-100">
                <button
                    type="button"
                    class="flex w-full items-center justify-between px-4 py-3 text-left font-medium"
                    @click="advanced = ! advanced"
                    :aria-expanded="advanced.toString()"
                >
                    <span>{{ __('Advanced') }}</span>
                    <span class="text-lg leading-none" x-text="advanced ? '−' : '+'"></span>
                </button>

                <div x-show="advanced" x-cloak class="space-y-3 border-t border-base-300 p-4">
                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Stable key') }}</span>
                        <input class="input input-bordered input-sm w-full @error('key') input-error @enderror" name="key" value="{{ old('key', $page->key) }}">
                        @error('key')
                            <span class="mt-1 text-xs text-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Template') }}</span>
                        <input class="input input-bordered input-sm w-full" name="template" value="{{ old('template', $page->template) }}">
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Publish at') }}</span>
                        <input class="input input-bordered input-sm w-full" type="datetime-local" name="published_at" value="{{ old('published_at', $page->published_at?->format('Y-m-d\TH:i')) }}">
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Expire at') }}</span>
                        <input class="input input-bordered input-sm w-full" type="datetime-local" name="expires_at" value="{{ old('expires_at', $page->expires_at?->format('Y-m-d\TH:i')) }}">
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Position') }}</span>
                        <input class="input input-bordered input-sm w-full" type="number" min="0" name="position" value="{{ old('position', $page->position ?? 0) }}">
                    </label>
                </div>
            </section>
        </aside>
    </div>

    <div class="rounded-box border border-base-300 bg-base-100 px-4 py-3">
        <div class="flex justify-end">
            <button class="btn btn-primary btn-sm min-w-28" type="submit">{{ $submitLabel }}</button>
        </div>
    </div>
</form>
