<?php
session_start();
include 'ketnoi.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM khachhang WHERE tendangnhap = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password == $user['matkhau']) {
            $_SESSION['user'] = $user;
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Tên đăng nhập không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - HOTEL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background: #f8fafc;
        color: #0f172a;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    .auth-container {
        display: flex;
        min-height: 100vh;
    }

    .auth-left {
        flex: 1;
        background: linear-gradient(135deg, #0077b6, #00b4d8);
        padding: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: #fff;
    }

    .auth-left h1 {
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 16px;
    }

    .auth-left p {
        font-size: 16px;
        opacity: 0.9;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .auth-left .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        font-size: 20px;
        margin-bottom: 30px;
    }

    .auth-left .logo-icon {
        background: #fff;
        color: #0077b6;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 18px;
    }

    .auth-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: #f8fafc;
    }

    .auth-form-box {
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 450px;
    }

    .auth-form-box h2 {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .auth-form-box>p {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 24px;
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

    .form-group label i {
        color: #0077b6;
        margin-right: 4px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        font-family: inherit;
        background: #f8fafc;
        transition: all 0.2s;
    }

    .form-group input:focus {
        border-color: #0077b6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(0, 119, 182, 0.1);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        font-family: inherit;
        text-decoration: none;
        width: 100%;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0077b6, #005f8a);
        color: #fff;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 119, 182, 0.3);
    }

    .btn-outline {
        background: transparent;
        color: #fff;
        border: 2px solid #fff;
        width: fit-content;
        padding: 10px 20px;
    }

    .btn-outline:hover {
        background: #fff;
        color: #0077b6;
    }

    .alert {
        padding: 14px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }

    .auth-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #64748b;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }

    .auth-footer a {
        color: #0077b6;
        font-weight: 600;
    }

    @media (max-width:900px) {
        .auth-container {
            flex-direction: column;
        }

        .auth-left {
            padding: 40px 30px;
        }

        .auth-left h1 {
            font-size: 28px;
        }

        .auth-right {
            padding: 30px 20px;
        }

        .auth-form-box {
            padding: 30px 24px;
        }
    }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-left">
            <div class="logo">
                <div class="logo-icon">H</div><span>HOTEL</span>
            </div>
            <h1>Chào mừng bạn quay lại.</h1>
            <p>Đăng nhập để đặt phòng nhanh hơn và xem toàn bộ lịch sử đặt phòng của bạn.</p>
            <a href="../index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Về trang chủ</a>
        </div>
        <div class="auth-right">
            <div class="auth-form-box">
                <h2>Đăng nhập</h2>
                <p>Sử dụng tài khoản khách hàng của bạn.</p>
                <?php if($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
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
                    <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Đăng nhập</button>
                </form>
                <div class="auth-footer">
                    Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a> • <a href="quenmatkhau.php">Quên mật
                        khẩu?</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>