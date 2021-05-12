<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

$projectAutoLoader = __DIR__ . '/../vendor/autoload.php';

if (file_exists($projectAutoLoader)) {
    // Installed as project
    require $projectAutoLoader;
}

$runner = new Runner();

$runner->run();