<?php
include 'db.php';

session_start();
require_once 'csrf.php';
date_default_timezone_set('Asia/Tehran');

        if(!isset($_SESSION['is_admin_logged']) || $_SESSION['is_admin_logged'] !== true){
            header("Location: login_admin.php");
            exit();
        }

// این کد برای آپدیت اطلاعات استاد هست یادم باشه

if(isset($_POST['update_info'])){
    csrf_validate();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if($username === '' || mb_strlen($username) > 255){
        $_SESSION['msg'] = "نام کاربری معتبر نیست";
    } elseif(strlen($password) < 8){
        $_SESSION['msg'] = "رمز عبور باید حداقل ۸ کاراکتر باشد";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin SET username = ?, password = ? WHERE id = 1");
        $stmt->bind_param('ss', $username, $hashed_password);
        $update = $stmt->execute();
        $stmt->close();
        if(!$update){
            error_log("settings.php update_info failed: " . mysqli_error($conn));
        }
        $_SESSION['msg'] = $update ? "اطلاعات استاد آپدیت شد" : "خطا در به‌روزرسانی";
    }
    header("Location: settings.php");
    exit();
}


//  این کد اضافه کردن دانشجو به لیست  هست یادم باشه

if(isset($_POST['add_student'])){
    csrf_validate();

    $code = trim($_POST['student_code'] ?? '');
    if($code === '' || mb_strlen($code) > 50){
        $_SESSION['msg'] = "کد دانشجویی معتبر نیست";
    } else {
        $stmt = $conn->prepare("SELECT id FROM allowed_students WHERE student_code = ?");
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $check = $stmt->get_result();
        $exists = mysqli_num_rows($check) > 0;
        $stmt->close();

        if($exists){
            $_SESSION['msg'] = "این دانشجو قبلاً اضافه شده";
        } else {
            $stmt = $conn->prepare("INSERT INTO allowed_students (student_code) VALUES (?)");
            $stmt->bind_param('s', $code);
            $insert = $stmt->execute();
            $stmt->close();
            if(!$insert){
                error_log("settings.php add_student failed: " . mysqli_error($conn));
            }
            $_SESSION['msg'] = $insert ? "دانشجو اضافه شد" : "خطا در افزودن دانشجو";
        }
    }
    header("Location: settings.php");
    exit();
}

//این کد حذف دانشجو هست یادم باشه


if(isset($_POST['delete_id'])){
    csrf_validate();
    $delete_id = intval($_POST['delete_id']);
    if($delete_id > 0){
        $stmt = $conn->prepare("DELETE FROM allowed_students WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $delete_id);
        $delete = $stmt->execute();
        $stmt->close();
        if(!$delete){
            error_log("settings.php delete failed: " . mysqli_error($conn));
        }
        $_SESSION['msg'] = $delete ? "دانشجو حذف شد" : "خطا در حذف";
    } else {
        $_SESSION['msg'] = "آی‌دی نامعتبر برای حذف";
    }
header("Location: settings.php");
    exit();
}

        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);
   ?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنظیمات استاد</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="settings_search.css">
</head>
<body>
    <h1>پنل تنظیمات استاد</h1>
    <a href="admin.php" class="logout_header">بازگشت به پنل استاد</a>
    <a href="logout_admin.php" class="logout_header">خروج</a>

    <?php if($msg) echo "<p style='color:green;'>".htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')."</p>"; ?>

    <hr>

    <h2>تغییر اطلاعات استاد</h2>
    <form method="post">
        <?php echo csrf_field(); ?>
        <label>نام کاربری:</label>
        <input type="text" name="username" required placeholder="">
        
        <label>رمز عبور:</label>
        <input type="password" name="password" id="password" required>
        <input type="checkbox" id="showPass">
        <label for="showPass">نمایش رمز عبور</label>
        <button type="submit" name="update_info">به روز رسانی</button>
    </form>

    <script>
// این کد برای نمایش  مخفی کردن رمز عبور هست یادم باشه
    document.getElementById('showPass').addEventListener('change', function(){
        var passInput = document.getElementById('password');
    passInput.type = this.checked ? 'text' : 'password';
    });
    </script>

    <hr>

    <h2>افزودن دانشجو به لیست</h2>
    <form method="post">
        <?php echo csrf_field(); ?>
        <input type="text" name="student_code" placeholder="کد دانشجو رو وارد کن" required>
        <button type="submit" name="add_student">افزودن</button>
    </form>

    <hr>

    <h2>جستجو در لیست دانشجویان</h2>
    <input type="text" id="searchInput" placeholder="دنبال دانشجویی؟">

    <ul id="myList">
    <?php
// این کد برای نمایش لیست دانشجویان هست یادم باشه

    $list = mysqli_query($conn,"SELECT * FROM allowed_students ORDER BY id ASC");
    $safe = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    while($row = mysqli_fetch_assoc($list)){
        echo "<li>";
        echo $safe($row['student_code']);
        echo " <form method='post' style='display:inline;' onsubmit=\"return confirm('حذف شود؟');\">";
        echo csrf_field();
        echo "<input type='hidden' name='delete_id' value='".$safe($row['id'])."'>";
        echo "<button type='submit' style='color:red; background:none; border:none; cursor:pointer;'>حذف</button>";
        echo "</form>";
        echo "</li>";
    }
    ?>
    </ul>

    <script>
        // این کد برای جستجو در لیست دانشجویان هست یادم باشه
    const searchInput = document.getElementById('searchInput');
    const listItems = document.querySelectorAll('#myList li');

    searchInput.addEventListener('input', function() {
        const filter = searchInput.value.toLowerCase();
        listItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if(filter === ''){
                item.style.display = 'list-item';
            }
            else if(text.includes(filter)){
                item.style.display = 'list-item';
            }
            else{
                item.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>
