<?php

declare(strict_types=1);

namespace Kahric\TheTheme\Helper;

use Closure;

final class Cache
{
    public static function memoize(string $key, Closure $callback, int $ttl = 600): mixed
    {
        $response = wp_cache_get( $key, 'the_theme' );

        if ( false === $response ) {
            $response = $callback();

            wp_cache_set( $key, $response, 'the_theme', $ttl );
        }

        return $response;
    }
}