<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Search;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Step2dev\LazyAdmin\Search\SearchRegistry;

#[Layout('lazy::livewire-layout', ['title' => 'lazy-admin::search.title'])]
class Page extends Component
{
    #[Url(as: 'q', history: true)]
    public string $query = '';

    public function render(): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
        $query = trim($this->query);

        return lazyView('lazy::search.index', [
            'query' => $query,
            'results' => mb_strlen($query) >= 2
                ? app(SearchRegistry::class)->search($query, $user)
                : [],
        ]);
    }
}
