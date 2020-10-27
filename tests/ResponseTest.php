<?php

use dvikan\SimpleParts\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function test()
    {
        $response = new Response();

        self::assertSame('', $response->body());
    }
}
