<?php
session_start();

unset(
    $_SESSION['is_logged'],
    $_SESSION['student_code'],
    $_SESSION['full_name'],
    $_SESSION['code_verified']
);
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background: #f0f2f5;
            text-align: center;
            padding: 50px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        h3 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 15px;
            background: #d4edda;
            display: inline-block;
            padding: 15px 30px;
            border-radius: 10px;
        }

        p {
            font-size: 16px;
            color: #666;
            margin-top: 10px;
        }
    </style>

    <script>
        setTimeout(function(){
            window.location.href = "login.php";
        }, 6000);
    </script>
</head>
<body>
    <h3>✅ شما از پنل خارج شدید</h3>
    <p>تا 6 ثانیه دیگر منتقل می‌شوید...</p>
</body>
</html>
