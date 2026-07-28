<?php
require_once 'includes/functions.php';
include 'db.php';
start_app_session();
require_admin();

    // حذف کردن  دانشجو از لیست حضور یادم باشه
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM attendance WHERE id=$id");
    redirect("admin.php");
}

if(isset($_POST['generate_code'])){
    $adad = random_int(1000,9999);
    $expire = date("Y-m-d H:i:s", time() + 120); 
    mysqli_query($conn, "DELETE FROM admin_codes");
    mysqli_query($conn, "INSERT INTO admin_codes(code, expires_at) VALUES('$adad','$expire')");
    redirect("admin.php");
}

if(isset($_POST['end_term'])){
    mysqli_query($conn, "DELETE FROM attendance");
    redirect("admin.php");
}

// گرفتن آخرین کد فعال استاد (که هنوز منقضی نشده)
$activeCode = get_active_admin_code($conn);
if($activeCode){
    $result = $activeCode['code'];
    $expireTime = strtotime($activeCode['expires_at']) * 1000;
} else {
    $result = null;
    $expireTime = 0;
}
delete_expired_admin_codes($conn);
$list = mysqli_query($conn, "SELECT * FROM attendance ORDER BY id DESC");

render_head('پنل استاد', ['admin.css']);
?>
<body>
    <form method="post" onsubmit="return confirm('مطمئنی میخوای کله لیست حضور ها پاک بشه؟  یادت باشه این کار غیر قابل برگشته (:');">
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
    <button type="submit" name="generate_code">ساخت کد جدید</button>
</form>

<hr>

<h2>کد فعلی استاد</h2>

<?php if($result): ?>
    <h1><?php echo $result; ?></h1>
<?php else: ?>
    <p>هیچ کدی فعال نیست</p>
<?php endif; ?>

<div id="timer"></div>

<hr>

<h2>لیست حضور دانشجویان</h2>

<?php
if(mysqli_num_rows($list) > 0){
    while($row = mysqli_fetch_assoc($list)){
        echo "<p>";
        // echo $row['full_name']." - ".$row['student_code']." - ".$row['created_at'];
        echo $row['full_name']." - ".$row['student_code']." - ".$row['dars']." - ".$row['created_at'];

        echo " <a href='admin.php?delete_id=".$row['id']."' 
        onclick=\"return confirm('مطمئنی میخوای این دانشجو حذف بشه از لیست حضور؟');\"
        style='color:red; margin-right:10px;'>حذف</a>";

        echo "</p>";
    }
} else {
    echo "<p>هنوز حضوری ثبت نشده </p>";
}
?>

<script>
let expireTime = <?php echo $expireTime; ?>;

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
