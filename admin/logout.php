<?php
session_start();

// تدمير كافة سجلات الجلسة وإلغاء تفعيلها
session_unset();
session_destroy();

// تحويل المستخدم فوراً إلى صفحة تسجيل الدخول
header("Location: login.php");
exit();