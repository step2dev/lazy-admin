<?php

use Illuminate\Support\Facades\Storage;
use Step2dev\LazyAdmin\Support\Branding;

it('falls back to the primary logo when the dark logo is unset', function (mixed $dark): void {
    $store = Mockery::mock();
    $store->shouldReceive('get')->with('admin.logo', null)->andReturn('/storage/primary.png');
    $store->shouldReceive('get')->with('admin.logo_dark', null)->andReturn($dark);
    app()->instance('setting', $store);

    expect(Branding::logoUrl())->toBe('/storage/primary.png')
        ->and(Branding::logoUrl(true))->toBe('/storage/primary.png');
})->with([null, '', false]);

it('resolves the dark logo from the public disk without changing the primary logo', function (): void {
    Storage::fake('public');
    $store = Mockery::mock();
    $store->shouldReceive('get')->with('admin.logo', null)->andReturn('lazy-admin/settings/light.png');
    $store->shouldReceive('get')->with('admin.logo_dark', null)->andReturn('lazy-admin/settings/dark.png');
    app()->instance('setting', $store);

    expect(Branding::logoUrl())->toBe(Storage::disk('public')->url('lazy-admin/settings/light.png'))
        ->and(Branding::logoUrl(true))->toBe(Storage::disk('public')->url('lazy-admin/settings/dark.png'));
});

it('preserves the configured primary logo fallback', function (): void {
    $store = Mockery::mock();
    $store->shouldReceive('get')->with('admin.logo', '/brand.svg')->andReturn('/brand.svg');
    $store->shouldReceive('get')->with('admin.logo_dark', null)->andReturn(null);
    app()->instance('setting', $store);

    expect(Branding::logoUrl(true, '/brand.svg'))->toBe('/brand.svg');
});

it('renders one accessible image and observes live theme changes only when needed', function (): void {
    $data = ['logoUrl' => '/light.png', 'darkLogoUrl' => '/dark.png', 'logoAlt' => 'Example', 'logoClass' => 'h-10'];
    $html = view('lazy::brand-logo', $data)->render();

    expect(substr_count($html, '<img'))->toBe(1);
    expect($html)->toContain('src="/light.png"', 'alt="Example"', 'MutationObserver', "['data-theme']", 'disconnect');

    $html = view('lazy::brand-logo', array_replace($data, ['darkLogoUrl' => '/light.png']))->render();
    expect($html)->toContain('src="/light.png"')->not->toContain('MutationObserver');
});
