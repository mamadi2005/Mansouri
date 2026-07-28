<?php
//کد های این صفحه مربوط به  صفحه presenc.php هست که اطلاعات حضور دانشجو رو ثبت میکنه
session_start();
header('Content-Type: application/json; charset=utf-8');
//
if (!isset($_SESSION['is_logged']) || $_SESSION['is_logged'] !== true
    || !isset($_SESSION['code_verified']) || $_SESSION['code_verified'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'ابتدا وارد شوید']);
    exit();
}
// فقط اجازه می‌دهیم درخواست‌های POST برای ثبت حضور ارسال شود
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'روش ارسال پشتیبانی نمی‌شود']);
    exit();
}

// توکن CSRF را بررسی می‌کنیم
$token = $_POST['csrf_token'] ?? '';
if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'درخواست نامعتبر است']);
    exit();
}

// هویت دانشجو فقط از سشن خوانده می‌شود نه از ورودی کاربر
$full_name = trim((string)($_SESSION['full_name'] ?? ''));
$student_code = trim((string)($_SESSION['student_code'] ?? ''));
$dars = trim($_POST['dars'] ?? '');

if ($full_name === '' || $student_code === '' || $dars === '' || mb_strlen($dars) > 255) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'اطلاعات کامل نیست']);
    exit();
}

require_once 'db.php';

$stmt = $conn->prepare("INSERT INTO attendance (student_code, full_name, dars) VALUES (?, ?, ?)");

if (!$stmt) {
    error_log('submit_presence.php prepare failed: ' . $conn->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'خطا در ثبت اطلاعات']);
    exit();
}
// پارامترها را به صورت ایمن به کوئری متصل می‌کنیم
$stmt->bind_param('sss', $student_code, $full_name, $dars);

$executed = $stmt->execute();
$stmt->close();
// اگر اجرا موفق بود پاسخ موفقیت‌آمیز می‌فرستیم
if ($executed) {
    echo json_encode(['success' => true, 'message' => 'حضور ثبت شد']);
    exit();
}
// اگر به اینجا رسیدیم یعنی خطا در اجرا بوده
http_response_code(500);
echo json_encode(['success' => false, 'message' => 'خطا در ثبت اطلاعات']);
exit();
