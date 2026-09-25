<?php

namespace Step2dev\LazyAdmin\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class CreateAdminCommand extends Command
{
    protected $signature = 'make:admin {--email=} {--password=}';

    protected $description = 'Create a Lazy Admin super administrator';

    public function handle(): int
    {
        if (! confirm(
            label: 'Do you want create an admin user?',
            default: true,
            hint: 'This will create a super administrator with the provided email and password.'
        )) {
            return Command::SUCCESS;
        }

        $tables = (array) config('permission.table_names', []);

        if (
            empty($tables['roles'])
            || empty($tables['permissions'])
            || ! Schema::hasTable($tables['roles'])
            || ! Schema::hasTable($tables['permissions'])
        ) {
            $this->error('Permission tables are not installed. Publish the permission migrations and run php artisan migrate first.');

            return Command::FAILURE;
        }

        $modelClass = $this->userModel();
        $userModel = new $modelClass;

        if (! method_exists($userModel, 'assignRole')) {
            $this->error('The configured user model must use Step2dev\\LazyAdmin\\Authorization\\HasLazyAdminPermissions or Spatie\\Permission\\Traits\\HasRoles.');

            return Command::FAILURE;
        }

        $email = text(
            label: 'What is your email?',
            placeholder: 'E.g. admin@admin.com',
            default: $this->option('email') ?? 'admin@admin.com',
            required: true,
            validate: fn (string $value) => match (true) {
                ! filter_var($value, FILTER_VALIDATE_EMAIL) => 'The email must be a valid email address.',
                $modelClass::query()->where('email', $value)->exists() => 'The email has already been taken.',
                default => null
            },
            hint: 'Login email for admin user'
        );

        $password = text(
            label: 'What is your password?',
            placeholder: 'E.g. password',
            default: $this->option('password') ?? 'password',
            required: true,
            validate: fn (string $value) => match (true) {
                strlen($value) < 8 => 'The password must be at least 8 characters.',
                default => null
            },
            hint: 'Minimum 8 characters.'
        );

        /** @var Model $user */
        $user = $modelClass::query()->create([
            'name' => 'Admin',
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        app(AuthorizationManager::class)->seedDefaults();

        /** @phpstan-ignore-next-line The method is guaranteed by the runtime HasRoles check above. */
        $user->assignRole((string) config('lazy.admin.permissions.super_admin_role', 'superadmin'));

        $this->info('Super administrator created successfully.');

        return Command::SUCCESS;
    }

    /**
     * @return class-string<Model>
     */
    private function userModel(): string
    {
        $guard = (string) config('lazy.auth.guard', 'web');
        $provider = config('lazy.auth.provider') ?: config("auth.guards.{$guard}.provider", 'users');
        $modelClass = config("auth.providers.{$provider}.model");

        if (! is_string($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            throw new RuntimeException('Lazy Admin could not resolve the configured authentication user model.');
        }

        return $modelClass;
    }
}
