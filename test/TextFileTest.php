<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class TextFileTest extends TestCase
{
    private $filePath;
    private $sut;

    public function __construct()
    {
        $this->filePath = tempnam('/tmp/', 'TextFileTest_');
        $this->sut = new TextFile($this->filePath);
    }

    function test()
    {
        $this->assert($this->sut->exists());
        $this->assertSame($this->filePath, $this->sut->getRealPath());
        $this->assertSame('', $this->sut->read());

        $file = new TextFile('/tmp/test.txt');
        $this->assertSame('test.txt', $file->getFileName());
        $this->assertSame('test', $file->getBaseName());
        $this->assertSame('txt', $file->getExtension());
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

    public function __destruct()
    {
        $this->sut->delete();
    }
}