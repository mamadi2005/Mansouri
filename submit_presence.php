<?php
//کد های این صفحه مربوط به  صفحه presenc.php هست که اطلاعات حضور دانشجو رو ثبت میکنه
session_start();
require_once __DIR__ . '/lib/helpers.php';
header('Content-Type: application/json; charset=utf-8');
//
if (!is_student_logged($_SESSION)) {
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

$validation = validate_presence_input($_POST);

if (!$validation['ok']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $validation['message']]);
    exit();
}

$full_name = $validation['fields']['full_name'];
$student_code = $validation['fields']['student_code'];
$dars = $validation['fields']['dars'];

require_once 'db.php';

$stmt = $conn->prepare("INSERT INTO attendance (student_code, full_name, dars) VALUES (?, ?, ?)");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'خطا در prepare']);
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
