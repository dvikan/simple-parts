<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private const HANDLER = ['Handler', 'index'];

    public function testNonExistingRoute()
    {
        $sut = new Router(['/test' => self::HANDLER]);

        self::assertEquals([], $sut->match('/nope'));
    }

    public function testStaticRoute()
    {
        $sut = new Router(['/test' => self::HANDLER]);

        self::assertEquals([self::HANDLER, []], $sut->match('/test'));
    }

    public function testDynamicRoute()
    {
        $sut = new Router(['/user/([a-z]+)/([0-9]+)' => self::HANDLER]);

        self::assertEquals([self::HANDLER, ['joe', '42']], $sut->match('/user/joe/42'));
    }
}
