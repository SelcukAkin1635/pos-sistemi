<?php
$host = "sql109.infinityfree.com";   // MySQL Hostname
$user = "if0_40010650";              // MySQL Username
$pass = "En8dtfyATDsp7";      // MySQL Password (gizlediğin kısım)
$db   = "if0_40010650_pos_sistemi";          // MySQL Database Name (XXX yerine gerçek db ismini yaz)

$conn = new mysqli($host, $user, $pass, $db, 3306); // 3306 port
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
// ✅ PHP tarafında saat dilimini ayarla
date_default_timezone_set('Europe/Istanbul');

// ✅ MySQL session için saat dilimini zorla
$conn->query("SET time_zone = '+03:00'");
?>