<?php
session_start();
include('config/db.php');

// إذا كان المستخدم مسجلاً دخوله بالفعل، يتم توجيهه للمتجر مباشرة
if (isset($_SESSION['user_logged']) && $_SESSION['user_logged'] === true) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['login_btn'])) {
    // التحقق من المدخلات وتنظيفها لمنع ثغرات الحقن
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // الاستعلام عن حساب المستخدم وصلاحيته
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='user' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // تعيين الجلسات (Sessions) الخاصة بالعميل
        $_SESSION['user_logged'] = true;
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['user_role'] = $row['role'];

        header("Location: index.php");
        exit();
    } else {
        $error_message = "بيانات الاعتماد غير صحيحة، أو الحساب لا يملك صلاحية عميل.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول العميل</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            max-width: 450px;
            margin-top: 100px;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container login-container">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-5">
                <h3 class="fw-bold text-center text-dark mb-4">تسجيل دخول العميل</h3>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger text-center small py-2"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">اسم المستخدم</label>
                        <input type="text" name="username" class="form-control py-2" required placeholder="أدخل اسم المستخدم">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">كلمة المرور</label>
                        <input type="password" name="password" class="form-control py-2" required placeholder="أدخل كلمة المرور">
                    </div>
                    <button type="submit" name="login_btn" class="btn btn-dark w-100 fw-bold py-2 mb-3">تسجيل الدخول</button>
                    <div class="text-center">
                        <a href="index.php" class="text-decoration-none text-muted small">العودة للمتجر تصفح فقط</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>