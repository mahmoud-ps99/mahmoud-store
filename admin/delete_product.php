<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset(_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. جلب اسم الصورة أولاً لحذفها من المجلد
    $img_query = "SELECT product_image FROM products WHERE id='$id'";
    $img_result = mysqli_query($conn, $img_query);
    
    if (mysqli_num_rows($img_result) == 1) {
        $row = mysqli_fetch_assoc($img_result);
        $image_name = $row['product_image'];
        $image_path = "../uploads/" . $image_name;

        // حذف الصورة من المجلد إذا كانت موجودة فعلياً
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // 2. حذف سجل المنتج من قاعدة البيانات
    $query = "DELETE FROM products WHERE id='$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('تم حذف المنتج وصورته بنجاح!'); window.location.href='view_products.php';</script>";
    } else {
        echo "<script>alert('حدث خطأ أثناء الحذف!'); window.location.href='view_products.php';</script>";
    }
} else {
    header("Location: view_products.php");
    exit();
}
?>