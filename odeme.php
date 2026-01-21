<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'kasiyer') {
    header("Location: index.php");
    exit;
}

$masa_id = intval($_GET['masa']);
$adisyon = $conn->query("SELECT * FROM adisyonlar WHERE masa_id=$masa_id AND kapali=0")->fetch_assoc();
$adisyon_id = $adisyon['id'];

$detay = $conn->query("SELECT adisyon_detay.*, urunler.urun_adi, urunler.fiyat 
                       FROM adisyon_detay 
                       JOIN urunler ON urunler.id=adisyon_detay.urun_id
                       WHERE adisyon_id=$adisyon_id");

$toplam = 0;
while($d=$detay->fetch_assoc()) { $toplam += $d['adet']*$d['fiyat']; }
$detay->data_seek(0);

if (isset($_POST['odeme'])) {
    $conn->query("UPDATE adisyonlar SET kapali=1 WHERE id=$adisyon_id");
    $conn->query("UPDATE masalar SET durum=0 WHERE id=$masa_id");
    header("Location: kasiyer.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Ödeme</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Masa <?=$masa_id?> Ödeme</h2>
  <ul>
    <?php while($d=$detay->fetch_assoc()) { ?>
      <li><?=$d['urun_adi']?> x <?=$d['adet']?> - <?=$d['fiyat']*$d['adet']?>₺</li>
    <?php } ?>
  </ul>
  <h3>Toplam: <?=$toplam?>₺</h3>
  <form method="post">
    <button type="submit" name="odeme">💳 Ödeme Al</button>
  </form>
</body>
</html>