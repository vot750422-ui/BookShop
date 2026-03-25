/**
Hiển thị thông báo popup góc trên phải
 */
function showPopup(message, type = "success") {
  const old = document.getElementById("popup-notify");
  if (old) old.remove();

  const colors = {
    success: { bg: "#16a34a", icon: "✓" },
    error: { bg: "#dc2626", icon: "✕" },
    info: { bg: "#2563eb", icon: "ℹ" },
    warning: { bg: "#d97706", icon: "⚠" },
  };
  const c = colors[type] || colors.success;

  const box = document.createElement("div");
  box.id = "popup-notify";
  box.innerHTML = `<span style="font-size:16px;font-weight:700;">${c.icon}</span> ${message}`;

  Object.assign(box.style, {
    position: "fixed",
    top: "20px",
    right: "20px",
    background: c.bg,
    color: "#fff",
    padding: "12px 20px",
    borderRadius: "12px",
    fontFamily: "sans-serif",
    fontSize: "14px",
    fontWeight: "500",
    display: "flex",
    alignItems: "center",
    gap: "8px",
    boxShadow: "0 8px 24px rgba(0,0,0,0.18)",
    zIndex: "9999",
    opacity: "0",
    transform: "translateY(-10px)",
    transition: "all 0.3s ease",
  });
  document.body.appendChild(box);

  requestAnimationFrame(() => {
    box.style.opacity = "1";
    box.style.transform = "translateY(0)";
  });

  setTimeout(() => {
    box.style.opacity = "0";
    box.style.transform = "translateY(-10px)";
    setTimeout(() => box.remove(), 300);
  }, 2800);
}
document.addEventListener("DOMContentLoaded", function () {
  if (window.history && window.history.replaceState) {
    const url = new URL(window.location.href);

    const paramsToClear = ["msg", "error", "success", "updated", "huyed"];
    let urlChanged = false;

    paramsToClear.forEach((param) => {
      if (url.searchParams.has(param)) {
        url.searchParams.delete(param);
        urlChanged = true;
      }
    });

    if (urlChanged) {
      window.history.replaceState(null, "", url.toString());
    }
  }
});
