<?php

declare(strict_types=1);

namespace Kahric\TheTheme\Helper;

final class TemplateRenderer
{
    public static function render(
        string $template,
        array $params = [],
    ): string
    {
        extract($params);
        ob_start();
        $level = ob_get_level();

        include $template;

        return ob_get_clean();
    }

    public static function echo(
        string $template,
        array $params = [],
    ): void
    {
        echo self::render($template, $params);
    }
}
