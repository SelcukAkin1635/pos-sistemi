<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = strtolower(trim($_POST['username']));
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Rol bazlı session ismi
        if ($row['role'] === 'garson') {
            session_name("garson_session");
        } elseif ($row['role'] === 'kasiyer') {
            session_name("kasiyer_session");
        } elseif ($row['role'] === 'admin') {
            session_name("admin_session");
        } elseif ($row['role'] === 'mutfak') {
            session_name("mutfak_session");
        } else {
            header("Location: index.php?error=Bilinmeyen rol!");
            exit;
        }

        session_start();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];

        // Rolüne göre yönlendir
        switch ($row['role']) {
            case 'garson':
                header("Location: masalar.php");
                break;
            case 'kasiyer':
                header("Location: kasiyer.php");
                break;
            case 'admin':
                header("Location: admin.php");
                break;
            case 'mutfak':
                header("Location: mutfak.php");
                break;
        }
        exit;
    } else {
        header("Location: index.php?error=Hatalı giriş!");
        exit;
    }
}