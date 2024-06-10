<div>
    <x-lazy-form-input label="Name" wire:model="settings.name"/>
    <x-lazy-form-textarea label="Email" wire:model="settings.description"/>
    <x-lazy-btn primary label="Save" wire:click="save"/>
    <x-lazy-form-image label="Logo" required wire:model="settings.logo" :src="image_path(setting('site.logo'))"/>
    @dump($settings)
    @foreach(lazyLocalization()->getSupportedLocales() as $locale)
        @dump($locale)
    @endforeach
</div>
