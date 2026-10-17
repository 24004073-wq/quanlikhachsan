<?php
include 'check_loginadmin.php';
include '../khachhang/ketnoi.php';

if (isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $tt = $_POST['trangthai'];
    
    // Lấy thông tin đơn
    $don = $conn->query("SELECT * FROM datphong WHERE id=$id")->fetch_assoc();
    
    // ⭐ TỰ ĐỘNG CẬP NHẬT THANH TOÁN KHI ĐỔI TRẠNG THÁI
    if ($tt == 'Trả phòng') {
        // Khách trả phòng → đánh dấu đã trả đủ
        $conn->query("UPDATE datphong SET thanhtoan_truoc = tongtien, trangthai='$tt' WHERE id=$id");
        // Phòng về trống
        $conn->query("UPDATE phong SET trangthai='Trống' WHERE id={$don['phong_id']}");
    } elseif ($tt == 'Đã xác nhận') {
        $conn->query("UPDATE datphong SET trangthai='$tt' WHERE id=$id");
        $conn->query("UPDATE phong SET trangthai='Đã đặt' WHERE id={$don['phong_id']}");
    } elseif ($tt == 'Đã hủy') {
        $conn->query("UPDATE datphong SET trangthai='$tt' WHERE id=$id");
        $conn->query("UPDATE phong SET trangthai='Trống' WHERE id={$don['phong_id']}");
    } else {
        $conn->query("UPDATE datphong SET trangthai='$tt' WHERE id=$id");
    }
    
    ghiLichSu($conn, $_SESSION['admin']['id'], 'Cập nhật đơn', 'datphong', $id, "Đổi trạng thái thành $tt");
    header("Location: quanlidatphong.php?msg=Cập nhật thành công!");
    exit();
}

$result = $conn->query("SELECT d.*, k.hoten, k.sdt, p.tenphong FROM datphong d
                        JOIN khachhang k ON d.khachhang_id=k.id
                        JOIN phong p ON d.phong_id=p.id ORDER BY d.id DESC");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý đặt phòng</title>
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
        padding: 14px 12px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    table td {
        padding: 14px 12px;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
    }

    table tbody tr:hover {
        background: #f8fafc;
    }

    select {
        padding: 6px 10px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        font-size: 12px;
        outline: none;
        font-family: inherit;
        cursor: pointer;
        background: #fff;
    }

    select:focus {
        border-color: #0077b6;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .money-blue {
        color: #0077b6;
        font-weight: 700;
    }

    .money-green {
        color: #10b981;
        font-weight: 700;
    }

    .money-red {
        color: #ef4444;
        font-weight: 700;
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
                <h1><i class="fas fa-calendar-check" style="color:#0077b6;"></i> Quản lý đặt phòng</h1>
                <a href="xuat_excel.php?type=datphong" class="btn btn-success"><i class="fas fa-file-excel"></i> Xuất
                    Excel</a>
            </div>

            <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Ngày nhận</th>
                            <th>Ngày trả</th>
                            <th>Tổng tiền</th>
                            <th>Đã trả</th>
                            <th>Còn lại</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): 
                        $conlai = $row['tongtien'] - $row['thanhtoan_truoc'];
                        $cls = 'badge-warning';
                        if (in_array($row['trangthai'],['Đã xác nhận','Trả phòng'])) $cls='badge-success';
                        elseif ($row['trangthai']=='Đã hủy') $cls='badge-danger';
                    ?>
                        <tr>
                            <td><b>#<?php echo $row['id']; ?></b></td>
                            <td><b><?php echo htmlspecialchars($row['hoten']); ?></b><br><small
                                    style="color:#64748b;"><?php echo htmlspecialchars($row['sdt']); ?></small></td>
                            <td><?php echo htmlspecialchars($row['tenphong']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['ngaynhan'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['ngaytra'])); ?></td>
                            <td class="money-blue"><?php echo number_format($row['tongtien'],0,',','.'); ?> đ</td>
                            <td class="money-green"><?php echo number_format($row['thanhtoan_truoc'],0,',','.'); ?> đ
                            </td>
                            <td class="<?php echo $conlai > 0 ? 'money-red' : 'money-green'; ?>">
                                <?php echo number_format($conlai,0,',','.'); ?> đ
                            </td>
                            <td>
                                <form method="POST" style="display:flex;gap:4px;align-items:center;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <select name="trangthai">
                                        <?php foreach(['Chờ xác nhận','Đã xác nhận','Trả phòng','Đã hủy'] as $tt): ?>
                                        <option value="<?php echo $tt; ?>"
                                            <?php echo $row['trangthai']==$tt?'selected':''; ?>><?php echo $tt; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary btn-sm"><i
                                            class="fas fa-sync"></i></button>
                                </form>
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="in_hoadon.php?id=<?php echo $row['id']; ?>" target="_blank"
                                    class="btn btn-secondary btn-sm"><i class="fas fa-print"></i></a>
                                <a href="datphong_xoa.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Xóa đơn này?')"><i class="fas fa-trash"></i></a>
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