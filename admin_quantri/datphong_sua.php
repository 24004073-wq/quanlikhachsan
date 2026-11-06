<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT d.*, k.hoten, p.tenphong 
                     FROM datphong d
                     JOIN khachhang k ON d.khachhang_id = k.id
                     JOIN phong p ON d.phong_id = p.id
                     WHERE d.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$don = $stmt->get_result()->fetch_assoc();
if (!$don) { header("Location: quanlidatphong.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ngaynhan = $_POST['ngaynhan'];
    $ngaytra = $_POST['ngaytra'];
    $sokhach = (int)$_POST['sokhach'];
    $diachi = trim($_POST['diachi']);
    $trangthai = $_POST['trangthai'];
    $ghichu = trim($_POST['ghichu']);

    $d1 = new DateTime($ngaynhan);
    $d2 = new DateTime($ngaytra);
    $sodem = max(1, $d1->diff($d2)->days);
    $stmtGia = $conn->prepare("SELECT gia FROM phong WHERE id = ?");
    $stmtGia->bind_param("i", $don['phong_id']);
    $stmtGia->execute();
    $gia = $stmtGia->get_result()->fetch_assoc()['gia'];
    $tongtien = $sodem * $gia;

    $stmt = $conn->prepare("UPDATE datphong SET ngaynhan=?, ngaytra=?, sokhach=?, diachi=?, tongtien=?, trangthai=?, ghichu=? WHERE id=?");
    $stmt->bind_param("ssississi", $ngaynhan, $ngaytra, $sokhach, $diachi, $tongtien, $trangthai, $ghichu, $id);
    $stmt->execute();
    ghiLichSu($conn, $_SESSION['admin']['id'], 'Sửa đơn', 'datphong', $id, "Cập nhật đơn #$id");
    header("Location: quanlidatphong.php?msg=Cập nhật thành công!");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa đơn đặt phòng</title>
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
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        font-family: inherit;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0077b6, #005f8a);
        color: #fff;
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #0f172a;
    }

    .form-card {
        background: #fff;
        padding: 32px;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        max-width: 900px;
    }

    .form-card .form-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 20px;
        margin-bottom: 24px;
        border-bottom: 2px solid #f1f5f9;
    }

    .form-card .form-header .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 22px;
    }

    .form-card .form-header h2 {
        font-size: 20px;
        font-weight: 800;
    }

    .form-card .form-header p {
        font-size: 13px;
        color: #64748b;
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
        transition: all 0.2s;
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
        gap: 20px;
    }

    .form-footer {
        padding-top: 20px;
        border-top: 2px solid #f1f5f9;
        margin-top: 8px;
        display: flex;
        gap: 12px;
    }

    .info-box {
        background: #e0f2fe;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 4px solid #0077b6;
        font-size: 14px;
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

        .form-row {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="brand">
                <div class="logo-icon">H</div><span>HOTEL ADMIN</span>
            </div>
            <nav>
                <a href="admin.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
                <a href="quanliphong.php"><i class="fas fa-bed"></i> <span>Quản lý phòng</span></a>
                <a href="quanlikhachhang.php"><i class="fas fa-users"></i> <span>Khách hàng</span></a>
                <a href="quanlidatphong.php" class="active"><i class="fas fa-calendar-check"></i> <span>Đặt
                        phòng</span></a>
                <a href="lichsudatphong.php"><i class="fas fa-history"></i> <span>Lịch sử đặt phòng</span></a>
                <a href="lichsuthanhtoan.php"><i class="fas fa-money-bill-wave"></i> <span>Lịch sử thanh toán</span></a>
                <a href="thongke.php"><i class="fas fa-chart-bar"></i> <span>Thống kê</span></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span></a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <h1><i class="fas fa-edit" style="color:#f59e0b;"></i> Sửa đơn #<?php echo $id; ?></h1>
                <a href="quanlidatphong.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>

            <div class="form-card">
                <div class="form-header">
                    <div class="icon-box"><i class="fas fa-calendar-edit"></i></div>
                    <div>
                        <h2>Sửa đơn đặt phòng #<?php echo $id; ?></h2>
                        <p>Cập nhật thông tin đơn đặt phòng</p>
                    </div>
                </div>

                <div class="info-box">
                    <b><?php echo htmlspecialchars($don['tenphong']); ?></b> — Khách:
                    <?php echo htmlspecialchars($don['hoten']); ?>
                </div>

                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-check"></i> Ngày nhận</label>
                            <input type="date" name="ngaynhan" value="<?php echo $don['ngaynhan']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-times"></i> Ngày trả</label>
                            <input type="date" name="ngaytra" value="<?php echo $don['ngaytra']; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-users"></i> Số khách</label>
                            <input type="number" name="sokhach" value="<?php echo $don['sokhach']; ?>" required min="1">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-info-circle"></i> Trạng thái</label>
                            <select name="trangthai">
                                <?php foreach(['Chờ xác nhận','Đã xác nhận','Trả phòng','Đã hủy'] as $tt): ?>
                                <option value="<?php echo $tt; ?>" <?php echo $don['trangthai']==$tt?'selected':''; ?>>
                                    <?php echo $tt; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Địa chỉ</label>
                        <input type="text" name="diachi" value="<?php echo htmlspecialchars($don['diachi']); ?>">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Ghi chú</label>
                        <textarea name="ghichu" rows="3"><?php echo htmlspecialchars($don['ghichu']); ?></textarea>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                        <a href="quanlidatphong.php" class="btn btn-secondary"><i class="fas fa-times"></i> Hủy</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>