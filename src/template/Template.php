<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Template
{
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function render(string $filePath, array $context = []): string
    {
        // todo: validate filePath
        extract($context);
        ob_start();
        require $filePath;
        return ob_get_clean();
    }
}