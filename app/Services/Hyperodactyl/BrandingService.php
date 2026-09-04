<?php

namespace Hyperodactyl\Services\Hyperodactyl;

use Hyperodactyl\Models\HyperSetting;
use Illuminate\Support\Facades\Cache;

/**
 * Handles reading/writing branding, theme and layout customization settings
 * for the panel. Values are cached indefinitely and the cache is flushed any
 * time a value is written.
 */
class BrandingService
{
    public const CACHE_KEY = 'hyper.settings';

    /**
     * Default values used when a setting has never been persisted.
     */
    public const DEFAULTS = [
        'site_name' => 'HyperNet',
        'site_short_name' => 'HN',
        'logo_url' => '/assets/img/hypernet-logo.png',
        'favicon_url' => '/favicons/favicon.ico',
        'social_description' => 'A modern game server management panel.',
        'meta_keywords' => 'game server, hosting, panel, hypernet, hyperodactyl, lxc',
        'og_image_url' => '/assets/img/hypernet-og.png',
        'color_primary' => '#5b8cff',
        'color_accent' => '#5b8cff',
        'color_background' => '#0b0e14',
        'color_surface' => '#141821',
        'color_text' => '#f5f7fa',
        'color_danger' => '#e6485d',
        'color_success' => '#3bd671',
        'border_radius' => '0.75rem',
        'font' => 'Inter, sans-serif',
        'sidebar_layout' => [
            ['key' => 'overview', 'label' => 'Overview', 'icon' => 'home', 'enabled' => true, 'order' => 0],
            ['key' => 'servers', 'label' => 'Servers', 'icon' => 'server', 'enabled' => true, 'order' => 1],
            ['key' => 'account', 'label' => 'Account', 'icon' => 'user', 'enabled' => true, 'order' => 2],
        ],
        'dashboard_widgets' => [
            ['key' => 'server-list', 'enabled' => true, 'order' => 0],
            ['key' => 'announcements', 'enabled' => true, 'order' => 1],
        ],
        'allow_user_theme_override' => false,
        'user_customizable_keys' => ['color_accent'],
    ];

    /**
     * Keys whose values are stored/returned as decoded JSON (arrays).
     */
    protected const JSON_KEYS = ['sidebar_layout', 'dashboard_widgets', 'user_customizable_keys'];

    /**
     * Keys whose values are stored/returned as booleans.
     */
    protected const BOOLEAN_KEYS = ['allow_user_theme_override'];

    /**
     * Keys that map onto CSS custom properties for theming.
     */
    protected const COLOR_KEYS = [
        'color_primary',
        'color_accent',
        'color_background',
        'color_surface',
        'color_text',
        'color_danger',
        'color_success',
    ];

    /**
     * Return every branding/theme/layout setting, typed and merged with defaults.
     */
    public function all(): array
    {
        $stored = Cache::rememberForever(self::CACHE_KEY, function () {
            return HyperSetting::query()->pluck('value', 'key')->toArray();
        });

        $result = [];
        foreach (self::DEFAULTS as $key => $default) {
            $result[$key] = array_key_exists($key, $stored) ? $this->cast($key, $stored[$key]) : $default;
        }

        return $result;
    }

    /**
     * Return a single setting value, typed.
     */
    public function get(string $key): mixed
    {
        return $this->all()[$key] ?? null;
    }

    /**
     * Persist a set of settings. Only known keys (present in DEFAULTS) are stored,
     * everything else is silently ignored to avoid arbitrary key/value injection.
     */
    public function update(array $values): array
    {
        foreach ($values as $key => $value) {
            if (!array_key_exists($key, self::DEFAULTS)) {
                continue;
            }

            HyperSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $this->prepareForStorage($key, $value)]
            );
        }

        Cache::forget(self::CACHE_KEY);

        return $this->all();
    }

    /**
     * Branding data that is safe to expose publicly (unauthenticated pages,
     * the login screen, etc). Currently the entire branding set is considered
     * public/non-sensitive.
     */
    public function publicBranding(): array
    {
        return $this->all();
    }

    /**
     * Build a `:root { ... }` CSS block from the current theme settings so it
     * can be injected directly into blade layouts and the SPA shell.
     */
    public function cssVariables(): string
    {
        $settings = $this->all();

        $lines = [];
        foreach (self::COLOR_KEYS as $key) {
            $property = '--hyper-' . str_replace('_', '-', substr($key, 6));
            $lines[] = "{$property}: {$settings[$key]};";
        }
        $lines[] = '--hyper-radius: ' . $settings['border_radius'] . ';';
        $lines[] = '--hyper-font: ' . $settings['font'] . ';';

        return ':root { ' . implode(' ', $lines) . ' }';
    }

    /**
     * Filter a set of user-requested appearance overrides down to only the
     * keys an administrator has allowed users to customize. Returns an empty
     * array if user overrides are disabled entirely.
     */
    public function filterUserAppearance(array $requested): array
    {
        $settings = $this->all();

        if (!$settings['allow_user_theme_override']) {
            return [];
        }

        $allowedKeys = $settings['user_customizable_keys'];

        return array_intersect_key($requested, array_flip($allowedKeys));
    }

    /**
     * Cast a raw stored string value into its proper PHP type.
     */
    protected function cast(string $key, ?string $value): mixed
    {
        if (in_array($key, self::JSON_KEYS, true)) {
            $decoded = json_decode((string) $value, true);

            return is_array($decoded) ? $decoded : self::DEFAULTS[$key];
        }

        if (in_array($key, self::BOOLEAN_KEYS, true)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $value ?? self::DEFAULTS[$key];
    }

    /**
     * Convert a typed value back into the string representation persisted
     * in the database.
     */
    protected function prepareForStorage(string $key, mixed $value): string
    {
        if (in_array($key, self::JSON_KEYS, true)) {
            if (is_string($value)) {
                // Already JSON encoded (e.g. coming from a blade textarea).
                json_decode($value);

                return json_last_error() === JSON_ERROR_NONE ? $value : json_encode([]);
            }

            return json_encode($value);
        }

        if (in_array($key, self::BOOLEAN_KEYS, true)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
        }

        return (string) $value;
    }
}
