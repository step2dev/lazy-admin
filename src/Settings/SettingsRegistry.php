<?php

namespace Step2dev\LazyAdmin\Settings;

class SettingsRegistry
{
    /** @var array<string, array{id:string,label:string,description:?string,component:string,permission:?string,priority:int}> */
    private array $sections = [];

    public function registerSection(
        string $id,
        string $label,
        string $component,
        ?string $permission = 'settings.view',
        int $priority = 100,
        ?string $description = null,
    ): void {
        $this->sections[$id] = compact('id', 'label', 'description', 'component', 'permission', 'priority');
    }

    /** @return list<array{id:string,label:string,description:?string,component:string,permission:?string,priority:int}> */
    public function sectionsFor(?object $user): array
    {
        $sections = array_values(array_filter(
            $this->sections,
            fn (array $section): bool => $this->allowed($user, $section['permission']),
        ));

        usort($sections, static fn (array $a, array $b): int => $a['priority'] <=> $b['priority']);

        return $sections;
    }

    private function allowed(?object $user, ?string $permission): bool
    {
        if ($permission === null || ! config('lazy.admin.permissions.enforce', true)) {
            return true;
        }

        return $user !== null && method_exists($user, 'can') && (bool) $user->can($permission);
    }
}
