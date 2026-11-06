<?php
session_start();
include 'ketnoi.php';

if (!isset($_SESSION['user'])) {
    header("Location: dangnhap.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

if (isset($_GET['huy'])) {
    $id = (int)$_GET['huy'];
    $checkStmt = $conn->prepare("SELECT id FROM datphong WHERE id = ? AND khachhang_id = ? AND trangthai = 'Chờ xác nhận'");
    $checkStmt->bind_param("ii", $id, $user_id);
    $checkStmt->execute();
    $check = $checkStmt->get_result();
    if ($check->num_rows > 0) {
        $cancelStmt = $conn->prepare("UPDATE datphong SET trangthai = 'Đã hủy' WHERE id = ?");
        $cancelStmt->bind_param("i", $id);
        $cancelStmt->execute();
        header("Location: donhangcuatoi.php?msg=Đã hủy đơn!");
        exit();
    }
}

$filter = $_GET['status'] ?? 'all';
$where = "WHERE d.khachhang_id = ?";
if ($filter != 'all') {
    $filter_safe = $filter;
    $where .= " AND d.trangthai = ?";
}

$query = "SELECT d.*, p.tenphong, p.loaiphong, p.hinhanh 
                        FROM datphong d 
                        JOIN phong p ON d.phong_id = p.id 
                        $where
                        ORDER BY d.id DESC";

$stmt = $conn->prepare($query);
if ($filter == 'all') {
    $stmt->bind_param("i", $user_id);
} else {
    $stmt->bind_param("is", $user_id, $filter_safe);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt phòng của tôi - HOTEL</title>
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
        gap: 30px;
        font-weight: 500;
        font-size: 15px;
        color: #475569;
    }

    .nav-links a:hover {
        color: #0077b6;
    }

    .auth-buttons {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
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

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 119, 182, 0.3);
    }

    .btn-outline {
        background: #f1f5f9;
        color: #0f172a;
        border: 1px solid #e2e8f0;
    }

    .btn-outline:hover {
        background: #e2e8f0;
    }

    .page-wrap {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .page-title {
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        color: #0077b6;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        padding: 14px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-left: 4px solid #10b981;
        font-weight: 600;
    }

    .filter-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .filter-bar a {
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        color: #475569;
        background: #fff;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .filter-bar a.active,
    .filter-bar a:hover {
        background: linear-gradient(135deg, #0077b6, #005f8a);
        color: #fff;
        border-color: #0077b6;
    }

    .booking-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        display: flex;
        gap: 20px;
        align-items: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .booking-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .booking-card img {
        width: 130px;
        height: 95px;
        border-radius: 10px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .booking-info {
        flex: 1;
    }

    .booking-info h3 {
        font-size: 17px;
        margin-bottom: 6px;
        color: #0f172a;
        font-weight: 700;
    }

    .booking-info p {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .booking-info p i {
        color: #0077b6;
        margin-right: 4px;
    }

    .booking-info .price {
        font-size: 20px;
        font-weight: 800;
        color: #0077b6;
        margin-top: 8px;
    }

    .badge {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .empty-state {
        background: #fff;
        padding: 60px 20px;
        border-radius: 16px;
        text-align: center;
        color: #64748b;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .empty-state i {
        font-size: 56px;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }

    .empty-state p {
        font-size: 15px;
        margin-bottom: 20px;
    }

    .footer {
        background: #0f172a;
        color: #fff;
        padding: 40px 0 20px;
        margin-top: 60px;
        text-align: center;
    }

    .footer p {
        color: #94a3b8;
        font-size: 13px;
    }

    @media (max-width:768px) {
        .header-container {
            flex-direction: column;
            gap: 16px;
        }

        .booking-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .booking-card img {
            width: 100%;
            height: 180px;
        }
    }
    </style>
</head>

<body>

    <header class="header">
        <div class="container header-container">
            <a href="trangchu.php" class="logo">
                <div class="logo-icon">H</div>
                <span>HOTEL</span>
            </a>
            <nav class="nav-links">
                <a href="trangchu.php">Trang chủ</a>
                <a href="donhangcuatoi.php">Đặt phòng của tôi</a>
                <a href="lienhe.php">Liên hệ</a>
            </nav>
            <div class="auth-buttons">
                <span style="font-weight:600;">👤 <?php echo htmlspecialchars($_SESSION['user']['hoten']); ?></span>
                <a href="dangxuat.php" class="btn btn-outline">Đăng xuất</a>
            </div>
        </div>
    </header>

    <div class="page-wrap">
        <h1 class="page-title"><i class="fas fa-calendar-check"></i> Đặt phòng của tôi</h1>

        <?php if(isset($_GET['msg'])): ?>
        <div class="alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
        <?php endif; ?>

        <div class="filter-bar">
            <a href="?status=all" class="<?php echo $filter=='all'?'active':''; ?>">Tất cả</a>
            <a href="?status=Chờ xác nhận" class="<?php echo $filter=='Chờ xác nhận'?'active':''; ?>">Chờ xác nhận</a>
            <a href="?status=Đã xác nhận" class="<?php echo $filter=='Đã xác nhận'?'active':''; ?>">Đã xác nhận</a>
            <a href="?status=Trả phòng" class="<?php echo $filter=='Trả phòng'?'active':''; ?>">Trả phòng</a>
            <a href="?status=Đã hủy" class="<?php echo $filter=='Đã hủy'?'active':''; ?>">Đã hủy</a>
        </div>

        <?php if ($result->num_rows == 0): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Bạn chưa có đơn đặt phòng nào.</p>
            <a href="trangchu.php#rooms" class="btn btn-primary"><i class="fas fa-plus"></i> Đặt phòng ngay</a>
        </div>
        <?php else: ?>
        <?php while($row = $result->fetch_assoc()): 
            $d1 = new DateTime($row['ngaynhan']);
            $d2 = new DateTime($row['ngaytra']);
            $sodem = max(1, $d1->diff($d2)->days);
            $cls = 'badge-warning';
            if ($row['trangthai']=='Đã xác nhận') $cls='badge-success';
            elseif ($row['trangthai']=='Trả phòng') $cls='badge-info';
            elseif ($row['trangthai']=='Đã hủy') $cls='badge-danger';
        ?>
        <div class="booking-card">
            <img src="../image/<?php echo htmlspecialchars($row['hinhanh']); ?>" alt="phòng">
            <div class="booking-info">
                <h3><?php echo htmlspecialchars($row['tenphong']); ?> —
                    <?php echo htmlspecialchars($row['loaiphong']); ?></h3>
                <p><i class="fas fa-calendar-alt"></i> Nhận:
                    <b><?php echo date('d/m/Y', strtotime($row['ngaynhan'])); ?></b> — Trả:
                    <b><?php echo date('d/m/Y', strtotime($row['ngaytra'])); ?></b>
                    (<?php echo $sodem; ?> đêm)</p>
                <p><i class="fas fa-users"></i> <?php echo $row['sokhach']; ?> khách • Đặt ngày
                    <?php echo date('d/m/Y H:i', strtotime($row['ngaydat'])); ?></p>
                <p><span class="badge <?php echo $cls; ?>"><?php echo $row['trangthai']; ?></span></p>
                <div class="price"><?php echo number_format($row['tongtien'],0,',','.'); ?> đ</div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php if ($row['trangthai'] == 'Chờ xác nhận'): ?>
                <a href="?huy=<?php echo $row['id']; ?>" class="btn btn-outline"
                    onclick="return confirm('Bạn chắc chắn muốn hủy đơn này?')"
                    style="color:#ef4444;border-color:#fecaca;">
                    <i class="fas fa-times"></i> Hủy
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p>© 2026 HOTEL · Website đặt phòng khách hàng</p>
        </div>
    </footer>

</body>

</html>