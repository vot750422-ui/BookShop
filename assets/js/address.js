const API = "https://provinces.open-api.vn/api";

document.addEventListener("DOMContentLoaded", async function () {
  const sel = document.getElementById("tinh");
  // Nếu trang hiện tại không có ô chọn tỉnh thì bỏ qua, không chạy lỗi
  if (!sel) return;

  try {
    const res = await fetch(`${API}/p/`);
    const data = await res.json();
    data.forEach((t) => {
      sel.innerHTML += `<option value="${t.name}" data-code="${t.code}">${t.name}</option>`;
    });
  } catch (e) {
    console.log("Lỗi load tỉnh");
  }
});

async function loadQuan() {
  const tinhSel = document.getElementById("tinh");
  const quanSel = document.getElementById("quan");
  const phuongSel = document.getElementById("phuong");

  quanSel.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
  phuongSel.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

  const code = tinhSel.selectedOptions[0]?.dataset.code;
  if (!code) return;

  try {
    const res = await fetch(`${API}/p/${code}?depth=2`);
    const data = await res.json();
    data.districts.forEach((q) => {
      quanSel.innerHTML += `<option value="${q.name}" data-code="${q.code}">${q.name}</option>`;
    });
  } catch (e) {
    console.log("Lỗi load quận");
  }
}

async function loadPhuong() {
  const quanSel = document.getElementById("quan");
  const phuongSel = document.getElementById("phuong");

  phuongSel.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

  const code = quanSel.selectedOptions[0]?.dataset.code;
  if (!code) return;

  try {
    const res = await fetch(`${API}/d/${code}?depth=2`);
    const data = await res.json();
    data.wards.forEach((p) => {
      phuongSel.innerHTML += `<option value="${p.name}">${p.name}</option>`;
    });
  } catch (e) {
    console.log("Lỗi load phường");
  }
}
