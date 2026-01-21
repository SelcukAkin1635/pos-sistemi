<?php
// Bilgisayarın IP adresi
$ip = "192.168.18.17"; 
$menuLink = "http://$ip/pos%20sistemi/menu.php"; // menu.php dosyanın yolu

// Google Chart API ile QR kod URL oluştur
$qrUrl = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . urlencode($menuLink);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>QR Menü Test</title>
  <style>
    body { font-family: sans-serif; text-align:center; padding: 50px; background:#f7f7f7;}
    img { margin-top:20px; }
  </style>
</head>
<body>
  <h1>📱 Menü QR Kodu</h1>
  <p>Telefonun kamerasıyla okutursan menü açılacak</p>
  <img src="<?= $qrUrl ?>" alt="QR Kod">
</body>
</html>