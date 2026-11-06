-- 1. Tạo database nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS quanlikhachsan
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Sử dụng database
USE quanlikhachsan;

-- 3. Tạo bảng nếu chưa có; không xóa dữ liệu hiện có khi chạy lại file
CREATE TABLE IF NOT EXISTS khachhang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hoten VARCHAR(100) NOT NULL,
    cccd VARCHAR(20) NOT NULL,
    sdt VARCHAR(20),
    email VARCHAR(100),
    diachi VARCHAR(255),
    ngaynhan DATE,
    ngaytra DATE
);

CREATE TABLE IF NOT EXISTS lienhe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hoten VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    sdt VARCHAR(20) NULL,
    chude VARCHAR(150) NULL,
    noidung TEXT NOT NULL,
    ngaygui TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Thêm dữ liệu mẫu
INSERT INTO khachhang (hoten, cccd, sdt, email, diachi, ngaynhan, ngaytra)
VALUES
('Nguyễn Văn An', '079123456789', '0901234567', 'an@gmail.com', 'Vĩnh Long', '2026-09-20', '2026-09-22'),
('Trần Thị Bình', '079234567890', '0912345678', 'binh@gmail.com', 'Cần Thơ', '2026-09-20', '2026-09-23'),
('Lê Minh Hoàng', '079345678901', '0923456789', 'hoang@gmail.com', 'TP. Hồ Chí Minh', '2026-09-21', '2026-09-24'),
('Phạm Thị Lan', '079456789012', '0934567890', 'lan@gmail.com', 'Bến Tre', '2026-09-21', '2026-09-25'),
('Võ Quốc Huy', '079567890123', '0945678901', 'huy@gmail.com', 'Đồng Tháp', '2026-09-22', '2026-09-24'),
('Đặng Ngọc Mai', '079678901234', '0956789012', 'mai@gmail.com', 'Tiền Giang', '2026-09-22', '2026-09-26'),
('Huỳnh Thanh Tùng', '079789012345', '0967890123', 'tung@gmail.com', 'An Giang', '2026-09-23', '2026-09-27'),
('Nguyễn Thị Hương', '079890123456', '0978901234', 'huong@gmail.com', 'Trà Vinh', '2026-09-23', '2026-09-25');

-- 6. Kiểm tra dữ liệu
SELECT * FROM khachhang;