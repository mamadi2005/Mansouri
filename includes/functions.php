<?php
// توابع مشترک بین صفحات سایت

define('APP_TIMEZONE', 'Asia/Tehran');

function start_app_session(): void
{
    date_default_timezone_set(APP_TIMEZONE);
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function redirect(string $url): void
{
    header("Location: $url");
    exit();
}

function is_admin_logged(): bool
{
    return isset($_SESSION['is_admin_logged']) && $_SESSION['is_admin_logged'] === true;
}

function is_student_logged(): bool
{
    return isset($_SESSION['is_logged']) && $_SESSION['is_logged'] === true;
}

function is_code_verified(): bool
{
    return isset($_SESSION['code_verified']) && $_SESSION['code_verified'] === true;
}

function require_admin(): void
{
    if (!is_admin_logged()) {
        redirect('login_admin.php');
    }
}

function require_student(): void
{
    if (!is_student_logged()) {
        redirect('login.php');
    }
}

function require_code_verified(): void
{
    if (!is_code_verified()) {
        redirect('panel.php');
    }
}

function set_flash(string $message): void
{
    $_SESSION['msg'] = $message;
}

function take_flash(): string
{
    $message = $_SESSION['msg'] ?? '';
    unset($_SESSION['msg']);
    return $message;
}

// آخرین کد استاد که هنوز منقضی نشده
function get_active_admin_code(mysqli $conn): ?array
{
    $result = mysqli_query($conn, "SELECT * FROM admin_codes WHERE expires_at > NOW() ORDER BY id DESC LIMIT 1");
    if (!$result) {
        return null;
    }
    $row = mysqli_fetch_assoc($result);
    return $row ?: null;
}

function delete_expired_admin_codes(mysqli $conn): void
{
    mysqli_query($conn, "DELETE FROM admin_codes WHERE expires_at <= NOW()");
}

function json_response(bool $success, string $message, int $status = 200): void
{
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit();
}

function render_head(string $title, array $stylesheets = [], string $lang = 'fa'): void
{
    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"$lang\">\n";
    echo "<head>\n";
    echo "    <meta charset=\"UTF-8\">\n";
    echo "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
    echo '    <title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</title>\n";
    foreach ($stylesheets as $stylesheet) {
        echo '    <link rel="stylesheet" href="' . htmlspecialchars($stylesheet, ENT_QUOTES, 'UTF-8') . "\">\n";
    }
    echo "</head>\n";
}
