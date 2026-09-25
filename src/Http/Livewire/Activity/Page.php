<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Activity;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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

        $activities = $modelClass::query()
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

        $activities->setCollection(
            $activities->getCollection()->map(fn (Activity $activity): array => $this->present($activity))
        );

        return $activities;
    }

    /**
     * @return array{id: string, when: string, user: string, ip: string, event: string, description: string, has_changes: bool, changes: string}
     */
    private function present(Activity $activity): array
    {
        $properties = $activity->properties instanceof Collection
            ? $activity->properties
            : collect($activity->properties);

        $causer = data_get($activity->causer, 'name');

        if ($causer === null || $causer === '') {
            $causer = data_get($activity->causer, 'email');
        }

        $changes = json_encode([
            'old' => $properties->get('old', []),
            'new' => $properties->get('new', []),
            'ip' => $properties->get('ip'),
            'user_agent' => $properties->get('user_agent'),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return [
            'id' => $this->displayValue($activity->getKey(), ''),
            'when' => $activity->created_at?->format('Y-m-d H:i') ?? '—',
            'user' => $this->displayValue($causer),
            'ip' => $this->displayValue($properties->get('ip'), ''),
            'event' => $this->displayValue($activity->event),
            'description' => $this->displayValue($activity->description),
            'has_changes' => $properties->has('old') || $properties->has('new'),
            'changes' => is_string($changes) ? $changes : '{}',
        ];
    }

    private function displayValue(mixed $value, string $fallback = '—'): string
    {
        if ($value === null || $value === '') {
            return $fallback;
        }

        if (is_string($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return is_string($encoded) ? $encoded : $fallback;
    }
}
