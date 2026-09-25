<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Step2dev\LazyAdmin\Http\Livewire\Activity\Page;

it('normalizes array activity values before rendering', function (): void {
    $causer = new class extends Model
    {
        protected $guarded = [];

        public function getNameAttribute(): array
        {
            return ['uk' => 'Адмін', 'en' => 'Admin'];
        }
    };

    $causer->email = 'admin@example.com';

    $activity = new Activity;
    $activity->setAttribute('id', 1);
    $activity->setAttribute('event', ['name' => 'updated']);
    $activity->setAttribute('description', ['uk' => 'Оновлено', 'en' => 'Updated']);
    $activity->setAttribute('created_at', Carbon::parse('2026-09-25 22:00:00'));
    $activity->setAttribute('properties', [
        'ip' => ['127.0.0.1'],
        'old' => ['slug' => 'terms'],
        'new' => ['slug' => 'terms2'],
    ]);
    $activity->setRelation('causer', $causer);

    $component = new Page;
    $method = new ReflectionMethod($component, 'present');
    $method->setAccessible(true);

    $row = $method->invoke($component, $activity);

    expect($row)
        ->toBeArray()
        ->and($row['user'])->toContain('"uk":"Адмін"')
        ->and($row['ip'])->toBe('["127.0.0.1"]')
        ->and($row['event'])->toContain('"name":"updated"')
        ->and($row['description'])->toContain('"uk":"Оновлено"')
        ->and($row['changes'])->toContain('"terms2"');

    $activities = new LengthAwarePaginator([$row], 1, 25, 1, [
        'path' => '/admin/activity',
    ]);

    expect(__('lazy-admin::activity.columns.user'))->toBe('User');

    $html = view('lazy::activity.index', compact('activities'))->render();

    expect($html)
        ->toContain('&quot;uk&quot;')
        ->toContain('terms2')
        ->toContain('updated')
        ->toContain('User');
});
