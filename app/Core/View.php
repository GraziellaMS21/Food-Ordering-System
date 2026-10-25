<?php

namespace App\Core;

use RuntimeException;

class View
{
    /**
     * Render a view template with an optional layout.
     *
     * @param string $template View template path relative to app/Views (without .php)
     * @param array $data Variables to extract into the view
     * @param string|null $layout Layout file path relative to app/Views (without .php)
     */
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): void
    {
        $baseDir = dirname(__DIR__) . '/Views/';
        $templatePath = $baseDir . trim($template, '/') . '.php';

        if (!file_exists($templatePath)) {
            throw new RuntimeException("View template not found: {$templatePath}");
        }

        extract($data);

        // Render view template into $content
        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        if ($layout !== null) {
            $layoutPath = $baseDir . trim($layout, '/') . '.php';
            if (!file_exists($layoutPath)) {
                throw new RuntimeException("Layout not found: {$layoutPath}");
            }
            require $layoutPath;
        } else {
            echo $content;
        }
    }
}
