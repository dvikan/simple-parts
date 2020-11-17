<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function test_set_get()
    {
        $sut = new Container();

        $sut['dep1'] = 1;
        $sut['dep2'] = '2';
        $sut['dep3'] = [3];
        $sut['dep4'] = function() { return 4; };
        $sut['dep5'] = function() { return [5]; };
        $sut['dep6'] = function() { return []; };

        self::assertFalse(isset($sut['non_dep']));
        self::assertTrue(isset($sut['dep1']));
        self::assertSame(1, $sut['dep1']);
        self::assertSame('2', $sut['dep2']);
        self::assertSame([3], $sut['dep3']);
        self::assertSame(4, $sut['dep4']);
        self::assertSame([5], $sut['dep5']);
        self::assertSame([], $sut['dep6']);
    }

    public function test_get_nonexisting_dep()
    {
        $sut = new Container();

        $this->expectException(SimpleException::class);
        $sut['dep1'];
    }

    public function test_set_existing_dep()
    {
        $sut = new Container();

        $this->expectException(SimpleException::class);
        $sut['dep1'] = 1;
        $sut['dep1'] = 2;
    }
}
