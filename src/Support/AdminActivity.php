<?php

namespace Step2dev\LazyAdmin\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

class AdminActivity
{
    public static function log(
        string $event,
        string $description,
        ?object $subject = null,
        array $old = [],
        array $new = [],
        array $properties = [],
    ): void {
        $activityModel = config('activitylog.activity_model', Activity::class);

        if (! is_string($activityModel) || ! is_subclass_of($activityModel, Model::class)) {
            return;
        }

        $table = (new $activityModel)->getTable();

        if (! Schema::hasTable($table)) {
            return;
        }

        $properties = array_filter([
            ...$properties,
            'old' => self::sanitize($old),
            'new' => self::sanitize($new),
            'ip' => app()->bound('request') ? request()->ip() : null,
            'user_agent' => app()->bound('request') ? request()->userAgent() : null,
        ], static fn (mixed $value): bool => $value !== [] && $value !== null && $value !== '');

        $logger = activity('lazy-admin')
            ->event($event)
            ->withProperties($properties);

        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        if ($user instanceof Model) {
            $logger->causedBy($user);
        }

        if ($subject instanceof Model) {
            $logger->performedOn($subject);
        }

        $logger->log($description);
    }

    private static function sanitize(array $data): array
    {
        foreach (['password', 'password_confirmation', 'remember_token', 'token', 'secret'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
