<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به پنل استاد</title>
    <link rel="stylesheet" href="login_admin.css">
    <style>
       
    </style>
</head>
<body>

<?php

session_start();
$error = "";
if(isset($_SESSION['is_admin_logged']) && $_SESSION['is_admin_logged'] === true){
    header("location: admin.php");
    exit();
}
include "db.php";
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = $_POST['full_namee'];
    $password = $_POST['full_password'];
    
    $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result && $row = $result->fetch_assoc()){
        $storedPassword = $row['password'];
        if(password_verify($password, $storedPassword) || $storedPassword === $password){
            $_SESSION['is_admin_logged'] = true;
            header("location: admin.php");
            exit();
        }
    }
    $error = "نام کاربری یا پسورد اشتباه است";
}

?>

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
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <input type="submit" value="ورود">
    </form>
</div>

</body>
</html>
