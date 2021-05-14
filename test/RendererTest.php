<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class RendererTest extends TestCase
{
    /**
     * @var TextFile
     */
    private $file;
    /**
     * @var Renderer
     */
    private $sut;

    public function __construct()
    {
        $this->file = new TextFile(tempnam('/tmp/', 'RendererTest_'));
        $this->sut = new Renderer();
    }

    function test()
    {
        $this->file->write('hello <?= $user ?>');

        $this->assertSame('hello root', $this->sut->render($this->file->name(), ['user' => 'root']));
    }

    function test_escape_html()
    {
        $this->file->write(<<<'TEMPLATE'
<?php namespace dvikan\SimpleParts; ?>
hello <?= e($user) ?>
TEMPLATE
);

        $this->assertSame('hello &lt;', $this->sut->render($this->file->name(), ['user' => '<']));
    }

    function test_illegal_file_path_1()
    {
        $this->expectException(SimpleException::class);
        $this->sut->render('/');
    }

    public function __destruct()
    {
        unlink($this->file->name());
    }
}