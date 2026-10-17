<?php
session_start();
include 'ketnoi.php';
$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten = trim($_POST['hoten']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $chude = trim($_POST['chude']);
    $noidung = trim($_POST['noidung']);

    if (empty($hoten) || empty($email) || empty($noidung)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        $stmt = $conn->prepare("INSERT INTO lienhe (hoten, email, sdt, chude, noidung) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $hoten, $email, $sdt, $chude, $noidung);
        if ($stmt->execute()) {
            $success = "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.";
        } else {
            $error = "Có lỗi xảy ra, vui lòng thử lại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Liên hệ - HOTEL</title>
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

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        border: none;
        font-family: inherit;
        transition: 0.2s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0077b6, #005f8a);
        color: #fff;
    }

    .btn-outline {
        background: #f1f5f9;
        color: #0f172a;
        border: 1px solid #e2e8f0;
    }

    .header {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 0;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        font-size: 20px;
    }

    .logo-icon {
        background: linear-gradient(135deg, #0077b6, #00b4d8);
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 0;
        font-weight: 500;
        font-size: 15px;
    }

    .nav-links a {
        color: #475569;
        padding: 12px 24px;
        position: relative;
        transition: all 0.2s;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .nav-links a:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 1px;
        height: 20px;
        background: #e2e8f0;
    }

    .nav-links a:hover {
        color: #0077b6;
        background: #f0f9ff;
    }

    .nav-links a.active {
        color: #0077b6;
        font-weight: 700;
        background: #f0f9ff;
    }

    .nav-links a.active::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60%;
        height: 3px;
        background: #0077b6;
        border-radius: 3px 3px 0 0;
    }

    .auth-buttons {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .page-hero {
        background: linear-gradient(135deg, #0077b6, #00b4d8);
        color: #fff;
        padding: 60px 0;
        text-align: center;
    }

    .page-hero h1 {
        font-size: 42px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .page-hero p {
        font-size: 16px;
        opacity: 0.9;
    }

    .contact-wrap {
        max-width: 1200px;
        margin: -40px auto 60px;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 30px;
        position: relative;
        z-index: 2;
    }

    .info-box {
        background: #fff;
        padding: 36px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .info-box h2 {
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 24px;
    }

    .info-item {
        display: flex;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #e6f0f9, #dbeafe);
        color: #0077b6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .info-content h3 {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .info-content p {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    .info-content a {
        color: #0077b6;
        font-weight: 600;
    }

    .social-links {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }

    .social-links a {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #f1f5f9;
        color: #0077b6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .social-links a:hover {
        background: linear-gradient(135deg, #0077b6, #00b4d8);
        color: #fff;
    }

    .form-box {
        background: #fff;
        padding: 36px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .form-box h2 {
        font-size: 22px;
        font-weight: 800;
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

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        font-family: inherit;
        background: #f8fafc;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #0077b6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(0, 119, 182, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .alert {
        padding: 14px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border-left: 4px solid #10b981;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }

    .map-box {
        max-width: 1200px;
        margin: 0 auto 60px;
        padding: 0 20px;
    }

    .map-box iframe {
        width: 100%;
        height: 400px;
        border-radius: 20px;
        border: none;
    }

    .footer {
        background: #0f172a;
        color: #fff;
        padding: 60px 0 20px;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-brand {
        max-width: 400px;
    }

    .footer-brand .logo {
        color: #fff;
        margin-bottom: 16px;
    }

    .footer-brand p {
        color: #94a3b8;
        font-size: 14px;
    }

    .footer-bottom {
        text-align: center;
        border-top: 1px solid #1e293b;
        padding-top: 20px;
        color: #64748b;
        font-size: 13px;
    }

    @media (max-width:900px) {
        .contact-wrap {
            grid-template-columns: 1fr;
        }

        .page-hero h1 {
            font-size: 32px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .header-container {
            flex-direction: column;
            gap: 16px;
        }
    }
    </style>
</head>

<body>

    <header class="header">
        <div class="container header-container">
            <a href="../index.php" class="logo">
                <div class="logo-icon">H</div>
                <span>HOTEL</span>
            </a>
            <nav class="nav-links">
                <a href="../index.php">🏠 Phòng</a>
                <a href="donhangcuatoi.php">📋 Đặt phòng của tôi</a>
                <a href="lienhe.php" class="active">📞 Liên hệ</a>
            </nav>
            <div class="auth-buttons">
                <?php if(isset($_SESSION['user'])): ?>
                <span style="font-weight:600;">👤 <?php echo htmlspecialchars($_SESSION['user']['hoten']); ?></span>
                <a href="dangxuat.php" class="btn btn-outline">Đăng xuất</a>
                <?php else: ?>
                <a href="dangnhap.php" class="btn btn-outline">Đăng nhập</a>
                <a href="dangky.php" class="btn btn-primary">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="page-hero">
        <div class="container">
            <h1>📞 Liên hệ với chúng tôi</h1>
            <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn 24/7</p>
        </div>
    </section>

    <div class="contact-wrap">
        <div class="info-box">
            <h2>📇 Thông tin liên hệ</h2>
            <div class="info-item">
                <div class="info-icon">📍</div>
                <div class="info-content">
                    <h3>Địa chỉ</h3>
                    <p>123 Đường Nguyễn Huệ, Quận 1<br>TP. Hồ Chí Minh</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">📞</div>
                <div class="info-content">
                    <h3>Điện thoại</h3>
                    <p><a href="tel:0901234567">0901 234 567</a></p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">✉️</div>
                <div class="info-content">
                    <h3>Email</h3>
                    <p><a href="mailto:info@hotel.com">info@hotel.com</a></p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">🕐</div>
                <div class="info-content">
                    <h3>Giờ làm việc</h3>
                    <p>Lễ tân: 24/7<br>Đặt phòng: 8:00 - 22:00</p>
                </div>
            </div>
            <div class="social-links">
                <a href="#">📘</a><a href="#">📷</a><a href="#">▶️</a><a href="#">💬</a>
            </div>
        </div>

        <div class="form-box">
            <h2>✉️ Gửi tin nhắn</h2>
            <?php if($success): ?><div class="alert alert-success">✓ <?php echo $success; ?></div><?php endif; ?>
            <?php if($error): ?><div class="alert alert-error">⚠️ <?php echo $error; ?></div><?php endif; ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>👤 Họ tên *</label>
                        <input type="text" name="hoten" required
                            value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['hoten']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>📞 SĐT</label>
                        <input type="text" name="sdt"
                            value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['sdt']) : ''; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>✉️ Email *</label>
                    <input type="email" name="email" required
                        value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>🏷️ Chủ đề</label>
                    <select name="chude">
                        <option value="Tư vấn đặt phòng">🏨 Tư vấn đặt phòng</option>
                        <option value="Hỏi về dịch vụ">💼 Hỏi về dịch vụ</option>
                        <option value="Khiếu nại">📢 Khiếu nại</option>
                        <option value="Góp ý">💡 Góp ý</option>
                        <option value="Khác">❓ Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>💬 Nội dung *</label>
                    <textarea name="noidung" rows="5" required placeholder="Nhập nội dung..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;font-size:15px;">📤 Gửi tin
                    nhắn</button>
            </form>
        </div>
    </div>

    <div class="map-box">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.489!2d106.703!3d10.776!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTDCsDQ2JzMzLjYiTiAxMDbCsDQyJzEwLjgiRQ!5e0!3m2!1svi!2svn!4v1234567890"
            allowfullscreen="" loading="lazy"></iframe>
    </div>

    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-brand">
                <div class="logo">
                    <div class="logo-icon">H</div><span>HOTEL</span>
                </div>
                <p>Website đặt phòng dành cho khách hàng.</p>
            </div>
            <div style="color:#94a3b8;font-size:14px;">
                <h4 style="color:#fff;font-size:16px;margin-bottom:12px;">Liên hệ</h4>
                <p>📍 123 Nguyễn Huệ, Q.1</p>
                <p>📞 0901 234 567</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 HOTEL</p>
        </div>
    </footer>

</body>

</html>