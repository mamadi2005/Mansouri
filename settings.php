<?php
require_once 'db.php';



session_start();
date_default_timezone_set('Asia/Tehran');

        if(!isset($_SESSION['is_admin_logged']) || $_SESSION['is_admin_logged'] !== true){
            header("Location: login_admin.php");
            exit();
        }

// این کد برای آپدیت اطلاعات استاد هست یادم باشه

if(isset($_POST['update_info'])){
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $conn->prepare("UPDATE admin SET username=?, password=? WHERE id=1");
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();
        $stmt->close();
        $_SESSION['msg'] = "اطلاعات استاد آپدیت شد";
    } catch (Throwable $e) {
        $_SESSION['msg'] = db_log_error($e, 'settings_update_admin');
    }
    header("Location: settings.php");
    exit();
}


//  این کد اضافه کردن دانشجو به لیست  هست یادم باشه

if(isset($_POST['add_student'])){
    $code = $_POST['student_code'] ?? '';
    try {
        $stmt = $conn->prepare("SELECT id FROM allowed_students WHERE student_code=?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if($exists){
            $_SESSION['msg'] = "این دانشجو قبلاً اضافه شده";
        } else {
            $stmt = $conn->prepare("INSERT INTO allowed_students (student_code) VALUES (?)");
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $stmt->close();
            $_SESSION['msg'] = "دانشجو اضافه شد";
        }
    } catch (Throwable $e) {
        $_SESSION['msg'] = db_log_error($e, 'settings_add_student');
    }
    header("Location: settings.php");
    exit();
}

//این کد حذف دانشجو هست یادم باشه


if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    if($delete_id > 0){
        try {
            $stmt = $conn->prepare("DELETE FROM allowed_students WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $stmt->close();
            $_SESSION['msg'] = "دانشجو حذف شد";
        } catch (Throwable $e) {
            $_SESSION['msg'] = db_log_error($e, 'settings_delete_student');
        }
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

    <?php if($msg) echo "<p style='color:green;'>" . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . "</p>"; ?>

    <hr>

    <h2>تغییر اطلاعات استاد</h2>
    <form method="post">
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
        <input type="text" name="student_code" placeholder="کد دانشجو رو وارد کن" required>
        <button type="submit" name="add_student">افزودن</button>
    </form>

    <hr>

    <h2>جستجو در لیست دانشجویان</h2>
    <input type="text" id="searchInput" placeholder="دنبال دانشجویی؟">

    <ul id="myList">
    <?php
// این کد برای نمایش لیست دانشجویان هست یادم باشه

    try {
        $list = mysqli_query($conn,"SELECT * FROM allowed_students ORDER BY id ASC");
        while($row = mysqli_fetch_assoc($list)){
            echo "<li>";
            echo htmlspecialchars($row['student_code'], ENT_QUOTES, 'UTF-8');
            echo " <a href='settings.php?delete_id=".$row['id']."' onclick=\"return confirm('حذف شود؟');\" style='color:red;'>حذف</a>";
            echo "</li>";
        }
    } catch (Throwable $e) {
        echo "<li>" . htmlspecialchars(db_log_error($e, 'settings_list_students'), ENT_QUOTES, 'UTF-8') . "</li>";
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
