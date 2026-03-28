const API = "https://provinces.open-api.vn/api";

document.addEventListener("DOMContentLoaded", async () => {
  const sel = document.getElementById("tinh-addr");
  if (!sel) return;
  try {
    const res = await fetch(`${API}/p/`);
    const data = await res.json();
    // Tối ưu: Thay vì dùng += nội suy chuỗi nhiều lần, ta map ra mảng rồi nối lại 1 lần
    const options = data
      .map(
        (t) =>
          `<option value="${t.name}" data-code="${t.code}">${t.name}</option>`,
      )
      .join("");
    sel.insertAdjacentHTML("beforeend", options);
  } catch (e) {}
});

async function loadQuanAddr() {
  const tinhSel = document.getElementById("tinh-addr");
  const quanSel = document.getElementById("quan-addr");
  const phuongSel = document.getElementById("phuong-addr");
  quanSel.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
  phuongSel.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

  const code = tinhSel.selectedOptions[0]?.dataset.code;
  if (!code) return;

  try {
    const res = await fetch(`${API}/p/${code}?depth=2`);
    const data = await res.json();
    const options = data.districts
      .map(
        (q) =>
          `<option value="${q.name}" data-code="${q.code}">${q.name}</option>`,
      )
      .join("");
    quanSel.insertAdjacentHTML("beforeend", options);
  } catch (e) {}
}

async function loadPhuongAddr() {
  const quanSel = document.getElementById("quan-addr");
  const phuongSel = document.getElementById("phuong-addr");
  phuongSel.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

  const code = quanSel.selectedOptions[0]?.dataset.code;
  if (!code) return;

  try {
    const res = await fetch(`${API}/d/${code}?depth=2`);
    const data = await res.json();
    const options = data.wards
      .map((p) => `<option value="${p.name}">${p.name}</option>`)
      .join("");
    phuongSel.insertAdjacentHTML("beforeend", options);
  } catch (e) {}
}
