<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit();
}

$categories_query = "SELECT * FROM categories ORDER BY id DESC";
$categories_result = mysqli_query($conn, $categories_query);

if (isset($_POST['add_product_btn'])) {
    // 1. تنظيف والتحقق من المدخلات النصية (Validation)
    $product_name = mysqli_real_escape_string($conn, trim($_POST['product_name']));
    $product_price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);
    $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
    
    // 2. التحقق من ملف الصورة المرفوعة والأمان
    $image_name = $_FILES['product_image']['name'];
    $image_tmp = $_FILES['product_image']['tmp_name'];
    $image_size = $_FILES['product_image']['size'];
    
    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');

    if (empty($product_name) || $product_price === false || $category_id === false || empty($image_name)) {
        $error_message = "الرجاء إدخال بيانات صحيحة ومكتملة للطلب.";
    } elseif (!in_array($image_ext, $allowed_extensions)) {
        $error_message = "امتداد الملف غير مسموح به. يرجى رفع صورة حقيقية فقط (JPG, PNG, GIF).";
    } elseif ($image_size > 5 * 1024 * 1024) { // حد أقصى 5 ميجابايت
        $error_message = "حجم الصورة كبير جداً، الحد الأقصى هو 5 ميجابايت.";
    } else {
        // توليد اسم عشوائي فريد للصورة لمنع تداخل الأسماء أو استغلال الثغرات
        $new_image_name = uniqid('prod_', true) . '.' . $image_ext;
        $upload_path = "../uploads/" . $new_image_name;

        if (move_uploaded_file($image_tmp, $upload_path)) {
            $insert_query = "INSERT INTO products (category_id, product_name, product_price, product_image) 
                             VALUES ('$category_id', '$product_name', '$product_price', '$new_image_name')";
            
            if (mysqli_query($conn, $insert_query)) {
                $success_message = "تم إضافة المنتج بنجاح وتأمين الصورة المرفوعة.";
            } else {
                $error_message = "فشل تسجيل المنتج في قاعدة البيانات.";
            }
        } else {
            $error_message = "فشل نقل الملف المرفوع إلى سيرفر النظام.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة منتج جديد</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>
<body class="bg-light">
    <div class="container my-5" style="max-width: 600px;">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-5">
                <h3 class="fw-bold text-dark mb-4 text-center">إضافة منتج جديد للمتجر</h3>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger text-center small py-2"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success text-center small py-2"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">اسم المنتج</label>
                        <input type="text" name="product_name" class="form-control py-2" required placeholder="مثال: تيشيرت قطني">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">القسم التابع له</label>
                        <select name="category_id" class="form-select py-2" required>
                            <option value="">اختر القسم...</option>
                            <?php
                            while ($cat = mysqli_fetch_assoc($categories_result)) {
                                echo "<option value='" . $cat['id'] . "'>" . $cat['category_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">سعر المنتج (USD)</label>
                        <input type="number" step="0.01" name="product_price" class="form-control py-2" required placeholder="0.00">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">صورة المنتج</label>
                        <input type="file" name="product_image" class="form-control py-2" required accept="image/*">
                    </div>
                    <button type="submit" name="add_product_btn" class="btn btn-dark w-100 fw-bold py-2 mb-3">حفظ المنتج بالنظام</button>
                    <div class="text-center">
                        <a href="dashboard.php" class="text-decoration-none text-muted small">العودة للوحة التحكم</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>