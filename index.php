<?php
session_start();
include('config/db.php');

// التقاط معرف القسم إذا تم الضغط عليه للتصفية
$category_filter = "";
if (isset($_GET['category']) && is_numeric($_GET['category'])) {
    $category_id = mysqli_real_escape_string($conn, $_GET['category']);
    $category_filter = " WHERE products.category_id = '$category_id' ";
}

// جلب الأقسام
$categories_query = "SELECT * FROM categories ORDER BY id DESC";
$categories_result = mysqli_query($conn, $categories_query);

// جلب المنتجات مع تطبيق التصفية الديناميكية
$products_query = "SELECT products.*, categories.category_name 
                   FROM products 
                   LEFT JOIN categories ON products.category_id = categories.id 
                   $category_filter 
                   ORDER BY products.id DESC";
$products_result = mysqli_query($conn, $products_query);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المتجر الإلكتروني - الواجهة الرئيسية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .page-header {
            background-color: #ffffff;
            padding: 25px 0;
            border-bottom: 1px solid #eef2f5;
            margin-bottom: 30px;
        }
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #eef2f5 !important;
            background-color: #ffffff;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.06) !important;
        }
        .product-image-container {
            height: 280px;
            overflow: hidden;
            background-color: #fdfdfd;
        }
        .product-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .product-card:hover img {
            transform: scale(1.02);
        }
        .filter-sidebar .list-group-item {
            border: none;
            padding: 12px 20px;
            margin-bottom: 4px;
            border-radius: 6px !important;
            font-weight: 500;
            color: #495057;
            transition: all 0.2s ease;
        }
        .filter-sidebar .list-group-item.active {
            background-color: #212529;
            color: #ffffff;
        }
        .filter-sidebar .list-group-item:hover:not(.active) {
            background-color: #f1f3f5;
            color: #212529;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold tracking-wide" href="index.php">BRAND STORE</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">الرئيسية</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if (isset($_SESSION['user_logged']) && $_SESSION['user_logged'] === true): ?>
                        <span class="navbar-text text-white me-2">مرحبًا، <strong><?php echo $_SESSION['username']; ?></strong></span>
                        <a href="user_logout.php" class="btn btn-outline-danger btn-sm px-3">تسجيل الخروج</a>
                    <?php else: ?>
                        <a href="user_login.php" class="btn btn-light btn-sm px-3 fw-bold">تسجيل دخول العميل</a>
                    <?php endif; ?>
                    <a href="admin/dashboard.php" class="btn btn-outline-light btn-sm px-3">لوحة التحكم</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="page-header">
        <div class="container text-center">
            <h1 class="fw-bold text-dark fs-3 mb-1">المنصة الإلكترونية للمنتجات</h1>
            <p class="text-muted small mb-0">تصفح التشكيلة الحصرية المضافة مباشرة من قاعدة البيانات</p>
        </div>
    </header>

    <div class="container mb-5">
        <div class="row g-4">
            
            <div class="col-md-3">
                <div class="filter-sidebar position-sticky" style="top: 90px;">
                    <h6 class="fw-bold text-secondary mb-3 px-1">تصفية الحساب والتصنيفات</h6>
                    <div class="list-group shadow-sm bg-white p-2 rounded-3 border-0">
                        <a href="index.php" class="list-group-item list-group-item-action <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">كافة المنتجات</a>
                        <?php
                        if (mysqli_num_rows($categories_result) > 0) {
                            while ($cat = mysqli_fetch_assoc($categories_result)) {
                                $active_class = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'active' : '';
                                echo "<a href='index.php?category=" . $cat['id'] . "' class='list-group-item list-group-item-action " . $active_class . "'>" . $cat['category_name'] . "</a>";
                            }
                        } else {
                            echo "<div class='p-3 text-muted small text-center'>لا تتوفر تصنيفات حالياً</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row g-4">
                    <?php
                    if (mysqli_num_rows($products_result) > 0) {
                        while ($product = mysqli_fetch_assoc($products_result)) {
                            ?>
                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm border-0 product-card rounded-3 overflow-hidden">
                                    
                                    <div class="product-image-container">
                                        <img src="uploads/<?php echo $product['product_image']; ?>" alt="صورة المنتج">
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column p-4">
                                        <span class="badge bg-light text-secondary border mb-2 align-self-start py-2 px-3 rounded-pill fw-semibold small"><?php echo $product['category_name']; ?></span>
                                        <h5 class="card-title fw-bold text-dark mb-2 fs-6"><?php echo $product['product_name']; ?></h5>
                                        <p class="card-text text-dark fw-bold fs-5 mt-auto mb-4"><?php echo number_format($product['product_price'], 2); ?> USD</p>
<a href="cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn btn-dark w-100 fw-bold py-2 rounded-2 text-decoration-none">إضافة إلى سلة المشتريات</a>                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<div class='col-12 text-center text-muted py-5 bg-white border rounded shadow-sm'>لا توجد منتجات متوفرة ضمن هذا القسم حالياً.</div>";
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>