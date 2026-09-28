<?php

namespace Step2dev\LazyAdmin\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class MenuItem extends Component
{
    public string $href;

    public bool $hasChildren;

    public bool $active;

    public string $label;

    public ?string $linkTarget;

    public function __construct(public array $item)
    {
        $target = $item['url'] ?? $item['route'] ?? null;

        $this->href = $target && Route::has($target)
            ? route($target, $item['parameters'] ?? [])
            : ($target ?? '#');

        $this->hasChildren = ! empty($item['children']);

        $this->active = (bool) ($item['active']
            ?? ($target && Route::has($target) && request()->routeIs($target)));

        $this->label = __($item['label'] ?? '');

        $isNamedRoute = is_string($target) && Route::has($target);
        $external = ! $isNamedRoute
            && is_string($target)
            && preg_match('/^https?:\/\//i', $target) === 1
            && parse_url($target, PHP_URL_HOST) !== request()->getHost();

        $this->linkTarget = $item['target'] ?? ($external ? '_blank' : null);
    }

    public function render(): View
    {
        return lazyView('lazy::menu-item');
    }
}
