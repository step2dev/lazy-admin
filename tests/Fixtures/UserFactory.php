<?php

namespace Step2dev\LazyAdmin\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    /** @return array{name: string, email: string, password: string} */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'unused-test-password',
        ];
    }
}
