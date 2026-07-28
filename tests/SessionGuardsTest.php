<?php

use PHPUnit\Framework\TestCase;

final class SessionGuardsTest extends TestCase
{
    public function testStudentLoggedOnlyWithStrictTrue(): void
    {
        $this->assertTrue(is_student_logged(['is_logged' => true]));
        $this->assertFalse(is_student_logged(['is_logged' => 1]));
        $this->assertFalse(is_student_logged(['is_logged' => "true"]));
        $this->assertFalse(is_student_logged(['is_logged' => false]));
        $this->assertFalse(is_student_logged([]));
    }

    public function testAdminLoggedOnlyWithStrictTrue(): void
    {
        $this->assertTrue(is_admin_logged(['is_admin_logged' => true]));
        $this->assertFalse(is_admin_logged(['is_admin_logged' => 'yes']));
        $this->assertFalse(is_admin_logged(['is_logged' => true]));
        $this->assertFalse(is_admin_logged([]));
    }

    public function testCodeVerifiedOnlyWithStrictTrue(): void
    {
        $this->assertTrue(is_code_verified(['code_verified' => true]));
        $this->assertFalse(is_code_verified(['code_verified' => '1']));
        $this->assertFalse(is_code_verified(['code_verified' => null]));
        $this->assertFalse(is_code_verified([]));
    }
}
