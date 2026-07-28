<?php

session_start();
$error = "";
if(isset($_SESSION['is_admin_logged']) && $_SESSION['is_admin_logged'] === true){
    header("location: admin.php");
    exit();
}
require_once "db.php";
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = $_POST['full_namee'] ?? "";
    $password = $_POST['full_password'] ?? "";

    try {
        $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if($row){
            $storedPassword = $row['password'];
            if(password_verify($password, $storedPassword) || $storedPassword === $password){
                $_SESSION['is_admin_logged'] = true;
                header("location: admin.php");
                exit();
            }
        }
        $error = "نام کاربری یا پسورد اشتباه است";
    } catch (Throwable $e) {
        $error = db_log_error($e, 'login_admin');
    }
}

?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به پنل استاد</title>
    <link rel="stylesheet" href="login_admin.css">
</head>
<body>

<div class="contanin">
    <form method="POST" action="">
        <div class="form_admin">
            <label for="full_namee">نام کاربری استاد</label>
            <input type="text" name="full_namee" id="full_namee" required>
        </div>
        
        <div class="from_admin">
            <label for="full_password">پسورد را وارد کنید</label>
            <input type="password" name="full_password" id="full_password" required>
        </div>
        
       <?php if($error != ""): ?>
            <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <input type="submit" value="ورود">
    </form>
</div>

</body>
</html>
