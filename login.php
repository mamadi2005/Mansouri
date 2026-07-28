<?php
include 'db.php';
session_start();
require_once 'csrf.php';
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    csrf_validate();

    $student_code = trim($_POST["student_code"] ?? '');
    $full_name = trim($_POST["full_name"] ?? '');

    if($student_code === '' || $full_name === '' || mb_strlen($student_code) > 50 || mb_strlen($full_name) > 255){
        $message = "اطلاعات وارد شده معتبر نیست";
    } else {
        $stmt = $conn->prepare("SELECT id FROM allowed_students WHERE student_code = ?");
        $stmt->bind_param('s', $student_code);
        $stmt->execute();
        $check = $stmt->get_result();

        if (mysqli_num_rows($check) > 0) {
            session_regenerate_id(true);
            $_SESSION['is_logged'] = true;
            $_SESSION['student_code'] = $student_code;
            $_SESSION['full_name'] = $full_name;

            header("Location: panel.php");
            exit;

        } else {

            $message = "کد دانشجویی وجود ندارد";

        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به پنل</title>
<link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
<h1>ورود به پنل</h1>   
<p>لطفا کد دانشجویی و نام و نام خانوادگی را وارد کنید</p>
<span class="hint">نام و نام خانوادگی را به حروف فارسی تایپ کنید :)</span>
    <form method="post">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="student_code">کد دانشجویی</label>
            <input type="text" name="student_code" id="student_code" required>
        </div>
            <div class="form-group">
                <label for="full_name">نام و نام خانوادگی</label>
                <input type="text" name="full_name" id="full_name" required>
            </div>
            <input type="submit" value="ورود">
    </form>
        <?php if ($message != ""): ?>
            <div class="error"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
