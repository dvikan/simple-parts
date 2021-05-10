<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Renderer
{
    private const CONFIG = [
        'templates' => '.',
        'extension' => 'php',
    ];

    /**
     * @var Config
     */
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = Config::fromArray(self::CONFIG, $config);
    }

    public function render(string $template, array $context = []): string
    {
        extract($context);
        ob_start();
        require $this->resolveTemplate($template);
        return ob_get_clean();
    }

    private function resolveTemplate(string $template): string
    {
        // These names typically collide with an unrelated index.php in cwd
        if ($template === 'index' || $template === 'index.php') {
            throw new SimpleException('bad idea');
        }

        // Absolute or relative as is
        $filePath0 = $template;

        // Absolute or relative with configured extension
        $filePath1 = sprintf('%s.%s', $template, $this->config['extension']);

        // Relative to templates folder
        $filePath2 = sprintf('%s/%s', $this->config['templates'], $template);

        // Relative to templates folder and with configured extension
        $filePath3 = sprintf('%s/%s.%s', $this->config['templates'], $template, $this->config['extension']);

        if (file_exists($filePath0)) {
            return $filePath0;
        }

        if (file_exists($filePath1)) {
            return $filePath1;
        }

        if (file_exists($filePath2)) {
            return $filePath2;
        }

        if (file_exists($filePath3)) {
            return $filePath3;
        }

        throw new SimpleException(sprintf('Unable to find template: "%s"', $template));
    }
}