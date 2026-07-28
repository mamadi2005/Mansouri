<?php

use PHPUnit\Framework\TestCase;

final class PresenceInputTest extends TestCase
{
    public function testValidInputIsTrimmed(): void
    {
        $result = validate_presence_input([
            'full_name' => '  علی رضایی ',
            'student_code' => " 40110001\n",
            'dars' => "\tریاضی ",
        ]);

        $this->assertTrue($result['ok']);
        $this->assertSame('', $result['message']);
        $this->assertSame([
            'full_name' => 'علی رضایی',
            'student_code' => '40110001',
            'dars' => 'ریاضی',
        ], $result['fields']);
    }

    /**
     * @dataProvider provideIncompleteInput
     */
    public function testIncompleteInputIsRejected(array $post): void
    {
        $result = validate_presence_input($post);

        $this->assertFalse($result['ok']);
        $this->assertSame('اطلاعات کامل نیست', $result['message']);
    }

    public static function provideIncompleteInput(): array
    {
        $complete = [
            'full_name' => 'علی',
            'student_code' => '40110001',
            'dars' => 'ریاضی',
        ];

        return [
            'nothing sent' => [[]],
            'missing dars' => [array_diff_key($complete, ['dars' => null])],
            'blank full name' => [array_merge($complete, ['full_name' => '   '])],
            'blank student code' => [array_merge($complete, ['student_code' => ''])],
            'whitespace only dars' => [array_merge($complete, ['dars' => "\t\n "])],
        ];
    }

    public function testFieldsAreReturnedEvenWhenInvalid(): void
    {
        $result = validate_presence_input(['full_name' => ' علی ']);

        $this->assertSame('علی', $result['fields']['full_name']);
        $this->assertSame('', $result['fields']['student_code']);
        $this->assertSame('', $result['fields']['dars']);
    }

    public function testNumericValuesAreCastToString(): void
    {
        $result = validate_presence_input([
            'full_name' => 'علی',
            'student_code' => 40110001,
            'dars' => 'ریاضی',
        ]);

        $this->assertTrue($result['ok']);
        $this->assertSame('40110001', $result['fields']['student_code']);
    }
}
