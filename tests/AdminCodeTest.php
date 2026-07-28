<?php

use PHPUnit\Framework\TestCase;

final class AdminCodeTest extends TestCase
{
    public function testGeneratedCodeIsAlwaysFourDigits(): void
    {
        for ($i = 0; $i < 200; $i++) {
            $code = generate_admin_code();
            $this->assertGreaterThanOrEqual(1000, $code);
            $this->assertLessThanOrEqual(9999, $code);
            $this->assertSame(4, strlen((string)$code));
        }
    }

    public function testExpiryDefaultsToTwoMinutesAfterNow(): void
    {
        $now = mktime(10, 0, 0, 1, 1, 2026);

        $this->assertSame(date("Y-m-d H:i:s", $now + 120), admin_code_expiry($now));
        $this->assertSame(date("Y-m-d H:i:s", $now + 30), admin_code_expiry($now, 30));
    }

    public function testExpiryIsComparableAsString(): void
    {
        $now = mktime(23, 59, 0, 12, 31, 2026);

        $this->assertGreaterThan(date("Y-m-d H:i:s", $now), admin_code_expiry($now));
    }

    public function testValidCodeBeforeExpiry(): void
    {
        $row = ['code' => '1234', 'expires_at' => '2026-01-01 10:02:00'];

        $this->assertTrue(is_admin_code_valid('1234', $row, '2026-01-01 10:00:00'));
        $this->assertTrue(is_admin_code_valid(1234, $row, '2026-01-01 10:02:00'));
    }

    public function testExpiredCodeIsRejected(): void
    {
        $row = ['code' => 1234, 'expires_at' => '2026-01-01 10:02:00'];

        $this->assertFalse(is_admin_code_valid('1234', $row, '2026-01-01 10:02:01'));
    }

    public function testWrongCodeIsRejected(): void
    {
        $row = ['code' => 1234, 'expires_at' => '2026-01-01 10:02:00'];

        $this->assertFalse(is_admin_code_valid('4321', $row, '2026-01-01 10:00:00'));
        $this->assertFalse(is_admin_code_valid('', $row, '2026-01-01 10:00:00'));
    }

    public function testLooseNumericMatchesAreRejected(): void
    {
        $row = ['code' => 1234, 'expires_at' => '2026-01-01 10:02:00'];

        $this->assertFalse(is_admin_code_valid('1234abc', $row, '2026-01-01 10:00:00'));
        $this->assertFalse(is_admin_code_valid(' 1234', $row, '2026-01-01 10:00:00'));
    }

    public function testMissingOrMalformedRowIsRejected(): void
    {
        $this->assertFalse(is_admin_code_valid('1234', null, '2026-01-01 10:00:00'));
        $this->assertFalse(is_admin_code_valid('1234', false, '2026-01-01 10:00:00'));
        $this->assertFalse(is_admin_code_valid('1234', [], '2026-01-01 10:00:00'));
        $this->assertFalse(is_admin_code_valid('1234', ['code' => '1234'], '2026-01-01 10:00:00'));
    }
}
