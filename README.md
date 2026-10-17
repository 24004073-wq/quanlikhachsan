# 🏨 HotelManager - Hệ Thống Quản Lý Khách Sạn Toàn Diện

<p align="center">
  <img src="image/giuong1.jpg" alt="HotelManager Banner" width="220" />
</p>

<p align="center">
  <strong>Hệ thống Quản lý Khách sạn & Đặt phòng Trực tuyến Tích hợp Thanh toán VietQR</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2" />
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/HTML5-CSS3-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5" />
  <img src="https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript" />
  <img src="https://img.shields.io/badge/VietQR-Payment-00A651?style=for-the-badge&logo=vietqr&logoColor=white" alt="VietQR" />
  <img src="https://img.shields.io/badge/XAMPP-Compatible-FB7A24?style=for-the-badge&logo=xampp&logoColor=white" alt="XAMPP" />
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License" />
</p>

---

## 🌟 Tổng Quan Dự Án (Project Overview)

**HotelManager** là hệ thống quản lý khách sạn chuyên nghiệp được thiết kế theo phong cách **Modern Light Theme** tối giản, sang trọng và dễ sử dụng.

Dự án kết hợp kiến trúc **PHP thuần + MySQL** ở backend và **HTML/CSS/JS** ở frontend, trang bị đầy đủ các tính năng đặt phòng, thanh toán QR, thống kê doanh thu, in hóa đơn và xuất file Excel/PDF.

---

## ✨ Tính Năng Nổi Bật (Key Features)

### 🛍️ 1. Khách Hàng (Customer Experience)
- **Trang chủ hiện đại**:
  - Hero banner với slogan ấn tượng.
  - Bộ lọc phòng theo loại: Tất cả / Phòng đơn / Phòng đôi / VIP.
  - Danh sách phòng dạng card trực quan với ảnh, giá, số khách.
- **Đặt phòng linh hoạt**:
  - Modal đặt phòng: chọn ngày nhận/trả, số khách, địa chỉ, ghi chú.
  - Chọn thanh toán trước: 0% / 30% / 100%.
  - Tự động tính tiền theo ngày + đêm.
- **Quản lý đơn cá nhân**:
  - Xem toàn bộ đơn đã đặt.
  - Lọc theo trạng thái: Chờ xác nhận / Đã xác nhận / Trả phòng / Đã hủy.
  - Hủy đơn khi còn ở trạng thái "Chờ xác nhận".
- **Đăng nhập / Đăng ký / Quên mật khẩu** đầy đủ.

### 🛡️ 2. Quản Trị Viên (Admin Portal)
- **Dashboard thống kê**:
  - 4 thẻ KPI: Tổng phòng, Khách hàng, Đơn đặt phòng, Doanh thu.
  - Bảng 5 đơn mới nhất với trạng thái.
- **Quản lý phòng (CRUD)**:
  - Thêm / Sửa / Xóa phòng.
  - Upload ảnh, chọn loại phòng, giá, số khách, trạng thái.
- **Quản lý khách hàng (CRUD)**:
  - Thêm / Sửa / Xóa khách.
  - Xem lịch sử đặt phòng của từng khách.
- **Quản lý đặt phòng**:
  - Xem tất cả đơn, lọc theo trạng thái.
  - Sửa trạng thái: Chờ xác nhận → Đã xác nhận → Đang ở → Trả phòng (hoặc Đã hủy).
- **Thanh toán + QR VietQR** ⭐:
  - Hiển thị công nợ: Tổng tiền, đã cọc, còn lại.
  - Nút "Thu tiền" mở modal có **mã QR VietQR động**.
  - Số tiền = số còn lại, nội dung CK = mã đơn (`GD00016`).
  - Chọn phương thức: Chuyển khoản QR / Tiền mặt.
  - Xác nhận thu → cập nhật DB, ghi lịch sử, đổi trạng thái.
- **Lịch sử đặt phòng & thanh toán**:
  - Lịch sử đặt phòng: Toàn bộ đơn, lọc theo trạng thái.
  - Lịch sử thanh toán: Mã giao dịch `GDTT00001`, số tiền, phương thức, admin thu.
- **Thống kê doanh thu**:
  - Biểu đồ doanh thu theo tháng.
  - Top phòng được đặt nhiều nhất.
- **In & Xuất file**:
  - In hóa đơn, in phiếu thu.
  - Xuất Excel, xuất PDF.

### 🔒 3. Bảo Mật & Hạ Tầng Backend
- **Session PHP** quản lý đăng nhập khách hàng và admin.
- **Prepared Statement (MySQLi)** chống SQL Injection.
- **htmlspecialchars()** chống XSS.
- **check_loginadmin.php** bảo vệ toàn bộ trang admin.
- **VietQR API** — không cần cài đặt thư viện, chỉ cần link ảnh.

---

## 🏗️ Sơ Đồ Kiến Trúc Hệ Thống (System Architecture)

```mermaid
graph TD
    subgraph Client Layer
        A[Khách hàng - khachhang/]
        A --> A1[Trang chủ & Danh sách phòng]
        A --> A2[Đặt phòng & Thanh toán cọc]
        A --> A3[Đơn đặt phòng của tôi]
    end

    subgraph Admin Layer
        B[Admin - admin_quantri/]
        B --> B1[Dashboard & Thống kê]
        B --> B2[Quản lý phòng / khách / đơn]
        B --> B3[Thu tiền QR VietQR]
        B --> B4[Lịch sử & In hóa đơn]
    end

    subgraph Data Layer
        C[(MySQL Database)]
        D[VietQR API]
    end

    A -->|Form POST / Session| C
    B -->|MySQLi Prepared Statement| C
    B -->|Link ảnh QR động| D