<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Renderer
{
    public function render(string $filePath, array $context = []): string
    {
        extract($context);
        ob_start();
        require $filePath;
        return ob_get_clean();
    }
}