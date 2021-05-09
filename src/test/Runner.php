<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use ReflectionClass;
use ReflectionMethod;

final class Runner
{
    public function run()
    {
        $folder = './test';

        foreach (glob($folder . '/*.php') as $filePath) {
            $this->runTest($filePath);
        }
    }

    private function runTest($filePath): void
    {
        $pathInfo = pathinfo($filePath);

        $dirName = $pathInfo['dirname'];
        $baseName = $pathInfo['basename'];
        $className = $pathInfo['filename'];

        $namespace = $this->extractNamespace($dirName . '/' . $baseName);

        $fullyQualifiedClassName = $namespace . '\\' . $className;

        $reflection = new ReflectionClass($fullyQualifiedClassName);

        $testMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($testMethods as $testMethod) {
            $name = $testMethod->getName();

            if (
                strpos($name, '__') === 0
                || $name === 'setUp'
                || $name === 'tearDown'
            ) {
                continue;
            }

            try {
                $testClass = new $fullyQualifiedClassName;
                $testClass->setUp();
                $testClass->{$name}();
            } catch(\Throwable $e) {
                throw $e;
            } finally {
                $testClass->tearDown();
            }
        }
    }

    private function extractNamespace(string $filePath)
    {
        $data = file_get_contents($filePath);
        if (preg_match('/^namespace ([a-zA-Z\\\]+);$/m', $data, $matches) === 1) {
            return $matches[1];
        }
    }
}