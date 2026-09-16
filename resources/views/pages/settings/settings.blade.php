<div>
    <x-lazy-form-input label="Name" wire:model="settings.name"/>
    <x-lazy-form-textarea label="Description" wire:model="settings.description"/>
    <x-lazy-form-image label="Logo" wire:model="logo" :src="setting('admin.logo')"/>

    <x-lazy-btn primary label="Save" wire:click="save"/>
</div>
