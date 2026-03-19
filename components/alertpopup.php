<!-- ===== NÚT LÊN ĐẦU TRANG ===== -->
<button id="backToTop" title="Lên đầu trang">▲</button>

<!-- ===== POPUP THÔNG BÁO ===== -->
<div id="popup-overlay"></div>
<div id="popup-box">
    <div id="popup-icon"></div>
    <p id="popup-msg"></p>
    <button id="popup-close">Đóng</button>
</div>

<style>
/* ===== NÚT LÊN ĐẦU TRANG ===== */
#backToTop {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 45px;
    height: 45px;
    background: #2c1a0e;
    color: #f0e6d3;
    border: none;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    display: none;        /* Ẩn mặc định */
    z-index: 998;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: background 0.3s, transform 0.2s;
}

#backToTop:hover {
    background: #c9a96e;
    transform: translateY(-3px);
}

/* ===== POPUP OVERLAY ===== */
#popup-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 1000;
}

/* ===== POPUP BOX ===== */
#popup-box {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.8);
    background: white;
    border-radius: 12px;
    padding: 35px 40px;
    text-align: center;
    z-index: 1001;
    min-width: 300px;
    max-width: 420px;
    width: 90%;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    border-top: 5px solid #c9a96e;
    opacity: 0;
    transition: transform 0.25s ease, opacity 0.25s ease;
}

#popup-box.show {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

#popup-icon {
    font-size: 50px;
    margin-bottom: 12px;
}

#popup-msg {
    color: #2c1a0e;
    font-size: 15px;
    margin-bottom: 20px;
    line-height: 1.6;
}

#popup-close {
    background: #2c1a0e;
    color: #f0e6d3;
    border: none;
    padding: 10px 28px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

#popup-close:hover {
    background: #c9a96e;
}
</style>

<script>
// ===== NÚT LÊN ĐẦU TRANG =====
const backToTopBtn = document.getElementById('backToTop');

// Hiện nút khi cuộn xuống > 300px
window.addEventListener('scroll', () => {
    backToTopBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
});

// Bấm nút → cuộn lên đầu mượt mà
backToTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// ===== POPUP =====
const overlay   = document.getElementById('popup-overlay');
const popupBox  = document.getElementById('popup-box');
const popupIcon = document.getElementById('popup-icon');
const popupMsg  = document.getElementById('popup-msg');
const popupClose = document.getElementById('popup-close');

/**
 * Hiện popup thông báo
 * @param {string} message  - Nội dung thông báo
 * @param {string} type     - 'success' | 'error' | 'warning' | 'info'
 * @param {function} callback - Hàm gọi sau khi đóng (tuỳ chọn)
 */
function showPopup(message, type = 'info', callback = null) {
    const icons = {
        success : '✅',
        error   : '❌',
        warning : '⚠️',
        info    : 'ℹ️',
    };

    const colors = {
        success : '#c9a96e',
        error   : '#e74c3c',
        warning : '#f39c12',
        info    : '#2c1a0e',
    };

    popupIcon.textContent = icons[type] || 'ℹ️';
    popupMsg.textContent  = message;
    popupBox.style.borderTopColor = colors[type] || '#c9a96e';

    overlay.style.display  = 'block';
    popupBox.style.display = 'block';

    // Trigger animation
    setTimeout(() => popupBox.classList.add('show'), 10);

    // Lưu callback để gọi sau khi đóng
    popupBox._callback = callback;
}

// Đóng popup
function closePopup() {
    popupBox.classList.remove('show');
    setTimeout(() => {
        overlay.style.display  = 'none';
        popupBox.style.display = 'none';

        // Gọi callback nếu có (ví dụ redirect sau khi đóng)
        if (typeof popupBox._callback === 'function') {
            popupBox._callback();
            popupBox._callback = null;
        }
    }, 250);
}

popupClose.addEventListener('click', closePopup);
overlay.addEventListener('click', closePopup);

// Đóng bằng phím Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePopup();
});
</script>