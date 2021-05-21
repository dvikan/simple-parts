<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

abstract class TestCase
{
    public $expectedException = '';

    protected function assert(bool $condition)
    {
        if ($condition === false) {
            throw new AssertionFailure(true, false, debug_backtrace());
        }
    }

    protected function assertSame($expected, $actual)
    {
        if ($expected !== $actual) {
            throw new AssertionFailure($expected, $actual, debug_backtrace());
        }
    }

    protected function assertInstanceOf(string $expected, $actual)
    {
        if (! $actual instanceof $expected) {
            throw new AssertionFailure($expected, $actual, debug_backtrace());
        }
    }

    protected function expectException(string $class = \Throwable::class)
    {
        $this->expectedException = $class;
    }
}