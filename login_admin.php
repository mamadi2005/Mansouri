<?php
require_once 'includes/functions.php';
include "db.php";

start_app_session();

if(is_admin_logged()){
    redirect("admin.php");
}

$error = "";
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
            redirect("admin.php");
        }
    }
    $error = "نام کاربری یا پسورد اشتباه است";
}

render_head('ورود به پنل استاد', ['login_admin.css']);
?>
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
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <input type="submit" value="ورود">
    </form>
</div>

</body>
</html>
