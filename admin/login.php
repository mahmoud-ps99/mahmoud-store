<?php
session_start();
include('../config/db.php');

if (isset($_POST['login_btn'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 

    // فحص مباشر بدون تشفير معقد
    $query = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $username;
        
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('اسم المستخدم أو كلمة المرور غير صحيحة!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول - لوحة التحكم</title>
</head>
<body>
    <div style="text-align: center; margin-top: 100px;">
        <h2>تسجيل دخول المسؤول</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="اسم المستخدم" required><br><br>
            <input type="password" name="password" placeholder="كلمة المرور" required><br><br>
            <button type="submit" name="login_btn">دخول</button>
        </form>
    </div>
</body>
</html>