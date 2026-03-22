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
    <title>Thong tin tai khoan - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-container {
            max-width: 1000px;
            margin: 40px auto;
            display: flex;
            gap: 30px;
            padding: 0 20px;
        }
        .profile-sidebar {
            flex: 0 0 220px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: fit-content;
        }
        .profile-sidebar h3 {
            color: #2c1a0e;
            margin-bottom: 20px;
            font-size: 16px;
            border-bottom: 2px solid #c9a96e;
            padding-bottom: 10px;
        }
        .profile-menu { list-style: none; padding: 0; }
        .profile-menu li { margin-bottom: 8px; }
        .profile-menu a {
            text-decoration: none;
            color: #555;
            font-size: 14px;
            display: block;
            padding: 8px 10px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .profile-menu a:hover,
        .profile-menu a.active {
            background: #fdf6ec;
            color: #c9a96e;
            font-weight: 600;
        }
        .profile-content {
            flex: 1;
            background: white;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .profile-content h2 {
            margin-bottom: 20px;
            color: #2c1a0e;
            font-size: 20px;
            border-bottom: 2px solid #f0e6d3;
            padding-bottom: 10px;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
            font-size: 13px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #c9a96e;
        }
        .btn-save {
            background: #2c1a0e;
            color: #f0e6d3;
            border: none;
            padding: 10px 22px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-save:hover { background: #c9a96e; }

        /* Bang don hang */
        .order-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .order-table th {
            background: #2c1a0e;
            color: #f0e6d3;
            padding: 11px 12px;
            text-align: left;
            font-size: 13px;
        }
        .order-table td {
            padding: 11px 12px;
            border-bottom: 1px solid #f0e6d3;
            font-size: 14px;
        }
        .order-table tr:hover td { background: #fdf6ec; }

        /* So dia chi */
        .address-card {
            border: 1px solid #e8d9c0;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 12px;
            position: relative;
            background: #fdf6ec;
        }
        .address-card p { margin: 4px 0; font-size: 14px; color: #555; }
        .address-card strong { color: #2c1a0e; }
        .badge-default {
            display: inline-block;
            background: #c9a96e;
            color: white;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-top: 5px;
            font-weight: 600;
        }
        .btn-xoa-addr {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            color: #e74c3c;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
        }
        .btn-xoa-addr:hover { text-decoration: underline; }

        /* Form them dia chi */
        .add-address-form {
            background: white;
            border: 1px solid #e8d9c0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .add-address-form h4 { margin: 0 0 15px; color: #2c1a0e; font-size: 15px; }
        .addr-row { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 12px; }
        .addr-row .form-group { flex: 1; min-width: 150px; margin-bottom: 0; }
        .addr-row .form-group input,
        .addr-row .form-group select {
            width: 100%;
            padding: 9px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="profile-container">
    <!-- Menu ben trai -->
    <div class="profile-sidebar">
        <h3>Tai khoan cua toi</h3>
        <ul class="profile-menu">
            <?php $tab = $_GET['tab'] ?? 'info'; ?>
            <li><a href="?tab=info"    class="<?= $tab=='info'    ? 'active':'' ?>">Ho so ca nhan</a></li>
            <li><a href="?tab=address" class="<?= $tab=='address' ? 'active':'' ?>">So dia chi</a></li>
            <li><a href="?tab=orders"  class="<?= $tab=='orders'  ? 'active':'' ?>">Lich su don hang</a></li>
        </ul>
    </div>

    <!-- Noi dung -->
    <div class="profile-content">

        <?php if ($tab == 'info'): ?>
            <h2>Ho so ca nhan</h2>
            <form action="capnhatinfo.php" method="POST">
                <div class="form-group">
                    <label>Ho va ten</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($user['FullName']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email (Khong the thay doi)</label>
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
                    <div class="addr-row">
                        <div class="form-group">
                            <label>Tinh/Thanh pho *</label>
                            <select id="tinh" name="tinh" required onchange="loadQuan()">
                                <option value="">-- Chon Tinh/TP --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quan/Huyen *</label>
                            <select id="quan" name="quan" required onchange="loadPhuong()">
                                <option value="">-- Chon Quan/Huyen --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Phuong/Xa *</label>
                            <select id="phuong" name="phuong" required>
                                <option value="">-- Chon Phuong/Xa --</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>So nha, ten duong *</label>
                        <input type="text" name="detail_address" placeholder="So nha, ten duong..." required>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_default" value="1">
                            Dat lam dia chi mac dinh
                        </label>
                    </div>
                    <button type="submit" class="btn-save">Them dia chi</button>
                </form>
            </div>

            <?php if (empty($addresses)): ?>
                <p style="color:#999;">Ban chua luu dia chi nao.</p>
            <?php else: ?>
                <?php foreach ($addresses as $addr): ?>
                <div class="address-card">
                    <p><strong><?= htmlspecialchars($addr['ReceiverName']) ?></strong> | <?= htmlspecialchars($addr['ReceiverPhone']) ?></p>
                    <p><?= htmlspecialchars($addr['DetailAddress']) ?></p>
                    <?php if ($addr['IsDefault'] == 1): ?>
                        <span class="badge-default">Mac dinh</span>
                    <?php endif; ?>
                    <form action="xulydiachi.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="address_id" value="<?= $addr['AddressID'] ?>">
                        <button type="button" class="btn-xoa-addr"
                                onclick="confirmXoaAddr('xoa-addr-<?= $addr['AddressID'] ?>')">
                            Xoa
                        </button>
                        <span id="xoa-addr-<?= $addr['AddressID'] ?>" style="display:none;"></span>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php elseif ($tab == 'orders'): ?>
            <h2>Lich su don hang</h2>
            <?php if (empty($orders)): ?>
                <p style="color:#999;">Ban chua co don hang nao.</p>
            <?php else: ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Ma DH</th>
                            <th>Ngay dat</th>
                            <th>Tong tien</th>
                            <th>Trang thai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>
                                <a href="chitietdonhang.php?id=<?= $o['OrderID'] ?>"
                                   style="color:#c9a96e; font-weight:bold; text-decoration:none;">
                                    #<?= $o['OrderID'] ?>
                                </a>
                            </td>
                            <td><?= $o['NgayDat'] ? date('d/m/Y H:i', strtotime($o['NgayDat'])) : '---' ?></td>
                            <td><?= number_format($o['TongTien'], 0, ',', '.') ?> d</td>
                            <td><strong><?= htmlspecialchars($o['TrangThai']) ?></strong></td>
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

<script src="assets/js/address.js"></script>

<script>
// Popup thong bao sau capnhatinfo
<?php if (!empty($_GET['msg'])): ?>
    window.addEventListener('load', () => {
        <?php if ($_GET['msg'] === 'ok'): ?>
            showPopup('Cap nhat thong tin thanh cong!', 'success');
        <?php else: ?>
            showPopup('Co loi xay ra, vui long thu lai!', 'error');
        <?php endif; ?>
    });
<?php endif; ?>

// Popup xac nhan xoa dia chi
function confirmXoaAddr(spanId) {
    showPopup('Xoa dia chi nay?', 'warning');

    const closeBtn = document.getElementById('popup-close');
    closeBtn.style.display = 'none';

    const old = document.getElementById('popup-confirm-group');
    if (old) old.remove();

    const btnGroup = document.createElement('div');
    btnGroup.id = 'popup-confirm-group';
    btnGroup.style.cssText = 'display:flex; gap:10px; justify-content:center; margin-top:5px;';
    btnGroup.innerHTML = `
        <button id="popup-yes" style="background:#e74c3c;color:white;border:none;padding:10px 25px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;">Xac nhan</button>
        <button id="popup-no"  style="background:#eee;color:#555;border:none;padding:10px 25px;border-radius:6px;font-size:14px;cursor:pointer;">Huy</button>
    `;
    closeBtn.parentNode.insertBefore(btnGroup, closeBtn);

    document.getElementById('popup-yes').onclick = function () {
        closePopup();
        setTimeout(() => {
            // Tim form chua span co id tuong ung roi submit
            const span = document.getElementById(spanId);
            if (span) span.closest('form').submit();
        }, 300);
    };

    document.getElementById('popup-no').onclick = function () {
        closePopup();
        setTimeout(() => {
            btnGroup.remove();
            closeBtn.style.display = 'block';
        }, 300);
    };
}
</script>

</body>
</html>