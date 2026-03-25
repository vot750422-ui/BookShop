<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: dangnhap.php");
    exit();
}

require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {


    // ADDbook

    case 'them':
        $title       = trim($_POST['title']       ?? '');
        $author      = trim($_POST['author']      ?? '');
        $theloai     = trim($_POST['theloai']     ?? '');
        $price       = (int)($_POST['price']      ?? 0);
        $stock       = (int)($_POST['stock']      ?? 0);
        $imageurl    = trim($_POST['imageurl']    ?? 'book-default.jpg');
        $description = trim($_POST['description'] ?? '');

        if (empty($title) || empty($author) || $price <= 0) {
            header("Location: admin_sanpham.php?msg=loi");
            exit();
        }

        try {
            $sql  = "INSERT INTO books (Title, Author, TheLoai, Price, Stock, ImageURL, Description)
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$title, $author, $theloai, $price, $stock, $imageurl, $description]);

            header("Location: admin_sanpham.php?msg=them_ok");
            exit();

        } catch (PDOException $e) {
            header("Location: admin_sanpham.php?msg=loi");
            exit();
        }


    // repair book

    case 'sua':
        $bookID      = (int)($_POST['bookID']     ?? 0);
        $title       = trim($_POST['title']       ?? '');
        $author      = trim($_POST['author']      ?? '');
        $theloai     = trim($_POST['theloai']     ?? '');
        $price       = (int)($_POST['price']      ?? 0);
        $stock       = (int)($_POST['stock']      ?? 0);
        $imageurl    = trim($_POST['imageurl']    ?? 'book-default.jpg');
        $description = trim($_POST['description'] ?? '');

        if ($bookID <= 0 || empty($title) || empty($author) || $price <= 0) {
            header("Location: admin_sanpham.php?msg=loi");
            exit();
        }

        try {
            $sql  = "UPDATE books
                     SET Title=?, Author=?, TheLoai=?, Price=?, Stock=?, ImageURL=?, Description=?
                     WHERE BookID=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$title, $author, $theloai, $price, $stock, $imageurl, $description, $bookID]);

            header("Location: admin_sanpham.php?msg=sua_ok");
            exit();

        } catch (PDOException $e) {
            header("Location: admin_sanpham.php?msg=loi");
            exit();
        }


    // delete book

    case 'xoa':
        $bookID = (int)($_GET['bookID'] ?? 0);

        if ($bookID <= 0) {
            header("Location: admin_sanpham.php?msg=loi");
            exit();
        }

        try {
            $stmt = $conn->prepare("DELETE FROM books WHERE BookID = ?");
            $stmt->execute([$bookID]);

            header("Location: admin_sanpham.php?msg=xoa_ok");
            exit();

        } catch (PDOException $e) {
            header("Location: admin_sanpham.php?msg=loi");
            exit();
        }

    default:
        header("Location: admin_sanpham.php");
        exit();
}
?>