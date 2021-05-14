<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class JsonTest extends TestCase
{
    public function test()
    {
        $this->assertSame(
            '{"f":"b"}',
            Json::encode(['f' => 'b'])
        );

        $json = <<<JSON
{
    "f": "b"
}
JSON;

        $this->assertSame(
            $json,
            Json::encode(['f' => 'b'], JSON_PRETTY_PRINT)
        );
    }
}