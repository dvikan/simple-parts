<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class RendererTest extends TestCase
{
    private $file;
    private $sut;

    public function __construct()
    {
        $this->file = new TextFile(tempnam('/tmp/', 'RendererTest_'));
        $this->sut = new Renderer();
    }

    function test()
    {
        $this->file->write('foo <?= $bar ?>');

        $this->assertSame('foo bar', $this->sut->render($this->file->getRealPath(), ['bar' => 'bar']));
    }

    public function __destruct()
    {
        $this->file->delete();
    }
}