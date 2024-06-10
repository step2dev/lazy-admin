<x-lazy-layout>
    <x-lazy-tabs type="bordered" size="lg">
        <x-lazy-tab :title="__('lazy-admin::settings.general')" active/>
        <x-lazy-tab :title="__('lazy-admin::settings.other')"/>
    </x-lazy-tabs>

    @foreach(lazyLocalization()->getSupportedLocales() as $locale)
        @dump($locale)
    @endforeach
    @foreach(setting() as $setting)
        @dump($setting)
    @endforeach
</x-lazy-layout>
