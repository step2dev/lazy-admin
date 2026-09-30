<x-lazy-form wire:submit="save" class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <fieldset @disabled(! $canEdit) class="contents">
    <header class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold tracking-tight">{{ __('lazy-admin::settings.general_settings') }}</h1>
        <p class="text-sm opacity-70">{{ __('lazy-admin::settings.manage_site') }}</p>
    </header>

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
        <section aria-labelledby="site-details-title" class="rounded-2xl border border-base-300 bg-base-100 p-5 shadow-sm sm:p-6 lg:col-span-2">
            <div class="mb-6 flex flex-col gap-1">
                <h2 id="site-details-title" class="text-lg font-semibold">{{ __('lazy-admin::settings.site_details') }}</h2>
                <p class="text-sm opacity-70">{{ __('lazy-admin::settings.site_details_help') }}</p>
            </div>

            <div class="flex flex-col gap-5">
                <x-lazy-form-input id="settings-name" wire:model="settings.name" maxlength="255" hr
                                   :label="__('lazy-admin::settings.name')" aria-label="{{ __('lazy-admin::settings.name') }}"
                                   :placeholder="__('lazy-admin::settings.site_name')" />

                <x-lazy-form-textarea id="settings-description" wire:model="settings.description" rows="5" hr
                                      :label="__('lazy-admin::settings.description')" aria-label="{{ __('lazy-admin::settings.description') }}"
                                      class="resize-y" :placeholder="__('lazy-admin::settings.site_description_placeholder')" />
            </div>
        </section>

        <section aria-labelledby="site-logo-title" class="flex flex-col gap-5 rounded-2xl border border-base-300 bg-base-100 p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-1">
                <h2 id="site-logo-title" class="text-lg font-semibold">{{ __('lazy-admin::settings.logo') }}</h2>
                <p class="text-sm opacity-70">{{ __('lazy-admin::settings.logo_help') }}</p>
            </div>

            <div class="flex min-h-40 items-center justify-center rounded-xl border border-dashed border-base-300 bg-base-200 p-6">
                @if ($currentLogoUrl)
                    <x-lazy-image :src="$currentLogoUrl" :alt="__('lazy-admin::settings.current_logo')" class="max-h-28 max-w-full object-contain" />
                @else
                    <span class="text-sm opacity-60">{{ __('lazy-admin::settings.no_logo') }}</span>
                @endif
            </div>

            <div class="flex flex-col gap-2">
                <x-lazy-form-image id="settings-logo" wire:model="logo" accept="image/*" hr
                                   :label="__('lazy-admin::settings.upload_logo')" aria-label="{{ __('lazy-admin::settings.upload_logo') }}"
                                   class="min-w-0 text-sm"
                                   aria-describedby="settings-logo-help" />
                <p id="settings-logo-help" class="text-xs opacity-70">{{ __('lazy-admin::settings.logo_limit') }}</p>
                <p wire:loading wire:target="logo" role="status" class="text-sm opacity-70">{{ __('lazy-admin::settings.uploading') }}</p>
                @if ($logo)
                    <p class="break-words text-sm opacity-70">{{ __('lazy-admin::settings.selected', ['name' => $logo->getClientOriginalName()]) }}</p>
                @endif
            </div>
        </section>

        <section aria-labelledby="login-background-title" class="flex flex-col gap-5 rounded-2xl border border-base-300 bg-base-100 p-5 shadow-sm sm:p-6 lg:col-span-3">
            <div class="flex flex-col gap-1">
                <h2 id="login-background-title" class="text-lg font-semibold">{{ __('lazy-admin::settings.login_background') }}</h2>
                <p class="text-sm opacity-70">{{ __('lazy-admin::settings.login_background_help') }}</p>
            </div>

            <div class="overflow-hidden rounded-xl border border-dashed border-base-300 bg-base-200">
                @if ($currentBackgroundUrl)
                    <x-lazy-image :src="$currentBackgroundUrl" :alt="__('lazy-admin::settings.current_background')" class="aspect-[16/7] w-full object-cover" />
                @else
                    <div class="flex aspect-[16/7] items-center justify-center p-6">
                        <span class="text-sm opacity-60">{{ __('lazy-admin::settings.no_background') }}</span>
                    </div>
                @endif
            </div>

            <div class="flex flex-col gap-2">
                <x-lazy-form-image id="settings-background" wire:model="background" accept="image/*" hr
                                   :label="__('lazy-admin::settings.upload_background')" aria-label="{{ __('lazy-admin::settings.upload_background') }}"
                                   class="min-w-0 text-sm"
                                   aria-describedby="settings-background-help" />
                <p id="settings-background-help" class="text-xs opacity-70">{{ __('lazy-admin::settings.background_limit') }}</p>
                <p wire:loading wire:target="background" role="status" class="text-sm opacity-70">{{ __('lazy-admin::settings.uploading') }}</p>
                @if ($background)
                    <p class="break-words text-sm opacity-70">{{ __('lazy-admin::settings.selected', ['name' => $background->getClientOriginalName()]) }}</p>
                @endif
            </div>
        </section>
    </div>

    </fieldset>

    <footer class="flex flex-col gap-4 rounded-2xl border border-base-300 bg-base-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <p class="text-sm opacity-70">{{ __('lazy-admin::settings.changes_after_save') }}</p>
        <div class="flex items-center justify-end gap-3">
            @if($canEdit)
                <span wire:dirty class="text-xs opacity-70">{{ __('lazy-admin::settings.unsaved_changes') }}</span>
                <x-lazy-btn primary type="submit" wire:loading.attr="disabled" wire:target="save,logo,background">
                    <span wire:loading.remove wire:target="save">{{ __('lazy-admin::settings.save_changes') }}</span>
                    <span wire:loading wire:target="save" role="status">{{ __('lazy-admin::settings.saving') }}</span>
                </x-lazy-btn>
            @else
                <x-lazy-badge ghost :label="__('lazy-admin::settings.read_only')" />
            @endif
        </div>
    </footer>
</x-lazy-form>
