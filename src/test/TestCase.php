<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class TestCase
{
    public function setUp(){}
    public function tearDown(){}

    protected function assert($expected, $actual)
    {
        $result = $expected === $actual;

        if (!$result) {
            $this->fail($expected, $actual);
        }
    }

    private function fail($expected, $actual)
    {
        $stackTrace = debug_backtrace();
        $stackFrame = $stackTrace[1];

        printf("Fail at %s line %s\n\n", $stackFrame['file'], $stackFrame['line']);

        printf("Expected:\n%s\n\n", $this->asString($expected));

        printf("Actual:\n%s\n", $this->asString($actual));
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
            return '"';
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