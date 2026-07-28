<!DOCTYPE html>
<html lang="en">
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
        <input type="number" name="code" required>
        <button type="submit" name="submit">ورود</button>
    </form>
    </div>

   <?php
session_start();

require_once 'db.php';
require_once __DIR__ . '/lib/helpers.php';

if(!is_student_logged($_SESSION)){
    header("location: login.php");
    exit();
}

$error = "";

if(isset($_POST['submit'])){

    $entered_code = $_POST['code'];

    // $res = mysqli_query($conn, "
    //     SELECT * FROM admin_codes
    //     ORDER BY id DESC
    //     LIMIT 1
    // ");
    $res = mysqli_query($conn, "
    SELECT * FROM admin_codes
    WHERE expires_at > NOW()
    ORDER BY id DESC
    LIMIT 1
");

    $row = mysqli_fetch_assoc($res);

    if($row){

        $now = date("Y-m-d H:i:s");

        if(is_admin_code_valid($entered_code, $row, $now)){

            $_SESSION['code_verified'] = true;

            header("location: presenc.php");
            exit();

        } else {

            $error = "کد اشتباه یا منقضی شده ";

        }

    } else {

        $error = "کد اشتباه یا منقضی شده";

    }
}
?>
 <?php if(isset($error)) echo "<p>$error</p>"; ?>
</body>
</html>
