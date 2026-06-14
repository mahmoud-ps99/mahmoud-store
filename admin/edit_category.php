<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit();
}

// 1. جلب البيانات القديمة لعرضها في الخانة
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM categories WHERE id='$id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
    } else {
        header("Location: view_categories.php");
        exit();
    }
} else {
    header("Location: view_categories.php");
    exit();
}

// 2. معالجة البيانات وتحديثها عند الضغط على زر التحديث
if (isset($_POST['update_category_btn'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);

    if (!empty($category_name)) {
        $update_query = "UPDATE categories SET category_name='$category_name' WHERE id='$id'";
        $update_result = mysqli_query($conn, $update_query);

        if ($update_result) {
            echo "<script>alert('تم تحديث القسم بنجاح!'); window.location.href='view_categories.php';</script>";
        } else {
            echo "<script>alert('حدث خطأ أثناء التحديث!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل القسم</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-dark fw-bold text-center py-3">
                        ✏️ تعديل اسم القسم الحالي
                    </div>
                    <div class="card-body p-4">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">اسم القسم:</label>
                                <input type="text" name="category_name" class="form-control" value="<?php echo $row['category_name']; ?>" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" name="update_category_btn" class="btn btn-warning fw-bold">تحديث البيانات</button>
                                <a href="view_categories.php" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>