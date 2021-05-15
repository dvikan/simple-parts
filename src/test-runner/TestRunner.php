<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

use ReflectionClass;
use ReflectionMethod;
use Throwable;

final class TestRunner
{
    private $tests = 0;
    private $assertions = 0;
    private $console;

    public function __construct()
    {
        $this->console = new Console();
    }

    public function run(string $testFolder = './test')
    {
        foreach (glob($testFolder . '/*.php') as $filePath) {
            $this->test($filePath);
        }

        $this->console->greenln('%s tests and %s assertions', $this->tests, $this->assertions);
    }

    private function test(string $filePath): void
    {
        $this->tests++;

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

            $this->assertions++;

            $test = new $fullyQualifiedClassName;

            $this->method($test, $method);
        }
    }

    private function method(TestCase $test, string $method): void
    {
        try {
            $test->$method();

            if ($test->expectException) {
                $this->failException(get_class($test), $method, $test->expectException);
            }
        } catch (AssertionFailure $e) {
            $this->failAssertion($e);
        } catch (Throwable $e) {
            if (get_class($e) !== $test->expectException) {
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

    private function failException(string $class, string $method, string $expected = null)
    {
        $this->console->println('%s::%s()', $class, $method);

        if ($expected) {
            $this->console->println('Expected: %s', $this->getValueAsString($expected));
        }

        $this->console->exit(1);
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