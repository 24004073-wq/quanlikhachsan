<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

if (isset($_GET['xoa'])) {
    $id = (int)$_GET['xoa'];
    $stmtKh = $conn->prepare("SELECT hoten FROM khachhang WHERE id = ?");
    $stmtKh->bind_param("i", $id);
    $stmtKh->execute();
    $kh = $stmtKh->get_result()->fetch_assoc();
    if ($kh) {
        $checkStmt = $conn->prepare("SELECT COUNT(*) as t FROM datphong WHERE khachhang_id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $check = $checkStmt->get_result()->fetch_assoc()['t'];
        if ($check > 0) {
            $error = "Không thể xóa! Khách hàng có $check đơn đặt phòng.";
        } else {
            $deleteStmt = $conn->prepare("DELETE FROM khachhang WHERE id = ?");
            $deleteStmt->bind_param("i", $id);
            $deleteStmt->execute();
            ghiLichSu($conn, $_SESSION['admin']['id'], 'Xóa KH', 'khachhang', $id, "Xóa KH {$kh['hoten']}");
            header("Location: quanlikhachhang.php?msg=Đã xóa!");
            exit();
        }
    }
}

$kw = $_GET['search'] ?? '';
if ($kw) {
    $stmt = $conn->prepare("SELECT * FROM khachhang WHERE hoten LIKE ? OR sdt LIKE ? OR email LIKE ? ORDER BY id DESC");
    $like = "%$kw%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM khachhang ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý khách hàng</title>
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

    .btn-danger {
        background: #ef4444;
        color: #fff;
    }

    .btn-success {
        background: #10b981;
        color: #fff;
    }

    .btn-warning {
        background: #f59e0b;
        color: #fff;
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #0f172a;
    }

    .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
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

    .search-box {
        background: #fff;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        gap: 10px;
        max-width: 500px;
    }

    .search-box input {
        flex: 1;
        padding: 10px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        font-family: inherit;
    }

    .search-box input:focus {
        border-color: #0077b6;
    }

    .table-wrapper {
        background: #fff;
        border-radius: 14px;
        overflow-x: auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table thead {
        background: linear-gradient(135deg, #0077b6, #005f8a);
        color: #fff;
    }

    table th {
        padding: 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
    }

    table td {
        padding: 14px 16px;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    table tbody tr:hover {
        background: #f8fafc;
    }

    .badge {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .actions {
        display: flex;
        gap: 6px;
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
                <h1><i class="fas fa-users" style="color:#0077b6;"></i> Quản lý khách hàng</h1>
                <div style="display:flex;gap:8px;">
                    <a href="xuat_excel.php?type=khachhang" class="btn btn-success"><i class="fas fa-file-excel"></i>
                        Excel</a>
                    <a href="khachhang_them.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm</a>
                </div>
            </div>
            <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="🔍 Tìm tên, SĐT, email..."
                    value="<?php echo htmlspecialchars($kw); ?>">
                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>CCCD</th>
                            <th>SĐT</th>
                            <th>Email</th>
                            <th>Tài khoản</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><b>#<?php echo $row['id']; ?></b></td>
                            <td><b><?php echo htmlspecialchars($row['hoten']); ?></b></td>
                            <td><?php echo htmlspecialchars($row['cccd']); ?></td>
                            <td><?php echo htmlspecialchars($row['sdt']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span
                                    class="badge badge-info"><?php echo htmlspecialchars($row['tendangnhap']); ?></span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($row['ngaytao'])); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="lichsudatphong.php?kh_id=<?php echo $row['id']; ?>"
                                        class="btn btn-secondary btn-sm"><i class="fas fa-history"></i></a>
                                    <a href="khachhang_sua.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <a href="quanlikhachhang.php?xoa=<?php echo $row['id']; ?>"
                                        class="btn btn-danger btn-sm" onclick="return confirm('Xóa khách hàng này?')"><i
                                            class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>