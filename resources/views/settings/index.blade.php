<x-lazy-layout>
    <x-lazy-tabs type="bordered" size="lg">
        <x-lazy-tab :title="__('lazy-admin::settings.general')" active/>
        <x-lazy-tab :title="__('lazy-admin::settings.other')"/>
    </x-lazy-tabs>

    @dd(lazyLocalization()->getSupportedLocales())
    @foreach(setting() as $setting)
        @dump($setting)
    @endforeach
</x-lazy-layout>
