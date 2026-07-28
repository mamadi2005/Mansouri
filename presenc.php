<?php
session_start();
if(
    !isset($_SESSION['code_verified']) ||
    $_SESSION['code_verified'] !== true
){
    header("location: panel.php");
    exit();
}

$full_name = $_SESSION['full_name'] ?? '';
$student_code = $_SESSION['student_code'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل حضور</title>
    <link rel="stylesheet" href="presenc.css">
    <style>
        body {
    margin: 0;
    font-family: Tahoma, sans-serif;
    background: #f4f6f8;
    direction: rtl;

    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.header {
    background: #2c3e50;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.container {
    flex: 1;

    display: flex;
    justify-content: center;
    align-items: center;
}

.box {
    background: white;
    width: 340px;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    text-align: right;
}

.box input[type="text"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.check {
    display: block;
    margin: 12px 0;
    font-size: 14px;
}

button {
    width: 100%;
    padding: 10px;
    background: #2c3e50;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

button:hover {
    background: #2c3e50;
}

.logout {
    color: white;
    text-decoration: none;
    background: #e74c3c;
    padding: 8px 12px;
    border-radius: 6px;
}
    </style>
</head>
<body>

<header>
    <div class="para_test">
        <p class="paragraph">پنل حضور دانشجو</p>
        <div class="logout_header">
            <a href="logout.php">خروج از پنل</a>
        </div>
    </div>
</header>

<main>

<div class="center_para">
    <p>ثبت حضور + انتخاب درس</p>
</div>

<label>نام درس:</label>
<input type="text" id="darsInput" placeholder="اسم درست رو وارد کن">

<br><br>

<label>
    <input type="checkbox" id="checkbox">
    تایید حضور
</label>

<br><br>

<button id="submit">ارسال نهایی</button>

</main>

<script>
document.getElementById('submit').onclick = function(){

    let full_name = <?php echo json_encode($full_name); ?>;
    let student_code = <?php echo json_encode($student_code); ?>;
    let dars = document.getElementById('darsInput').value;
    let check = document.getElementById('checkbox').checked;

    if(dars.trim() === ""){
        alert("اسم درس را وارد کن");
        return;
    }
    if(!check){
        alert("تایید حضور را بزن");
        return;
    }

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function(){
        if(this.readyState != 4){
            return;
        }

        let response = null;
        try {
            response = JSON.parse(this.responseText);
        } catch (e) {
            response = null;
        }

        if(this.status == 200 && response && response.success){
            alert(response.message || "حضور + درس ثبت شد");
            window.location.href = "logout.php";
            return;
        }

        alert((response && response.message) || "ثبت حضور انجام نشد. لطفا دوباره تلاش کنید");
    };

    xhttp.onerror = function(){
        alert("ارتباط با سرور برقرار نشد");
    };

    xhttp.open("POST", "submit_presence.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.send(
        "full_name=" + encodeURIComponent(full_name) +
        "&student_code=" + encodeURIComponent(student_code) +
        "&dars=" + encodeURIComponent(dars)
    );
};
</script>








<footer class="footer">
    <div class="footer-content">
        <p>تمامی حقوق این سایت محفوظ است © 2026</p>
        <p>سامانه ثبت حضور و غیاب دانشجویان</p>
    </div>
</footer>
</body>
</html>
