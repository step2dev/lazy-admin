<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Search;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Step2dev\LazyAdmin\Search\SearchRegistry;

class HeaderSearch extends Component
{
    public string $query = '';

    /** @var list<array{provider:string,title:string,url:string,description:?string,type:?string}> */
    public array $results = [];

    public function updatedQuery(): void
    {
        $query = trim($this->query);

        if (mb_strlen($query) < 2) {
            $this->results = [];

            return;
        }

        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
        $this->results = app(SearchRegistry::class)->search($query, $user, 6);
    }

    public function render(): View
    {
        return lazyView('lazy::search.header');
    }
}
