<?php
session_start();
include('config/db.php');

// حماية الصفحة: يجب أن يكون العميل مسجلاً دخوله ليتمكن من الشراء
if (!isset($_SESSION['user_logged']) || $_SESSION['user_logged'] !== true) {
    echo "<script>alert('الرجاء تسجيل الدخول أولاً لإضافة المنتجات إلى السلة.'); window.location.href='user_login.php';</script>";
    exit();
}

// 1. معالجة إضافة منتج إلى السلة عبر الجلسات (Sessions)
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $product_id = intval($_GET['id']);
    
    // جلب بيانات المنتج للتأكد من وجوده وسعره
    $p_query = "SELECT * FROM products WHERE id = '$product_id' LIMIT 1";
    $p_result = mysqli_query($conn, $p_query);
    
    if (mysqli_num_rows($p_result) == 1) {
        $product = mysqli_fetch_assoc($p_result);
        
        // إذا لم تكن السلة مجهزة مسبقاً، ننشئ مصفوفة لها
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        
        // إذا كان المنتج موجوداً مسبقاً في السلة، نزيد الكمية
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            // إضافة المنتج لأول مرة في السلة
            $_SESSION['cart'][$product_id] = array(
                'name' => $product['product_name'],
                'price' => $product['product_price'],
                'quantity' => 1
            );
        }
        // التعديل: التوجيه المباشر لصفحة السلة بدلاً من الرئيسية
        echo "<script>alert('تم إضافة المنتج إلى سلة المشتريات بنجاح.'); window.location.href='cart.php';</script>";
        exit();
    }
}

// 2. معالجة إفراغ السلة
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

// 3. معالجة إتمام عملية الشراء الفعلي وتخزينه في قاعدة البيانات
if (isset($_POST['checkout_btn'])) {
    if (!empty($_SESSION['cart'])) {
        $user_id = $_SESSION['user_id'];
        
        // حساب السعر الإجمالي للطلب كاملاً
        $total_price = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_price += ($item['price'] * $item['quantity']);
        }
        
        // أ) إدخال الطلب الرئيسي في جدول orders
        $order_query = "INSERT INTO orders (user_id, total_price, status) VALUES ('$user_id', '$total_price', 'pending')";
        $order_result = mysqli_query($conn, $order_query);
        
        if ($order_result) {
            // جلب رقم المعرف (ID) الخاص بهذا الطلب الذي تم إنشاؤه للتو
            $order_id = mysqli_insert_id($conn);
            
            // ب) إدخال تفاصيل كل منتج داخل جدول order_details
            foreach ($_SESSION['cart'] as $p_id => $item) {
                $price = $item['price'];
                $qty = $item['quantity'];
                
                $details_query = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES ('$order_id', '$p_id', '$qty', '$price')";
                mysqli_query($conn, $details_query);
            }
            
            // إفراغ السلة بعد نجاح الشراء
            unset($_SESSION['cart']);
            echo "<script>alert('تم تسجيل طلبك بنجاح ونقله لقاعدة البيانات الحية.'); window.location.href='index.php';</script>";
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سلة المشتريات</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold tracking-wide" href="index.php">BRAND STORE</a>
            <div>
                <a href="index.php" class="btn btn-outline-light btn-sm px-3">العودة للتسوق</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <h3 class="fw-bold text-secondary mb-4">تفاصيل سلة المشتريات الحالية</h3>
                
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>اسم المنتج</th>
                                    <th>السعر الفردي</th>
                                    <th>الكمية</th>
                                    <th>الإجمالي الفرعي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grand_total = 0;
                                if (!empty($_SESSION['cart'])) {
                                    foreach ($_SESSION['cart'] as $item) {
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $grand_total += $subtotal;
                                        echo "<tr>";
                                        echo "<td class='fw-bold text-dark'>" . $item['name'] . "</td>";
                                        echo "<td>" . number_format($item['price'], 2) . " USD</td>";
                                        echo "<td>" . $item['quantity'] . "</td>";
                                        echo "<td class='text-success fw-bold'>" . number_format($subtotal, 2) . " USD</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-muted p-5'>السلة فارغة حالياً.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <div class="card-footer bg-white p-4 d-flex justify-content-between align-items-center border-top">
                            <div>
                                <h5 class="fw-bold text-dark mb-0">المجموع الإجمالي العام: <span class="text-success"><?php echo number_format($grand_total, 2); ?> USD</span></h5>
                            </div>
                            <form action="" method="POST" class="m-0">
                                <a href="cart.php?action=clear" class="btn btn-outline-danger fw-bold me-2">تفريغ السلة</a>
                                <button type="submit" name="checkout_btn" class="btn btn-dark fw-bold px-4">تأكيد عملية الشراء</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>