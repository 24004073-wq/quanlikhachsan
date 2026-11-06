<aside class="sidebar">
    <div class="brand">
        <div class="logo-icon">H</div>
        <span>HOTEL ADMIN</span>
    </div>
    <nav>
        <?php $current = basename($_SERVER['PHP_SELF']); ?>
        <a href="admin.php" class="<?php echo $current=='admin.php'?'active':''; ?>"><i class="fas fa-chart-line"></i>
            <span>Dashboard</span></a>
        <a href="quanliphong.php" class="<?php echo strpos($current,'quanliphong')!==false?'active':''; ?>"><i
                class="fas fa-bed"></i> <span>Quản lý phòng</span></a>
        <a href="quanlikhachhang.php" class="<?php echo strpos($current,'khachhang')!==false?'active':''; ?>"><i
                class="fas fa-users"></i> <span>Khách hàng</span></a>
        <a href="quanlidatphong.php"
            class="<?php echo strpos($current,'datphong')!==false && strpos($current,'lichsu')===false?'active':''; ?>"><i
                class="fas fa-calendar-check"></i> <span>Đặt phòng</span></a>
        <a href="quanlithanhtoan.php" class="<?php echo strpos($current,'thanhtoan')!==false?'active':''; ?>"><i
                class="fas fa-receipt"></i> <span>Thanh toán</span></a>
        <a href="lichsudatphong.php" class="<?php echo $current=='lichsudatphong.php'?'active':''; ?>"><i
                class="fas fa-history"></i> <span>Lịch sử đặt phòng</span></a>
        <a href="lichsuthanhtoan.php" class="<?php echo $current=='lichsuthanhtoan.php'?'active':''; ?>"><i
                class="fas fa-money-check-alt"></i> <span>Lịch sử thanh toán</span></a>
        <a href="thongke.php" class="<?php echo $current=='thongke.php'?'active':''; ?>"><i
                class="fas fa-chart-bar"></i> <span>Thống kê</span></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span></a>
    </nav>
</aside>