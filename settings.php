<?php
require_once 'includes/functions.php';
include 'db.php';

start_app_session();
require_admin();

// این کد برای آپدیت اطلاعات استاد هست یادم باشه

if(isset($_POST['update_info'])){
$username = mysqli_real_escape_string($conn, $_POST['username']);
      $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
$update = mysqli_query($conn, "UPDATE admin SET username='$username', password='$hashed_password' WHERE id=1");
        set_flash($update ? "اطلاعات استاد آپدیت شد" : "خطا در به‌روزرسانی: ".mysqli_error($conn));
    redirect("settings.php");
}


//  این کد اضافه کردن دانشجو به لیست  هست یادم باشه

if(isset($_POST['add_student'])){
                $code = mysqli_real_escape_string($conn, $_POST['student_code']);
                $check = mysqli_query($conn, "SELECT * FROM allowed_students WHERE student_code='$code'");
                if(mysqli_num_rows($check) > 0){
                    set_flash("این دانشجو قبلاً اضافه شده");
                } else {
                    $insert = mysqli_query($conn,"INSERT INTO allowed_students (student_code) VALUES ('$code')");
                    set_flash($insert ? "دانشجو اضافه شد" : "خطا: ".mysqli_error($conn));
                }
                redirect("settings.php");
}

//این کد حذف دانشجو هست یادم باشه


if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    if($delete_id > 0){
        $delete = mysqli_query($conn, "DELETE FROM allowed_students WHERE id=$delete_id LIMIT 1");
        set_flash($delete ? "دانشجو حذف شد" : ("خطا در حذف: ".mysqli_error($conn)));
    } else {
        set_flash("آی‌دی نامعتبر برای حذف");
    }
    redirect("settings.php");
}

        $msg = take_flash();

render_head('تنظیمات استاد', ['admin.css', 'settings_search.css']);
   ?>
<body>
    <h1>پنل تنظیمات استاد</h1>
    <a href="admin.php" class="logout_header">بازگشت به پنل استاد</a>
    <a href="logout_admin.php" class="logout_header">خروج</a>

    <?php if($msg) echo "<p style='color:green;'>$msg</p>"; ?>

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

    $list = mysqli_query($conn,"SELECT * FROM allowed_students ORDER BY id ASC");
    while($row = mysqli_fetch_assoc($list)){
        echo "<li>";
        echo $row['student_code'];
        echo " <a href='settings.php?delete_id=".$row['id']."' onclick=\"return confirm('حذف شود؟');\" style='color:red;'>حذف</a>";
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
