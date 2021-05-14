<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Renderer
{
    private const ILLEGAL_FILE_PATHS = [
        '',
        '.',
        './',
        '..',
        'index',
        'index.php',
        '/'
    ];

    private const CONFIG = [
        'templates' => null,
        /**
         * perhaps remove this config
         */
        'extension' => 'php',

        /**
         * Default context
         */
        'context' => [],
    ];

    public function __construct(array $config = [])
    {
        $this->config = Config::fromArray(self::CONFIG, $config);
    }

    public function render(string $_filePath, array $_context = []): string
    {
        // todo: perhaps prevent collision with local vars
        extract(array_merge($this->config['context'], $_context));
        ob_start();
        require $this->resolve($_filePath);
        return ob_get_clean();
    }

    private function resolve(string $filePath): string
    {
        if (in_array($filePath, self::ILLEGAL_FILE_PATHS)) {
            throw new SimpleException(sprintf('Illegal template filepath: "%s"', $filePath));
        }

        // Absolute or relative as is
        if (file_exists($filePath)) {
            return $filePath;
        }

        // Absolute or relative with configured extension
        $filePath1 = sprintf('%s.%s', $filePath, $this->config['extension']);
        if (file_exists($filePath1)) {
            return $filePath1;
        }

        if ($this->config['templates']) {
            // Relative to template folder
            $filePath2 = sprintf('%s/%s', $this->config['templates'], $filePath);

            // Relative to template folder and with configured extension
            $filePath3 = sprintf('%s/%s.%s', $this->config['templates'], $filePath, $this->config['extension']);

            if (file_exists($filePath2)) {
                return $filePath2;
            }

            if (file_exists($filePath3)) {
                return $filePath3;
            }
        }

        throw new SimpleException(sprintf('Unable to find template: "%s"', $filePath));
    }
}