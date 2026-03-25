<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$userID = $_SESSION['user_id'];

$stmtUser = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
$stmtUser->execute([$userID]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

$stmtOrders = $conn->prepare("SELECT * FROM orders WHERE UserID = ? ORDER BY NgayDat DESC");
$stmtOrders->execute([$userID]);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

$stmtAddress = $conn->prepare("SELECT * FROM useraddresses WHERE UserID = ? ORDER BY IsDefault DESC, AddressID DESC");
$stmtAddress->execute([$userID]);
$addresses = $stmtAddress->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài khoản - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="profile-container">
    <div class="profile-sidebar">
        <h3>Tài khoản của tôi</h3>
        <?php $tab = $_GET['tab'] ?? 'info'; ?>
        <ul class="profile-menu">
            <li><a href="?tab=info"    class="<?= $tab=='info'    ?'active':'' ?>">Thông tin cá nhân</a></li>
            <li><a href="?tab=address" class="<?= $tab=='address' ?'active':'' ?>">Sổ địa chỉ</a></li>
            <li><a href="?tab=orders"  class="<?= $tab=='orders'  ?'active':'' ?>">Đơn đặt hàng</a></li>
        </ul>
    </div>

    <div class="profile-content">
        <?php if ($tab == 'info'): ?>
            <h2>Hồ sơ cá nhân</h2>
            <form action="capnhatinfo.php" method="POST">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($user['FullName']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?= htmlspecialchars($user['Email']) ?>" disabled style="background:#f5f5f5;">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['Phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" name="birthdate" value="<?= htmlspecialchars($user['BirthDate'] ?? '') ?>">
                </div>
                <button type="submit" class="btn-save">Lưu thay đổi</button>
            </form>

        <?php elseif ($tab == 'address'): ?>
            <h2>Sổ địa chỉ</h2>
            <div class="add-address-form">
                <h4>Thêm địa chỉ mới</h4>
                <form action="xulydiachi.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="redirect" value="<?= isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'profile' ?>">
                    <div class="addr-row">
                        <div class="form-group"><label>Tên người nhận *</label><input type="text" name="receiver_name" required></div>
                        <div class="form-group"><label>Số điện thoại *</label><input type="text" name="receiver_phone" required></div>
                    </div>
                    <div class="addr-row">
                        <div class="form-group">
                            <label>Tỉnh/TP *</label>
                            <select id="tinh-addr" name="tinh" required onchange="loadQuanAddr()"><option value="">-- Chọn Tỉnh/TP --</option></select>
                        </div>
                        <div class="form-group">
                            <label>Quận/Huyện *</label>
                            <select id="quan-addr" name="quan" required onchange="loadPhuongAddr()"><option value="">-- Chọn Quận/Huyện --</option></select>
                        </div>
                        <div class="form-group">
                            <label>Phường/Xã *</label>
                            <select id="phuong-addr" name="phuong" required><option value="">-- Chọn Phường/Xã --</option></select>
                        </div>
                    </div>
                    <div class="addr-row">
                        <div class="form-group" style="flex:100%;">
                            <label>Số nhà, tên đường *</label><input type="text" name="detail_address" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_default" value="1"> Đặt làm địa chỉ mặc định</label>
                    </div>
                    <button type="submit" class="btn-save">Thêm địa chỉ</button>
                </form>
            </div>

            <?php if (empty($addresses)): ?>
                <p style="color:#999;">Bạn chưa lưu địa chỉ nào.</p>
            <?php else: ?>
                <?php foreach ($addresses as $addr): ?>
                <div class="address-card">
                    <p><strong><?= htmlspecialchars($addr['ReceiverName']) ?></strong> | <?= htmlspecialchars($addr['ReceiverPhone']) ?></p>
                    <p><?= htmlspecialchars($addr['DiaChiDay']) ?>, <?= htmlspecialchars($addr['PhuongXa']) ?>, <?= htmlspecialchars($addr['QuanHuyen']) ?>, <?= htmlspecialchars($addr['TinhTP']) ?></p>
                    <?php if ($addr['IsDefault'] == 1): ?><span class="badge-default">Mặc định</span><?php endif; ?>
                    <form action="xulydiachi.php" method="POST" id="form-xoa-addr-<?= $addr['AddressID'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="address_id" value="<?= $addr['AddressID'] ?>">
                        <button type="button" class="btn-xoa-addr" onclick="confirmXoaAddr(<?= $addr['AddressID'] ?>)">Xoá</button>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php elseif ($tab == 'orders'): ?>
            <h2>Đơn đặt hàng</h2>
            <?php if (empty($orders)): ?>
                <p style="color:#999;">Bạn chưa có đơn hàng nào.</p>
            <?php else: ?>
                <table class="order-table">
                    <thead>
                        <tr><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th>Chi tiết</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= $o['NgayDat'] ? date('d/m/Y H:i', strtotime($o['NgayDat'])) : '---' ?></td>
                            <td><?= number_format($o['TongTien'],0,',','.') ?> đ</td>
                            <td>
                                <?php $c = (mb_strtolower(trim($o['TrangThai'])) === 'da huy' || mb_strtolower(trim($o['TrangThai'])) === 'hủy') ? '#e74c3c' : '#f39c12'; ?>
                                <span class="tt-tag" style="background:<?= $c ?>;"><?= htmlspecialchars($o['TrangThai']) ?></span>
                            </td>
                            <td><a href="chitietdonhang.php?id=<?= $o['OrderID'] ?>" class="btn-xemct">Xem chi tiết</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div id="modal-xoa-addr" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; align-items:center; justify-content:center;">
    <div style="background:white; padding:30px; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.3); text-align:center; max-width:400px; width:90%; border-top: 5px solid #e74c3c;">
        <h3 style="color:#2c1a0e; margin:0 0 10px 0; font-size:20px;">Xác nhận xoá</h3>
        <p style="color:#555; margin-bottom:25px; font-size:15px; line-height:1.5;">Bạn có chắc chắn muốn xoá địa chỉ này không?</p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button id="btn-confirm-xoa" style="background:#e74c3c; color:white; border:none; padding:12px 25px; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer;">Có, xoá ngay</button>
            <button onclick="document.getElementById('modal-xoa-addr').style.display='none'" style="background:#eee; color:#333; border:none; padding:12px 25px; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer;">Không, quay lại</button>
        </div>
    </div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

<script>
const API = 'https://provinces.open-api.vn/api';

window.addEventListener('load', async () => {
    try {
        const res  = await fetch(`${API}/p/`);
        const data = await res.json();
        const sel  = document.getElementById('tinh-addr');
        if (!sel) return;
        data.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.name; opt.dataset.code = t.code; opt.textContent = t.name;
            sel.appendChild(opt);
        });
    } catch(e) {}
});

async function loadQuanAddr() {
    const tinhSel = document.getElementById('tinh-addr');
    const quanSel = document.getElementById('quan-addr');
    const phuongSel = document.getElementById('phuong-addr');
    quanSel.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
    phuongSel.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    const code = tinhSel.selectedOptions[0]?.dataset.code;
    if (!code) return;
    try {
        const res = await fetch(`${API}/p/${code}?depth=2`);
        const data = await res.json();
        data.districts.forEach(q => {
            const opt = document.createElement('option');
            opt.value = q.name; opt.dataset.code = q.code; opt.textContent = q.name;
            quanSel.appendChild(opt);
        });
    } catch(e) {}
}

async function loadPhuongAddr() {
    const quanSel = document.getElementById('quan-addr');
    const phuongSel = document.getElementById('phuong-addr');
    phuongSel.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    const code = quanSel.selectedOptions[0]?.dataset.code;
    if (!code) return;
    try {
        const res = await fetch(`${API}/d/${code}?depth=2`);
        const data = await res.json();
        data.wards.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.name; opt.textContent = p.name;
            phuongSel.appendChild(opt);
        });
    } catch(e) {}
}

let currentDeleteId = null;
function confirmXoaAddr(id) {
    currentDeleteId = id;
    document.getElementById('modal-xoa-addr').style.display = 'flex';
}

document.getElementById('btn-confirm-xoa').addEventListener('click', function() {
    if (currentDeleteId) document.getElementById('form-xoa-addr-' + currentDeleteId).submit();
});

window.addEventListener('load', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    
    if (msg === 'ok') showPopup('Cập nhật thông tin thành công!', 'success');
    else if (msg === 'error') showPopup('Có lỗi xảy ra, vui lòng thử lại!', 'error');
    else if (msg === 'deleted') showPopup('Đã xoá địa chỉ thành công!', 'success');
    else if (msg === 'added') showPopup('Đã thêm địa chỉ thành công!', 'success');

    if (msg && window.history.replaceState) {
        const cleanUrl = new URL(window.location.href);
        cleanUrl.searchParams.delete('msg');
        window.history.replaceState(null, '', cleanUrl.toString());
    }
});
</script>
</body>
</html>