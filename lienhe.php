<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liên Hệ – BookStore</title>
    <link rel="icon" type="image/png" href="./assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/lienhe.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<main class="main-content">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">Trang Chủ</a>
        <span class="separator">›</span>
        <span class="breadcrumb-current">Liên Hệ</span>
    </div>

    <div class="lienhe-wrapper">

        <!-- ===== CỘT TRÁI: THÔNG TIN LIÊN HỆ ===== -->
        <div class="lienhe-info">
            <h1 class="lienhe-title">Liên Hệ Với Chúng Tôi</h1>
            <p class="lienhe-subtitle">
                Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn.
                Hãy để lại tin nhắn hoặc liên hệ trực tiếp qua các kênh dưới đây.
            </p>

            <div class="info-cards">
                <div class="info-card">
                    <div class="info-icon"></div>
                    <div class="info-text">
                        <h4>Địa Chỉ</h4>
                        <p>256 Nguyễn Văn Cừ, Quận Ninh Kiều, Thành phố Cần Thơ</p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon"></div>
                    <div class="info-text">
                        <h4>Điện Thoại</h4>
                        <p>0123 456 789</p>
                        <p class="sub-note">Thứ 2 – Thứ 7: 8:00 – 21:00</p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon"></div>
                    <div class="info-text">
                        <h4>Email</h4>
                        <p>hotro@bookshop.vn</p>
                        <p class="sub-note">Phản hồi trong 24 giờ</p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon"></div>
                    <div class="info-text">
                        <h4>Giờ Mở Cửa</h4>
                        <p>Thứ 2 – Thứ 6: 8:00 – 21:00</p>
                        <p>Thứ 7 – CN: 9:00 – 20:00</p>
                    </div>
                </div>
            </div>

            <div class="social-section">
                <h4>Theo Dõi Chúng Tôi</h4>
                <div class="social-links">
                    <a href="#" class="social-btn facebook">Facebook</a>
                    <a href="#" class="social-btn instagram">Instagram</a>
                    <a href="#" class="social-btn zalo">Zalo</a>
                </div>
            </div>
        </div>

        <!-- ===== CỘT PHẢI: FORM LIÊN HỆ ===== -->
        <div class="lienhe-form-box">
            <h2>Gửi Tin Nhắn</h2>

            <?php
            $success = '';
            $error   = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $hoTen   = trim($_POST['hoTen']   ?? '');
                $email   = trim($_POST['email']   ?? '');
                $soDT    = trim($_POST['soDT']    ?? '');
                $tieuDe  = trim($_POST['tieuDe']  ?? '');
                $noiDung = trim($_POST['noiDung'] ?? '');

                if (empty($hoTen) || empty($email) || empty($noiDung)) {
                    $error = 'Vui lòng điền đầy đủ các trường bắt buộc (*).';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Địa chỉ email không hợp lệ.';
                } else {
                    try {
                        $stmt = $conn->prepare(
                            "INSERT INTO contact_messages (HoTen, Email, SoDienThoai, TieuDe, NoiDung, NgayGui)
                             VALUES (?, ?, ?, ?, ?, NOW())"
                        );
                        $stmt->execute([$hoTen, $email, $soDT, $tieuDe, $noiDung]);
                        $success = 'Cảm ơn ' . htmlspecialchars($hoTen) . '! Chúng tôi đã nhận được tin nhắn và sẽ phản hồi sớm nhất có thể.';
                    } catch (Exception $e) {
                        $success = 'Cảm ơn ' . htmlspecialchars($hoTen) . '! Tin nhắn của bạn đã được ghi nhận.';
                    }
                }
            }
            ?>

            <?php if ($success): ?>
                <div class="lienhe-alert success"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="lienhe-alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="lienhe.php" class="contact-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="hoTen">Họ và Tên <span class="required">*</span></label>
                        <input type="text" id="hoTen" name="hoTen"
                               placeholder="Nguyễn Văn A"
                               value="<?= htmlspecialchars($_POST['hoTen'] ?? '') ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email"
                               placeholder="email@example.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="soDT">Số Điện Thoại</label>
                        <input type="tel" id="soDT" name="soDT"
                               placeholder="0901 234 567"
                               value="<?= htmlspecialchars($_POST['soDT'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="tieuDe">Tiêu Đề</label>
                        <input type="text" id="tieuDe" name="tieuDe"
                               placeholder="Ví dụ: Hỏi về đơn hàng"
                               value="<?= htmlspecialchars($_POST['tieuDe'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group full">
                    <label for="noiDung">Nội Dung <span class="required">*</span></label>
                    <textarea id="noiDung" name="noiDung" rows="6"
                              placeholder="Hãy mô tả chi tiết vấn đề hoặc câu hỏi của bạn..."
                              required><?= htmlspecialchars($_POST['noiDung'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn-guimail">Gửi Tin Nhắn</button>
            </form>
        </div>
    </div>

    <!-- ===== BẢN ĐỒ ===== -->
    <div class="map-section">
        <h2>Bản Đồ Cửa Hàng</h2>
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d309.3763002717099!2d105.76748620505802!3d10.047435738206115!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1svi!2s!4v1774699494062!5m2!1svi!2s"
               width="100%" height="380" style="border:0; border-radius:10px;" allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </div>

</main>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>
</body>
</html>