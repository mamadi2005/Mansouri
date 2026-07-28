<?php
require_once 'includes/functions.php';
include 'db.php';

start_app_session();

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_code = $_POST["student_code"];
    $full_name = $_POST["full_name"];

    $check = mysqli_query($conn,"SELECT * FROM allowed_students WHERE student_code = '$student_code'");

    if (mysqli_num_rows($check) > 0) {
        $_SESSION['is_logged'] = true;
        $_SESSION['student_code'] = $student_code;
        $_SESSION['full_name'] = $full_name;

        redirect("panel.php");
    } else {

        $message = "کد دانشجویی وجود ندارد";

    }
}

render_head('ورود به پنل', ['login.css']);
?>
<body>
    <div class="container">
<h1>ورود به پنل</h1>   
<p>لطفا کد دانشجویی و نام و نام خانوادگی را وارد کنید</p>
<span class="hint">نام و نام خانوادگی را به حروف فارسی تایپ کنید :)</span>
    <form method="post">
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
            <div class="error"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
