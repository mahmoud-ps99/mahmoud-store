<?php
session_start();

// فحص الجلسة والأمان
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - الرئيسية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">لوحة تحكم النظام</a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3">مرحبًا، <strong><?php echo $_SESSION['admin_user']; ?></strong></span>
                <a href="logout.php" class="btn btn-danger btn-sm">تسجيل الخروج</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-11 text-center">
                <h1 class="mb-4 fw-bold text-secondary">إدارة النظام وقاعدة البيانات</h1>
                <p class="text-muted mb-5">مرحبًا بك مجددًا في نظام الإدارة المركزي. يمكنك التحكم بكافة الأقسام والمنتجات المتاحة على الموقع من خلال الأدوات أدناه.</p>
                
                <div class="row g-4 justify-content-center">
                    
                    <div class="col-md-5">
                        <div class="card h-100 shadow-sm border-0 p-3">
                            <div class="card-body text-center">
                                <h4 class="card-title fw-bold mb-3">إدارة الأقسام</h4>
                                <p class="text-muted small mb-4">إضافة وتعديل وحذف التصنيفات الرئيسية المتاحة في المتجر.</p>
                                <div class="d-grid gap-2">
                                    <a href="add_category.php" class="btn btn-success fw-bold">إضافة قسم جديد</a>
                                    <a href="view_categories.php" class="btn btn-outline-primary fw-bold">عرض الأقسام الحالية</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card h-100 shadow-sm border-0 p-3">
                            <div class="card-body text-center">
                                <h4 class="card-title fw-bold mb-3">إدارة المنتجات</h4>
                                <p class="text-muted small mb-4">إضافة منتجات جديدة، تحديد الأسعار، ورفع الصور الخاصة بها وتحديثها.</p>
                                <div class="d-grid gap-2">
                                    <a href="add_product.php" class="btn btn-info text-white fw-bold">إضافة منتج جديد</a>
                                    <a href="view_products.php" class="btn btn-outline-info fw-bold">عرض المنتجات الحالية</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>