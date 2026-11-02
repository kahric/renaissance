<?php

declare(strict_types=1);

namespace Kahric\TheTheme;

final class Config
{
    private static array|null $cache = null;
    private const array DEFAULT_CONFIG = [];

    public static function set(array $config): void
    {
        $config = array_merge(self::get(), $config);

        update_option('the_theme_settings', $config);

        self::$cache = $config;
    }

    public static function get(): array
    {
        if (null !== self::$cache) {
            return self::$cache;
        }

        $theme_options = get_option('the_theme_settings', self::DEFAULT_CONFIG);

        self::$cache = wp_parse_args($theme_options, self::DEFAULT_CONFIG);

        return self::get();
    }

    public static function getAsString(string $name, string $default = ''): string
    {
        return self::get()[$name] ?? $default;
    }

    public static function getAsBool(string $name): bool
    {
        return 'on' === self::getAsString($name, 'off');
    }
}
