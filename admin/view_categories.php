<?php
session_start();
include('../config/db.php');

// فحص الجلسة لحماية الصفحة
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit();
}

// استعلام لجلب الأقسام من قاعدة البيانات
$query = "SELECT * FROM categories ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأقسام الحالية</title>
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
            <div class="col-md-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-secondary">📂 قائمة الأقسام المضافة</h2>
                    <a href="add_category.php" class="btn btn-success fw-bold">➕ إضافة قسم جديد</a>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped mb-0 text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 15%;">رقم القسم (ID)</th>
                                    <th>اسم التصنيف / القسم</th>
                                    <th style="width: 25%;">تاريخ الإضافة</th>
                                    <th style="width: 20%;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td class='fw-bold text-muted'>#" . $row['id'] . "</td>";
                                        echo "<td class='fw-bold text-dark'>" . $row['category_name'] . "</td>";
                                        echo "<td>" . $row['created_at'] . "</td>";
                                        echo "<td>
                                                <a href='edit_category.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm fw-bold me-1'>✏️ تعديل</a>
                                                <a href='delete_category.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm fw-bold' onclick='return confirm(\"هل أنت متأكد من حذف هذا القسم؟\")'>🗑️ حذف</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-muted p-4'>لا توجد أقسام مضافة حالياً في قاعدة البيانات.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>