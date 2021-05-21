<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class ConfigTest extends TestCase
{
    public function test()
    {
        $sut = Config::fromArray(['env' => 'dev']);

        $this->assertSame('dev', $sut['env']);
    }

    public function test_custom_config()
    {
        $sut = Config::fromArray(['env' => 'dev'], ['env' => 'prod']);

        $this->assertSame('prod', $sut['env']);
    }

    public function test_cexception()
    {
        $this->expectException();

        Config::fromArray([], ['env' => 'prod']);
    }
}