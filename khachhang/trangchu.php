<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($result)) {
    if (!isset($conn)) include 'ketnoi.php';
    $result = $conn->query("SELECT * FROM phong");
}
$base = (strpos($_SERVER['PHP_SELF'], '/khachhang/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>HOTEL - Đặt phòng khách sạn</title>
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
        transition: 0.2s;
        font-family: inherit;
        text-decoration: none;
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

    .btn-lg {
        padding: 14px 32px;
        font-size: 16px;
        border-radius: 10px;
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

    .auth-buttons {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .hero {
        background: linear-gradient(135deg, #e6f0f9, #dbeafe);
        padding: 80px 0;
        text-align: center;
    }

    .badge {
        display: inline-block;
        background: #fff;
        color: #0077b6;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 20px;
        border: 1px solid #bae6fd;
    }

    .hero h1 {
        font-size: 48px;
        font-weight: 800;
        margin-bottom: 16px;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero p {
        font-size: 16px;
        color: #64748b;
        margin-bottom: 30px;
    }

    .rooms-section {
        padding: 80px 0;
    }

    .section-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .section-header h2 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .section-header p {
        color: #64748b;
        font-size: 15px;
    }

    .filter-tabs {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 8px 20px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        font-size: 14px;
    }

    .filter-btn.active,
    .filter-btn:hover {
        background: #0077b6;
        color: #fff;
        border-color: #0077b6;
    }

    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 24px;
    }

    .room-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
    }

    .room-img {
        height: 160px;
        background: linear-gradient(135deg, #0077b6, #00b4d8);
        position: relative;
        background-size: cover;
        background-position: center;
    }

    .room-img .badge-check {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #fff;
        color: #0f172a;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
    }

    .room-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .room-info h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .room-price {
        font-size: 20px;
        font-weight: 800;
        color: #0077b6;
        margin-bottom: 12px;
    }

    .room-price span {
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
    }

    .room-type {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 12px;
    }

    .room-meta {
        display: flex;
        gap: 12px;
        font-size: 12px;
        color: #64748b;
        margin-bottom: 20px;
    }

    .room-info .btn {
        width: 100%;
        margin-top: auto;
        background: #e6f0f9;
        color: #0077b6;
        font-weight: 700;
    }

    .my-bookings {
        padding: 80px 0;
        background: #fff;
    }

    .empty-state {
        border: 1px dashed #cbd5e1;
        padding: 40px;
        text-align: center;
        border-radius: 16px;
        color: #64748b;
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

    .footer-links {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .footer-links h4 {
        font-size: 16px;
        margin-bottom: 8px;
    }

    .footer-links a {
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

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: #fff;
        width: 100%;
        max-width: 600px;
        border-radius: 16px;
        padding: 28px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f1f5f9;
    }

    .modal-header h3 {
        font-size: 20px;
        font-weight: 800;
    }

    .close-modal {
        background: #f1f5f9;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        color: #64748b;
        font-size: 16px;
    }

    .room-info-box {
        background: #e6f0f9;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 4px solid #0077b6;
        font-size: 14px;
    }

    .room-info-box strong {
        color: #0077b6;
        display: block;
        margin-bottom: 4px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        margin-bottom: 18px;
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
        transition: all 0.2s;
        font-family: inherit;
        background: #f8fafc;
    }

    select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230077b6' stroke-width='3'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 16px;
        padding-right: 44px;
        cursor: pointer;
        font-weight: 600;
    }

    .bank-box {
        display: none;
        background: #f0f9ff;
        padding: 16px;
        border-radius: 12px;
        border-left: 4px solid #0077b6;
        margin-bottom: 18px;
    }

    .bank-box h4 {
        color: #0077b6;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .bank-box p {
        font-size: 13px;
        line-height: 1.9;
    }

    .bank-box .hl {
        color: #ef4444;
        font-weight: 700;
    }

    .note {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 20px;
        background: #f0f9ff;
        padding: 14px 18px;
        border-radius: 10px;
        border-left: 3px solid #0077b6;
        line-height: 1.7;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 2px solid #f1f5f9;
    }

    @media (max-width:768px) {
        .header-container {
            flex-direction: column;
            gap: 16px;
        }

        .hero h1 {
            font-size: 32px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>

    <header class="header">
        <div class="container header-container">
            <a href="<?php echo $base; ?>index.php" class="logo">
                <div class="logo-icon">H</div>
                <span>HOTEL</span>
            </a>
            <nav class="nav-links">
                <a href="<?php echo $base; ?>index.php">Phòng</a>
                <a href="<?php echo $base; ?>khachhang/donhangcuatoi.php">Đặt phòng của tôi</a>
                <a href="<?php echo $base; ?>khachhang/lienhe.php">Liên hệ</a>
            </nav>
            <div class="auth-buttons">
                <?php if(isset($_SESSION['user'])): ?>
                <span style="font-weight:600;">👤 <?php echo htmlspecialchars($_SESSION['user']['hoten']); ?></span>
                <a href="<?php echo $base; ?>khachhang/dangxuat.php" class="btn btn-outline">Đăng xuất</a>
                <?php else: ?>
                <a href="<?php echo $base; ?>khachhang/dangnhap.php" class="btn btn-outline">Đăng nhập</a>
                <a href="<?php echo $base; ?>khachhang/dangky.php" class="btn btn-primary">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <span class="badge">Đặt phòng trực tuyến</span>
            <h1>Tìm căn phòng phù hợp cho bạn</h1>
            <p>Xem giá phòng, chọn ngày lưu trú và đặt phòng nhanh chóng chỉ trong vài bước.</p>
            <a href="#rooms" class="btn btn-primary btn-lg">Xem phòng ngay</a>
        </div>
    </section>

    <section class="rooms-section" id="rooms">
        <div class="container">
            <div class="section-header">
                <h2>Phòng dành cho bạn</h2>
                <p>Danh sách phòng được lấy trực tiếp từ hệ thống quản lý khách sạn.</p>
            </div>
            <div class="filter-tabs">
                <button type="button" class="filter-btn active" data-filter="all">Tất cả</button>
                <button type="button" class="filter-btn" data-filter="Phòng đơn">Phòng đơn</button>
                <button type="button" class="filter-btn" data-filter="Phòng đôi">Phòng đôi</button>
                <button type="button" class="filter-btn" data-filter="VIP">VIP</button>
            </div>
            <div class="room-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    $giangay = isset($row['giangay']) ? $row['giangay'] : 0;
                ?>
                <div class="room-card" data-type="<?php echo htmlspecialchars($row['loaiphong']); ?>">
                    <div class="room-img"
                        style="background-image:url('<?php echo $base; ?>image/<?php echo htmlspecialchars($row['hinhanh']); ?>');">
                        <span class="badge-check">✓ Có thể kiểm tra lịch</span>
                    </div>
                    <div class="room-info">
                        <h3><?php echo htmlspecialchars($row['tenphong']); ?></h3>
                        <div class="room-price"><?php echo number_format($row['gia'],0,',','.'); ?> đ <span>/đêm</span>
                        </div>
                        <div class="room-type"><?php echo htmlspecialchars($row['loaiphong']); ?></div>
                        <div class="room-meta">
                            <span>👥 <?php echo $row['sokhach']; ?> khách</span>
                            <span>✓ Đặt trực tuyến</span>
                        </div>
                        <button type="button" class="btn"
                            onclick="openBookingModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['tenphong']); ?>', '<?php echo htmlspecialchars($row['loaiphong']); ?>', <?php echo $row['gia']; ?>, <?php echo $giangay; ?>, <?php echo $row['sokhach']; ?>)">Xem
                            & đặt</button>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                <p>Chưa có phòng nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="my-bookings">
        <div class="container">
            <div class="section-header">
                <h2>Đặt phòng của tôi</h2>
                <p>Theo dõi các đơn đã đặt bằng tài khoản đang đăng nhập.</p>
            </div>
            <div class="empty-state">
                <?php if(isset($_SESSION['user'])): ?>
                <p>Bạn chưa có đơn đặt phòng nào. <a href="<?php echo $base; ?>khachhang/donhangcuatoi.php"
                        style="color:#0077b6;font-weight:600;">Xem tất cả</a></p>
                <?php else: ?>
                <p>Đăng nhập để xem các đặt phòng của bạn.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-brand">
                <div class="logo">
                    <div class="logo-icon">H</div><span>HOTEL</span>
                </div>
                <p>Website đặt phòng dành cho khách hàng.</p>
            </div>
            <div class="footer-links">
                <h4>Khám phá</h4>
                <a href="#rooms">Danh sách phòng</a>
                <a href="<?php echo $base; ?>khachhang/donhangcuatoi.php">Đặt phòng của tôi</a>
                <a href="<?php echo $base; ?>khachhang/lienhe.php">Liên hệ</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 HOTEL</p>
        </div>
    </footer>

    <!-- MODAL ĐẶT PHÒNG -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-calendar-plus" style="color:#0077b6;"></i> Đặt phòng</h3>
                <button type="button" class="close-modal" onclick="closeModal()"><i class="fas fa-times"></i></button>
            </div>
            <form action="<?php echo $base; ?>khachhang/datphong.php" method="POST">
                <div class="modal-body">
                    <div class="room-info-box" id="modalRoomInfo"></div>
                    <input type="hidden" name="phong_id" id="modalPhongId">
                    <input type="hidden" name="gia" id="modalGiaDem">
                    <input type="hidden" name="giangay" id="modalGiaNgay">
                    <input type="hidden" name="sokhach_max" id="modalSokhachMax">

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-check"></i> Ngày nhận *</label>
                            <input type="date" name="ngaynhan" id="ngaynhan" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar-times"></i> Ngày trả *</label>
                            <input type="date" name="ngaytra" id="ngaytra" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-users"></i> Số khách *</label>
                            <input type="number" name="sokhach" id="sokhach" value="1" min="1" required
                                style="text-align:center;font-weight:700;">
                            <small id="maxGuestNote" style="color:#64748b;font-size:11px;"></small>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-moon"></i> Số đêm *</label>
                            <input type="number" id="sodem" name="sodem" value="1" min="0" max="365"
                                style="background:#f0f9ff;font-weight:800;color:#0077b6;text-align:center;font-size:18px;">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-sun"></i> Số ngày (tùy chọn)</label>
                            <input type="number" id="songay" name="songay" value="0" min="0" max="365"
                                style="background:#fef3c7;font-weight:800;color:#92400e;text-align:center;font-size:18px;">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Địa chỉ *</label>
                            <input type="text" name="diachi" placeholder="Nhập địa chỉ" required>
                        </div>
                    </div>

                    <div id="priceBreakdown"
                        style="background:linear-gradient(135deg,#e6f0f9,#dbeafe);padding:18px 20px;border-radius:12px;margin-bottom:18px;border-left:4px solid #0077b6;">
                        <div style="font-size:13px;color:#64748b;margin-bottom:8px;font-weight:600;">💰 TỔNG TIỀN</div>
                        <div style="font-size:28px;font-weight:800;color:#0077b6;margin-bottom:12px;" id="showTongTien">
                            450.000 đ</div>
                        <div style="font-size:13px;color:#334155;line-height:1.9;background:#fff;padding:12px 16px;border-radius:8px;"
                            id="showCongThuc">
                            📦 <b>Số đơn vị:</b> 0 ngày + 1 đêm = <b>1</b><br>
                            💰 <b>Tổng tiền:</b> 1 × 450.000 đ = <b>450.000 đ</b><br>
                            💳 <b>TT trước 30%:</b> 450.000 đ × 30% = <b>135.000 đ</b><br>
                            📌 <b>Còn lại:</b> 450.000 đ − 135.000 đ = <b>315.000 đ</b>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-percent"></i> Mức TT trước</label>
                            <select name="thanhtoan" id="thanhtoan">
                                <option value="0">💵 0% - Không TT trước</option>
                                <option value="10">💰 10%</option>
                                <option value="20">💰 20%</option>
                                <option value="30" selected>💳 30% - Phổ biến</option>
                                <option value="50">💳 50%</option>
                                <option value="70">💳 70%</option>
                                <option value="100">✅ 100%</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-money-bill"></i> Số tiền TT trước</label>
                            <input type="text" id="tienThanhToan" value="135.000 đ" readonly
                                style="background:#fef2f2;font-weight:800;color:#ef4444;text-align:center;font-size:16px;cursor:not-allowed;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-wallet"></i> Phương thức thanh toán</label>
                        <select name="phuongthuc" id="phuongthuc">
                            <option value="Tiền mặt">💵 Tiền mặt</option>
                            <option value="Chuyển khoản" selected>🏦 Chuyển khoản</option>
                            <option value="Ví điện tử">📱 Ví điện tử</option>
                            <option value="Thẻ tín dụng">💳 Thẻ tín dụng</option>
                        </select>
                    </div>

                    <div class="bank-box" id="bankInfo">
                        <h4><i class="fas fa-university"></i> Thông tin chuyển khoản</h4>
                        <p><b>Ngân hàng:</b> Vietcombank</p>
                        <p><b>Số tài khoản:</b> <span class="hl">0123 456 789</span></p>
                        <p><b>Chủ tài khoản:</b> KHACH SAN HOTEL</p>
                        <p><b>Số tiền:</b> <span class="hl" id="sotienCK">135.000 đ</span></p>
                        <p><b>Nội dung:</b> <span class="hl" id="noidungCK">DATPHONG</span></p>
                    </div>

                    <div class="note">
                        <i class="fas fa-info-circle" style="color:#0077b6;margin-right:6px;"></i>
                        Khách hàng có thể thanh toán trước <b style="color:#0077b6;">0% - 100%</b> tổng tiền để xác nhận
                        giữ phòng.
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Ghi chú</label>
                        <textarea name="ghichu" rows="3" placeholder="Yêu cầu thêm nếu có..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()"><i class="fas fa-times"></i>
                        Đóng</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Xác nhận đặt
                        phòng</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo $base; ?>khachhang/script.js"></script>
</body>

</html>