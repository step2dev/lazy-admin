<?php

namespace Step2dev\LazyAdmin\Search;

use Closure;
use Stringable;

class SearchRegistry
{
    /** @var array<string, array{id:string,provider:Closure,permission:?string,priority:int}> */
    private array $providers = [];

    public function register(
        string $id,
        Closure $provider,
        ?string $permission = null,
        int $priority = 100,
    ): void {
        $this->providers[$id] = compact('id', 'provider', 'permission', 'priority');
    }

    /**
     * @return list<array{provider:string,title:string,url:string,description:?string,type:?string}>
     */
    public function search(string $query, ?object $user, int $limitPerProvider = 8): array
    {
        $providers = array_values(array_filter(
            $this->providers,
            fn (array $provider): bool => $this->allowed($user, $provider['permission']),
        ));

        usort($providers, static fn (array $a, array $b): int => $a['priority'] <=> $b['priority']);

        $results = [];

        foreach ($providers as $definition) {
            $items = ($definition['provider'])($query, $limitPerProvider);

            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $title = $this->stringValue($item['title'] ?? null);
                $url = $this->stringValue($item['url'] ?? null);

                if ($title === null || $url === null) {
                    continue;
                }

                $results[] = [
                    'provider' => $definition['id'],
                    'title' => $title,
                    'url' => $url,
                    'description' => $this->stringValue($item['description'] ?? null),
                    'type' => $this->stringValue($item['type'] ?? null),
                ];
            }
        }

        return $results;
    }

    private function stringValue(mixed $value): ?string
    {
        if (is_string($value)) {
            $value = trim($value);

            return $value !== '' ? $value : null;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if ($value instanceof Stringable) {
            $value = trim((string) $value);

            return $value !== '' ? $value : null;
        }

        if (! is_array($value)) {
            return null;
        }

        $locales = array_values(array_unique(array_filter([
            app()->getLocale(),
            config('app.fallback_locale'),
            'en',
        ], 'is_string')));

        foreach ($locales as $locale) {
            $candidate = $value[$locale] ?? null;

            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        foreach ($value as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return null;
    }

    private function allowed(?object $user, ?string $permission): bool
    {
        if ($permission === null || ! config('lazy.admin.permissions.enforce', true)) {
            return true;
        }

        return $user !== null && method_exists($user, 'can') && (bool) $user->can($permission);
    }
}
