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

    public ?string $currentLogoUrl = null;

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
        $this->currentLogoUrl = $this->resolveLogoUrl(setting('admin.logo'));
    }

    public function rules(): array
    {
        return [
            'settings.name' => ['nullable', 'string', 'max:255'],
            'settings.description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:1024'],
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
        ];

        setting()->set('admin.name', $this->settings['name'] ?? '');
        setting()->set('admin.description', $this->settings['description'] ?? '');

        $logoPath = $old['logo'];

        if ($this->logo instanceof UploadedFile) {
            $logoPath = $this->logo->store('lazy-admin/settings', 'public');

            setting()->set('admin.logo', $logoPath, 'image');
            $this->deleteStoredLogo($old['logo']);
            $this->currentLogoUrl = Storage::disk('public')->url($logoPath);
            $this->logo = null;
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
            ],
        );
    }

    public function render(): View
    {
        return lazyView('lazy::pages.settings.settings');
    }

    private function resolveLogoUrl(mixed $value): ?string
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

    private function deleteStoredLogo(mixed $value): void
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
