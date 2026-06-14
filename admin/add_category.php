<?php
session_start();
include('../config/db.php');

// حماية الصفحة
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit();
}

// معالجة البيانات عند الضغط على زر الحفظ
if (isset($_POST['add_category_btn'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);

    if (!empty($category_name)) {
        $query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo "<script>alert('تم إضافة القسم بنجاح!'); window.location.href='view_categories.php';</script>";
        } else {
            echo "<script>alert('حدث خطأ أثناء الإضافة!');</script>";
        }
    } else {
        echo "<script>alert('الرجاء كتابة اسم القسم!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة قسم جديد</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">📊 لوحة التحكم</a>
            <div class="d-flex align-items-center">
                <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">الرئيسية</a>
                <a href="logout.php" class="btn btn-danger btn-sm">🚪 خروج</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white fw-bold text-center py-3">
                        ➕ إضافة قسم وتصنيف جديد للموقع
                    </div>
                    <div class="card-body p-4">
                        <form action="add_category.php" method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">اسم القسم الجديد:</label>
                                <input type="text" name="category_name" class="form-control form-control-lg" placeholder="مثال: هواتف ذكية، إلكترونيات، ملابس..." required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" name="add_category_btn" class="btn btn-success fw-bold px-4">حفظ القسم</button>
                                <a href="dashboard.php" class="btn btn-secondary px-4">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>