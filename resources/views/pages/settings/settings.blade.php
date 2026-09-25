<x-lazy-form wire:submit="save" class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <fieldset @disabled(! $canEdit) class="contents">
    <header class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold tracking-tight">{{ __('General settings') }}</h1>
        <p class="text-sm opacity-70">{{ __('Manage the name, description and logo of your site.') }}</p>
    </header>

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
        <section aria-labelledby="site-details-title" class="rounded-2xl border border-base-300 bg-base-100 p-5 shadow-sm sm:p-6 lg:col-span-2">
            <div class="mb-6 flex flex-col gap-1">
                <h2 id="site-details-title" class="text-lg font-semibold">{{ __('Site details') }}</h2>
                <p class="text-sm opacity-70">{{ __('Basic information about your site.') }}</p>
            </div>

            <div class="flex flex-col gap-5">
                <x-lazy-form-input id="settings-name" wire:model="settings.name" maxlength="255" hr
                                   :label="__('Name')" aria-label="{{ __('Name') }}"
                                   class="input-bordered" :placeholder="__('Site name')" />

                <x-lazy-form-textarea id="settings-description" wire:model="settings.description" rows="5" hr
                                      :label="__('Description')" aria-label="{{ __('Description') }}"
                                      class="textarea textarea-bordered resize-y" :placeholder="__('A short description of your site')" />
            </div>
        </section>

        <section aria-labelledby="site-logo-title" class="flex flex-col gap-5 rounded-2xl border border-base-300 bg-base-100 p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-1">
                <h2 id="site-logo-title" class="text-lg font-semibold">{{ __('Logo') }}</h2>
                <p class="text-sm opacity-70">{{ __('Choose an image for your site.') }}</p>
            </div>

            <div class="flex min-h-40 items-center justify-center rounded-xl border border-dashed border-base-300 bg-base-200 p-6">
                @if ($currentLogoUrl)
                    <x-lazy-image :src="$currentLogoUrl" :alt="__('Current site logo')" class="max-h-28 max-w-full object-contain" />
                @else
                    <span class="text-sm opacity-60">{{ __('No logo uploaded') }}</span>
                @endif
            </div>

            <div class="flex flex-col gap-2">
                <x-lazy-form-image id="settings-logo" wire:model="logo" accept="image/*" hr
                                   :label="__('Upload logo')" aria-label="{{ __('Upload logo') }}"
                                   class="file-input file-input-bordered min-w-0 text-sm"
                                   aria-describedby="settings-logo-help" />
                <p id="settings-logo-help" class="text-xs opacity-70">{{ __('Image file, up to 1 MB.') }}</p>
                <p wire:loading wire:target="logo" role="status" class="text-sm opacity-70">{{ __('Uploading…') }}</p>
                @if ($logo)
                    <p class="break-words text-sm opacity-70">{{ __('Selected: :name', ['name' => $logo->getClientOriginalName()]) }}</p>
                @endif
            </div>
        </section>
    </div>

    </fieldset>

    <footer class="flex flex-col gap-4 rounded-2xl border border-base-300 bg-base-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <p class="text-sm opacity-70">{{ __('Changes take effect after saving.') }}</p>
        <div class="flex items-center justify-end gap-3">
            @if($canEdit)
                <span wire:dirty class="text-xs opacity-70">{{ __('Unsaved changes') }}</span>
                <x-lazy-btn primary type="submit" wire:loading.attr="disabled" wire:target="save,logo">
                    <span wire:loading.remove wire:target="save">{{ __('Save changes') }}</span>
                    <span wire:loading wire:target="save" role="status">{{ __('Saving…') }}</span>
                </x-lazy-btn>
            @else
                <span class="badge badge-ghost">{{ __('Read only') }}</span>
            @endif
        </div>
    </footer>
</x-lazy-form>
