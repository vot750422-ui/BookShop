<?php
session_start();
require_once 'config.php';

$news = [
    [
        'id'      => 1,
        'title'   => 'Top 10 cuốn sách hay nhất năm 2025',
        'summary' => 'Điểm qua những cuốn sách được độc giả yêu thích và giới phê bình đánh giá cao nhất trong năm 2025, từ văn học trong nước đến các tác phẩm dịch nổi bật.',
        'image'   => 'news1.jpg',
        'date'    => '20/03/2026',
        'tag'     => 'Đề xuất',
        'content' => 'Năm 2025 chứng kiến nhiều tác phẩm văn học xuất sắc ra đời. Từ những cuốn tiểu thuyết lịch sử hào hùng đến những tập truyện ngắn tinh tế, thị trường sách Việt Nam ngày càng phong phú và đa dạng. Dưới đây là danh sách 10 cuốn sách được độc giả yêu thích nhất năm qua...',
    ],
    [
        'id'      => 2,
        'title'   => 'Lợi ích của việc đọc sách mỗi ngày',
        'summary' => 'Nghiên cứu khoa học chứng minh đọc sách 30 phút mỗi ngày giúp cải thiện trí nhớ, giảm stress và tăng khả năng tư duy sáng tạo đáng kể.',
        'image'   => 'news2.jpg',
        'date'    => '15/03/2026',
        'tag'     => 'Kiến thức',
        'content' => 'Đọc sách là một trong những thói quen lành mạnh nhất mà bạn có thể xây dựng. Theo nhiều nghiên cứu từ các trường đại học danh tiếng, chỉ cần dành 30 phút mỗi ngày cho việc đọc sách, bạn có thể cải thiện đáng kể trí nhớ, khả năng tập trung và tư duy phân tích...',
    ],
    [
        'id'      => 3,
        'title'   => 'Xu hướng đọc sách của giới trẻ năm 2025',
        'summary' => 'Giới trẻ ngày càng yêu thích các thể loại sách kỹ năng sống, tâm lý học và khởi nghiệp. Tìm hiểu những xu hướng đọc sách nổi bật trong năm 2025.',
        'image'   => 'news3.jpg',
        'date'    => '10/03/2026',
        'tag'     => 'Xu hướng',
        'content' => 'Thế hệ trẻ ngày nay đang dần thay đổi thói quen đọc sách theo những hướng thú vị. Thay vì chỉ tập trung vào sách giáo khoa hay văn học kinh điển, họ ngày càng tìm đến các thể loại sách thực dụng như kỹ năng sống, tâm lý học ứng dụng và kinh doanh khởi nghiệp...',
    ],
    [
        'id'      => 4,
        'title'   => 'Cách chọn sách phù hợp cho trẻ em',
        'summary' => 'Hướng dẫn phụ huynh cách lựa chọn sách phù hợp với từng độ tuổi, giúp trẻ xây dựng thói quen đọc sách từ nhỏ và phát triển toàn diện.',
        'image'   => 'news4.jpg',
        'date'    => '05/03/2026',
        'tag'     => 'Phụ huynh',
        'content' => 'Việc chọn đúng cuốn sách cho trẻ không chỉ giúp các em giải trí mà còn góp phần quan trọng vào sự phát triển nhận thức và cảm xúc. Phụ huynh cần lưu ý đến độ tuổi, sở thích và khả năng đọc hiểu của từng trẻ khi lựa chọn sách...',
    ],
    [
        'id'      => 5,
        'title'   => 'Văn học cổ điển và sức hút vượt thời gian',
        'summary' => 'Tại sao những tác phẩm văn học cổ điển vẫn được độc giả hiện đại yêu thích? Khám phá vẻ đẹp trường tồn của những kiệt tác văn chương thế giới.',
        'image'   => 'news5.jpg',
        'date'    => '01/03/2026',
        'tag'     => 'Văn học',
        'content' => 'Những tác phẩm văn học cổ điển như Chiến tranh và Hòa bình, Trăm năm cô đơn hay Đại gia Gatsby vẫn tiếp tục thu hút hàng triệu độc giả mỗi năm. Điều gì khiến những cuốn sách được viết cách đây hàng thế kỷ vẫn còn nguyên giá trị trong thế giới hiện đại?...',
    ],
    [
        'id'      => 6,
        'title'   => 'Mẹo đọc sách nhanh mà vẫn hiểu sâu',
        'summary' => 'Chia sẻ các kỹ thuật đọc sách hiệu quả giúp bạn tiếp thu nhiều thông tin hơn trong thời gian ngắn hơn mà không mất đi chiều sâu của nội dung.',
        'image'   => 'news6.jpg',
        'date'    => '25/02/2026',
        'tag'     => 'Kỹ năng',
        'content' => 'Nhiều người than thở rằng họ không có đủ thời gian để đọc sách. Tuy nhiên, với một số kỹ thuật đọc đúng đắn, bạn hoàn toàn có thể đọc nhiều hơn mà vẫn hiểu sâu nội dung. Từ kỹ thuật đọc lướt thông minh đến phương pháp ghi chú hiệu quả...',
    ],
];

$selectedID = (int)($_GET['id'] ?? 0);
$selected   = null;
if ($selectedID > 0) {
    foreach ($news as $n) {
        if ($n['id'] === $selectedID) { $selected = $n; break; }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $selected ? htmlspecialchars($selected['title']) . ' - ' : '' ?>Tin Tức - BookStore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .tintuc-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px 60px;
        }

        /* ===== BREADCRUMB ===== */
        .breadcrumb {
            font-size: 14px;
            color: #888;
            margin-bottom: 30px;
        }
        .breadcrumb a { color: #8b5e3c; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb .separator { margin: 0 8px; }

        /* ===== TRANG DANH SÁCH ===== */
        .tintuc-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .tintuc-header h1 {
            font-size: 32px;
            color: #2c1a0e;
            margin-bottom: 8px;
        }
        .tintuc-header p {
            color: #888;
            font-size: 15px;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .news-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .news-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.13);
        }

        .news-card-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: linear-gradient(135deg, #c9a96e 0%, #2c1a0e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }
        .news-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .news-card-body {
            padding: 18px 20px 20px;
        }

        .news-tag {
            display: inline-block;
            background: #fff3e0;
            color: #c9a96e;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .news-card-body h3 {
            font-size: 16px;
            color: #2c1a0e;
            margin: 0 0 10px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .news-card-body p {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0 0 14px;
        }

        .news-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #aaa;
        }

        .news-meta .read-more {
            color: #c9a96e;
            font-weight: 600;
            font-size: 13px;
        }

        /* ===== TRANG CHI TIẾT ===== */
        .news-detail-wrapper {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 40px;
            align-items: start;
        }

        .news-detail-main {}

        .news-detail-main h1 {
            font-size: 28px;
            color: #2c1a0e;
            line-height: 1.4;
            margin: 0 0 14px;
        }

        .news-detail-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #999;
        }

        .news-detail-img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            border-radius: 10px;
            background: linear-gradient(135deg, #c9a96e, #2c1a0e);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            margin-bottom: 28px;
            overflow: hidden;
        }
        .news-detail-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .news-detail-content {
            font-size: 15px;
            color: #444;
            line-height: 1.8;
        }

        .news-detail-content p {
            margin-bottom: 18px;
        }

        /* Sidebar */
        .news-sidebar h3 {
            font-size: 17px;
            color: #2c1a0e;
            margin: 0 0 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #c9a96e;
        }

        .sidebar-news-item {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
            text-decoration: none;
            color: inherit;
        }
        .sidebar-news-item:hover .sidebar-news-title { color: #c9a96e; }

        .sidebar-news-thumb {
            width: 70px;
            height: 60px;
            border-radius: 6px;
            background: linear-gradient(135deg, #c9a96e, #2c1a0e);
            flex-shrink: 0;
            overflow: hidden;
        }
        .sidebar-news-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar-news-title {
            font-size: 13px;
            color: #2c1a0e;
            line-height: 1.4;
            font-weight: 600;
            transition: color 0.2s;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .sidebar-news-date {
            font-size: 11px;
            color: #aaa;
            margin-top: 4px;
        }

        @media (max-width: 900px) {
            .news-grid { grid-template-columns: repeat(2, 1fr); }
            .news-detail-wrapper { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .news-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="tintuc-wrapper">
    <div class="breadcrumb">
        <a href="index.php">Trang Chủ</a>
        <span class="separator">›</span>
        <?php if ($selected): ?>
            <a href="tintuc.php">Tin Tức</a>
            <span class="separator">›</span>
            <span class="breadcrumb-current"><?= htmlspecialchars($selected['title']) ?></span>
        <?php else: ?>
            <span class="breadcrumb-current">Tin Tức</span>
        <?php endif; ?>
    </div>

    <?php if ($selected): ?>
        <!-- ===== CHI TIẾT TIN TỨC ===== -->
        <div class="news-detail-wrapper">
            <div class="news-detail-main">
                <span class="news-tag"><?= htmlspecialchars($selected['tag']) ?></span>
                <h1><?= htmlspecialchars($selected['title']) ?></h1>
                <div class="news-detail-meta">
                    <span>📅 <?= $selected['date'] ?></span>
                    <span>✍️ BookStore</span>
                </div>
                <div class="news-detail-img">
                    <img src="assets/images/<?= htmlspecialchars($selected['image']) ?>"
                         onerror="this.style.display='none'">
                </div>
                <div class="news-detail-content">
                    <p><?= htmlspecialchars($selected['summary']) ?></p>
                    <p><?= htmlspecialchars($selected['content']) ?></p>
                    <p>Hãy ghé thăm BookStore để khám phá thêm nhiều đầu sách hay và cập nhật những tin tức mới nhất về thế giới sách. Chúng tôi luôn sẵn sàng đồng hành cùng bạn trên hành trình đọc sách.</p>
                </div>
            </div>

            <div class="news-sidebar">
                <h3>Tin tức khác</h3>
                <?php foreach ($news as $n): ?>
                    <?php if ($n['id'] === $selected['id']) continue; ?>
                    <a href="tintuc.php?id=<?= $n['id'] ?>" class="sidebar-news-item">
                        <div class="sidebar-news-thumb">
                            <img src="assets/images/<?= htmlspecialchars($n['image']) ?>"
                                 onerror="this.style.display='none'">
                        </div>
                        <div>
                            <div class="sidebar-news-title"><?= htmlspecialchars($n['title']) ?></div>
                            <div class="sidebar-news-date"><?= $n['date'] ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- ===== DANH SÁCH TIN TỨC ===== -->
        <div class="tintuc-header">
            <h1>Tin Tức & Góc Đọc Sách</h1>
            <p>Cập nhật những thông tin mới nhất về sách và văn hóa đọc</p>
        </div>

        <div class="news-grid">
            <?php foreach ($news as $n): ?>
            <a href="tintuc.php?id=<?= $n['id'] ?>" class="news-card">
                <div class="news-card-img">
                    <img src="assets/images/<?= htmlspecialchars($n['image']) ?>"
                         onerror="this.style.display='none'">
                </div>
                <div class="news-card-body">
                    <span class="news-tag"><?= htmlspecialchars($n['tag']) ?></span>
                    <h3><?= htmlspecialchars($n['title']) ?></h3>
                    <p><?= htmlspecialchars($n['summary']) ?></p>
                    <div class="news-meta">
                        <span><?= $n['date'] ?></span>
                        <span class="read-more">Đọc tiếp →</span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'components/footer.html'; ?>
<?php include 'components/alertpopup.php'; ?>
</body>
</html>
