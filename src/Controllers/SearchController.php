<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Step2dev\LazyAdmin\Search\SearchRegistry;

class SearchController extends Controller
{
    public function __construct(private readonly SearchRegistry $search) {}

    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = trim((string) ($validated['q'] ?? ''));
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        return lazyView('lazy::search.index', [
            'query' => $query,
            'results' => mb_strlen($query) >= 2
                ? $this->search->search($query, $user)
                : [],
        ]);
    }
}
