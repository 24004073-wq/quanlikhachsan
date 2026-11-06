<?php
session_start();
include '../khachhang/ketnoi.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE tendangnhap = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if ($password == $admin['matkhau']) {
            $_SESSION['admin'] = $admin;
            header("Location: admin.php");
            exit();
        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin - HOTEL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0077b6 0%, #00b4d8 50%, #48cae4 100%);
        padding: 20px;
    }

    .login-box {
        background: #fff;
        padding: 45px 40px;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 420px;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-logo .logo-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #0077b6, #00b4d8);
        color: #fff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 15px;
        box-shadow: 0 8px 20px rgba(0, 119, 182, 0.4);
    }

    .login-logo h2 {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: 1px;
    }

    .login-logo p {
        color: #64748b;
        font-size: 13px;
        margin-top: 6px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #334155;
    }

    .form-group input {
        width: 100%;
        padding: 13px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 15px;
        outline: none;
        font-family: inherit;
        transition: all 0.2s;
        background: #f8fafc;
    }

    .form-group input:focus {
        border-color: #0077b6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(0, 119, 182, 0.1);
    }

    .btn-login {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #0077b6, #005f8a);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        font-family: inherit;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 10px;
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #005f8a, #00456a);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 119, 182, 0.3);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .hint {
        text-align: center;
        margin-top: 20px;
        font-size: 13px;
        color: #64748b;
        padding-top: 20px;
        border-top: 1px dashed #e2e8f0;
    }

    .hint b {
        color: #0077b6;
    }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="login-logo">
            <div class="logo-icon">H</div>
            <h2>HOTEL ADMIN</h2>
            <p>Hệ thống quản trị khách sạn</p>
        </div>

        <?php if($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Tên đăng nhập</label>
                <input type="text" name="username" required placeholder="Nhập tên đăng nhập" autofocus>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Mật khẩu</label>
                <input type="password" name="password" required placeholder="Nhập mật khẩu">
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Đăng nhập
            </button>
        </form>

        <p class="hint">
            Tài khoản mặc định: <b>admin / admin123</b>
        </p>
    </div>
</body>

</html>