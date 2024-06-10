<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Step2dev\LazyAdmin\Services\SettingService;

class Setting extends Component
{
    /**
     * @var mixed|SettingService
     */
    public mixed $settings;

    /**
     * @var mixed|SettingService
     */
    protected mixed $settingsSettingService;

    public function mount(): void
    {
        $this->settingsSettingService = setting();
        $this->settings = $this->settingsSettingService->all()->mapWithKeys(fn ($setting) => [$setting->key => $setting->value]);
    }

    public function render(): View
    {
        return view('lazy::pages.settings.settings');
    }
}
