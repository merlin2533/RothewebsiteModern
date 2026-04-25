<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = [], string $layout = 'public'): string
    {
        $base = dirname(__DIR__) . '/src/Views';
        $templatePath = $base . '/pages/' . $template . '.php';
        $layoutPath = $base . '/layouts/' . $layout . '.php';

        if (!is_readable($templatePath)) {
            throw new \RuntimeException("View template not found: {$templatePath}");
        }

        // Render inner content
        $content = self::capture($templatePath, $data);

        if (!is_readable($layoutPath)) {
            // No layout: return raw
            return $content;
        }

        return self::capture($layoutPath, array_merge($data, ['content' => $content, '_template' => $template]));
    }

    public static function display(string $template, array $data = [], string $layout = 'public'): void
    {
        echo self::render($template, $data, $layout);
    }

    public static function admin(string $template, array $data = []): string
    {
        $base = dirname(__DIR__) . '/src/Views';
        $templatePath = $base . '/admin/' . $template . '.php';
        $layoutPath = $base . '/layouts/admin.php';

        if (!is_readable($templatePath)) {
            throw new \RuntimeException("Admin template not found: {$templatePath}");
        }
        $content = self::capture($templatePath, $data);
        if (!is_readable($layoutPath)) {
            return $content;
        }
        return self::capture($layoutPath, array_merge($data, ['content' => $content, '_template' => $template]));
    }

    public static function displayAdmin(string $template, array $data = []): void
    {
        echo self::admin($template, $data);
    }

    public static function partial(string $name, array $data = []): string
    {
        $path = dirname(__DIR__) . '/src/Views/partials/' . $name . '.php';
        if (!is_readable($path)) {
            return '';
        }
        return self::capture($path, $data);
    }

    private static function capture(string $file, array $data): string
    {
        ob_start();
        (static function (string $__file, array $__data) {
            extract($__data, EXTR_SKIP);
            require $__file;
        })($file, $data);
        return (string) ob_get_clean();
    }
}
