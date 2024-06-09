<div x-data="{ open: false }" class="locale-select"
     :class="{ 'open': open }">
    <div @click="open = !open" class="locale-flag"
         title="Click to select the language">{{ $flags[app()->currentLocale()] }}</div>
    <ul x-show="open" @click.away="open = false" class="locale-list relative"
        :class="{ '!absolute': open }"
    >
        @foreach ($supportedLocales as $key => $locale)
            <li class="locale relative{{ app()->currentLocale() === $key ? ' selected' : '' }}">
                <a href="{{ $locale['url'] }}" hreflang="{{ $key }}" title="{{ $locale['native'] }}"
                >{{ $flags[$key] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
