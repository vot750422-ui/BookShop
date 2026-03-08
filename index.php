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
        .book-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            background: #fff;
        }
        .book-card img { width: 100%; height: 200px; object-fit: cover; }
    </style>
</head>
<body>

<div id="navbar"></div>

<main class="main-content">
    <h1>SÁCH MỚI NHẤT</h1>
    <div class="book-grid">
        <?php
        try {
            $stmt = $conn->query("SELECT * FROM Books");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "
                <div class='book-card'>
                    <img src='assets/images/book-default.jpg' alt='book'>
                    <h3>{$row['Title']}</h3>
                    <p>{$row['Author']}</p>
                    <p style='color: orangered; font-weight: bold;'>" . number_format($row['Price'], 0, ',', '.') . " đ</p>
                    <button style='background: #00bcd4; color: white; border: none; padding: 10px; cursor: pointer; width: 100%;'>Thêm vào giỏ</button>
                </div>";
            }
        } catch (Exception $e) {
            echo "<p>Đang cập nhật dữ liệu sách...</p>";
        }
        ?>
    </div>
</main>

<div id="footer"></div>

<script>
fetch("components/navbar.php") 
.then(res => res.text())
.then(data => {
    document.getElementById("navbar").innerHTML = data;
});

fetch("components/footer.html")
.then(res => res.text())
.then(data => {
    document.getElementById("footer").innerHTML = data;
});
</script>
</body>
</html>