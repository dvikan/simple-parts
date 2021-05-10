<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class TestCase
{
    public function setUp()
    {
        // noop
    }

    public function tearDown()
    {
        // noop
    }

    protected function assert($expected, $actual)
    {
        if ($expected !== $actual) {
            $this->fail($expected, $actual);
        }
    }

    private function fail($expected, $actual)
    {
        $stackTrace = debug_backtrace();
        $stackFrame = $stackTrace[1];

        $console = new Console();

        $console->redln(
            "Fail at %s line %s",
            $stackFrame['file'],
            $stackFrame['line']
        );

        $console->println('Expected: %s', $this->asString($expected));

        $console->println("Actual: %s", $this->asString($actual));
    }

    private function asString($value): string
    {
        $delimiter = $this->getDelimiter($value);
        $valueAsString = $this->valueAsString($value);

        return $delimiter . $valueAsString . $delimiter;
    }

    private function getDelimiter($value): string
    {
        if (gettype($value) === 'string') {
            return "'";
        }
        return '';
    }

    private function valueAsString($value)
    {
        switch (gettype($value)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'integer':
                return (string) $value;
            case 'string':
                return $value;
            case 'array':
                return Json::encode($value);
            case 'object':
                return 'object';
            default:
                return 'oopsie';
        }
    }
}