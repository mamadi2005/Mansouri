<?php
$db_host = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "univercity";

// خطاهای mysqli به صورت استثنا پرتاب می‌شوند تا هیچ خطایی بی‌صدا نادیده گرفته نشود
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * خطا را در لاگ سرور ثبت می‌کند و یک پیام عمومی برای نمایش به کاربر برمی‌گرداند.
 */
function db_log_error(Throwable $e, string $context): string
{
    error_log("[$context] " . $e->getMessage());
    return "خطایی در ارتباط با پایگاه داده رخ داد. لطفا دوباره تلاش کنید";
}

/**
 * صفحه را با وضعیت ۵۰۰ متوقف می‌کند بدون آنکه جزئیات خطا افشا شود.
 */
function db_fail(Throwable $e, string $context): void
{
    $message = db_log_error($e, $context);
    if (!headers_sent()) {
        http_response_code(500);
    }

    $wants_json = false;
    foreach (headers_list() as $header) {
        if (stripos($header, 'content-type: application/json') === 0) {
            $wants_json = true;
        }
    }

    echo $wants_json
        ? json_encode(['success' => false, 'message' => $message])
        : htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    exit();
}

try {
    $conn = new mysqli($db_host, $db_username, $db_password, $db_database);
    $conn->set_charset("utf8mb4");
} catch (Throwable $e) {
    db_fail($e, 'db_connect');
}
