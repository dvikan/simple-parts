<?php
declare(strict_types=1,ticks=1);

namespace dvikan\SimpleParts;

final class TextFileTest extends TestCase
{
    private $sut;
    private $filePath;

    public function __construct()
    {
        $this->filePath = tempnam('/tmp/', 'TextFileTest_');
        $this->sut = new TextFile($this->filePath);
    }

    function test()
    {
        $this->assert($this->sut->exists());
        $this->assertSame($this->filePath, $this->sut->name());
        $this->assertSame('', $this->sut->read());
    }

    function test_write()
    {
        $this->sut->write('a');

        $this->assertSame('a', $this->sut->read());
    }

    function test_multiple_write()
    {
        $this->sut->write('a');
        $this->sut->write('a');

        $this->assertSame('a', $this->sut->read());
        $this->assertSame('a', $this->sut->read());
        $this->assertSame('a', $this->sut->read());
    }

    function test_append()
    {
        $this->sut->append('a');
        $this->sut->append('a');

        $this->assertSame('aa', $this->sut->read());
    }

    function test_write_append()
    {
        $this->sut->write('a');
        $this->sut->append('a');
        $this->sut->append('a');

        $this->assertSame('aaa', $this->sut->read());
    }

    function test_append_write()
    {
        $this->sut->append('a');
        $this->sut->write('a');

        $this->assertSame('a', $this->sut->read());
    }

    protected function test_pseudo_locking()
    {
        $file1 = new TextFile($this->filePath);
        $file1->write('');

        $file2 = new TextFile($this->filePath);

        $this->expectException(SimpleException::class);

        sleep(2);

        $file2->write('');

        $file1->write('');
    }

    function test_foo()
    {
        $file = new TextFile('/tmp/keka');

        //$file->write('a');
    }

    public function __destruct()
    {
        unlink($this->filePath);
    }
}