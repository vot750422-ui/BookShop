<?php ?>
<nav class="navbar">
    <div class="topbar">
        <div class="topbar-right">
    <?php if (isset($_SESSION['user_name'])): ?>
        <span>Chào, <a href="profile.php" title="Xem thông tin tài khoản" style="text-decoration: none; font-weight: bold; color: #e74c3c;">
    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
</a></span>
        <span>|</span>
        
        <!-- KIỂM TRA QUYỀN ADMIN Ở ĐÂY -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin'): ?>
            <a href="admin.php" style="color: #e74c3c; font-weight: bold;">Trang Quản Trị</a>
            <span>|</span>
        <?php endif; ?>

        <a href="Logout.php">Đăng xuất</a>
    <?php else: ?>
        <a href="Dangnhap.php">Đăng nhập</a>
        <span>|</span>
        <a href="Dangky.php">Đăng ký</a>
    <?php endif; ?>
</div>
    </div>

    <div class="nav-top">
        <div class="logo">
            <a href="index.php">
                <img src="./assets/images/logo.png" alt="logo">
            </a>
        </div>
        <ul class="menu">
            <li><a href="index.php">Trang chủ</a></li>
            <li><a href="#">Tin tức</a></li>
            <li><a href="#">Liên hệ</a></li>
        </ul>
    </div>

    <div class="nav-bottom">

        <!-- DANH MỤC SẢN PHẨM - CLICK ĐỂ MỞ -->
        <div class="category-wrapper">
            <div class="category" id="categoryBtn"> Danh mục sản phẩm</div>

            <div class="mega-menu" id="megaMenu">

                <!-- CỘT TRÁI -->
                <ul class="mega-left">
                    <li class="active" data-panel="panel-sach">
                        <a> Sách</a>
                        <span>›</span>
                    </li>
                    <li data-panel="panel-vpp">
                        <a> Văn phòng phẩm</a>
                        <span>›</span>
                    </li>
                </ul>

                <!-- PANEL: Sách -->
                <div class="mega-panel active" id="panel-sach">
                    <div class="mega-group">
                        <h4> Văn Học</h4>
                        <ul>
                            <li><a href="?theloai=tieu-thuyet">Tiểu Thuyết</a></li>
                            <li><a href="?theloai=truyen-ngan">Truyện Ngắn</a></li>
                            <li><a href="?theloai=co-dien">Văn Học Cổ Điển</a></li>
                        </ul>
                    </div>
                    <div class="mega-group">
                        <h4> Tâm Lý</h4>
                        <ul>
                            <li><a href="?theloai=kinh-di">Kinh Dị</a></li>
                            <li><a href="?theloai=tam-ly-toi-pham">Tâm Lý Học Tội Phạm</a></li>
                            <li><a href="?theloai=ky-nang-song">Kỹ Năng Sống</a></li>
                        </ul>
                    </div>
                </div>

                <!-- PANEL: Văn phòng phẩm -->
                <div class="mega-panel" id="panel-vpp">
                    <div class="mega-group">
                        <h4> Dụng Cụ Viết</h4>
                        <ul>
                            <li><a href="?theloai=but-bi">Bút Bi</a></li>
                            <li><a href="?theloai=but-chi">Bút Chì</a></li>
                            <li><a href="?theloai=but-da-quang">Bút Dạ Quang</a></li>
                        </ul>
                    </div>
                    <div class="mega-group">
                        <h4> Vở & Giấy</h4>
                        <ul>
                            <li><a href="?theloai=vo-o-ly">Vở Ô Li</a></li>
                            <li><a href="?theloai=so-tay">Sổ Tay</a></li>
                            <li><a href="?theloai=giay-note">Giấy Note</a></li>
                        </ul>
                    </div>
                </div>

            </div><!-- end mega-menu -->
        </div><!-- end category-wrapper -->

        <form action="timkiem.php" method="GET" class="search-box">
    <input type="text" name="tukhoa" placeholder="Tìm kiếm tên sách, tác giả..." required 
           value="<?= isset($_GET['tukhoa']) ? htmlspecialchars($_GET['tukhoa']) : '' ?>">
    <button type="submit">Tìm kiếm</button>
</form>

        <div class="cart" onclick="window.location.href='GioHang.php';" style="cursor: pointer;">
    🛒 Giỏ hàng <span class="cart-count">0</span>
</div>
    </div>
</nav>

<style>
/* ===== MEGA MENU ===== */

.category-wrapper {
    position: relative;
}

.category {
    color: white;
    font-weight: 600;
    cursor: pointer;
    padding: 6px 14px;
    border-radius: 4px;
    transition: background 0.2s;
    user-select: none;
    font-size: 15px;
}

.category:hover {
    background: rgba(255, 255, 255, 0.15);
}

/* Mega menu — ẩn mặc định */
.mega-menu {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.18);
    z-index: 999;
    min-width: 580px;
    flex-direction: row;
    overflow: hidden;
}

/* Class .open để JS bật lên */
.mega-menu.open {
    display: flex;
}

/* ===== CỘT TRÁI ===== */
.mega-left {
    list-style: none;
    width: 200px;
    background: #f7f7f7;
    border-right: 1px solid #eee;
    padding: 8px 0;
    flex-shrink: 0;
}

.mega-left li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 18px;
    cursor: pointer;
    transition: background 0.2s;
    border-left: 3px solid transparent;
}

.mega-left li:hover,
.mega-left li.active {
    background: white;
    border-left: 3px solid #0f3a63;
}

.mega-left li a {
    text-decoration: none;
    color: #333;
    font-size: 14px;
    font-weight: 500;
    pointer-events: none;
}

.mega-left li span {
    color: #aaa;
    font-size: 16px;
}

.mega-left li.active a,
.mega-left li:hover a {
    color: #0f3a63;
    font-weight: 600;
}

/* ===== CỘT PHẢI - PANEL ===== */
.mega-panel {
    display: none;
    flex: 1;
    padding: 20px 25px;
    gap: 30px;
    flex-wrap: wrap;
    align-content: flex-start;
}

.mega-panel.active {
    display: flex;
}

.mega-group {
    min-width: 150px;
}

.mega-group h4 {
    color: #0f3a63;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 10px;
    padding-bottom: 6px;
    border-bottom: 2px solid #00bcd4;
    letter-spacing: 0.5px;
}

.mega-group ul {
    list-style: none;
    padding: 0;
}

.mega-group ul li {
    margin-bottom: 8px;
}

.mega-group ul li a {
    text-decoration: none;
    color: #555;
    font-size: 14px;
    transition: color 0.2s, padding-left 0.2s;
    display: block;
}

.mega-group ul li a:hover {
    color: #0f3a63;
    padding-left: 6px;
}
</style>

<script>
const categoryBtn = document.getElementById('categoryBtn');
const megaMenu    = document.getElementById('megaMenu');

// ── CLICK nút "Danh mục" → mở / đóng menu ──
categoryBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    megaMenu.classList.toggle('open');
});

// ── Click ra ngoài → đóng menu ──
document.addEventListener('click', function (e) {
    if (!megaMenu.contains(e.target) && e.target !== categoryBtn) {
        megaMenu.classList.remove('open');
    }
});

// ── Hover vào danh mục bên trái → đổi panel bên phải ──
document.querySelectorAll('.mega-left li').forEach(item => {
    item.addEventListener('mouseenter', () => {
        document.querySelectorAll('.mega-left li').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.mega-panel').forEach(p => p.classList.remove('active'));

        item.classList.add('active');

        const panelID = item.getAttribute('data-panel');
        const panel   = document.getElementById(panelID);
        if (panel) panel.classList.add('active');
    });
});
</script>