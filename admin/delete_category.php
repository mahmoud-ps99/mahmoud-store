<?php
session_start();
include('../config/db.php');

// حماية الملف
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit();
}

// التأكد من إرسال رقم القسم المراد حذفه عبر الرابط
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // استعلام الحذف من قاعدة البيانات
    $query = "DELETE FROM categories WHERE id='$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('تم حذف القسم بنجاح!'); window.location.href='view_categories.php';</script>";
    } else {
        echo "<script>alert('حدث خطأ أثناء محاولة الحذف!'); window.location.href='view_categories.php';</script>";
    }
} else {
    header("Location: view_categories.php");
    exit();
}
?>