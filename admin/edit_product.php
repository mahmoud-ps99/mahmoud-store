<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit();
}

// 1. جلب بيانات المنتج الحالي لعرضها داخل الخانات
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM products WHERE id='$id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $product = mysqli_fetch_assoc($result);
    } else {
        header("Location: view_products.php");
        exit();
    }
} else {
    header("Location: view_products.php");
    exit();
}

// جلب الأقسام لعرضها في القائمة المنسدلة
$categories_result = mysqli_query($conn, "SELECT * FROM categories");

// 2. معالجة البيانات وتحديثها عند الضغط على زر التحديث
if (isset($_POST['update_product_btn'])) {
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    
    $new_image = $_FILES['product_image']['name'];
    $image_tmp = $_FILES['product_image']['tmp_name'];

    // فحص ما إذا كان المستخدم قد اختار صورة جديدة أم يريد الإبقاء على القديمة
    if (!empty($new_image)) {
        $image_ext = pathinfo($new_image, PATHINFO_EXTENSION);
        $new_image_name = time() . '_' . rand(100, 999) . '.' . $image_ext;
        $upload_path = "../uploads/" . $new_image_name;

        if (move_uploaded_file($image_tmp, $upload_path)) {
            // حذف الصورة القديمة من المجلد لتوفير المساحة
            $old_image_path = "../uploads/" . $product['product_image'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
            $image_sql = $new_image_name;
        }
    } else {
        // إذا لم يختر صورة جديدة، نترك الصورة القديمة كما هي
        $image_sql = $product['product_image'];
    }

    // تحديث البيانات في قاعدة البيانات
    $update_query = "UPDATE products SET 
                    category_id='$category_id', 
                    product_name='$product_name', 
                    product_price='$product_price', 
                    product_image='$image_sql' 
                    WHERE id='$id'";
    
    $update_result = mysqli_query($conn, $update_query);

    if ($update_result) {
        echo "<script>alert('تم تحديث المنتج بنجاح!'); window.location.href='view_products.php';</script>";
    } else {
        echo "<script>alert('حدث خطأ أثناء التحديث!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المنتج</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-dark fw-bold text-center py-3">
                        ✏️ تعديل بيانات المنتج
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">القسم:</label>
                                <select name="category_id" class="form-select" required>
                                    <?php
                                    while ($cat = mysqli_fetch_assoc($categories_result)) {
                                        $selected = ($cat['id'] == $product['category_id']) ? "selected" : "";
                                        echo "<option value='".$cat['id']."' $selected>".$cat['category_name']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم المنتج:</label>
                                <input type="text" name="product_name" class="form-control" value="<?php echo $product['product_name']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">السعر ($):</label>
                                <input type="number" step="0.01" name="product_price" class="form-control" value="<?php echo $product['product_price']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block fw-bold">الصورة الحالية:</label>
                                <img src="../uploads/<?php echo $product['product_image']; ?>" class="img-thumbnail mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                <input type="file" name="product_image" class="form-control" accept="image/*">
                                <small class="text-muted">اتركه فارغاً إذا كنت لا تريد تغيير الصورة.</small>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" name="update_product_btn" class="btn btn-warning fw-bold">تحديث المنتج</button>
                                <a href="view_products.php" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>