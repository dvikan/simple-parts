<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use ReflectionClass;
use ReflectionMethod;

final class Runner
{
    public function __construct()
    {
        $_ = ErrorHandler::create(new Logger('test', [new CliHandler()]));
    }

    public function run(string $testFolder = './test')
    {
        foreach (glob($testFolder . '/*.php') as $filePath) {
            $this->runTest($filePath);
        }
    }

    private function runTest(string $filePath): void
    {
        // Extract namespace
        $code = file_get_contents(realpath($filePath));
        preg_match('/^namespace ([a-zA-Z\\\]+);$/m', $code, $matches);
        $namespace = $matches[1] ?? '';

        $fullyQualifiedClassName = $namespace . '\\' . pathinfo($filePath, PATHINFO_FILENAME);

        if (!class_exists($fullyQualifiedClassName)) {
            // These classes are not autoloaded
            require $filePath;
        }
        $reflection = new ReflectionClass($fullyQualifiedClassName);

        $testMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($testMethods as $testMethod) {
            $methodName = $testMethod->getName();

            if (
                strpos($methodName, '__') === 0
                || $methodName === 'setUp'
                || $methodName === 'tearDown'
            ) {
                continue;
            }

            $this->runTestMethod($fullyQualifiedClassName, $methodName);
        }
    }

    private function runTestMethod(string $fullyQualifiedClassName, string $methodName): void
    {
        /** @var TestCase $testClass */
        $testClass = new $fullyQualifiedClassName;

        $testClass->setUp();

        try {
            $testClass->{$methodName}();
            return;
        } finally {
            $testClass->tearDown();
        }
    }
}