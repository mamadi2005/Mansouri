<?php

use PHPUnit\Framework\TestCase;

final class DeleteIdTest extends TestCase
{
    /**
     * @dataProvider provideIds
     */
    public function testNormalization(mixed $raw, int $expected): void
    {
        $this->assertSame($expected, normalize_delete_id($raw));
    }

    public static function provideIds(): array
    {
        return [
            'numeric string' => ['12', 12],
            'integer' => [7, 7],
            'zero' => ['0', 0],
            'negative' => ['-5', 0],
            'empty' => ['', 0],
            'non numeric' => ['abc', 0],
            'sql injection attempt' => ['1 OR 1=1; DROP TABLE attendance', 1],
            'float string' => ['3.9', 3],
            'null' => [null, 0],
        ];
    }
}
