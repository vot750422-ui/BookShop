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

    $stmtItems = $conn->prepare(
        "SELECT od.*, b.Title, b.ImageURL
         FROM orderdetails od
         JOIN books b ON od.BookID = b.BookID
         WHERE od.OrderID = ?"
    );
    $stmtItems->execute([$orderID]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    // Lay so dia chi cua user
    $stmtAddr = $conn->prepare(
        "SELECT * FROM useraddresses WHERE UserID = ? ORDER BY IsDefault DESC, AddressID DESC"
    );
    $stmtAddr->execute([$userID]);
    $addresses = $stmtAddr->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Loi: " . $e->getMessage());
}

$trangThai = $order['TrangThai'] ?? '';
$daHuy     = (mb_strtolower(trim($trangThai)) === 'da huy');

// Lay dia chi mac dinh tu so dia chi
$defaultAddr = null;
foreach ($addresses as $addr) {
    if ($addr['IsDefault'] == 1) { $defaultAddr = $addr; break; }
}
// Neu chua co mac dinh thi lay cai dau tien
if (!$defaultAddr && !empty($addresses)) {
    $defaultAddr = $addresses[0];
}

// ✅ Neu don hang chua co thong tin dia chi thi lay tu so dia chi mac dinh
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
    <title>Chi tiet don hang #<?= $orderID ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .order-detail-wrapper { max-width:850px; margin:40px auto; padding:0 20px; }
        .order-detail-box { background:white; border-radius:10px; padding:30px 35px; box-shadow:0 2px 15px rgba(0,0,0,0.1); margin-bottom:20px; }
        .detail-header { display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #c9a96e; padding-bottom:15px; margin-bottom:25px; }
        .detail-header h2 { color:#2c1a0e; margin:0; font-size:20px; }
        .tt-tag { padding:5px 14px; border-radius:20px; font-size:13px; font-weight:600; color:white; }
        .section-title { color:#2c1a0e; font-size:15px; margin:0 0 15px; border-left:4px solid #c9a96e; padding-left:10px; }

        /* Bang san pham */
        .item-table { width:100%; border-collapse:collapse; }
        .item-table th { background:#2c1a0e; color:#f0e6d3; padding:11px 12px; text-align:left; font-size:13px; }
        .item-table td { padding:11px 12px; border-bottom:1px solid #f0e6d3; font-size:14px; vertical-align:middle; }
        .item-table tr:last-child td { border-bottom:none; }
        .item-table img { width:45px; height:55px; object-fit:cover; border-radius:4px; }
        .tong-row { text-align:right; font-size:18px; font-weight:bold; color:#c9a96e; margin-top:12px; }

        /* Form */
        .edit-section { background:#fdf6ec; border-radius:8px; padding:20px 25px; margin-bottom:20px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px; }
        .form-row.one { grid-template-columns:1fr; }
        .form-group { display:flex; flex-direction:column; gap:5px; }
        .form-group label { font-size:13px; font-weight:600; color:#2c1a0e; }
        .form-group input,
        .form-group textarea {
            padding:9px 12px; border:1px solid #ddc9a3; border-radius:5px;
            font-size:14px; outline:none; transition:border 0.2s;
            font-family:Arial,sans-serif; background:white;
        }
        .form-group input:focus,
        .form-group textarea:focus { border-color:#c9a96e; }
        .form-group input:disabled,
        .form-group textarea:disabled { background:#f0f0f0; color:#999; cursor:not-allowed; }
        .form-group textarea { resize:vertical; min-height:70px; }

        /* Dropdown so dia chi */
        .addr-dropdown-wrapper { position:relative; margin-bottom:15px; }
        .addr-dropdown-btn {
            width:100%; padding:10px 14px; background:white; border:1px solid #c9a96e;
            border-radius:6px; text-align:left; cursor:pointer; font-size:14px;
            color:#2c1a0e; font-weight:600; display:flex;
            justify-content:space-between; align-items:center; box-sizing:border-box;
            transition:background 0.2s;
        }
        .addr-dropdown-btn:hover { background:#fdf6ec; }
        .arrow { font-size:12px; transition:transform 0.2s; }
        .addr-dropdown-btn.open .arrow { transform:rotate(180deg); }
        .addr-list {
            display:none; position:absolute; top:calc(100% + 4px); left:0; width:100%;
            background:white; border:1px solid #ddc9a3; border-radius:6px;
            box-shadow:0 4px 15px rgba(0,0,0,0.1); z-index:100; max-height:220px; overflow-y:auto;
        }
        .addr-list.open { display:block; }
        .addr-item { padding:11px 14px; border-bottom:1px solid #f0e6d3; cursor:pointer; transition:background 0.2s; font-size:13px; }
        .addr-item:hover { background:#fdf6ec; }
        .addr-item strong { color:#2c1a0e; font-size:13px; display:block; }
        .addr-item span   { color:#888; font-size:12px; margin-top:2px; display:block; }
        .badge-default { display:inline-block; background:#c9a96e; color:white; font-size:10px; padding:1px 6px; border-radius:8px; margin-left:5px; }
        .addr-them { padding:11px 14px; color:#c9a96e; font-weight:600; font-size:13px; display:block; text-decoration:none; transition:background 0.2s; }
        .addr-them:hover { background:#fdf6ec; }

        /* Dia chi hien tai */
        .current-addr-box { background:#fdf6ec; border:1px solid #e8d9c0; border-radius:6px; padding:10px 14px; margin-bottom:12px; font-size:13px; color:#555; }
        .current-addr-box strong { color:#2c1a0e; }

        /* Thong bao da huy */
        .notice-huy { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:12px 18px; border-radius:6px; margin-bottom:20px; font-size:14px; text-align:center; font-weight:600; }

        /* Nut */
        .btn-group { display:flex; gap:12px; justify-content:flex-end; margin-top:20px; flex-wrap:wrap; }
        .btn-update { background:#2c1a0e; color:#f0e6d3; border:none; padding:12px 28px; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer; transition:background 0.2s; }
        .btn-update:hover { background:#c9a96e; }
        .btn-huy { background:white; color:#e74c3c; border:2px solid #e74c3c; padding:12px 28px; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer; transition:all 0.2s; }
        .btn-huy:hover { background:#e74c3c; color:white; }
        .btn-back { display:inline-block; background:white; color:#2c1a0e; border:2px solid #c9a96e; padding:12px 22px; border-radius:6px; font-size:14px; font-weight:600; text-decoration:none; transition:all 0.2s; }
        .btn-back:hover { background:#f0e6d3; }

        @media (max-width:768px) {
            .form-row { grid-template-columns:1fr; }
            .order-detail-box { padding:20px; }
        }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="order-detail-wrapper">

    <!-- San pham -->
    <div class="order-detail-box">
        <div class="detail-header">
            <h2>Don hang #<?= $orderID ?></h2>
            <span class="tt-tag" style="background:<?= $daHuy ? '#e74c3c' : '#f39c12' ?>;">
                <?= htmlspecialchars($trangThai) ?>
            </span>
        </div>

        <h3 class="section-title">San pham da dat</h3>
        <table class="item-table">
            <thead>
                <tr>
                    <th>San pham</th><th>Don gia</th>
                    <th>So luong</th><th style="text-align:right;">Thanh tien</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="assets/images/<?= htmlspecialchars($item['ImageURL'] ?? 'book-default.jpg') ?>"
                                 onerror="this.src='assets/images/book-default.jpg'">
                            <?= htmlspecialchars($item['Title']) ?>
                        </div>
                    </td>
                    <td><?= number_format($item['DonGia'],0,',','.') ?> d</td>
                    <td><?= $item['SoLuong'] ?></td>
                    <td style="text-align:right;"><?= number_format($item['DonGia']*$item['SoLuong'],0,',','.') ?> d</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="tong-row">Tong cong: <?= number_format($order['TongTien'],0,',','.') ?> VND</div>
    </div>

    <!-- Thong tin giao hang -->
    <div class="order-detail-box">
        <h3 class="section-title" style="font-size:17px;margin-bottom:20px;">Thong tin giao hang</h3>

        <?php if ($daHuy): ?>
            <div class="notice-huy">Don hang nay da bi huy. Khong the chinh sua.</div>
        <?php endif; ?>

        <form id="form-capnhat" action="xulycapnhatdonhang.php" method="POST"
              onsubmit="return <?= $daHuy ? 'khongChoCapNhat()' : 'validateForm()' ?>">
            <input type="hidden" name="orderID" value="<?= $orderID ?>">
            <input type="hidden" name="action"  value="capnhat">

            <div class="edit-section">
                <h3 class="section-title">Dia chi giao hang</h3>

                <?php if (!$daHuy && !empty($addresses)): ?>
                <!-- ✅ Dropdown chon so dia chi -->
                <div class="addr-dropdown-wrapper">
                    <button type="button" class="addr-dropdown-btn" id="addrDropBtn">
                        <span id="addrDropLabel">
                            <?= htmlspecialchars(
                                $displayHoTen . ' - ' .
                                $displayDiaChi . ', ' .
                                $displayPhuong . ', ' .
                                $displayQuan   . ', ' .
                                $displayTinh
                            ) ?>
                        </span>
                        <span class="arrow">&#9660;</span>
                    </button>
                    <div class="addr-list" id="addrList">
                        <?php foreach ($addresses as $addr): ?>
                        <div class="addr-item"
                             data-name="<?=   htmlspecialchars($addr['ReceiverName'])  ?>"
                             data-phone="<?=  htmlspecialchars($addr['ReceiverPhone']) ?>"
                             data-diachi="<?= htmlspecialchars($addr['DiaChiDay'])     ?>"
                             data-phuong="<?= htmlspecialchars($addr['PhuongXa'])      ?>"
                             data-quan="<?=   htmlspecialchars($addr['QuanHuyen'])     ?>"
                             data-tinh="<?=   htmlspecialchars($addr['TinhTP'])        ?>"
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
                        <a class="addr-them" href="profile.php?tab=address&redirect=chitietdonhang&orderID=<?= $orderID ?>">
                            + Them dia chi moi
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Input hidden dia chi -->
                <input type="hidden" name="HoTen"     id="HoTen"     value="<?= htmlspecialchars($displayHoTen)  ?>">
                <input type="hidden" name="Phone"     id="Phone"     value="<?= htmlspecialchars($displayPhone)  ?>">
                <input type="hidden" name="DiaChiDay" id="DiaChiDay" value="<?= htmlspecialchars($displayDiaChi) ?>">
                <input type="hidden" name="PhuongXa"  id="PhuongXa"  value="<?= htmlspecialchars($displayPhuong) ?>">
                <input type="hidden" name="QuanHuyen" id="QuanHuyen" value="<?= htmlspecialchars($displayQuan)   ?>">
                <input type="hidden" name="TinhTP"    id="TinhTP"    value="<?= htmlspecialchars($displayTinh)   ?>">

                <!-- Hien thi dia chi hien tai -->
                <div class="current-addr-box" id="current-addr-box">
                    <strong><?= htmlspecialchars($displayHoTen) ?></strong>
                    | <?= htmlspecialchars($displayPhone) ?><br>
                    <?= htmlspecialchars($displayDiaChi) ?>,
                    <?= htmlspecialchars($displayPhuong) ?>,
                    <?= htmlspecialchars($displayQuan)   ?>,
                    <?= htmlspecialchars($displayTinh)   ?>
                </div>
            </div>

            <!-- ✅ Email va Ghi chu: hien thi tu dong, cho chinh sua -->
            <div class="edit-section">
                <h3 class="section-title">Thong tin them</h3>
                <div class="form-row one">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="Email"
                               value="<?= htmlspecialchars($order['Email'] ?? '') ?>"
                               placeholder="Email (khong bat buoc)"
                               <?= $daHuy ? 'disabled' : '' ?>>
                    </div>
                </div>
                <div class="form-row one">
                    <div class="form-group">
                        <label>Ghi chu</label>
                        <textarea name="GhiChu"
                                  placeholder="Ghi chu them..."
                                  <?= $daHuy ? 'disabled' : '' ?>><?= htmlspecialchars($order['GhiChu'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <a href="profile.php?tab=orders" class="btn-back">Quay lai</a>
                <?php if (!$daHuy): ?>
                    <button type="button" class="btn-huy" onclick="confirmHuyDon()">Huy don hang</button>
                    <button type="submit" class="btn-update">Cap nhat don hang</button>
                <?php endif; ?>
            </div>
        </form>

        <form id="form-huy" action="xulycapnhatdonhang.php" method="POST" style="display:none;">
            <input type="hidden" name="orderID" value="<?= $orderID ?>">
            <input type="hidden" name="action"  value="huy">
        </form>
    </div>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>

<script>
const addrDropBtn = document.getElementById('addrDropBtn');
const addrList    = document.getElementById('addrList');
const daHuy       = <?= $daHuy ? 'true' : 'false' ?>;

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

// Chon dia chi tu so → cap nhat input hidden + hien thi box
function chonDiaChi(el) {
    document.getElementById('HoTen').value     = el.dataset.name;
    document.getElementById('Phone').value     = el.dataset.phone;
    document.getElementById('DiaChiDay').value = el.dataset.diachi;
    document.getElementById('PhuongXa').value  = el.dataset.phuong;
    document.getElementById('QuanHuyen').value = el.dataset.quan;
    document.getElementById('TinhTP').value    = el.dataset.tinh;

    document.getElementById('addrDropLabel').textContent =
        el.dataset.name + ' - ' + el.dataset.diachi + ', ' +
        el.dataset.phuong + ', ' + el.dataset.quan + ', ' + el.dataset.tinh;

    document.getElementById('current-addr-box').innerHTML =
        '<strong>' + el.dataset.name + '</strong> | ' + el.dataset.phone + '<br>' +
        el.dataset.diachi + ', ' + el.dataset.phuong + ', ' + el.dataset.quan + ', ' + el.dataset.tinh;

    addrDropBtn.classList.remove('open');
    addrList.classList.remove('open');
}

function khongChoCapNhat() {
    showPopup('Don hang nay da bi huy, khong the cap nhat!', 'error');
    return false;
}

function validateForm() {
    if (!document.getElementById('HoTen').value.trim()) {
        showPopup('Vui long chon dia chi giao hang!', 'error');
        return false;
    }
    return true;
}

function confirmHuyDon() {
    showPopup('Ban co chac chan muon huy don hang nay khong?', 'warning');
    const closeBtn = document.getElementById('popup-close');
    closeBtn.style.display = 'none';
    const old = document.getElementById('popup-confirm-group');
    if (old) old.remove();
    const g = document.createElement('div');
    g.id = 'popup-confirm-group';
    g.style.cssText = 'display:flex;gap:10px;justify-content:center;margin-top:5px;';
    g.innerHTML = `
        <button id="popup-yes" style="background:#e74c3c;color:white;border:none;padding:10px 25px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;">Co, huy don</button>
        <button id="popup-no"  style="background:#eee;color:#555;border:none;padding:10px 25px;border-radius:6px;font-size:14px;cursor:pointer;">Khong</button>
    `;
    closeBtn.parentNode.insertBefore(g, closeBtn);
    document.getElementById('popup-yes').onclick = () => {
        closePopup();
        setTimeout(() => document.getElementById('form-huy').submit(), 300);
    };
    document.getElementById('popup-no').onclick = () => {
        closePopup();
        setTimeout(() => { g.remove(); closeBtn.style.display = 'block'; }, 300);
    };
}

window.addEventListener('load', () => {
    <?php if (!empty($_GET['updated'])): ?>
        showPopup('Cap nhat don hang thanh cong!', 'success');
    <?php endif; ?>
    <?php if (!empty($_GET['huyed'])): ?>
        showPopup('Don hang da duoc huy thanh cong!', 'success', () => {
            window.location.href = 'profile.php?tab=orders';
        });
    <?php endif; ?>
});
</script>

</body>
</html>