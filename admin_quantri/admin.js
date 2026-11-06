// Xác nhận xóa
document.addEventListener("DOMContentLoaded", function () {
  // Auto hide alert sau 3 giây
  document.querySelectorAll(".alert").forEach((el) => {
    setTimeout(() => {
      el.style.display = "none";
    }, 3000);
  });

  // Preview ảnh khi upload
  const imgInput = document.querySelector('input[type="file"][name="hinhanh"]');
  if (imgInput) {
    imgInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
          let preview = document.getElementById("previewImg");
          if (!preview) {
            preview = document.createElement("img");
            preview.id = "previewImg";
            preview.style.cssText =
              "width:120px;margin-top:10px;border-radius:8px;display:block;";
            imgInput.parentNode.appendChild(preview);
          }
          preview.src = ev.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }
});

// Xác nhận trước khi xóa
function xacNhanXoa(msg) {
  return confirm(msg || "Bạn chắc chắn muốn xóa?");
}
