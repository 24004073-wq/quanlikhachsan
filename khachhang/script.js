function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN").format(amount) + " đ";
}

function openBookingModal(id, ten, loai, gia_dem, gia_ngay, sokhach) {
  document.getElementById("modalPhongId").value = id;
  document.getElementById("modalGiaDem").value = gia_dem;
  document.getElementById("modalGiaNgay").value = gia_ngay;
  document.getElementById("modalSokhachMax").value = sokhach;

  document.getElementById("modalRoomInfo").innerHTML =
    "<strong>" +
    ten +
    " • " +
    loai +
    "</strong>" +
    "<p>💰 Đơn giá: <b>" +
    formatCurrency(gia_dem) +
    "</b> / đêm hoặc ngày</p>" +
    "<p>👥 Tối đa " +
    sokhach +
    " khách</p>";

  document.getElementById("sokhach").max = sokhach;
  document.getElementById("maxGuestNote").textContent =
    "Tối đa " + sokhach + " khách";

  document.getElementById("ngaynhan").value = "";
  document.getElementById("ngaytra").value = "";
  document.getElementById("sodem").value = 1;
  document.getElementById("songay").value = 0;
  document.getElementById("thanhtoan").value = 30;

  togglePaymentInfo();
  tinhTongTien();
  document.getElementById("bookingModal").classList.add("active");
}

function closeModal() {
  document.getElementById("bookingModal").classList.remove("active");
}

function togglePaymentInfo() {
  var pt = document.getElementById("phuongthuc");
  var bankInfo = document.getElementById("bankInfo");
  if (!pt || !bankInfo) return;
  bankInfo.style.display = pt.value === "Chuyển khoản" ? "block" : "none";
}

function tinhTongTien() {
  var soDem = parseInt(document.getElementById("sodem").value) || 0;
  var soNgay = parseInt(document.getElementById("songay").value) || 0;
  var giaDem = parseInt(document.getElementById("modalGiaDem").value) || 0;
  var phanTram = parseInt(document.getElementById("thanhtoan").value) || 0;

  var soDonVi = soNgay + soDem;
  var tong = giaDem * soDonVi;
  var tienCoc = Math.round((tong * phanTram) / 100);
  var conLai = tong - tienCoc;

  var congThuc =
    "📦 <b>Số đơn vị:</b> " +
    soNgay +
    " ngày + " +
    soDem +
    " đêm = <b>" +
    soDonVi +
    "</b><br>" +
    "💰 <b>Tổng tiền:</b> " +
    soDonVi +
    " × " +
    formatCurrency(giaDem) +
    " = <b>" +
    formatCurrency(tong) +
    "</b><br>" +
    "💳 <b>TT trước " +
    phanTram +
    "%:</b> " +
    formatCurrency(tong) +
    " × " +
    phanTram +
    "% = <b>" +
    formatCurrency(tienCoc) +
    "</b><br>" +
    "📌 <b>Còn lại:</b> " +
    formatCurrency(tong) +
    " − " +
    formatCurrency(tienCoc) +
    " = <b>" +
    formatCurrency(conLai) +
    "</b>";

  document.getElementById("showCongThuc").innerHTML = congThuc;
  document.getElementById("showTongTien").textContent = formatCurrency(tong);
  document.getElementById("tienThanhToan").value = formatCurrency(tienCoc);

  var sotienCK = document.getElementById("sotienCK");
  if (sotienCK) sotienCK.textContent = formatCurrency(tienCoc);

  var noidungCK = document.getElementById("noidungCK");
  if (noidungCK) {
    var phongId = document.getElementById("modalPhongId").value || "X";
    noidungCK.textContent = "HOTEL DP" + String(phongId).padStart(3, "0");
  }
}

function capNhatTuNgay() {
  var ngaynhan = document.getElementById("ngaynhan").value;
  var ngaytra = document.getElementById("ngaytra").value;
  if (!ngaynhan || !ngaytra) return;

  var d1 = new Date(ngaynhan);
  var d2 = new Date(ngaytra);
  var today = new Date();
  today.setHours(0, 0, 0, 0);

  if (d1 < today) {
    alert("⚠️ Ngày nhận không được trong quá khứ!");
    document.getElementById("ngaynhan").value = "";
    return;
  }

  if (d2 <= d1) {
    alert("⚠️ Ngày trả phải SAU ngày nhận!");
    document.getElementById("ngaytra").value = "";
    return;
  }

  var soDem = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
  document.getElementById("sodem").value = soDem;
  tinhTongTien();
}

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".filter-btn").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      document.querySelectorAll(".filter-btn").forEach(function (b) {
        b.classList.remove("active");
      });
      e.target.classList.add("active");
      var filter = e.target.getAttribute("data-filter");
      document.querySelectorAll(".room-card").forEach(function (card) {
        var type = card.getAttribute("data-type");
        card.style.display =
          filter === "all" || type === filter ? "flex" : "none";
      });
    });
  });

  var ngaynhan = document.getElementById("ngaynhan");
  var ngaytra = document.getElementById("ngaytra");
  if (ngaynhan)
    ngaynhan.addEventListener("change", function () {
      if (ngaytra && ngaytra.value) capNhatTuNgay();
    });
  if (ngaytra) ngaytra.addEventListener("change", capNhatTuNgay);

  var sokhach = document.getElementById("sokhach");
  if (sokhach)
    sokhach.addEventListener("input", function () {
      var max = parseInt(document.getElementById("modalSokhachMax").value) || 1;
      var val = parseInt(this.value);
      if (isNaN(val) || val < 1) val = 1;
      if (val > max) {
        val = max;
        alert("⚠️ Tối đa " + max + " khách cho phòng này!");
      }
      this.value = val;
    });

  var sodem = document.getElementById("sodem");
  if (sodem)
    sodem.addEventListener("input", function () {
      var val = parseInt(this.value);
      if (isNaN(val) || val < 0) val = 0;
      this.value = val;
      tinhTongTien();
    });

  var songay = document.getElementById("songay");
  if (songay)
    songay.addEventListener("input", function () {
      var val = parseInt(this.value);
      if (isNaN(val) || val < 0) val = 0;
      this.value = val;
      tinhTongTien();
    });

  var thanhtoan = document.getElementById("thanhtoan");
  if (thanhtoan) thanhtoan.addEventListener("change", tinhTongTien);

  var phuongthuc = document.getElementById("phuongthuc");
  if (phuongthuc) phuongthuc.addEventListener("change", togglePaymentInfo);
});
