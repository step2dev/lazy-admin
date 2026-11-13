<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Step2dev\LazyAdmin\Support\AdminActivity;

class Setting extends Component
{
    use WithFileUploads;

    public array $settings = [];

    public array $originalSettings = [];

    public mixed $logo = null;

    public mixed $darkLogo = null;

    public mixed $background = null;

    public ?string $currentLogoUrl = null;

    public ?string $currentDarkLogoUrl = null;

    public ?string $currentBackgroundUrl = null;

    public bool $canEdit = false;

    public function mount(): void
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();
        $enforce = (bool) config('lazy.admin.permissions.enforce', true);

        abort_unless(! $enforce || ($user && $user->can('settings.view')), 403);

        $this->canEdit = ! $enforce || (bool) $user->can('settings.edit');

        $this->settings = setting()
            ->all()
            ->where('group', 'admin')
            ->mapWithKeys(fn ($setting) => [$setting->key => $setting->value])
            ->all();

        $this->originalSettings = $this->settings;
        $this->currentLogoUrl = $this->resolveImageUrl(setting('admin.logo'));
        $this->currentDarkLogoUrl = $this->resolveImageUrl(setting('admin.logo_dark'));
        $this->currentBackgroundUrl = $this->resolveImageUrl(setting('admin.background'));
    }

    public function rules(): array
    {
        return [
            'settings.name' => ['nullable', 'string', 'max:255'],
            'settings.description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:1024'],
            'darkLogo' => ['nullable', 'image', 'max:1024'],
            'background' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function save(): void
    {
        abort_unless($this->canEdit, 403);

        $this->validate();

        $old = [
            'name' => $this->originalSettings['name'] ?? null,
            'description' => $this->originalSettings['description'] ?? null,
            'logo' => setting('admin.logo'),
            'logo_dark' => setting('admin.logo_dark'),
            'background' => setting('admin.background'),
        ];

        setting()->set('admin.name', $this->settings['name'] ?? '');
        setting()->set('admin.description', $this->settings['description'] ?? '');

        $logoPath = $old['logo'];
        $darkLogoPath = $old['logo_dark'];
        $backgroundPath = $old['background'];

        if ($this->logo instanceof UploadedFile) {
            $logoPath = $this->logo->store('lazy-admin/settings', 'public');

            setting()->set('admin.logo', $logoPath, 'image');
            if ($old['logo'] !== $darkLogoPath) {
                $this->deleteStoredImage($old['logo']);
            }
            $this->currentLogoUrl = Storage::disk('public')->url($logoPath);
            $this->logo = null;
        }

        if ($this->darkLogo instanceof UploadedFile) {
            $darkLogoPath = $this->darkLogo->store('lazy-admin/settings', 'public');

            setting()->set('admin.logo_dark', $darkLogoPath, 'image');
            if ($old['logo_dark'] !== $logoPath) {
                $this->deleteStoredImage($old['logo_dark']);
            }
            $this->currentDarkLogoUrl = Storage::disk('public')->url($darkLogoPath);
            $this->darkLogo = null;
        }

        if ($this->background instanceof UploadedFile) {
            $backgroundPath = $this->background->store('lazy-admin/settings', 'public');

            setting()->set('admin.background', $backgroundPath, 'image');
            $this->deleteStoredImage($old['background']);
            $this->currentBackgroundUrl = Storage::disk('public')->url($backgroundPath);
            $this->background = null;
        }

        $this->originalSettings = $this->settings;

        AdminActivity::log(
            event: 'updated',
            description: 'Admin settings updated',
            old: $old,
            new: [
                'name' => $this->settings['name'] ?? '',
                'description' => $this->settings['description'] ?? '',
                'logo' => $logoPath,
                'logo_dark' => $darkLogoPath,
                'background' => $backgroundPath,
            ],
        );
    }

    public function render(): View
    {
        return lazyView('lazy::pages.settings.settings');
    }

    private function resolveImageUrl(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        if (
            str_starts_with($value, 'http://')
            || str_starts_with($value, 'https://')
            || str_starts_with($value, '/')
        ) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }

    private function deleteStoredImage(mixed $value): void
    {
        if (
            ! is_string($value)
            || $value === ''
            || str_starts_with($value, 'http://')
            || str_starts_with($value, 'https://')
            || str_starts_with($value, '/')
        ) {
            return;
        }

        Storage::disk('public')->delete($value);
    }
}
