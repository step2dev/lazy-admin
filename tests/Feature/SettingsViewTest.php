<?php

use Livewire\Livewire;
use Step2dev\LazyAdmin\Http\Livewire\Settings\Setting;

beforeEach(function (): void {
    config()->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
    config()->set('lazy.admin.permissions.enforce', false);
    $this->settingsStore = Mockery::mock();
    $this->settingsStore->shouldReceive('all')->andReturn(collect([
        (object) ['group' => 'admin', 'key' => 'name', 'value' => 'Example site'],
        (object) ['group' => 'admin', 'key' => 'description', 'value' => 'Example description'],
    ]));
    $this->app->instance('setting', $this->settingsStore);
});

it('renders accessible settings fields and the empty logo state', function (): void {
    $this->settingsStore->shouldReceive('get')->with('admin.logo', null)->andReturn(null);

    Livewire::test(Setting::class)
        ->assertOk()
        ->assertSet('settings.name', 'Example site')
        ->assertSee('Site details')
        ->assertSee('No logo uploaded')
        ->assertSee('aria-label="Name"', false)
        ->assertSee('aria-label="Description"', false)
        ->assertSee('aria-label="Upload logo"', false)
        ->assertSee('wire:submit="save"', false)
        ->assertSee('Save changes');
});

it('renders the current logo', function (): void {
    $this->settingsStore->shouldReceive('get')->with('admin.logo', null)->andReturn('/storage/logo.png');

    Livewire::test(Setting::class)
        ->assertSee('src="/storage/logo.png"', false)
        ->assertDontSee('No logo uploaded');
});

it('saves the settings through the form action', function (): void {
    $this->settingsStore->shouldReceive('get')->with('admin.logo', null)->andReturn(null);
    $this->settingsStore->shouldReceive('set')->once()->with('admin.name', 'Updated site');
    $this->settingsStore->shouldReceive('set')->once()->with('admin.description', 'Updated description');

    Livewire::test(Setting::class)
        ->set('settings.name', 'Updated site')
        ->set('settings.description', 'Updated description')
        ->call('save')->assertHasNoErrors();
});

it('displays validation errors next to the fields', function (): void {
    $this->settingsStore->shouldReceive('get')->with('admin.logo', null)->andReturn(null);
    $this->settingsStore->shouldNotReceive('set');

    Livewire::test(Setting::class)
        ->set('settings.name', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['settings.name' => 'max'])
        ->assertSee(__('validation.max.string', ['attribute' => 'settings.name', 'max' => 255]));
});
