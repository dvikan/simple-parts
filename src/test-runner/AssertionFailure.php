<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class AssertionFailure extends \Exception
{
    public $expected;
    public $actual;
    public $stackFrame;

    public function __construct($expected, $actual, array $stackFrame)
    {
        parent::__construct();

        $this->stackFrame = $stackFrame;
        $this->expected = $expected;
        $this->actual = $actual;
    }
}