<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class JsonTest extends TestCase
{
    public function test()
    {
        $this->assertSame('{"foo":"bar"}', Json::encode(['foo' => 'bar']));
    }
}