<?php
session_start();
require_once 'db.php';
require_once 'csrf.php';

date_default_timezone_set('Asia/Tehran');

if(!isset($_SESSION['is_logged']) || $_SESSION['is_logged'] !== true){
    header("location: login.php");
    exit();
}

const MAX_CODE_ATTEMPTS = 5;
const CODE_ATTEMPT_WINDOW = 300;

$error = "";

if(isset($_POST['submit'])){
    csrf_validate();

    // محدود کردن تعداد تلاش‌ها تا کد ۴ رقمی قابل حدس زدن نباشد
    if(!isset($_SESSION['code_attempts_started']) || (time() - $_SESSION['code_attempts_started']) > CODE_ATTEMPT_WINDOW){
        $_SESSION['code_attempts_started'] = time();
        $_SESSION['code_attempts'] = 0;
    }

    if($_SESSION['code_attempts'] >= MAX_CODE_ATTEMPTS){
        $error = "تعداد تلاش‌های ناموفق زیاد است، بعداً دوباره تلاش کنید";
    } else {
        $_SESSION['code_attempts']++;

        $entered_code = trim($_POST['code'] ?? '');

        $stmt = $conn->prepare("
            SELECT code FROM admin_codes
            WHERE expires_at > NOW()
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($row && hash_equals((string)$row['code'], $entered_code)){
            session_regenerate_id(true);
            $_SESSION['code_verified'] = true;
            $_SESSION['code_attempts'] = 0;

            header("location: presenc.php");
            exit();
        }

        $error = "کد اشتباه یا منقضی شده";
    }
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل دانشجو</title>
    <link rel="stylesheet" href="panel.css">
</head>
<body>
    <div>
        <p>کد تایید استاد را وارد کنید تا وارد پنل حضور  شوید </p>
    </div>

    <div>
        <form method="post">
        <?php echo csrf_field(); ?>
        <input type="number" name="code" required>
        <button type="submit" name="submit">ورود</button>
    </form>
    </div>

 <?php if($error !== "") echo "<p>".htmlspecialchars($error, ENT_QUOTES, 'UTF-8')."</p>"; ?>
</body>
</html>
