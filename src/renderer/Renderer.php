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
        try {
            ob_start();
            extract(array_merge($this->config['context'], $_context));
            require $this->resolveFilePath($_filePath);
            $output = ob_get_clean();
        } catch(\Throwable $t) {
            ob_end_clean();
            throw $t;
        }

        return $output;
    }

    private function resolveFilePath(string $filePath): string
    {
        static $badFilePaths = [
            '',
            '.',
            './',
            '..',
            'index',
            'index.php',
            '/'
        ];

        if (in_array($filePath, $badFilePaths)) {
            throw new SimpleException(sprintf('Illegal template filepath: "%s"', $filePath));
        }

        if (file_exists($filePath)) {
            return $filePath;
        }

        throw new SimpleException(sprintf('Unable to find template: "%s"', $filePath));
    }
}