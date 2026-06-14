<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit();
}

$query = "SELECT products.*, categories.category_name 
          FROM products 
          LEFT JOIN categories ON products.category_id = categories.id 
          ORDER BY products.id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المنتجات الحالية</title>
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
            <div class="col-md-11">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-secondary">📦 قائمة المنتجات الحالية</h2>
                    <a href="add_product.php" class="btn btn-info text-white fw-bold">➕ إضافة منتج جديد</a>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped mb-0 text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 15%;">صورة المنتج</th>
                                    <th>اسم المنتج</th>
                                    <th>القسم التابع له</th>
                                    <th>السعر</th>
                                    <th>تاريخ الإضافة</th>
                                    <th style="width: 20%;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td><img src='../uploads/" . $row['product_image'] . "' class='rounded border shadow-sm' style='width: 60px; height: 60px; object-fit: cover;'></td>";
                                        echo "<td class='fw-bold text-dark'>" . $row['product_name'] . "</td>";
                                        echo "<td><span class='badge bg-secondary px-3 py-2'>" . ($row['category_name'] ? $row['category_name'] : 'غير مصنف') . "</span></td>";
                                        echo "<td class='fw-bold text-success'>" . number_format($row['product_price'], 2) . " $</td>";
                                        echo "<td>" . $row['created_at'] . "</td>";
                                        // إضافة روابط العمليات الديناميكية هنا
                                        echo "<td>
                                                <a href='edit_product.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm fw-bold me-1'>✏️ تعديل</a>
                                                <a href='delete_product.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm fw-bold' onclick='return confirm(\"هل أنت متأكد من حذف هذا المنتج؟\")'>🗑️ حذف</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-muted p-4'>لا توجد منتجات مضافة حالياً.</td></tr>";
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