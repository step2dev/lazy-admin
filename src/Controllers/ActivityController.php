<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        if (config('lazy.admin.permissions.enforce', true)) {
            abort_unless($user && $user->can('activity.view'), 403);
        }

        $search = trim((string) $request->query('search', ''));
        $event = trim((string) $request->query('event', ''));

        return lazyView('lazy::activity.index', [
            'activities' => $this->activities($search, $event),
            'search' => $search,
            'event' => $event,
        ]);
    }

    private function activities(string $search, string $event): LengthAwarePaginatorContract
    {
        $modelClass = config('activitylog.activity_model', Activity::class);

        if (! is_string($modelClass) || ! is_a($modelClass, Activity::class, true)) {
            $modelClass = Activity::class;
        }

        $model = new $modelClass;

        if (! Schema::hasTable($model->getTable())) {
            return new LengthAwarePaginator([], 0, 25);
        }

        return $modelClass::query()
            ->with(['causer', 'subject'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('description', 'like', '%'.$search.'%')
                        ->orWhere('log_name', 'like', '%'.$search.'%');
                });
            })
            ->when($event !== '', fn ($query) => $query->where('event', $event))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();
    }
}
