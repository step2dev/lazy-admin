<?php

namespace Step2dev\LazyAdmin\Search;

use Closure;

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
                if (! is_array($item) || empty($item['title']) || empty($item['url'])) {
                    continue;
                }

                $results[] = [
                    'provider' => $definition['id'],
                    'title' => (string) $item['title'],
                    'url' => (string) $item['url'],
                    'description' => isset($item['description']) ? (string) $item['description'] : null,
                    'type' => isset($item['type']) ? (string) $item['type'] : null,
                ];
            }
        }

        return $results;
    }

    private function allowed(?object $user, ?string $permission): bool
    {
        if ($permission === null || ! config('lazy.admin.permissions.enforce', true)) {
            return true;
        }

        return $user !== null && method_exists($user, 'can') && (bool) $user->can($permission);
    }
}
