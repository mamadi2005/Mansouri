<?php

use PHPUnit\Framework\TestCase;

final class AdminPasswordTest extends TestCase
{
    public function testHashedPasswordIsAccepted(): void
    {
        $hash = password_hash('secret123', PASSWORD_DEFAULT);

        $this->assertTrue(verify_admin_password('secret123', $hash));
        $this->assertFalse(verify_admin_password('secret124', $hash));
    }

    public function testLegacyPlainTextPasswordIsAccepted(): void
    {
        $this->assertTrue(verify_admin_password('plain', 'plain'));
        $this->assertFalse(verify_admin_password('plain', 'other'));
    }

    public function testEmptyStoredPasswordNeverMatchesNonEmptyInput(): void
    {
        $this->assertFalse(verify_admin_password('anything', ''));
    }

    public function testComparisonIsCaseSensitive(): void
    {
        $this->assertFalse(verify_admin_password('Plain', 'plain'));
        $this->assertFalse(verify_admin_password('SECRET', password_hash('secret', PASSWORD_DEFAULT)));
    }
}
