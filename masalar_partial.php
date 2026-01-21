<?php
session_name("kasiyer_session");
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kasiyer') {
    exit("Yetkisiz erişim");
}
include "db.php";

$masalar = $conn->query("
    SELECT m.*, a.onayli 
    FROM masalar m
    LEFT JOIN adisyonlar a ON a.masa_id=m.id AND a.kapali=0
");

while($masa = $masalar->fetch_assoc()) {
    $cssClass = "bos";
    if ($masa['durum'] == 1) {
        if ($masa['onayli'] == 1) {
            $cssClass = "dolu"; // kırmızı
        } else {
            $cssClass = "bekliyor"; // sarı
        }
    }
    echo '<div class="masa '.$cssClass.'" onclick="loadOrder('.$masa['id'].')">Masa '.$masa['masa_no'].'</div>';
}
?>