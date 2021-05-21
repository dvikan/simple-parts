<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

use ReflectionClass;
use ReflectionMethod;
use Throwable;

final class TestRunner
{
    private $classes = 0;
    private $tests = 0;
    private $console;

    public function __construct()
    {
        $this->console = new Console();
    }

    public function run(int $argc, array $argv)
    {
        $testFolder = $argv[1] ?? './test/';

        foreach (glob($testFolder . '*.php') as $filePath) {
            $this->console->println($filePath);
            $this->test($filePath);
        }

        $this->console->greenln('%s classes and %s tests', $this->classes, $this->tests);
    }

    private function test(string $filePath): void
    {
        $this->classes++;

        $code = file_get_contents(realpath($filePath));
        preg_match('/^namespace ([a-zA-Z\\\]+);$/m', $code, $matches);
        $namespace = $matches[1] ?? '';

        $fullyQualifiedClassName = $namespace . '\\' . pathinfo($filePath, PATHINFO_FILENAME);

        if (!class_exists($fullyQualifiedClassName)) {
            require $filePath;
        }

        $reflection = new ReflectionClass($fullyQualifiedClassName);

        $testMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($testMethods as $testMethod) {
            $method = $testMethod->getName();

            if (strpos($method, '__') === 0) {
                continue;
            }

            $this->tests++;

            $test = new $fullyQualifiedClassName;

            $this->method($test, $method);
        }
    }

    private function method(TestCase $test, string $method): void
    {
        try {
            $test->$method();

            if ($test->expectedException) {
                $this->failException(get_class($test), $method, $test->expectedException, null);
                $this->console->exit(1);
            }
        }
        catch (AssertionFailure $e) {
            $this->failAssertion($e);
        }
        catch (Throwable $e) {
            if (! ($e instanceof $test->expectedException)) {
                $this->failException(get_class($test), $method, $test->expectedException, get_class($e));
                throw $e;
            }
        }
    }

    public function failAssertion(AssertionFailure $e)
    {
        $expected = $e->expected;
        $actual = $e->actual;
        $stackFrame = $e->stackFrame;

        $testClass = $stackFrame[1]['class'];
        $testMethod = $stackFrame[1]['function'];

        $testFile = $stackFrame[0]['file'];
        $testLine = $stackFrame[0]['line'];

        $this->console->println(sprintf('%s::%s() at %s line %s', $testClass, $testMethod, $testFile, $testLine));
        $this->console->println('Expected: %s', $this->getValueAsString($expected));
        $this->console->println("Actual: %s", $this->getValueAsString($actual));
        $this->console->exit(1);
    }

    private function failException(string $class, string $method, string $expected = null, string $actual = null)
    {
        $this->console->println('%s::%s()', $class, $method);

        if ($expected) {
            $this->console->println('Expected: %s', $this->getValueAsString($expected));
        }

        if ($actual) {
            $this->console->println('Actual: %s', $this->getValueAsString($actual));
        }
    }

    private function getValueAsString($value): string
    {
        switch (gettype($value)) {
            case 'string':
                return "'" . $value . "'";

            case 'double':
                return $value;

            case 'NULL':
                return 'null';

            case 'boolean':
                return $value ? 'true' : 'false';

            case 'integer':
                return (string) $value;

            case 'array':
                try {
                    return Json::encode($value);
                } catch (SimpleException $e) {
                    return $e->getMessage();
                }

            case 'object':
                return 'object';

            default:
                return 'unknown type';
        }
    }
}