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

    <div class="grid gap-4 xl:grid-cols-12">
        <section class="rounded-box border border-base-300 bg-base-100 xl:col-span-9">
            <div class="border-b border-base-300 px-4 py-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold">{{ __('Content') }}</h2>
                        <p class="text-xs opacity-60">{{ __('Edit translated page content.') }}</p>
                    </div>

                    <x-lazy-join>
                        @foreach ($locales as $locale)
                            <x-lazy-btn
                                type="button"
                                sm
                                class="min-w-12"
                                :class="locale === '{{ $locale }}' ? 'btn-active btn-neutral' : 'btn-ghost border border-base-300'"
                                @click="locale = '{{ $locale }}'; $nextTick(() => $dispatch('lazy-page-locale-changed', { locale }))"
                            >
                                {{ strtoupper($locale) }}
                            </x-lazy-btn>
                        @endforeach
                    </x-lazy-join>
                </div>
            </div>

            <div class="p-4">
                @foreach ($translationItems as $translationItem)
                    <div
                        x-show="locale === '{{ $translationItem['locale'] }}'"
                        x-cloak
                        class="space-y-4"
                        data-lazy-page-locale-panel="{{ $translationItem['locale'] }}"
                    >
                        <label class="form-control">
                            <span class="label-text mb-1 text-sm">{{ __('Title') }} ({{ strtoupper($translationItem['locale']) }})</span>
                            <x-lazy-input
                                sm
                                name="translations[{{ $translationItem['locale'] }}][title]"
                                :value="old('translations.'.$translationItem['locale'].'.title', $translationItem['translation']?->title)"
                                :color="$errors->has('translations.'.$translationItem['locale'].'.title') ? 'error' : ''"
                            />
                            @error('translations.'.$translationItem['locale'].'.title')
                                <span class="mt-1 text-xs text-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="form-control">
                            <span class="label-text mb-1 text-sm">{{ __('Description') }}</span>
                            <x-lazy-textarea
                                rows="4"
                                id="lazy-page-description-{{ $translationItem['locale'] }}"
                                name="translations[{{ $translationItem['locale'] }}][description]"
                                data-lazy-page-editor="description"
                                data-locale="{{ $translationItem['locale'] }}"
                                class="min-h-24"
                            >{{ old('translations.'.$translationItem['locale'].'.description', $translationItem['translation']?->description) }}</x-lazy-textarea>
                        </label>

                        <label class="form-control">
                            <span class="label-text mb-1 text-sm">{{ __('Content') }}</span>
                            <x-lazy-textarea
                                rows="22"
                                id="lazy-page-content-{{ $translationItem['locale'] }}"
                                name="translations[{{ $translationItem['locale'] }}][content]"
                                data-lazy-page-editor="content"
                                data-locale="{{ $translationItem['locale'] }}"
                                class="min-h-[28rem] font-mono"
                            >{{ old('translations.'.$translationItem['locale'].'.content', $translationItem['translation']?->content) }}</x-lazy-textarea>
                        </label>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="space-y-4 self-start xl:sticky xl:top-4 xl:col-span-3">
            <section class="rounded-box border border-base-300 bg-base-100 p-4">
                <h2 class="mb-4 font-semibold">{{ __('Page settings') }}</h2>

                <div class="space-y-3">
                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Slug') }}</span>
                        <x-lazy-input
                            sm
                            name="slug"
                            :value="old('slug', $page->slug)"
                            :color="$errors->has('slug') ? 'error' : ''"
                            required
                        />
                        @error('slug')
                            <span class="mt-1 text-xs text-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Status') }}</span>
                        <x-lazy-select sm name="status">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $page->status?->value ?? 'draft') === $status->value)>
                                    {{ ucfirst($status->value) }}
                                </option>
                            @endforeach
                        </x-lazy-select>
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Original locale') }}</span>
                        <x-lazy-select
                            sm
                            name="original_locale"
                            x-model="locale"
                            @change="$nextTick(() => $dispatch('lazy-page-locale-changed', { locale }))"
                        >
                            @foreach ($locales as $locale)
                                <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                            @endforeach
                        </x-lazy-select>
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Parent page') }}</span>
                        <x-lazy-select sm name="parent_id" :placeholder="__('No parent')">
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((string) old('parent_id', $page->parent_id) === (string) $parent->id)>
                                    {{ $parent->path() }}
                                </option>
                            @endforeach
                        </x-lazy-select>
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
                        <x-lazy-input sm name="key" :value="old('key', $page->key)" :color="$errors->has('key') ? 'error' : ''" />
                        @error('key')
                            <span class="mt-1 text-xs text-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Template') }}</span>
                        <x-lazy-input sm name="template" :value="old('template', $page->template)" />
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Publish at') }}</span>
                        <x-lazy-input sm type="datetime-local" name="published_at" :value="old('published_at', $page->published_at?->format('Y-m-d\TH:i'))" />
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Expire at') }}</span>
                        <x-lazy-input sm type="datetime-local" name="expires_at" :value="old('expires_at', $page->expires_at?->format('Y-m-d\TH:i'))" />
                    </label>

                    <label class="form-control">
                        <span class="label-text mb-1 text-sm">{{ __('Position') }}</span>
                        <x-lazy-input sm type="number" min="0" name="position" :value="old('position', $page->position ?? 0)" />
                    </label>
                </div>
            </section>

            <section class="rounded-box border border-base-300 bg-base-100 p-4">
                <x-lazy-btn primary sm block type="submit" :label="$submitLabel" />
            </section>
        </aside>
    </div>
</form>
