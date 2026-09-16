<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Livewire\Component;
use Livewire\WithFileUploads;

class Setting extends Component
{
    use WithFileUploads;

    public array $settings = [];

    public mixed $logo = null;

    public function mount(): void
    {
        $this->settings = setting()
            ->all()
            ->where('group', 'admin')
            ->mapWithKeys(fn ($setting) => [$setting->key => $setting->value])
            ->all();
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
        $this->validate();

        setting()->set('admin.name', $this->settings['name'] ?? '');
        setting()->set('admin.description', $this->settings['description'] ?? '');

        if ($this->logo instanceof UploadedFile) {
            $path = $this->logo->store('', 'public');

            setting()->set('admin.logo', $path, 'image');
            $this->settings['logo'] = $path;
            $this->logo = null;
        }
    }

    public function render(): View
    {
        return view('lazy::pages.settings.settings');
    }
}
