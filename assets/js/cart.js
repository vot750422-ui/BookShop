document.addEventListener("DOMContentLoaded", function () {
  // 1. Xử lý THÊM VÀO GIỎ HÀNG (Trang Chi tiết sách)
  const addForm = document.getElementById("form-add-to-cart");
  if (addForm) {
    addForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Đồng bộ số lượng từ ô nhập liệu (nếu có hàm syncQty)
      if (typeof syncQty === "function") syncQty("qty-them");

      const formData = new FormData(this);
      formData.append("ajax", "1");

      fetch("xulygiohang.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            if (typeof showPopup === "function") {
              showPopup("Đã thêm sản phẩm vào giỏ hàng!", "success");
            } else {
              alert("Đã thêm sản phẩm vào giỏ hàng!");
            }
          }
        })
        .catch((err) => console.error("Lỗi:", err));
    });
  }

  // 2. Xử lý CẬP NHẬT GIỎ HÀNG (Trang Giỏ hàng)
  document.addEventListener("click", function (e) {
    const btn = e.target.closest(".btn-qty-ajax");
    if (!btn) return;

    e.preventDefault();
    const bookID = btn.dataset.id;
    const action = btn.dataset.action;
    const row = btn.closest(".cart-item");

    const formData = new FormData();
    formData.append("BookID", bookID);
    formData.append("action", action);
    formData.append("ajax", "1");

    fetch("xulygiohang.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          if (row) {
            const qtyDisplay = row.querySelector(".qty-display");
            if (qtyDisplay) qtyDisplay.innerText = data.newQty;

            const subtotalDisplay = row.querySelector(".subtotal-display");
            if (subtotalDisplay)
              subtotalDisplay.innerText = data.newSubtotal + " đ";
          }

          const totalDisplay = document.querySelector(".total-all-display");
          if (totalDisplay) totalDisplay.innerText = data.newTotal + " đ";

          if (data.newQty <= 0 && row) {
            row.remove();
            if (document.querySelectorAll(".cart-item").length === 0)
              location.reload();
          }
        }
      })
      .catch((err) => console.error("Lỗi:", err));
  });
});
