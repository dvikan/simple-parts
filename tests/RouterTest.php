<?php

use PHPUnit\Framework\TestCase;
use dvikan\SimpleParts\Router;

class RouterTest extends TestCase
{
    private const HANDLER = ['Handler', 'index'];

    public function testNonExistingRoute()
    {
        $sut = new Router();

        $sut->map('/test', self::HANDLER);

        self::assertEquals([], $sut->match('/nope'));
    }

    public function testStaticRoute()
    {
        $sut = new Router();

        $sut->map('/test', self::HANDLER);

        self::assertEquals([self::HANDLER, []], $sut->match('/test'));
    }

    public function testDynamicRoute()
    {
        $sut = new Router();

        $sut->map('/user/([a-z]+)/([0-9]+)', self::HANDLER);

        self::assertEquals([self::HANDLER, ['joe', '42']], $sut->match('/user/joe/42'));
    }
}
