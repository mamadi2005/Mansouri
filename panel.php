<?php
require_once 'includes/functions.php';
require_once 'db.php';

start_app_session();
require_student();

$error = "";

if(isset($_POST['submit'])){

    $entered_code = $_POST['code'];

    $row = get_active_admin_code($conn);

    if($row){

        $now = date("Y-m-d H:i:s");

        if($entered_code == $row['code'] && $now <= $row['expires_at']){

            $_SESSION['code_verified'] = true;

            redirect("presenc.php");

        }
    }

    $error = "کد اشتباه یا منقضی شده ";
}

render_head('پنل دانشجو', ['panel.css']);
?>
<body>
    <div>
        <p>کد تایید استاد را وارد کنید تا وارد پنل حضور  شوید </p>
    </div>

    <div>
        <form method="post">
        <input type="number" name="code" required>
        <button type="submit" name="submit">ورود</button>
    </form>
    </div>

 <?php if($error !== "") echo "<p>$error</p>"; ?>
</body>
</html>
