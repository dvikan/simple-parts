<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testMemoryFile()
    {
        $this->_testFile(new MemoryFile());
        $this->_testFile(new LineFile(tempnam(sys_get_temp_dir(), 'FileTest_')));
        $this->_testFile(new StreamFile(tempnam(sys_get_temp_dir(), 'FileTest_')));
    }

    private function _testFile(File $sut): void
    {
        self::assertTrue($sut->exists());
        $sut->write('aaa');
        self::assertSame('aaa', $sut->read());
        $sut->append('bbb');
        self::assertSame('aaabbb', $sut->read());
        $sut->write('');
        self::assertSame('', $sut->read());
        $sut->append('aaa');
        self::assertSame('aaa', $sut->read());
        $sut->append('aaa');
        self::assertSame('aaaaaa', $sut->read());
    }
}
