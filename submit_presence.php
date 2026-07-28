<?php
//کد های این صفحه مربوط به  صفحه presenc.php هست که اطلاعات حضور دانشجو رو ثبت میکنه
require_once 'includes/functions.php';
start_app_session();
header('Content-Type: application/json; charset=utf-8');
//
if (!is_student_logged()) {
    json_response(false, 'ابتدا وارد شوید', 403);
}
// فقط اجازه می‌دهیم درخواست‌های POST برای ثبت حضور ارسال شود
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'روش ارسال پشتیبانی نمی‌شود', 405);
}

$full_name = trim($_POST['full_name'] ?? '');
$student_code = trim($_POST['student_code'] ?? '');
$dars = trim($_POST['dars'] ?? '');

if ($full_name === '' || $student_code === '' || $dars === '') {
    json_response(false, 'اطلاعات کامل نیست', 400);
}

require_once 'db.php';

$stmt = $conn->prepare("INSERT INTO attendance (student_code, full_name, dars) VALUES (?, ?, ?)");

if (!$stmt) {
    json_response(false, 'خطا در prepare', 500);
}
// پارامترها را به صورت ایمن به کوئری متصل می‌کنیم
$stmt->bind_param('sss', $student_code, $full_name, $dars);

$executed = $stmt->execute();
$stmt->close();
// اگر اجرا موفق بود پاسخ موفقیت‌آمیز می‌فرستیم
if ($executed) {
    json_response(true, 'حضور ثبت شد');
}
// اگر به اینجا رسیدیم یعنی خطا در اجرا بوده
json_response(false, 'خطا در ثبت اطلاعات', 500);
