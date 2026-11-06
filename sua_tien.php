<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT d.*, k.hoten, k.sdt, p.tenphong 
                     FROM datphong d
                     JOIN khachhang k ON d.khachhang_id = k.id
                     JOIN phong p ON d.phong_id = p.id
                     WHERE d.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$don = $stmt->get_result()->fetch_assoc();

if (!$don) {
    header("Location: quanlithanhtoan.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sotien = (int)$_POST['sotien'];
    $lydo = trim($_POST['lydo']);

    if ($sotien < 0 || $sotien > $don['tongtien']) {
        $error = "Số tiền phải từ 0 đến " . number_format($don['tongtien'],0,',','.') . " đ!";
    } else {
        $sotien_cu = $don['thanhtoan_truoc'];
        $updateStmt = $conn->prepare("UPDATE datphong SET thanhtoan_truoc = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $sotien, $id);
        $updateStmt->execute();
        ghiLichSu($conn, $_SESSION['admin']['id'], 'Sửa tiền', 'datphong', $id, 
            "Sửa tiền đã thu: " . number_format($sotien_cu,0,',','.') . " đ → " . number_format($sotien,0,',','.') . " đ. Lý do: $lydo");
        header("Location: quanlithanhtoan.php?msg=Đã sửa tiền thu của đơn #$id!");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa tiền đã thu</title>
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
        background: #f1f5f9;
        color: #0f172a;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    .admin-layout {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 260px;
        background: #0f172a;
        color: #fff;
        padding: 20px 0;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
    }

    .sidebar .brand {
        padding: 0 20px 20px;
        border-bottom: 1px solid #1e293b;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 800;
        font-size: 17px;
    }

    .sidebar .brand .logo-icon {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #0077b6, #00b4d8);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 18px;
    }

    .sidebar nav a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 20px;
        color: #cbd5e1;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }

    .sidebar nav a:hover,
    .sidebar nav a.active {
        background: #1e293b;
        color: #fff;
        border-left-color: #0077b6;
    }

    .sidebar nav a i {
        width: 20px;
        text-align: center;
        font-size: 15px;
    }

    .main-content {
        margin-left: 260px;
        flex: 1;
        padding: 24px;
    }

    .topbar {
        background: #fff;
        padding: 18px 24px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        flex-wrap: wrap;
        gap: 12px;
    }

    .topbar h1 {
        font-size: 22px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        font-family: inherit;
        text-decoration: none;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
    }

    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(245, 158, 11, 0.3);
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #0f172a;
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

    .form-card {
        background: #fff;
        padding: 32px;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        max-width: 800px;
    }

    .info-box {
        background: linear-gradient(135deg, #e0f2fe, #dbeafe);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        border-left: 4px solid #0077b6;
    }

    .info-box p {
        font-size: 14px;
        margin-bottom: 8px;
    }

    .info-box p:last-child {
        margin-bottom: 0;
    }

    .info-box b {
        color: #0077b6;
    }

    .warning-box {
        background: #fef3c7;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-left: 4px solid #f59e0b;
    }

    .warning-box p {
        font-size: 13px;
        color: #92400e;
    }

    .warning-box b {
        color: #92400e;
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

    .form-group input,
    .form-group textarea {
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

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #0077b6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(0, 119, 182, 0.1);
    }

    .btn-group {
        display: flex;
        gap: 12px;
        padding-top: 20px;
        border-top: 2px solid #f1f5f9;
    }

    @media (max-width:900px) {
        .sidebar {
            width: 70px;
        }

        .sidebar .brand span,
        .sidebar nav a span {
            display: none;
        }

        .main-content {
            margin-left: 70px;
        }
    }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="topbar">
                <h1><i class="fas fa-edit" style="color:#f59e0b;"></i> Sửa tiền thu đơn #<?php echo $id; ?></h1>
                <a href="quanlithanhtoan.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>

            <?php if($error): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-card">
                <div class="info-box">
                    <p><b>Khách hàng:</b> <?php echo htmlspecialchars($don['hoten']); ?></p>
                    <p><b>SĐT:</b> <?php echo htmlspecialchars($don['sdt']); ?></p>
                    <p><b>Phòng:</b> <?php echo htmlspecialchars($don['tenphong']); ?></p>
                    <p><b>Tổng tiền:</b> <?php echo number_format($don['tongtien'],0,',','.'); ?> đ</p>
                    <p><b>Đã thu hiện tại:</b> <span
                            style="color:#ef4444;font-weight:700;"><?php echo number_format($don['thanhtoan_truoc'],0,',','.'); ?>
                            đ</span></p>
                </div>

                <div class="warning-box">
                    <p><b>⚠️ Cảnh báo:</b> Bạn đang sửa lại số tiền đã thu. Hành động này sẽ ghi vào lịch sử.</p>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-money-bill"></i> Số tiền đã thu (sửa lại) *</label>
                        <input type="number" name="sotien" required min="0" max="<?php echo $don['tongtien']; ?>"
                            step="1000" value="<?php echo $don['thanhtoan_truoc']; ?>" placeholder="Nhập số tiền đúng">
                        <small style="color:#64748b;font-size:12px;display:block;margin-top:4px;">
                            Nhập số tiền khách THỰC SỰ đã trả (0 →
                            <?php echo number_format($don['tongtien'],0,',','.'); ?> đ)
                        </small>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Lý do sửa *</label>
                        <textarea name="lydo" rows="3" required
                            placeholder="VD: Nhập sai số tiền, khách thực tế chỉ trả 200.000đ"></textarea>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-warning"
                            onclick="return confirm('Bạn chắc chắn muốn sửa tiền đã thu?')">
                            <i class="fas fa-save"></i> Xác nhận sửa
                        </button>
                        <a href="quanlithanhtoan.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>