<?php
session_start();
include '../khachhang/ketnoi.php';

if (file_exists('check_loginadmin.php')) {
    include 'check_loginadmin.php';
} elseif (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT d.*, k.hoten, k.sdt, p.tenphong, p.loaiphong
                        FROM datphong d
                        JOIN khachhang k ON d.khachhang_id = k.id
                        JOIN phong p ON d.phong_id = p.id
                        ORDER BY d.id DESC");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Lịch sử đặt phòng - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="topbar">
                <h1>📅 Lịch sử đặt phòng</h1>
                <a href="xuat_excel.php?type=datphong" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i>
                    Xuất Excel</a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Ngày nhận</th>
                            <th>Ngày trả</th>
                            <th>Tổng tiền</th>
                            <th>Đã cọc</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): 
                        $ma_don = 'GD' . str_pad($row['id'], 5, '0', STR_PAD_LEFT);
                        $cls = 'badge-warning';
                        if ($row['trangthai']=='Đã xác nhận') $cls='badge-success';
                        elseif ($row['trangthai']=='Đã hủy') $cls='badge-danger';
                        elseif ($row['trangthai']=='Trả phòng') $cls='badge-info';
                    ?>
                        <tr>
                            <td><b><?php echo $ma_don; ?></b></td>
                            <td><?php echo htmlspecialchars($row['hoten']); ?><br><small
                                    style="color:#64748b;"><?php echo htmlspecialchars($row['sdt']); ?></small></td>
                            <td><?php echo htmlspecialchars($row['tenphong']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['ngaynhan'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['ngaytra'])); ?></td>
                            <td><?php echo number_format($row['tongtien'],0,',','.'); ?> đ</td>
                            <td style="color:#10b981;font-weight:600;">
                                <?php echo number_format($row['thanhtoan_truoc'],0,',','.'); ?> đ</td>
                            <td><span class="badge <?php echo $cls; ?>"><?php echo $row['trangthai']; ?></span></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['ngaydat'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>