<?php
session_start();
include '../khachhang/ketnoi.php';

if (file_exists('check_loginadmin.php')) {
    include 'check_loginadmin.php';
} elseif (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// ==================== XỬ LÝ THU TIỀN ====================
if (isset($_POST['xacnhan_thu'])) {
    $id = (int)$_POST['id'];
    $sotien_thu = (int)$_POST['sotien_thu'];
    $ghichu = trim($_POST['ghichu'] ?? '');
    $phuongthuc = $_POST['phuongthuc'] ?? 'Chuyển khoản QR';
    $admin_id = $_SESSION['admin']['id'] ?? 0;

    $don = $conn->query("SELECT tongtien, thanhtoan_truoc, trangthai FROM datphong WHERE id=$id")->fetch_assoc();
    if ($don) {
        $conlai = $don['tongtien'] - $don['thanhtoan_truoc'];
        $thucthu = min($sotien_thu, $conlai);
        $moi = $don['thanhtoan_truoc'] + $thucthu;

        // Xác định trạng thái mới
        $trangthai_moi = $don['trangthai'];
        if ($moi >= $don['tongtien'] && $don['trangthai'] == 'Chờ xác nhận') {
            $trangthai_moi = 'Đã xác nhận';
        } elseif ($moi >= $don['tongtien'] && $don['trangthai'] == 'Đã xác nhận') {
            $trangthai_moi = 'Đang ở';
        }

        $conn->query("UPDATE datphong 
                      SET thanhtoan_truoc = $moi, trangthai = '$trangthai_moi'
                      WHERE id = $id");

        $stmt = $conn->prepare("INSERT INTO lichsuthanhtoan (datphong_id, admin_id, sotien, phuongthuc, ghichu) VALUES (?,?,?,?,?)");
        $stmt->bind_param("iiiss", $id, $admin_id, $thucthu, $phuongthuc, $ghichu);
        $stmt->execute();

        header("Location: thu_tien.php?msg=Đã thu " . number_format($thucthu,0,',','.') . " đ!");
        exit();
    }
}

// ==================== XỬ LÝ ĐỔI TRẠNG THÁI ====================
if (isset($_GET['doitrangthai'])) {
    $id = (int)$_GET['id'];
    $tt = $_GET['tt'];
    $don = $conn->query("SELECT tongtien, thanhtoan_truoc FROM datphong WHERE id=$id")->fetch_assoc();
    
    // Không cho "Trả phòng" nếu còn nợ
    if ($tt == 'Trả phòng' && $don['thanhtoan_truoc'] < $don['tongtien']) {
        header("Location: thu_tien.php?err=Không thể trả phòng khi còn nợ " . number_format($don['tongtien']-$don['thanhtoan_truoc'],0,',','.') . " đ!");
        exit();
    }
    
    $conn->query("UPDATE datphong SET trangthai='$tt' WHERE id=$id");
    header("Location: thu_tien.php?msg=Đã chuyển trạng thái: $tt");
    exit();
}

// ==================== LẤY DANH SÁCH ====================
$filter = $_GET['filter'] ?? 'all';
$where = "WHERE d.trangthai != 'Đã hủy'";
if ($filter == 'conlai') $where = "WHERE d.tongtien > d.thanhtoan_truoc AND d.trangthai != 'Đã hủy'";
elseif ($filter == 'dathu') $where = "WHERE d.tongtien <= d.thanhtoan_truoc AND d.trangthai != 'Đã hủy'";
elseif ($filter == 'huy') $where = "WHERE d.trangthai = 'Đã hủy'";

$sql = "SELECT d.*, k.hoten, k.sdt, p.tenphong, p.loaiphong
        FROM datphong d
        JOIN khachhang k ON d.khachhang_id = k.id
        JOIN phong p ON d.phong_id = p.id
        $where
        ORDER BY d.id DESC";
$result = $conn->query($sql);

$tong_conlai = $conn->query("SELECT SUM(tongtien - thanhtoan_truoc) as t FROM datphong WHERE tongtien > thanhtoan_truoc AND trangthai != 'Đã hủy'")->fetch_assoc()['t'] ?? 0;
$tong_dathu = $conn->query("SELECT SUM(thanhtoan_truoc) as t FROM datphong WHERE trangthai != 'Đã hủy'")->fetch_assoc()['t'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thu tiền - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .filter-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-tabs a {
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        color: #475569;
        background: #fff;
        border: 1px solid #e2e8f0;
    }

    .filter-tabs a.active,
    .filter-tabs a:hover {
        background: #0077b6;
        color: #fff;
        border-color: #0077b6;
    }

    .modal-qr {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.7);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal-qr.active {
        display: flex;
    }

    .modal-qr-content {
        background: #fff;
        padding: 30px;
        border-radius: 20px;
        max-width: 480px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-qr-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #f1f5f9;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        color: #64748b;
    }

    .modal-qr-content h3 {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .modal-qr-content .order-code {
        color: #0077b6;
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 16px;
    }

    .modal-qr-content .qr-box {
        background: #f8fafc;
        padding: 16px;
        border-radius: 12px;
        margin: 16px 0;
    }

    .modal-qr-content .qr-box img {
        width: 100%;
        max-width: 260px;
        border-radius: 8px;
    }

    .modal-qr-content .amount {
        font-size: 26px;
        font-weight: 800;
        color: #0077b6;
        margin: 10px 0;
    }

    .modal-qr-content .info-line {
        font-size: 13px;
        color: #64748b;
        margin: 6px 0;
        text-align: left;
    }

    .modal-qr-content .info-line b {
        color: #0f172a;
    }

    .modal-qr-content .btn-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .modal-qr-content .btn-group .btn {
        flex: 1;
    }

    .modal-qr-content textarea,
    .modal-qr-content input[type=number],
    .modal-qr-content select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        margin-top: 8px;
    }

    .stat-mini {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-mini .card {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .stat-mini .card p {
        color: #64748b;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .stat-mini .card h3 {
        font-size: 20px;
        font-weight: 800;
    }

    .stat-mini .card.red h3 {
        color: #ef4444;
    }

    .stat-mini .card.green h3 {
        color: #10b981;
    }

    .stat-mini .card.blue h3 {
        color: #0077b6;
    }

    .btn-xs {
        padding: 5px 10px;
        font-size: 12px;
    }

    .canh-bao {
        background: #fee2e2;
        color: #991b1b;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        border-left: 4px solid #ef4444;
    }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="topbar">
                <h1>💰 Thu tiền</h1>
            </div>

            <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            <?php if(isset($_GET['err'])): ?>
            <div class="canh-bao"><i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($_GET['err']); ?></div>
            <?php endif; ?>

            <div class="stat-mini">
                <div class="card red">
                    <p><i class="fas fa-exclamation-circle"></i> Tổng tiền còn phải thu</p>
                    <h3><?php echo number_format($tong_conlai,0,',','.'); ?> đ</h3>
                </div>
                <div class="card green">
                    <p><i class="fas fa-check-circle"></i> Tổng tiền đã thu</p>
                    <h3><?php echo number_format($tong_dathu,0,',','.'); ?> đ</h3>
                </div>
                <div class="card blue">
                    <p><i class="fas fa-receipt"></i> Số đơn hiển thị</p>
                    <h3><?php echo $result->num_rows; ?> đơn</h3>
                </div>
            </div>

            <div class="filter-tabs">
                <a href="?filter=all" class="<?php echo $filter=='all'?'active':''; ?>">Tất cả</a>
                <a href="?filter=conlai" class="<?php echo $filter=='conlai'?'active':''; ?>">Còn phải thu</a>
                <a href="?filter=dathu" class="<?php echo $filter=='dathu'?'active':''; ?>">Đã thu đủ</a>
                <a href="?filter=huy" class="<?php echo $filter=='huy'?'active':''; ?>">Đã hủy</a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Tổng tiền</th>
                            <th>Đã cọc</th>
                            <th>Còn lại</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows == 0): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:30px;color:#64748b;">Không có đơn nào.</td>
                        </tr>
                        <?php else: ?>
                        <?php while($row = $result->fetch_assoc()): 
                        $conlai = $row['tongtien'] - $row['thanhtoan_truoc'];
                        $ma_don = 'GD' . str_pad($row['id'], 5, '0', STR_PAD_LEFT);
                        $tt = $row['trangthai'];
                        
                        // Xác định class badge
                        $cls = 'badge-warning';
                        if ($tt=='Đã xác nhận') $cls='badge-success';
                        elseif ($tt=='Đang ở') $cls='badge-info';
                        elseif ($tt=='Đã hủy') $cls='badge-danger';
                        elseif ($tt=='Trả phòng') $cls='badge-secondary';
                    ?>
                        <tr>
                            <td><b><?php echo $ma_don; ?></b></td>
                            <td><?php echo htmlspecialchars($row['hoten']); ?><br><small
                                    style="color:#64748b;"><?php echo htmlspecialchars($row['sdt']); ?></small></td>
                            <td><?php echo htmlspecialchars($row['tenphong']); ?></td>
                            <td><?php echo number_format($row['tongtien'],0,',','.'); ?> đ</td>
                            <td style="color:#10b981;font-weight:600;">
                                <?php echo number_format($row['thanhtoan_truoc'],0,',','.'); ?> đ</td>
                            <td style="color:<?php echo $conlai>0?'#ef4444':'#10b981'; ?>;font-weight:700;">
                                <?php echo number_format($conlai,0,',','.'); ?> đ</td>
                            <td><span class="badge <?php echo $cls; ?>"><?php echo $tt; ?></span></td>
                            <td>
                                <?php if ($tt == 'Đã hủy'): ?>
                                <!-- ĐÃ HỦY: Không hiện QR, chỉ hiện cảnh báo -->
                                <div style="color:#ef4444;font-size:12px;font-weight:600;">
                                    <i class="fas fa-ban"></i> Đơn đã hủy<br>
                                    <small>Không chuyển khoản</small>
                                </div>

                                <?php elseif ($conlai > 0 && in_array($tt, ['Chờ xác nhận', 'Đã xác nhận', 'Đang ở', 'Trả phòng'])): ?>
                                <!-- CÒN NỢ + trạng thái hợp lệ: hiện QR + nhập tiền mặt -->
                                <button class="btn btn-primary btn-sm"
                                    onclick="moQR(<?php echo $row['id']; ?>, '<?php echo $ma_don; ?>', <?php echo $conlai; ?>, '<?php echo htmlspecialchars(addslashes($row['hoten'])); ?>', '<?php echo htmlspecialchars(addslashes($row['tenphong'])); ?>', '<?php echo $tt; ?>')">
                                    <i class="fas fa-qrcode"></i> Thu tiền
                                </button>

                                <?php elseif ($conlai <= 0 && $tt == 'Đã xác nhận'): ?>
                                <!-- ĐÃ THU ĐỦ + Đã xác nhận: cho chuyển sang "Đang ở" -->
                                <a href="?doitrangthai=1&id=<?php echo $row['id']; ?>&tt=Đang ở"
                                    class="btn btn-info btn-sm"
                                    onclick="return confirm('Xác nhận khách đã nhận phòng?')">
                                    <i class="fas fa-door-open"></i> Nhận phòng
                                </a>

                                <?php elseif ($conlai <= 0 && $tt == 'Đang ở'): ?>
                                <!-- ĐANG Ở + đã thu đủ: cho trả phòng -->
                                <a href="?doitrangthai=1&id=<?php echo $row['id']; ?>&tt=Trả phòng"
                                    class="btn btn-secondary btn-sm"
                                    onclick="return confirm('Xác nhận khách trả phòng?')">
                                    <i class="fas fa-sign-out-alt"></i> Trả phòng
                                </a>
                                <a href="in_hoadon.php?id=<?php echo $row['id']; ?>" target="_blank"
                                    class="btn btn-outline btn-sm">
                                    <i class="fas fa-print"></i> In
                                </a>

                                <?php elseif ($conlai <= 0 && $tt == 'Trả phòng'): ?>
                                <!-- TRẢ PHÒNG + đã thu đủ: chỉ còn in -->
                                <a href="in_hoadon.php?id=<?php echo $row['id']; ?>" target="_blank"
                                    class="btn btn-outline btn-sm">
                                    <i class="fas fa-print"></i> In hóa đơn
                                </a>
                                <span class="badge badge-success"><i class="fas fa-check"></i> Đã thu đủ</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- MODAL THU TIỀN (QR + Tiền mặt) -->
    <div class="modal-qr" id="modalQR">
        <div class="modal-qr-content">
            <button class="modal-qr-close" onclick="dongQR()"><i class="fas fa-times"></i></button>
            <h3>Thu tiền</h3>
            <div class="order-code" id="qrMaDon">GD00000</div>
            <div class="qr-box"><img id="qrImage" src="" alt="QR Code"></div>
            <div class="amount" id="qrSoTien">0 đ</div>
            <div class="info-line">Khách hàng: <b id="qrKhach">—</b></div>
            <div class="info-line">Phòng: <b id="qrPhong">—</b></div>
            <div class="info-line">Nội dung CK: <b id="qrNoidung">—</b></div>

            <form method="POST">
                <input type="hidden" name="id" id="qrDonId">
                <input type="hidden" name="sotien_thu" id="qrSoTienInput">

                <select name="phuongthuc" id="qrPhuongthuc">
                    <option value="Chuyển khoản QR">Chuyển khoản QR</option>
                    <option value="Tiền mặt">Tiền mặt</option>
                </select>

                <textarea name="ghichu" rows="2" placeholder="Ghi chú (không bắt buộc)"></textarea>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline" onclick="dongQR()">Đóng</button>
                    <button type="submit" name="xacnhan_thu" class="btn btn-success"><i class="fas fa-check"></i> Xác
                        nhận đã thu</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // ⚙️ CẤU HÌNH NGÂN HÀNG — SỬA 3 DÒNG NÀY
    const BANK_ID = '970422';
    const ACCOUNT_NO = '0123456789';
    const ACCOUNT_NAME = 'TEN CHU TAI KHOAN';

    function moQR(idDon, maDon, soTien, khach, phong, trangthai) {
        const qrUrl =
            `https://img.vietqr.io/image/${BANK_ID}-${ACCOUNT_NO}-compact2.png?amount=${soTien}&addInfo=${encodeURIComponent(maDon)}&accountName=${encodeURIComponent(ACCOUNT_NAME)}`;
        document.getElementById('qrMaDon').textContent = maDon + ' — ' + trangthai;
        document.getElementById('qrImage').src = qrUrl;
        document.getElementById('qrSoTien').textContent = soTien.toLocaleString('vi-VN') + ' đ';
        document.getElementById('qrKhach').textContent = khach;
        document.getElementById('qrPhong').textContent = phong;
        document.getElementById('qrNoidung').textContent = maDon;
        document.getElementById('qrSoTienInput').value = soTien;
        document.getElementById('qrDonId').value = idDon;
        document.getElementById('modalQR').classList.add('active');
    }

    function dongQR() {
        document.getElementById('modalQR').classList.remove('active');
    }
    document.getElementById('modalQR').addEventListener('click', function(e) {
        if (e.target === this) dongQR();
    });
    </script>
</body>

</html>