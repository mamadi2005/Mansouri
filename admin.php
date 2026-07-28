<?php
include 'db.php';
date_default_timezone_set('Asia/Tehran');
session_start();
require_once 'csrf.php';

if(!isset($_SESSION['is_admin_logged']) || $_SESSION['is_admin_logged'] !== true){
    header("Location: login_admin.php");
    exit();
}

    // حذف کردن  دانشجو از لیست حضور یادم باشه
if(isset($_POST['delete_id'])){
    csrf_validate();
    $id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM attendance WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

if(isset($_POST['generate_code'])){
    csrf_validate();
    $adad = random_int(1000,9999);
    $expire = date("Y-m-d H:i:s", time() + 120); 
    mysqli_query($conn, "DELETE FROM admin_codes");
    $stmt = $conn->prepare("INSERT INTO admin_codes(code, expires_at) VALUES(?, ?)");
    $stmt->bind_param('is', $adad, $expire);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// گرفتن آخرین کد فعال استاد (که هنوز منقضی نشده)
$checkCode = mysqli_query($conn, "SELECT code, expires_at FROM admin_codes WHERE expires_at > NOW() ORDER BY id DESC LIMIT 1");
// این قسمت چک میکنه کدی توی دیتا بیس هست یا نه یادم باشه
if(mysqli_num_rows($checkCode) > 0){
    $row = mysqli_fetch_assoc($checkCode);
    $result = $row['code'];
    $expireTime = strtotime($row['expires_at']) * 1000;
} else {
    $result = null;
    $expireTime = 0;
}
mysqli_query($conn, "DELETE FROM admin_codes WHERE expires_at <= NOW()");
if(isset($_POST['end_term'])){
    csrf_validate();
    mysqli_query($conn, "DELETE FROM attendance");
    header("Location: admin.php");
    exit();
}

$list = mysqli_query($conn, "SELECT * FROM attendance ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل استاد</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <form method="post" onsubmit="return confirm('مطمئنی میخوای کله لیست حضور ها پاک بشه؟  یادت باشه این کار غیر قابل برگشته (:');">
    <?php echo csrf_field(); ?>
    <button type="submit" name="end_term" style="background:red;color:white;padding:10px;border:none;cursor:pointer;">
        پایان ترم (حذف کل حضورها)
    </button>
</form>
<hr>
<h1>به پنل استاد خوش آمدید</h1>

<a href="settings.php" class="logout_header">پنل تنظیمات</a>
<a href="logout_admin.php" class="logout_header">خروج از پنل</a>

<hr>

<h2>ساخت کد ۴ رقمی</h2>
<form method="post">
    <?php echo csrf_field(); ?>
    <button type="submit" name="generate_code">ساخت کد جدید</button>
</form>

<hr>

<h2>کد فعلی استاد</h2>

<?php if($result): ?>
    <h1><?php echo htmlspecialchars($result, ENT_QUOTES, 'UTF-8'); ?></h1>
<?php else: ?>
    <p>هیچ کدی فعال نیست</p>
<?php endif; ?>

<div id="timer"></div>

<hr>

<h2>لیست حضور دانشجویان</h2>

<?php
$safe = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
if(mysqli_num_rows($list) > 0){
    while($row = mysqli_fetch_assoc($list)){
        echo "<p>";
        echo $safe($row['full_name'])." - ".$safe($row['student_code'])." - ".$safe($row['dars'])." - ".$safe($row['created_at']);

        echo " <form method='post' style='display:inline;'
        onsubmit=\"return confirm('مطمئنی میخوای این دانشجو حذف بشه از لیست حضور؟');\">";
        echo csrf_field();
        echo "<input type='hidden' name='delete_id' value='".$safe($row['id'])."'>";
        echo "<button type='submit' style='color:red; margin-right:10px; background:none; border:none; cursor:pointer;'>حذف</button>";
        echo "</form>";

        echo "</p>";
    }
} else {
    echo "<p>هنوز حضوری ثبت نشده </p>";
}
?>

<script>
let expireTime = <?php echo (int)$expireTime; ?>;

if(expireTime > 0){
    let x = setInterval(function(){
        let now = Date.now();
        let distance = expireTime - now;

        if(distance <= 0){
            clearInterval(x);
            document.getElementById("timer").innerHTML = "⛔ کد منقضی شد";
        } else {
            let minutes = Math.floor(distance / 60000);
            let seconds = Math.floor((distance % 60000) / 1000);

            document.getElementById("timer").innerHTML =
                minutes + ":" + (seconds < 10 ? "0" + seconds : seconds);
        }
    }, 1000);
}
</script>

</body>
</html>
