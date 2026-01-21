<?php
session_name("garson_session");
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'garson') {
    header("Location: index.php?error=Yetkisiz erişim!");
    exit;
}
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'garson') {
    header("Location: index.php");
    exit;
}

$masalar = $conn->query("
    SELECT m.*, a.onayli 
    FROM masalar m
    LEFT JOIN adisyonlar a ON a.masa_id=m.id AND a.kapali=0
");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Masalar</title>
  <link rel="stylesheet" href="style.css">
  <style>
/* Grid yapı */
.masa-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 16px;
  padding: 20px;
}

/* Genel masa kutusu */
.masa {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  border-radius: 16px;
  font-weight: 600;
  font-size: 16px;
  text-decoration: none;
  color: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  transition: transform .2s ease, box-shadow .2s ease;
  position: relative;
  overflow: hidden;
}

/* Hover efekti */
.masa:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.25);
}

/* Durum renkleri */
.masa.bos {
  background: linear-gradient(135deg, #1cc88a, #17a673);
}
.masa.dolu {
  background: linear-gradient(135deg, #e74a3b, #be2617);
}
.masa.bekliyor {
  background: linear-gradient(135deg, #f6c23e, #dda20a);
  color: #222;
}

/* Bekliyor ikonu */
.masa.bekliyor::after {
  content: "⏱";
  position: absolute;
  top: 8px;
  right: 10px;
  font-size: 18px;
  animation: blink 1s infinite;
}

/* Yanıp sönme animasyonu */
@keyframes blink {
  0%, 50%, 100% { opacity: 1; }
  25%, 75% { opacity: 0; }
}

/* Responsive */
@media (max-width: 768px) {
  .masa-grid {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 12px;
  }
  .masa {
    padding: 20px;
    font-size: 14px;
  }
}
</style>
</head>
<body>
  <h2>🍽 Masalar (Garson)</h2>
  <div class="masa-grid" id="masaGrid">
    <?php while($masa = $masalar->fetch_assoc()) { ?>
   <?php 
$cssClass = "bos";
if ($masa['durum'] == 1) {
    if ($masa['onayli'] == 1) {
        $cssClass = "dolu"; // kırmızı
    } else {
        $cssClass = "bekliyor"; // sarı
    }
}
?>
<a href="adisyon.php?masa=<?=$masa['id']?>" class="masa <?=$cssClass?>">
   Masa <?=$masa['masa_no']?>
</a>
    <?php } ?>
  </div>
  <a href="logout.php">Çıkış</a>

<script>
function refreshMasalar() {
    fetch(window.location.href) 
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const newGrid = doc.querySelector("#masaGrid").innerHTML;
            document.querySelector("#masaGrid").innerHTML = newGrid;
        })
        .catch(err => console.error("Yenileme hatası:", err));
}

// İlk yükleme
setTimeout(refreshMasalar, 1000);

// 10 saniyede bir yenile
setInterval(refreshMasalar, 10000);
</script>
</body>
</html>