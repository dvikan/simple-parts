<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Renderer
{
    private const CONFIG = [
        'templates' => '.',
        'extension' => 'php',

        /**
         * Default context
         */
        'context' => [],
    ];

    /**
     * @var Config
     */
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = Config::fromArray(self::CONFIG, $config);
    }

    public function render(string $_template, array $_context = []): string
    {
        if (isset($_context['_template'])) {
            throw new SimpleException('Illegal context key: "_template"');
        }

        if (isset($_context['_context'])) {
            throw new SimpleException('Illegal context key: "_context"');
        }

        $_context = array_merge($this->config['context'], $_context);

        extract($_context, EXTR_SKIP); // Don't overwrite. This is the default.
        unset($_context); // Remove from scope

        ob_start();
        require $this->resolveTemplate($_template);
        return ob_get_clean();
    }

    private function resolveTemplate(string $template): string
    {
        // These names typically collide with an unrelated index.php in cwd
        if ($template === 'index' || $template === 'index.php') {
            throw new SimpleException('Illegal template names "index" or "index.php"');
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