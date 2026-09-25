<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Activity;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Layout('lazy::livewire-layout', ['title' => 'Activity log'])]
class Page extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $event = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEvent(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        if (config('lazy.admin.permissions.enforce', true)) {
            abort_unless($user?->can('activity.view'), 403);
        }

        return lazyView('lazy::activity.index', [
            'activities' => $this->activities(),
        ]);
    }

    private function activities(): LengthAwarePaginatorContract
    {
        $modelClass = config('activitylog.activity_model', Activity::class);

        if (! is_string($modelClass) || ! is_a($modelClass, Activity::class, true)) {
            $modelClass = Activity::class;
        }

        $model = new $modelClass;

        if (! Schema::hasTable($model->getTable())) {
            return new LengthAwarePaginator([], 0, 25);
        }

        $search = trim($this->search);
        $event = trim($this->event);

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
            ->paginate(25);
    }
}
