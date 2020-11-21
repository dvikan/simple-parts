<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testNullFile()
    {
        $sut = new NullFile();
        self::assertFalse($sut->exists());
        self::assertSame('', $sut->read());
        $sut->write('foo ');
        $sut->append('bar');
        self::assertSame('', $sut->read());
    }

    public function testMemoryFile()
    {
        $sut = new MemoryFile();
        self::assertTrue($sut->exists());
        self::assertSame('', $sut->read());
        $sut->write('foo ');
        $sut->append('bar');
        self::assertSame('foo bar', $sut->read());
    }

    public function testArrayFile()
    {
        $sut = new ArrayFile();
        self::assertTrue($sut->exists());
        self::assertSame('', $sut->read());
        $sut->write('foo ');
        $sut->append('bar');
        self::assertSame('foo bar', $sut->read());
    }

    public function testLineFile()
    {
        $sut = new LineFile(tempnam(sys_get_temp_dir(), 'FileTest_'));
        self::assertTrue($sut->exists());
        self::assertSame('', $sut->read());
        $sut->write('foo ');
        $sut->append('bar');
        self::assertSame("foo bar", $sut->read());
    }

    public function testJsonFile()
    {
        $sut = new JsonFile(tempnam(sys_get_temp_dir(), 'FileTest_'));
        self::assertTrue($sut->exists());
        self::assertSame('', $sut->read());
        $sut->write('foo');
        self::assertSame('foo', $sut->read());
    }
}
