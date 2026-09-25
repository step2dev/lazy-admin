<form method="POST" action="{{ $action }}" class="space-y-8" x-data="{ locale: '{{ old('original_locale', $page->original_locale ?: ($locales[0] ?? app()->getLocale())) }}' }">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <label class="form-control">
            <span class="label-text">{{ __('Slug') }}</span>
            <input class="input input-bordered @error('slug') input-error @enderror" name="slug" value="{{ old('slug', $page->slug) }}" required>
            @error('slug')<span class="mt-1 text-sm text-error">{{ $message }}</span>@enderror
        </label>

        <label class="form-control">
            <span class="label-text">{{ __('Stable key') }}</span>
            <input class="input input-bordered @error('key') input-error @enderror" name="key" value="{{ old('key', $page->key) }}">
            @error('key')<span class="mt-1 text-sm text-error">{{ $message }}</span>@enderror
        </label>

        <label class="form-control">
            <span class="label-text">{{ __('Parent page') }}</span>
            <select class="select select-bordered" name="parent_id">
                <option value="">{{ __('No parent') }}</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" @selected((string) old('parent_id', $page->parent_id) === (string) $parent->id)>
                        {{ $parent->path() }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')<span class="mt-1 text-sm text-error">{{ $message }}</span>@enderror
        </label>

        <label class="form-control">
            <span class="label-text">{{ __('Status') }}</span>
            <select class="select select-bordered" name="status">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $page->status?->value ?? 'draft') === $status->value)>
                        {{ ucfirst($status->value) }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="form-control">
            <span class="label-text">{{ __('Original locale') }}</span>
            <select class="select select-bordered" name="original_locale" x-model="locale">
                @foreach ($locales as $locale)
                    <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                @endforeach
            </select>
        </label>

        <label class="form-control">
            <span class="label-text">{{ __('Template') }}</span>
            <input class="input input-bordered" name="template" value="{{ old('template', $page->template) }}">
        </label>

        <label class="form-control">
            <span class="label-text">{{ __('Publish at') }}</span>
            <input class="input input-bordered" type="datetime-local" name="published_at" value="{{ old('published_at', $page->published_at?->format('Y-m-d\TH:i')) }}">
        </label>

        <label class="form-control">
            <span class="label-text">{{ __('Expire at') }}</span>
            <input class="input input-bordered" type="datetime-local" name="expires_at" value="{{ old('expires_at', $page->expires_at?->format('Y-m-d\TH:i')) }}">
        </label>

        <label class="form-control">
            <span class="label-text">{{ __('Position') }}</span>
            <input class="input input-bordered" type="number" min="0" name="position" value="{{ old('position', $page->position ?? 0) }}">
        </label>
    </div>

    <div>
        <div role="tablist" class="tabs tabs-bordered mb-6">
            @foreach ($locales as $locale)
                <button type="button" role="tab" class="tab" :class="{ 'tab-active': locale === '{{ $locale }}' }" @click="locale = '{{ $locale }}'">
                    {{ strtoupper($locale) }}
                </button>
            @endforeach
        </div>

        @foreach ($locales as $locale)
            @php($translation = $page->translate($locale))
            <div x-show="locale === '{{ $locale }}'" class="space-y-5">
                <label class="form-control">
                    <span class="label-text">{{ __('Title') }} ({{ strtoupper($locale) }})</span>
                    <input class="input input-bordered" name="translations[{{ $locale }}][title]" value="{{ old("translations.$locale.title", $translation?->title) }}">
                    @error("translations.$locale.title")<span class="mt-1 text-sm text-error">{{ $message }}</span>@enderror
                </label>

                <label class="form-control">
                    <span class="label-text">{{ __('Description') }}</span>
                    <textarea class="textarea textarea-bordered" rows="3" name="translations[{{ $locale }}][description]">{{ old("translations.$locale.description", $translation?->description) }}</textarea>
                </label>

                <label class="form-control">
                    <span class="label-text">{{ __('Content') }}</span>
                    <textarea class="textarea textarea-bordered min-h-80 font-mono" rows="16" name="translations[{{ $locale }}][content]">{{ old("translations.$locale.content", $translation?->content) }}</textarea>
                </label>
            </div>
        @endforeach
    </div>

    <div class="flex justify-end">
        <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
    </div>
</form>
