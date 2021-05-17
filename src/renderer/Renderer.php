<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Renderer
{
    private const CONFIG = [
        'context' => [],
    ];

    private $config;

    public function __construct(array $config = [])
    {
        $this->config = Config::fromArray(self::CONFIG, $config);
    }

    public function render(string $_filePath, array $_context = []): string
    {
        extract(array_merge($this->config['context'], $_context));
        ob_start();
        require $this->resolve($_filePath);
        return ob_get_clean();
    }

    private function resolve(string $filePath): string
    {
        static $badPaths = [
            '',
            '.',
            './',
            '..',
            'index',
            'index.php',
            '/'
        ];

        if (in_array($filePath, $badPaths)) {
            throw new SimpleException(sprintf('Illegal template filepath: "%s"', $filePath));
        }

        if (file_exists($filePath)) {
            return $filePath;
        }

        throw new SimpleException(sprintf('Unable to find template: "%s"', $filePath));
    }
}