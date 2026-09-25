<?php

namespace Step2dev\LazyAdmin\Http\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Step2dev\LazyAdmin\Settings\SettingsRegistry;

#[Layout('lazy::livewire-layout', ['title' => 'Settings'])]
class Page extends Component
{
    #[Url(as: 'section', history: true)]
    public string $section = '';

    /** @var list<array{id:string,label:string,description:?string,component:string,permission:?string,priority:int}> */
    public array $sections = [];

    public function mount(SettingsRegistry $settings): void
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        if (config('lazy.admin.permissions.enforce', true)) {
            abort_unless($user?->can('settings.view'), 403);
        }

        $this->sections = $settings->sectionsFor($user);

        abort_if($this->sections === [], 404);

        if (! in_array($this->section, array_column($this->sections, 'id'), true)) {
            $this->section = $this->sections[0]['id'];
        }
    }

    public function selectSection(string $section): void
    {
        abort_unless(in_array($section, array_column($this->sections, 'id'), true), 404);

        $this->section = $section;
    }

    public function render(): View
    {
        $active = collect($this->sections)->firstWhere('id', $this->section) ?? $this->sections[0];

        return lazyView('lazy::settings.index', [
            'activeSection' => $active,
        ]);
    }
}
