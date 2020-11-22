<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PHPUnit\Framework\TestCase;

final class ShellTest extends TestCase
{
    public function test()
    {
        $sut = new Shell();

        self::assertEquals('', $sut->execute('echo;id'));
        self::assertEquals('aaa', $sut->execute('echo', ['aaa']));
        self::assertEquals("1\n2\n3", $sut->execute('seq', [1, 3]));
    }
}
