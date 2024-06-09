<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class LanguageSwitcher extends Component
{
    protected array $locales;

    protected mixed $flags;

    public function __construct()
    {
        $this->locales = lazyLocalization()->getSupportedLocalesInfo();

        $this->flags = Cache::rememberForever('flags', fn () => collect(config('lazy.localization.locales'))
            ->filter(fn ($value) => $value['regional'] ?? null)
            ->map(fn ($locale, $key) => (string) str($locale['regional'])
                ->afterLast('_')
                ->upper()
                ->split('//u')
                ->filter()
                ->map(fn ($code) => mb_convert_encoding('&#'.ord($code) + 0x1F1A5 .';', 'UTF-8',
                    'HTML-ENTITIES'))
                ->implode('')
            )
            ->toArray()
        );
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): Factory|View
    {
        return view('lazy::language-switcher', [
            'supportedLocales' => $this->locales,
            'flags' => $this->flags,
        ]);
    }
}
