<?php
session_start();
require_once 'config.php';

$isBuyNow = isset($_GET['type']) && $_GET['type'] === 'buynow';

if ($isBuyNow) {
    $cart = $_SESSION['buy_now'] ?? [];
} else {
    $cart = $_SESSION['cart'] ?? [];
}

if (empty($cart)) {
    header("Location: giohang.php");
    exit();
}

$tongTien = 0;
$items    = [];
$ids  = implode(',', array_map('intval', array_keys($cart)));
$stmt = $conn->query("SELECT * FROM books WHERE BookID IN ($ids)");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $slg       = $cart[$row['BookID']]['slg'] ?? 1;
    $thanhTien = $row['Price'] * $slg;
    $tongTien += $thanhTien;
    $items[]   = [
        'BookID'    => $row['BookID'],
        'Title'     => $row['Title'],
        'Price'     => $row['Price'],
        'ImageURL'  => $row['ImageURL'] ?? 'book-default.jpg',
        'slg'       => $slg,
        'ThanhTien' => $thanhTien,
    ];
}

$userID    = $_SESSION['user_id']   ?? null;
$userName  = $_SESSION['user_name'] ?? '';
$userInfo  = null;
$addresses = [];

if ($userID) {
    try {
        $stmt = $conn->prepare("SELECT FullName, Phone FROM users WHERE UserID = ?");
        $stmt->execute([$userID]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmtAddr = $conn->prepare("SELECT * FROM useraddresses WHERE UserID = ? ORDER BY IsDefault DESC, AddressID DESC");
        $stmtAddr->execute([$userID]);
        $addresses = $stmtAddr->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

$defaultAddr = null;
foreach ($addresses as $addr) {
    if ($addr['IsDefault'] == 1) { $defaultAddr = $addr; break; }
}

$preHoTen  = $defaultAddr['ReceiverName']  ?? ($userInfo['FullName'] ?? $userName);
$prePhone  = $defaultAddr['ReceiverPhone'] ?? ($userInfo['Phone']    ?? '');
$preDiaChi = $defaultAddr['DiaChiDay']     ?? '';
$prePhuong = $defaultAddr['PhuongXa']      ?? '';
$preQuan   = $defaultAddr['QuanHuyen']     ?? '';
$preTinh   = $defaultAddr['TinhTP']        ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toan - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/thanhtoan.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="thanhtoan-wrapper">
<div class="thanhtoan-container">
    <h2>Trang Thanh Toan</h2>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="xulythanhtoan.php" method="POST" onsubmit="return validateForm(event)">
        <input type="hidden" name="checkout_type" value="<?= isset($_GET['type']) && $_GET['type'] === 'buynow' ? 'buynow' : 'cart' ?>">

        <div class="info-box">
            <h3>Thong tin nguoi nhan</h3>

            <?php if ($userID): ?>
                <?php if (!empty($addresses)): ?>
                <div class="addr-dropdown-wrapper">
                    <button type="button" class="addr-dropdown-btn" id="addrDropBtn">
                        <span id="addrDropLabel">
                            <?= $defaultAddr
                                ? htmlspecialchars($defaultAddr['ReceiverName'] . ' - ' .
                                  $defaultAddr['DiaChiDay'] . ', ' . $defaultAddr['PhuongXa'] . ', ' .
                                  $defaultAddr['QuanHuyen'] . ', ' . $defaultAddr['TinhTP'])
                                : 'Chon dia chi giao hang' ?>
                        </span>
                        <span class="arrow">&#9660;</span>
                    </button>
                    <div class="addr-list" id="addrList">
                        <?php foreach ($addresses as $addr): ?>
                        <div class="addr-item"
                             data-name="<?= htmlspecialchars($addr['ReceiverName']) ?>"
                             data-phone="<?= htmlspecialchars($addr['ReceiverPhone']) ?>"
                             data-diachi="<?= htmlspecialchars($addr['DiaChiDay']) ?>"
                             data-phuong="<?= htmlspecialchars($addr['PhuongXa']) ?>"
                             data-quan="<?= htmlspecialchars($addr['QuanHuyen']) ?>"
                             data-tinh="<?= htmlspecialchars($addr['TinhTP']) ?>"
                             onclick="chonDiaChi(this)">
                            <strong>
                                <?= htmlspecialchars($addr['ReceiverName']) ?>
                                <?php if ($addr['IsDefault'] == 1): ?>
                                    <span class="badge-default">Mac dinh</span>
                                <?php endif; ?>
                            </strong>
                            <span>
                                <?= htmlspecialchars($addr['ReceiverPhone']) ?> |
                                <?= htmlspecialchars($addr['DiaChiDay']) ?>,
                                <?= htmlspecialchars($addr['PhuongXa']) ?>,
                                <?= htmlspecialchars($addr['QuanHuyen']) ?>,
                                <?= htmlspecialchars($addr['TinhTP']) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                        <a class="addr-them" href="profile.php?tab=address&redirect=thanhtoan">
                            + Them dia chi moi
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div style="margin-bottom:15px; padding:12px; background:#fff8e6; border:1px solid #c9a96e; border-radius:6px; font-size:14px;">
                    Ban chua co dia chi nao.
                    <a href="profile.php?tab=address&redirect=thanhtoan" style="color:#c9a96e; font-weight:600; margin-left:6px;">Them dia chi ngay</a>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <div id="selected-addr-display" style="<?= $defaultAddr ? '' : 'display:none;' ?>">
                <div class="selected-addr-box" id="selected-addr-box">
                    <?php if ($defaultAddr): ?>
                    <p><strong><?= htmlspecialchars($defaultAddr['ReceiverName']) ?></strong>
                       | <?= htmlspecialchars($defaultAddr['ReceiverPhone']) ?></p>
                    <p><?= htmlspecialchars($defaultAddr['DiaChiDay']) ?>,
                       <?= htmlspecialchars($defaultAddr['PhuongXa']) ?>,
                       <?= htmlspecialchars($defaultAddr['QuanHuyen']) ?>,
                       <?= htmlspecialchars($defaultAddr['TinhTP']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <input type="hidden" name="HoTen" id="HoTen" value="<?= htmlspecialchars($preHoTen) ?>">
            <input type="hidden" name="Phone" id="Phone" value="<?= htmlspecialchars($prePhone) ?>">
            <input type="hidden" name="DiaChiDay" id="DiaChiDay" value="<?= htmlspecialchars($preDiaChi) ?>">
            <input type="hidden" name="PhuongXa" id="PhuongXa" value="<?= htmlspecialchars($prePhuong) ?>">
            <input type="hidden" name="QuanHuyen" id="QuanHuyen" value="<?= htmlspecialchars($preQuan) ?>">
            <input type="hidden" name="TinhTP" id="TinhTP" value="<?= htmlspecialchars($preTinh) ?>">

            <?php if (!$userID): ?>
            <div class="form-row">
                <div class="form-group">
                    <label>Ho ten *</label>
                    <input type="text" required placeholder="Nhap ho ten..." oninput="document.getElementById('HoTen').value=this.value">
                </div>
                <div class="form-group">
                    <label>So dien thoai *</label>
                    <input type="tel" required maxlength="10" placeholder="Nhap so dien thoai..." oninput="this.value = this.value.replace(/[^0-9]/g, ''); document.getElementById('Phone').value=this.value;">
                </div>
            </div>
            <div class="form-group">
                <label>So nha, ten duong *</label>
                <input type="text" required placeholder="So nha, ten duong..." oninput="document.getElementById('DiaChiDay').value=this.value">
            </div>
            <div class="form-row three-col">
                <div class="form-group">
                    <label>Tinh/TP *</label>
                    <input type="text" required placeholder="Tinh/TP..." oninput="document.getElementById('TinhTP').value=this.value">
                </div>
                <div class="form-group">
                    <label>Quan/Huyen *</label>
                    <input type="text" required placeholder="Quan/Huyen..." oninput="document.getElementById('QuanHuyen').value=this.value">
                </div>
                <div class="form-group">
                    <label>Phuong/Xa *</label>
                    <input type="text" required placeholder="Phuong/Xa..." oninput="document.getElementById('PhuongXa').value=this.value">
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="Email" placeholder="Nhap email...">
            </div>
            <div class="form-group">
                <label>Ghi chu</label>
                <textarea name="GhiChu" rows="3" placeholder="Ghi chu them cho don hang..."></textarea>
            </div>
        </div>

        <div class="info-box">
            <h3>San pham da chon</h3>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>San pham</th>
                        <th>Don gia</th>
                        <th>SL</th>
                        <th>Thanh tien</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="product-cell">
                                <img src="assets/images/<?= htmlspecialchars($item['ImageURL']) ?>" onerror="this.src='assets/images/book-default.jpg'" alt="<?= htmlspecialchars($item['Title']) ?>">
                                <span><?= htmlspecialchars($item['Title']) ?></span>
                            </div>
                        </td>
                        <td><?= number_format($item['Price'],0,',','.') ?> d</td>
                        <td><?= $item['slg'] ?></td>
                        <td><?= number_format($item['ThanhTien'],0,',','.') ?> d</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="info-box">
            <h3>Tong tien</h3>
            <p class="tong-tien"><?= number_format($tongTien,0,',','.') ?> VND</p>
        </div>
        <?php if ($userID): ?>
            <input type="hidden" name="userID" value="<?= $userID ?>">
        <?php endif; ?>

        <button type="submit" class="btn-thanhtoan">Xac nhan dat hang</button>
    </form>

    <a href="giohang.php" class="back-link">Quay lai gio hang</a>
</div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

<script>
const addrDropBtn = document.getElementById('addrDropBtn');
const addrList    = document.getElementById('addrList');

if (addrDropBtn) {
    addrDropBtn.addEventListener('click', e => {
        e.stopPropagation();
        addrDropBtn.classList.toggle('open');
        addrList.classList.toggle('open');
    });
    document.addEventListener('click', () => {
        addrDropBtn?.classList.remove('open');
        addrList?.classList.remove('open');
    });
}

function chonDiaChi(el) {
    document.getElementById('HoTen').value = el.dataset.name;
    document.getElementById('Phone').value = el.dataset.phone;
    document.getElementById('DiaChiDay').value = el.dataset.diachi;
    document.getElementById('PhuongXa').value = el.dataset.phuong;
    document.getElementById('QuanHuyen').value = el.dataset.quan;
    document.getElementById('TinhTP').value = el.dataset.tinh;

    document.getElementById('addrDropLabel').textContent = el.dataset.name + ' - ' + el.dataset.diachi + ', ' + el.dataset.phuong + ', ' + el.dataset.quan + ', ' + el.dataset.tinh;

    document.getElementById('selected-addr-box').innerHTML = `
        <p><strong>${el.dataset.name}</strong> | ${el.dataset.phone}</p>
        <p>${el.dataset.diachi}, ${el.dataset.phuong}, ${el.dataset.quan}, ${el.dataset.tinh}</p>
    `;
    document.getElementById('selected-addr-display').style.display = 'block';

    addrDropBtn.classList.remove('open');
    addrList.classList.remove('open');
}

function validateForm(event) {
    const phone = document.getElementById('Phone').value.trim();
    
    if (phone.length > 0 && phone.length < 10) {
        event.preventDefault();
        showPopup('Số điện thoại phải đủ 10 chữ số!', 'error');
        return false;
    }

    <?php if ($userID): ?>
    if (!document.getElementById('HoTen').value.trim()) {
        event.preventDefault();
        showPopup('Vui long chon dia chi giao hang!', 'error');
        return false;
    }
    if (!document.getElementById('TinhTP').value.trim()) {
        event.preventDefault();
        showPopup('Dia chi chua day du! Vui long chon lai.', 'error');
        return false;
    }
    <?php endif; ?>
    return true;
}
</script>

</body>
</html>