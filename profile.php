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

$stmtAddress = $conn->prepare(
    "SELECT * FROM useraddresses WHERE UserID = ? ORDER BY IsDefault DESC, AddressID DESC"
);
$stmtAddress->execute([$userID]);
$addresses = $stmtAddress->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tai khoan - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-container { max-width:1000px; margin:40px auto; display:flex; gap:30px; padding:0 20px; }
        .profile-sidebar { flex:0 0 220px; background:white; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); height:fit-content; }
        .profile-sidebar h3 { color:#2c1a0e; margin-bottom:20px; font-size:16px; border-bottom:2px solid #c9a96e; padding-bottom:10px; }
        .profile-menu { list-style:none; padding:0; }
        .profile-menu li { margin-bottom:8px; }
        .profile-menu a { text-decoration:none; color:#555; font-size:14px; display:block; padding:8px 10px; border-radius:4px; transition:all 0.2s; }
        .profile-menu a:hover, .profile-menu a.active { background:#fdf6ec; color:#c9a96e; font-weight:600; }
        .profile-content { flex:1; background:white; padding:25px 30px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        .profile-content h2 { margin-bottom:20px; color:#2c1a0e; font-size:20px; border-bottom:2px solid #f0e6d3; padding-bottom:10px; }
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; font-weight:600; margin-bottom:5px; color:#333; font-size:13px; }
        .form-group input, .form-group select {
            width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;
            font-size:14px; box-sizing:border-box;
        }
        .form-group input:focus, .form-group select:focus { outline:none; border-color:#c9a96e; }
        .btn-save { background:#2c1a0e; color:#f0e6d3; border:none; padding:10px 22px; border-radius:4px; cursor:pointer; font-weight:600; font-size:14px; transition:background 0.2s; }
        .btn-save:hover { background:#c9a96e; }

        /* Bang don hang */
        .order-table { width:100%; border-collapse:collapse; margin-top:10px; }
        .order-table th { background:#2c1a0e; color:#f0e6d3; padding:11px 12px; text-align:left; font-size:13px; }
        .order-table td { padding:11px 12px; border-bottom:1px solid #f0e6d3; font-size:14px; vertical-align:middle; }
        .order-table tr:hover td { background:#fdf6ec; }
        .btn-xemct { display:inline-block; background:#c9a96e; color:white; padding:5px 14px; border-radius:4px; text-decoration:none; font-size:13px; font-weight:600; transition:background 0.2s; }
        .btn-xemct:hover { background:#a07840; }
        .tt-tag { padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; color:white; }

        /* So dia chi */
        .address-card { border:1px solid #e8d9c0; padding:15px; border-radius:6px; margin-bottom:12px; position:relative; background:#fdf6ec; }
        .address-card p { margin:4px 0; font-size:14px; color:#555; }
        .badge-default { display:inline-block; background:#c9a96e; color:white; font-size:11px; padding:2px 8px; border-radius:10px; margin-top:5px; font-weight:600; }
        .btn-xoa-addr { position:absolute; top:12px; right:12px; background:none; border:none; color:#e74c3c; cursor:pointer; font-weight:600; font-size:13px; }
        .btn-xoa-addr:hover { text-decoration:underline; }

        .add-address-form { background:white; border:1px solid #e8d9c0; padding:20px; border-radius:8px; margin-bottom:25px; }
        .add-address-form h4 { margin:0 0 15px; color:#2c1a0e; font-size:15px; }
        .addr-row { display:flex; gap:15px; flex-wrap:wrap; margin-bottom:12px; }
        .addr-row .form-group { flex:1; min-width:150px; margin-bottom:0; }
        .addr-row .form-group input,
        .addr-row .form-group select { width:100%; padding:9px; border:1px solid #ddd; border-radius:4px; font-size:13px; box-sizing:border-box; }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="profile-container">
    <div class="profile-sidebar">
        <h3>Tai khoan cua toi</h3>
        <?php $tab = $_GET['tab'] ?? 'info'; ?>
        <ul class="profile-menu">
            <li><a href="?tab=info"    class="<?= $tab=='info'    ?'active':'' ?>">Ho so ca nhan</a></li>
            <li><a href="?tab=address" class="<?= $tab=='address' ?'active':'' ?>">So dia chi</a></li>
            <li><a href="?tab=orders"  class="<?= $tab=='orders'  ?'active':'' ?>">Don dat hang</a></li>
        </ul>
    </div>

    <div class="profile-content">

        <?php if ($tab == 'info'): ?>
            <h2>Ho so ca nhan</h2>
            <form action="capnhatinfo.php" method="POST">
                <div class="form-group">
                    <label>Ho va ten</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($user['FullName']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email (khong the thay doi)</label>
                    <input type="email" value="<?= htmlspecialchars($user['Email']) ?>" disabled style="background:#f5f5f5;">
                </div>
                <div class="form-group">
                    <label>So dien thoai</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['Phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Ngay sinh</label>
                    <input type="date" name="birthdate" value="<?= htmlspecialchars($user['BirthDate'] ?? '') ?>">
                </div>
                <button type="submit" class="btn-save">Luu thay doi</button>
            </form>

        <?php elseif ($tab == 'address'): ?>
            <h2>So dia chi</h2>
            <div class="add-address-form">
                <h4>Them dia chi moi</h4>
                <form action="xulydiachi.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="redirect" value="<?= isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'profile' ?>">
                    <div class="addr-row">
                        <div class="form-group">
                            <label>Ten nguoi nhan *</label>
                            <input type="text" name="receiver_name" placeholder="Ten nguoi nhan" required>
                        </div>
                        <div class="form-group">
                            <label>So dien thoai *</label>
                            <input type="text" name="receiver_phone" placeholder="So dien thoai" required>
                        </div>
                    </div>

                    <!-- ✅ Dropdown 3 cap rieng biet -->
                    <div class="addr-row">
                        <div class="form-group">
                            <label>Tinh/TP *</label>
                            <select id="tinh-addr" name="tinh" required onchange="loadQuanAddr()">
                                <option value="">-- Chon Tinh/TP --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quan/Huyen *</label>
                            <select id="quan-addr" name="quan" required onchange="loadPhuongAddr()">
                                <option value="">-- Chon Quan/Huyen --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Phuong/Xa *</label>
                            <select id="phuong-addr" name="phuong" required>
                                <option value="">-- Chon Phuong/Xa --</option>
                            </select>
                        </div>
                    </div>

                    <div class="addr-row">
                        <div class="form-group" style="flex:100%;">
                            <label>So nha, ten duong *</label>
                            <input type="text" name="detail_address" placeholder="So nha, ten duong, hem..." required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="is_default" value="1"> Dat lam dia chi mac dinh</label>
                    </div>
                    <button type="submit" class="btn-save">Them dia chi</button>
                </form>
            </div>

            <?php if (empty($addresses)): ?>
                <p style="color:#999;">Ban chua luu dia chi nao.</p>
            <?php else: ?>
                <?php foreach ($addresses as $addr): ?>
                <div class="address-card">
                    <p><strong><?= htmlspecialchars($addr['ReceiverName']) ?></strong>
                       | <?= htmlspecialchars($addr['ReceiverPhone']) ?></p>
                    <!-- ✅ Hien thi day du tung truong -->
                    <p><?= htmlspecialchars($addr['DiaChiDay']) ?>,
                       <?= htmlspecialchars($addr['PhuongXa']) ?>,
                       <?= htmlspecialchars($addr['QuanHuyen']) ?>,
                       <?= htmlspecialchars($addr['TinhTP']) ?></p>
                    <?php if ($addr['IsDefault'] == 1): ?>
                        <span class="badge-default">Mac dinh</span>
                    <?php endif; ?>
                    <form action="xulydiachi.php" method="POST"
                          id="form-xoa-addr-<?= $addr['AddressID'] ?>">
                        <input type="hidden" name="action"     value="delete">
                        <input type="hidden" name="address_id" value="<?= $addr['AddressID'] ?>">
                        <button type="button" class="btn-xoa-addr"
                                onclick="confirmXoaAddr(<?= $addr['AddressID'] ?>)">Xoa</button>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php elseif ($tab == 'orders'): ?>
            <h2>Don dat hang</h2>
            <?php if (empty($orders)): ?>
                <p style="color:#999;">Ban chua co don hang nao.</p>
            <?php else: ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Ngay dat</th>
                            <th>Tong tien</th>
                            <th>Trang thai</th>
                            <th>Chi tiet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= $o['NgayDat'] ? date('d/m/Y H:i', strtotime($o['NgayDat'])) : '---' ?></td>
                            <td><?= number_format($o['TongTien'],0,',','.') ?> d</td>
                            <td>
                                <?php
                                $c = (mb_strtolower(trim($o['TrangThai'])) === 'da huy') ? '#e74c3c' : '#f39c12';
                                ?>
                                <span class="tt-tag" style="background:<?= $c ?>;">
                                    <?= htmlspecialchars($o['TrangThai']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="chitietdonhang.php?id=<?= $o['OrderID'] ?>" class="btn-xemct">
                                    Xem chi tiet
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

<script>
const API = 'https://provinces.open-api.vn/api';

// Load tinh cho form them dia chi
window.addEventListener('load', async () => {
    try {
        const res  = await fetch(`${API}/p/`);
        const data = await res.json();
        const sel  = document.getElementById('tinh-addr');
        if (!sel) return;
        data.forEach(t => {
            const opt = document.createElement('option');
            opt.value        = t.name;
            opt.dataset.code = t.code;
            opt.textContent  = t.name;
            sel.appendChild(opt);
        });
    } catch(e) {}
});

async function loadQuanAddr() {
    const tinhSel   = document.getElementById('tinh-addr');
    const quanSel   = document.getElementById('quan-addr');
    const phuongSel = document.getElementById('phuong-addr');
    quanSel.innerHTML   = '<option value="">-- Chon Quan/Huyen --</option>';
    phuongSel.innerHTML = '<option value="">-- Chon Phuong/Xa --</option>';
    const code = tinhSel.selectedOptions[0]?.dataset.code;
    if (!code) return;
    try {
        const res  = await fetch(`${API}/p/${code}?depth=2`);
        const data = await res.json();
        data.districts.forEach(q => {
            const opt = document.createElement('option');
            opt.value        = q.name;
            opt.dataset.code = q.code;
            opt.textContent  = q.name;
            quanSel.appendChild(opt);
        });
    } catch(e) {}
}

async function loadPhuongAddr() {
    const quanSel   = document.getElementById('quan-addr');
    const phuongSel = document.getElementById('phuong-addr');
    phuongSel.innerHTML = '<option value="">-- Chon Phuong/Xa --</option>';
    const code = quanSel.selectedOptions[0]?.dataset.code;
    if (!code) return;
    try {
        const res  = await fetch(`${API}/d/${code}?depth=2`);
        const data = await res.json();
        data.wards.forEach(p => {
            const opt = document.createElement('option');
            opt.value       = p.name;
            opt.textContent = p.name;
            phuongSel.appendChild(opt);
        });
    } catch(e) {}
}

// Thong bao sau cap nhat thong tin
<?php if (!empty($_GET['msg'])): ?>
window.addEventListener('load', () => {
    <?php if ($_GET['msg'] === 'ok'): ?>
        showPopup('Cap nhat thong tin thanh cong!', 'success');
    <?php else: ?>
        showPopup('Co loi xay ra, vui long thu lai!', 'error');
    <?php endif; ?>
});
<?php endif; ?>

// Xac nhan xoa dia chi
function confirmXoaAddr(id) {
    showPopup('Xoa dia chi nay?', 'warning');
    const closeBtn = document.getElementById('popup-close');
    closeBtn.style.display = 'none';
    const old = document.getElementById('popup-confirm-group');
    if (old) old.remove();
    const g = document.createElement('div');
    g.id = 'popup-confirm-group';
    g.style.cssText = 'display:flex;gap:10px;justify-content:center;margin-top:5px;';
    g.innerHTML = `
        <button id="popup-yes" style="background:#e74c3c;color:white;border:none;padding:10px 25px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;">Xac nhan</button>
        <button id="popup-no"  style="background:#eee;color:#555;border:none;padding:10px 25px;border-radius:6px;font-size:14px;cursor:pointer;">Huy</button>
    `;
    closeBtn.parentNode.insertBefore(g, closeBtn);
    document.getElementById('popup-yes').onclick = () => {
        closePopup();
        setTimeout(() => document.getElementById('form-xoa-addr-' + id).submit(), 300);
    };
    document.getElementById('popup-no').onclick = () => {
        closePopup();
        setTimeout(() => { g.remove(); closeBtn.style.display = 'block'; }, 300);
    };
}
</script>

</body>
</html>