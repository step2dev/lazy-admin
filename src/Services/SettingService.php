<?php

namespace Step2dev\LazyAdmin\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Step2dev\LazyAdmin\Models\Setting;
use Throwable;

class SettingService
{
    public const CACHE_KEY = 'cache-settings';
    private Collection $settings;

    public function __construct()
    {
        $this->settings = Collection::make();
    }

    public function init(): static
    {
        if ($this->settings->isEmpty()) {
            $this->settings = cache()->rememberForever(self::CACHE_KEY, fn() => Setting::get());
        }

        return $this;
    }

    public function getSettings(): Collection
    {
        return $this->init()->settings;
    }

    /**
     * @throws Throwable
     * @noinspection MethodVisibilityInspection
     */
    protected function getKeyAndGroup(string $key): array
    {
        [$key, $group] = array_pad(
            array_reverse(explode('.', $key, 2)),
            2, null);

        $key = trim((string) $key);

        throw_if(! $key, new InvalidArgumentException('Key cannot be null or empty example: "app.name" or "name" received: "'.$group.'.'.$key.'"'));

        $group = trim((string) $group) ?: 'default';

        return compact('key', 'group');
    }

    public function get(string $key, mixed $default = null): string|null
    {
        return $this->getConfig($key)?->value ?? $default;
    }

    public function getConfig(string $key): Setting|null
    {
        try {
            ['group' => $group, 'key' => $key] = $this->getKeyAndGroup($key);
        } catch (InvalidArgumentException|Throwable $e) {
            Log::error(__METHOD__.' '.$e->getMessage());

            return null;
        }

        return $this->getSettings()
            ->where('group', $group)
            ->firstWhere('key', $key);
    }

    /**
     * @throws Throwable
     */
    public function set(string $key, mixed $data, string|null $type = null): Setting
    {
        if ($setting = $this->getConfig($key)) {
            $this->update($setting, $data);
        } else {
            $setting = $this->createIfNotExists($key, $data, $type);
        }

        return $setting;
    }

    public function update(Setting $setting, mixed $data): Setting
    {
        $setting->update(['value' => $data]);

        $this->clearCache();

        return $setting;
    }

    /**
     * @throws Throwable
     */
    public function createIfNotExists(string $key, mixed $data, string|null $type = null): Setting
    {
        $setting = Setting::firstOrCreate($this->getKeyAndGroup($key), [
            'type'  => $type ?? 'string',
            'value' => $data,
        ]);

        $this->clearCache();

        return $setting;
    }

    /**
     * @throws Throwable
     */
    public function create(string $key, array $data, string|null $type = null): Setting
    {
        $setting = Setting::create([
            ...$this->getKeyAndGroup($key),
            'type'  => $type ?? 'string',
            'value' => $data,
        ]);

        $this->clearCache();

        return $setting;
    }

    private function clearCache(bool $refresh = true): void
    {
        cache()->forget(self::CACHE_KEY);

        if ($refresh) {
            $this->init();
        }
    }

    public function delete(string $key): void
    {
        $setting = $this->getConfig($key);

        $setting?->delete();

        $this->clearCache();
    }

    public function getTypes(): array
    {
        return [
            'string',
            'integer',
            'float',
            'boolean',
            'array',
            'json',
            'image',
            'richtext',
            'textarea',
        ];
    }
}
