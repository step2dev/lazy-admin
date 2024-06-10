<div>
@dump(setting())
    @foreach(lazyLocalization()->getSupportedLocales() as $locale)
        @dump($locale)
    @endforeach
</div>
