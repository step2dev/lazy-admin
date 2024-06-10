<x-lazy-layout>
    <x-lazy-tabs type="bordered" size="lg">
        <x-lazy-tab :title="__('lazy-admin::settings.general')" active/>
        <x-lazy-tab :title="__('lazy-admin::settings.other')"/>
    </x-lazy-tabs>
    <livewire:settings.setting/>
</x-lazy-layout>
