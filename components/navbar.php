<link rel="stylesheet" href="assets/css/navbar.css">

<nav class="navbar">
    <div class="topbar">
        <div class="topbar-right">
            <?php if (isset($_SESSION['user_name'])): ?>
                <span>Chào, <a href="profile.php" title="Xem thông tin tài khoản" style="text-decoration: none; font-weight: bold; color: #e74c3c;">
                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </a></span>
                <span>|</span>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin'): ?>
                    <a href="admin.php" style="color: #e74c3c; font-weight: bold;">Trang Quản Trị</a>
                    <span>|</span>
                <?php endif; ?>

                <a href="logout.php">Đăng xuất</a>
            <?php else: ?>
                <a href="dangnhap.php">Đăng nhập</a>
                <span>|</span>
                <a href="dangky.php">Đăng ký</a>
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

                <!-- Sách -->
                <div class="mega-panel active" id="panel-sach">
                    <div class="mega-group">
                        <h4> Văn Học</h4>
                        <ul>
                            <li><a href="index.php?theloai=tieu-thuyet">Tiểu Thuyết</a></li>
                            <li><a href="index.php?theloai=truyen-ngan">Truyện Ngắn</a></li>
                            <li><a href="index.php?theloai=co-dien">Văn Học Cổ Điển</a></li>
                        </ul>
                    </div>
                    <div class="mega-group">
                        <h4> Tâm Lý</h4>
                        <ul>
                            <li><a href="index.php?theloai=kinh-di">Kinh Dị</a></li>
                            <li><a href="index.php?theloai=tam-ly-toi-pham">Tâm Lý Học Tội Phạm</a></li>
                            <li><a href="index.php?theloai=ky-nang-song">Kỹ Năng Sống</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Văn phòng phẩm -->
                <div class="mega-panel" id="panel-vpp">
                    <div class="mega-group">
                        <h4> Dụng Cụ Viết</h4>
                        <ul>
                            <li><a href="index.php?theloai=but-bi">Bút Bi</a></li>
                            <li><a href="index.php?theloai=but-chi">Bút Chì</a></li>
                            <li><a href="index.php?theloai=but-da-quang">Bút Dạ Quang</a></li>
                        </ul>
                    </div>
                    <div class="mega-group">
                        <h4> Vở & Giấy</h4>
                        <ul>
                            <li><a href="index.php?theloai=vo-o-ly">Vở Ô Li</a></li>
                            <li><a href="index.php?theloai=so-tay">Sổ Tay</a></li>
                            <li><a href="index.php?theloai=giay-note">Giấy Note</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <form action="timkiem.php" method="GET" class="search-box">
            <input type="text" name="tukhoa" placeholder="Tìm kiếm tên sách, tác giả..." required 
                   value="<?= isset($_GET['tukhoa']) ? htmlspecialchars($_GET['tukhoa']) : '' ?>">
            <button type="submit">Tìm kiếm</button>
        </form>

        <!-- GIỎ HÀNG -->
        <div class="cart" onclick="window.location.href='giohang.php';" style="cursor: pointer; display: inline-flex; align-items: center; margin-right: 10px;" title="Giỏ hàng của bạn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 28px; height: 28px; color: white;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
        </div>
    </div>
</nav>

<script>
// ── Hover vào danh mục bên trái → đổi panel bên phải ──
document.querySelectorAll('.mega-left li').forEach(item => {
    item.addEventListener('mouseenter', () => {
        document.querySelectorAll('.mega-left li').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.mega-panel').forEach(p => p.classList.remove('active'));

        // Bật active cho item đang hover
        item.classList.add('active');

        // Bật panel tương ứng
        const panelID = item.getAttribute('data-panel');
        const panel   = document.getElementById(panelID);
        if (panel) panel.classList.add('active');
    });
});
</script>