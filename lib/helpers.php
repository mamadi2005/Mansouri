<?php
// توابع منطقی مشترک بین صفحه‌ها که مستقل از دیتابیس هستند و قابل تست هستند

function is_student_logged(array $session): bool
{
    return isset($session['is_logged']) && $session['is_logged'] === true;
}

function is_admin_logged(array $session): bool
{
    return isset($session['is_admin_logged']) && $session['is_admin_logged'] === true;
}

function is_code_verified(array $session): bool
{
    return isset($session['code_verified']) && $session['code_verified'] === true;
}

function generate_admin_code(): int
{
    return random_int(1000, 9999);
}

function admin_code_expiry(int $now, int $ttl_seconds = 120): string
{
    return date("Y-m-d H:i:s", $now + $ttl_seconds);
}

function is_admin_code_valid($entered_code, $row, string $now): bool
{
    if (!is_array($row) || !isset($row['code'], $row['expires_at'])) {
        return false;
    }

    return (string)$entered_code === (string)$row['code'] && $now <= $row['expires_at'];
}

function verify_admin_password(string $input, string $stored): bool
{
    if ($stored !== '' && password_verify($input, $stored)) {
        return true;
    }

    return $stored === $input;
}

function normalize_delete_id($raw): int
{
    $id = intval($raw);

    return $id > 0 ? $id : 0;
}

/**
 * اعتبارسنجی اطلاعات ارسالی برای ثبت حضور
 *
 * @return array{ok: bool, message: string, fields: array<string, string>}
 */
function validate_presence_input(array $post): array
{
    $fields = [
        'full_name' => trim((string)($post['full_name'] ?? '')),
        'student_code' => trim((string)($post['student_code'] ?? '')),
        'dars' => trim((string)($post['dars'] ?? '')),
    ];

    foreach ($fields as $value) {
        if ($value === '') {
            return ['ok' => false, 'message' => 'اطلاعات کامل نیست', 'fields' => $fields];
        }
    }

    return ['ok' => true, 'message' => '', 'fields' => $fields];
}
