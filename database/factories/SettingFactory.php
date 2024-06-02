<?php

namespace Step2dev\LazyAdmin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory{
    public function definition(): array
    {
        return [
            'group' => $this->faker->word,
            'key' => $this->faker->word,
            'type' => $this->faker->word,
            'value' => $this->faker->word,
            'is_protected' => $this->faker->boolean,
            'is_encrypted' => $this->faker->boolean,
            'deletable' => $this->faker->boolean,
        ];
    }
}
