<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$userID  = $_SESSION['user_id'];
$orderID = (int)($_GET['id'] ?? 0);

if ($orderID <= 0) {
    header("Location: profile.php?tab=orders");
    exit();
}

try {
    $stmtOrder = $conn->prepare("SELECT * FROM orders WHERE OrderID = ? AND UserID = ?");
    $stmtOrder->execute([$orderID, $userID]);
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: profile.php?tab=orders");
        exit();
    }

    $stmtItems = $conn->prepare("SELECT od.*, b.Title, b.ImageURL FROM orderdetails od JOIN books b ON od.BookID = b.BookID WHERE od.OrderID = ?");
    $stmtItems->execute([$orderID]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    $stmtAddr = $conn->prepare("SELECT * FROM useraddresses WHERE UserID = ? ORDER BY IsDefault DESC, AddressID DESC");
    $stmtAddr->execute([$userID]);
    $addresses = $stmtAddr->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Loi: " . $e->getMessage());
}

$trangThai = $order['TrangThai'] ?? '';
$daHuy     = (mb_strtolower(trim($trangThai)) === 'da huy');

$defaultAddr = null;
foreach ($addresses as $addr) {
    if ($addr['IsDefault'] == 1) { $defaultAddr = $addr; break; }
}
if (!$defaultAddr && !empty($addresses)) { $defaultAddr = $addresses[0]; }

$displayHoTen  = ($order['HoTen']     ?? '') ?: ($defaultAddr['ReceiverName']  ?? '');
$displayPhone  = ($order['Phone']     ?? '') ?: ($defaultAddr['ReceiverPhone'] ?? '');
$displayDiaChi = ($order['DiaChiDay'] ?? '') ?: ($defaultAddr['DiaChiDay']     ?? '');
$displayPhuong = ($order['PhuongXa']  ?? '') ?: ($defaultAddr['PhuongXa']      ?? '');
$displayQuan   = ($order['QuanHuyen'] ?? '') ?: ($defaultAddr['QuanHuyen']     ?? '');
$displayTinh   = ($order['TinhTP']    ?? '') ?: ($defaultAddr['TinhTP']        ?? '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $orderID ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/chitietdonhang.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="order-detail-wrapper">
    <div class="order-detail-box">
        <div class="detail-header">
            <h2>Đơn hàng #<?= $orderID ?></h2>
            <span class="tt-tag" style="background:<?= $daHuy ? '#e74c3c' : '#f39c12' ?>;"><?= htmlspecialchars($trangThai) ?></span>
        </div>

        <h3 class="section-title">Sản phẩm đã đặt</h3>
        <table class="item-table">
            <thead>
                <tr><th>Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th style="text-align:right;">Thành tiền</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="assets/images/<?= htmlspecialchars($item['ImageURL'] ?? 'book-default.jpg') ?>">
                            <?= htmlspecialchars($item['Title']) ?>
                        </div>
                    </td>
                    <td><?= number_format($item['DonGia'],0,',','.') ?> đ</td>
                    <td><?= $item['SoLuong'] ?></td>
                    <td style="text-align:right;"><?= number_format($item['DonGia']*$item['SoLuong'],0,',','.') ?> đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="tong-row">Tổng cộng: <?= number_format($order['TongTien'],0,',','.') ?> VNĐ</div>
    </div>

    <div class="order-detail-box">
        <h3 class="section-title" style="font-size:17px;margin-bottom:20px;">Thông tin giao hàng</h3>

        <?php if ($daHuy): ?>
            <div class="notice-huy">Đơn hàng đã bị huỷ</div>
        <?php endif; ?>

        <form id="form-capnhat" action="xulycapnhatdonhang.php" method="POST" onsubmit="return <?= $daHuy ? 'khongChoCapNhat()' : 'validateForm()' ?>">
            <input type="hidden" name="orderID" value="<?= $orderID ?>">
            <input type="hidden" name="action"  value="capnhat">

            <div class="edit-section">
                <h3 class="section-title">Địa chỉ giao hàng</h3>

                <?php if (!$daHuy && !empty($addresses)): ?>
                <div class="addr-dropdown-wrapper">
                    <button type="button" class="addr-dropdown-btn" id="addrDropBtn">
                        <span id="addrDropLabel"><?= htmlspecialchars($displayHoTen . ' - ' . $displayDiaChi . ', ' . $displayPhuong . ', ' . $displayQuan . ', ' . $displayTinh) ?></span>
                        <span class="arrow">&#9660;</span>
                    </button>
                    <div class="addr-list" id="addrList">
                        <?php foreach ($addresses as $addr): ?>
                        <div class="addr-item" data-name="<?= htmlspecialchars($addr['ReceiverName']) ?>" data-phone="<?= htmlspecialchars($addr['ReceiverPhone']) ?>" data-diachi="<?= htmlspecialchars($addr['DiaChiDay']) ?>" data-phuong="<?= htmlspecialchars($addr['PhuongXa']) ?>" data-quan="<?= htmlspecialchars($addr['QuanHuyen']) ?>" data-tinh="<?= htmlspecialchars($addr['TinhTP']) ?>" onclick="chonDiaChi(this)">
                            <strong><?= htmlspecialchars($addr['ReceiverName']) ?></strong>
                            <span><?= htmlspecialchars($addr['DiaChiDay']) ?>, <?= htmlspecialchars($addr['PhuongXa']) ?>, <?= htmlspecialchars($addr['QuanHuyen']) ?>, <?= htmlspecialchars($addr['TinhTP']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <input type="hidden" name="HoTen" id="HoTen" value="<?= htmlspecialchars($displayHoTen) ?>">
                <input type="hidden" name="Phone" id="Phone" value="<?= htmlspecialchars($displayPhone) ?>">
                <input type="hidden" name="DiaChiDay" id="DiaChiDay" value="<?= htmlspecialchars($displayDiaChi) ?>">
                <input type="hidden" name="PhuongXa" id="PhuongXa" value="<?= htmlspecialchars($displayPhuong) ?>">
                <input type="hidden" name="QuanHuyen" id="QuanHuyen" value="<?= htmlspecialchars($displayQuan) ?>">
                <input type="hidden" name="TinhTP" id="TinhTP" value="<?= htmlspecialchars($displayTinh) ?>">

                <div class="current-addr-box" id="current-addr-box">
                    <strong><?= htmlspecialchars($displayHoTen) ?></strong> | <?= htmlspecialchars($displayPhone) ?><br>
                    <?= htmlspecialchars($displayDiaChi) ?>, <?= htmlspecialchars($displayPhuong) ?>, <?= htmlspecialchars($displayQuan) ?>, <?= htmlspecialchars($displayTinh) ?>
                </div>
            </div>

            <div class="edit-section">
                <h3 class="section-title">Thông tin thêm</h3>
                <div class="form-row one">
                    <div class="form-group"><label>Email</label><input type="email" name="Email" value="<?= htmlspecialchars($order['Email'] ?? '') ?>" <?= $daHuy ? 'disabled' : '' ?>></div>
                </div>
                <div class="form-row one">
                    <div class="form-group"><label>Ghi chú</label><textarea name="GhiChu" <?= $daHuy ? 'disabled' : '' ?>><?= htmlspecialchars($order['GhiChu'] ?? '') ?></textarea></div>
                </div>
            </div>

            <div class="btn-group">
                <a href="profile.php?tab=orders" class="btn-back">Quay lại</a>
                <?php if (!$daHuy): ?>
                    <button type="button" class="btn-huy" onclick="confirmHuyDon()">Huỷ đơn hàng</button>
                    <button type="submit" class="btn-update">Cập nhật đơn hàng</button>
                <?php endif; ?>
            </div>
        </form>

        <form id="form-huy" action="xulycapnhatdonhang.php" method="POST" style="display:none;">
            <input type="hidden" name="orderID" value="<?= $orderID ?>">
            <input type="hidden" name="action"  value="huy">
        </form>
    </div>
</div>

<div id="modal-huy-don" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; align-items:center; justify-content:center;">
    <div style="background:white; padding:30px; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.3); text-align:center; max-width:400px; width:90%; border-top: 5px solid #e74c3c;">
        <h3 style="color:#2c1a0e; margin:0 0 10px 0; font-size:20px;"> Xác nhận huỷ đơn</h3>
        <p style="color:#555; margin-bottom:25px; font-size:15px; line-height:1.5;">Bạn có chắc chắn muốn huỷ đơn hàng này không?</p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button onclick="document.getElementById('form-huy').submit()" style="background:#e74c3c; color:white; border:none; padding:12px 25px; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer;">Có</button>
            <button onclick="document.getElementById('modal-huy-don').style.display='none'" style="background:#eee; color:#333; border:none; padding:12px 25px; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer;">Không</button>
        </div>
    </div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>
<script src="assets/js/popup.js"></script>

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
    document.getElementById('HoTen').value     = el.dataset.name;
    document.getElementById('Phone').value     = el.dataset.phone;
    document.getElementById('DiaChiDay').value = el.dataset.diachi;
    document.getElementById('PhuongXa').value  = el.dataset.phuong;
    document.getElementById('QuanHuyen').value = el.dataset.quan;
    document.getElementById('TinhTP').value    = el.dataset.tinh;

    document.getElementById('addrDropLabel').textContent = `${el.dataset.name} - ${el.dataset.diachi}, ${el.dataset.phuong}, ${el.dataset.quan}, ${el.dataset.tinh}`;
    document.getElementById('current-addr-box').innerHTML = `<strong>${el.dataset.name}</strong> | ${el.dataset.phone}<br>${el.dataset.diachi}, ${el.dataset.phuong}, ${el.dataset.quan}, ${el.dataset.tinh}`;

    addrDropBtn.classList.remove('open');
    addrList.classList.remove('open');
}

function khongChoCapNhat() { showPopup('Đơn hàng này đã bị huỷ', 'error'); return false; }
function validateForm() {
    if (!document.getElementById('HoTen').value.trim()) { showPopup('Vui lòng chọn địa chỉ giao hàng!', 'error'); return false; }
    return true;
}
function confirmHuyDon() { document.getElementById('modal-huy-don').style.display = 'flex'; }
</script>
</body>
</html>