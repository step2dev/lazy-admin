<?php

namespace Step2dev\LazyAdmin\Navigation;

use Illuminate\Support\Facades\Route;

class MenuItemPresenter
{
    public function presentMany(iterable $items): array
    {
        $presented = [];

        foreach ($items as $item) {
            $presented[] = $this->present((array) $item);
        }

        return $presented;
    }

    public function present(array $item): array
    {
        if (isset($item['group'])) {
            return [
                ...$item,
                'isGroup' => true,
                'groupLabel' => __((string) $item['group']),
            ];
        }

        $target = $item['url'] ?? $item['route'] ?? null;
        $isNamedRoute = is_string($target) && Route::has($target);
        $href = $isNamedRoute
            ? route($target, $item['parameters'] ?? [])
            : ($target ?? '#');

        $children = $this->presentMany($item['children'] ?? []);
        $active = (bool) (
            $item['active']
            ?? ($isNamedRoute && request()->routeIs($target))
        );

        $external = ! $isNamedRoute
            && is_string($target)
            && preg_match('/^https?:\/\//i', $target) === 1
            && parse_url($target, PHP_URL_HOST) !== request()->getHost();

        $linkTarget = $item['target'] ?? ($external ? '_blank' : null);

        [$iconView, $svgIcon] = $this->resolveIcon($item);
        $badge = $item['badge'] ?? null;

        return [
            ...$item,
            'isGroup' => false,
            'target' => $target,
            'href' => $href,
            'hasChildren' => $children !== [],
            'children' => $children,
            'active' => $active,
            'labelText' => __((string) ($item['label'] ?? '')),
            'linkTarget' => $linkTarget,
            'external' => $external,
            'navClass' => implode(' ', array_filter([
                'nav-link flex gap-2 transition-all transform',
                $active ? 'active' : null,
            ])),
            'parentNavClass' => implode(' ', array_filter([
                'nav-link flex items-center gap-2 transition-all transform',
                $active ? 'active' : null,
            ])),
            'iconView' => $iconView,
            'iconViewExists' => is_string($iconView) && view()->exists($iconView),
            'svgIcon' => $svgIcon,
            'rawSvgIcon' => $svgIcon !== null,
            'hasCssIcon' => is_string($item['icon'] ?? null) && trim((string) $item['icon']) !== '',
            'badgeLabel' => $badge !== null
                ? (is_numeric($badge) && (int) $badge > 99 ? '99+' : (string) $badge)
                : null,
        ];
    }

    private function resolveIcon(array $item): array
    {
        $iconView = $item['icon_view'] ?? null;
        $icon = $item['icon'] ?? null;

        if (is_string($iconView) && ! str_contains($iconView, '::')) {
            $lazyIconView = 'lazy::'.$iconView;

            if (view()->exists($lazyIconView)) {
                $iconView = $lazyIconView;
            }
        }

        $svgIcon = null;

        if (is_string($icon)) {
            $normalizedIcon = ltrim($icon, "\xEF\xBB\xBF \t\n\r\0\x0B");
            $normalizedIcon = preg_replace('/^<\?xml[^?]*\?>\s*/i', '', $normalizedIcon) ?? $normalizedIcon;
            $normalizedIcon = preg_replace('/^<!DOCTYPE[^>]*>\s*/i', '', $normalizedIcon) ?? $normalizedIcon;

            if (preg_match('/^<svg\b/i', $normalizedIcon) === 1) {
                $svgIcon = $normalizedIcon;
            }
        }

        return [$iconView, $svgIcon];
    }
}
