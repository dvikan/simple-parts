<?php

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function test()
    {
        $request = \dvikan\SimpleParts\Request::fromArrays(
            ['id' => '5'],
            ['user' => 'root'],
            ['user-agent' => 'curl']
        );

        self::assertEquals(null, $request->get('id_'));
        self::assertEquals('5', $request->get('id'));
        self::assertEquals('root', $request->post('user'));
        self::assertEquals(null, $request->post('user_'));
        //self::assertEquals('5', $request->get('id'));
    }
}
