<?php session_start(); ?>
<nav class="navbar">
    <div class="topbar">
        <div class="topbar-right">
            <?php if (isset($_SESSION['user_name'])): ?>
                <span>Chào, <strong><?php echo $_SESSION['user_name']; ?></strong></span>
                <span>|</span>
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
            <img src="assets/images/logo.jpg" alt="logo">
            <span>Sách</span>
        </div>
        <ul class="menu">
            <li><a href="index.php">Trang chủ</a></li>
            <li><a href="#">Tin tức</a></li>
            <li><a href="#">Liên hệ</a></li>
        </ul>
    </div>
    
    <div class="nav-bottom">
        <div class="category">☰ Danh mục sản phẩm</div>
        <div class="search-box">
            <input type="text" placeholder="Tìm kiếm sách...">
            <button>Tìm kiếm</button>
        </div>
        <div class="cart">🛒 Giỏ hàng <span class="cart-count">0</span></div>
    </div>
</nav>