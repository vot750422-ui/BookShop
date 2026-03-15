<?php
session_start();
require_once 'Config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        /* ===== BOOK CARD ===== */
        .book-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            background: #fff;
            position: relative;
            overflow: hidden;
            transition: box-shadow 0.3s, transform 0.3s;
        }

        .book-card:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
            transform: translateY(-4px);
        }

        .book-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }

        .book-card h3 {
            font-size: 13px;
            margin: 10px 0 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: #2c1a0e;
        }

        .book-card .author {
            font-size: 12px;
            color: #888;
            margin-bottom: 5px;
        }

        .book-card .price {
            color: #c9a96e;
            font-weight: bold;
            font-size: 14px;
        }

        /* ===== OVERLAY KHI HOVER ===== */
        .book-overlay {
            position: absolute;
            bottom: -50px;        /* Ẩn dưới card */
            left: 0;
            width: 100%;
            background: #2c1a0e;
            color: #f0e6d3;
            text-align: center;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            transition: bottom 0.3s ease;
            text-decoration: none;
            display: block;
        }

        .book-overlay:hover {
            background: #c9a96e;
            color: white;
        }

        /* Khi hover card → overlay trượt lên */
        .book-card:hover .book-overlay {
            bottom: 0;
        }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>
<?php include 'components/breadcrumb.php'; ?>

<main class="main-content">

    <?php
    $theLoaiMap = [
        'tieu-thuyet'     => 'Tiểu Thuyết',
        'truyen-ngan'     => 'Truyện Ngắn',
        'co-dien'         => 'Văn Học Cổ Điển',
        'kinh-di'         => 'Kinh Dị',
        'tam-ly-toi-pham' => 'Tâm Lý Học Tội Phạm',
        'ky-nang-song'    => 'Kỹ Năng Sống',
        'but-bi'          => 'Bút Bi',
        'but-chi'         => 'Bút Chì',
        'but-da-quang'    => 'Bút Dạ Quang',
        'vo-o-ly'         => 'Vở Ô Li',
        'so-tay'          => 'Sổ Tay',
        'giay-note'       => 'Giấy Note',
    ];

    $theLoai    = $_GET['theloai'] ?? '';
    $tenHienThi = $theLoaiMap[$theLoai] ?? '';

    echo $tenHienThi
        ? "<h1>" . htmlspecialchars(strtoupper($tenHienThi)) . "</h1>"
        : "<h1>SÁCH MỚI NHẤT</h1>";
    ?>

    <div class="book-grid">
        <?php
        try {
            if (!empty($theLoai)) {
                $stmt = $conn->prepare("SELECT * FROM Books WHERE TheLoai = ?");
                $stmt->execute([$theLoai]);
            } else {
                $stmt = $conn->query("SELECT * FROM Books");
            }

            $count = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $count++;
                $imgSrc = 'assets/images/' . ($row['ImageURL'] ?? 'book-default.jpg');
                echo "
                <div class='book-card'>
                    <img src='{$imgSrc}'
                         alt='" . htmlspecialchars($row['Title']) . "'
                         onerror=\"this.src='assets/images/book-default.jpg'\">
                    <h3>" . htmlspecialchars($row['Title']) . "</h3>
                    <p class='author'>" . htmlspecialchars($row['Author']) . "</p>
                    <p class='price'>" . number_format($row['Price'], 0, ',', '.') . " đ</p>

                    <!-- Overlay trượt lên khi hover -->
                    <a href='chitietsach.php?id={$row['BookID']}' class='book-overlay'>
                         Xem chi tiết sách
                    </a>
                </div>";
            }

            if ($count === 0) {
                echo "<p>Không có sách nào trong thể loại này.</p>";
            }

        } catch (Exception $e) {
            echo "<p>Đang cập nhật dữ liệu sách...</p>";
        }
        ?>
    </div>
</main>

<?php include 'components/footer.html'; ?>

</body>
</html>