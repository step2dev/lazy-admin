<?php

namespace Step2dev\LazyAdmin\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! confirm(
            label: 'Do you want create an admin user?',
            default: true,
            hint: 'This will create an admin user with the provided email and password.'
        )) {
            return Command::SUCCESS;
        }

        $email = text(
            label: 'What is your email?',
            placeholder: 'E.g. admin@admin.com',
            default: $this->option('email') ?? 'admin@admin.com',
            required: true,
            validate: fn (string $value) => match (true) {
                ! filter_var($value, FILTER_VALIDATE_EMAIL) => 'The email must be a valid email address.',
                User::where('email', $value)->exists()      => 'The email has already been taken.',
                default                                     => null
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
                default            => null
            },
            hint: 'Minimum 8 characters.'
        );

        User::create([
            'name'     => 'Admin',
            'email'    => $email,
            'password' => bcrypt($password),
        ]);

        $this->info('Admin user created successfully.');

        return Command::SUCCESS;
    }
}
