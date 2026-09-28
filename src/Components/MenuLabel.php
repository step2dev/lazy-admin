<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MenuLabel extends Component
{
    public ?string $iconView;

    public mixed $icon;

    public ?string $svgIcon = null;

    public bool $rawSvgIcon = false;

    public ?string $badge = null;

    public string $label;

    public function __construct(public array $item)
    {
        $this->iconView = $item['icon_view'] ?? null;
        $this->icon = $item['icon'] ?? null;
        $this->label = __($item['label'] ?? '');

        if (is_string($this->iconView) && ! str_contains($this->iconView, '::')) {
            $lazyIconView = 'lazy::'.$this->iconView;

            if (view()->exists($lazyIconView)) {
                $this->iconView = $lazyIconView;
            }
        }

        if (is_string($this->icon)) {
            $normalizedIcon = ltrim($this->icon, "\xEF\xBB\xBF \t\n\r\0\x0B");
            $normalizedIcon = preg_replace('/^<\?xml[^?]*\?>\s*/i', '', $normalizedIcon) ?? $normalizedIcon;
            $normalizedIcon = preg_replace('/^<!DOCTYPE[^>]*>\s*/i', '', $normalizedIcon) ?? $normalizedIcon;

            if (preg_match('/^<svg\b/i', $normalizedIcon) === 1) {
                $this->svgIcon = $normalizedIcon;
                $this->rawSvgIcon = true;
            }
        }

        if (array_key_exists('badge', $item)) {
            $value = $item['badge'];

            if ($value !== null && $value !== '') {
                $this->badge = is_numeric($value) && (int) $value > 99
                    ? '99+'
                    : (string) $value;
            }
        }
    }

    public function render(): View
    {
        return lazyView('lazy::menu-label');
    }
}
