<?php

namespace Step2dev\LazyAdmin\Database\Seeders;

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws \Throwable
     */
    public function run(): void
    {
        $settings = [
            [
                'group' => 'admin',
                'key' => 'name',
                'type' => 'text',
                'value' => 'Lazy Admin',
                'deletable' => 0,
                'is_encrypted' => 0,
                'is_protected' => 0,
            ],
            [
                'group' => 'admin',
                'key' => 'description',
                'type' => 'textarea',
                'value' => 'Lazy Admin is a Laravel Admin Panel for lazy developers',
                'deletable' => 0,
            ],
            [
                'group' => 'admin',
                'key' => 'logo',
                'type' => 'image',
                'value' => '/images/logo.png',
                'deletable' => 0,
            ],
        ];

        foreach ($settings as $setting) {
            setting()->set($setting['group'].'.'.$setting['key'], $setting);
        }

    }
}
