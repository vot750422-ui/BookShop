<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $userID    = $_SESSION['user_id'];
    $fullname  = trim($_POST['fullname'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');

    try {
        $stmt = $conn->prepare("UPDATE users SET FullName = ?, Phone = ?, BirthDate = ? WHERE UserID = ?");
        $stmt->execute([$fullname, $phone, $birthdate, $userID]);


        $_SESSION['user_name'] = $fullname;

        header("Location: profile.php?tab=info&msg=ok");
        exit();
    } catch (PDOException $e) {
        header("Location: profile.php?tab=info&msg=error");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}