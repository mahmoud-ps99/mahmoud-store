<?php
session_start();
// إزالة الجلسات الخاصة بالعميل فقط دون المساس بجلسة المسؤول
unset($_SESSION['user_logged']);
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['user_role']);

header("Location: index.php");
exit();
?>