<?php
//کد های این صفحه مربوط به  صفحه presenc.php هست که اطلاعات حضور دانشجو رو ثبت میکنه
session_start();
header('Content-Type: application/json; charset=utf-8');
//
if (!isset($_SESSION['is_logged']) || $_SESSION['is_logged'] !== true) {
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

$full_name = trim($_POST['full_name'] ?? '');
$student_code = trim($_POST['student_code'] ?? '');
$dars = trim($_POST['dars'] ?? '');

if ($full_name === '' || $student_code === '' || $dars === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'اطلاعات کامل نیست']);
    exit();
}

require_once 'db.php';

try {
    $stmt = $conn->prepare("INSERT INTO attendance (student_code, full_name, dars) VALUES (?, ?, ?)");
    // پارامترها را به صورت ایمن به کوئری متصل می‌کنیم
    $stmt->bind_param('sss', $student_code, $full_name, $dars);
    $stmt->execute();
    $stmt->close();
} catch (Throwable $e) {
    // خطا در لاگ سرور ثبت می‌شود و پاسخ JSON با وضعیت ۵۰۰ برمی‌گردد
    $message = db_log_error($e, 'submit_presence');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

echo json_encode(['success' => true, 'message' => 'حضور ثبت شد']);
exit();
