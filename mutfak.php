<?php
session_name("mutfak_session");
session_start();
include "db.php";

// Türkiye saati dilimini ayarla (açılış saatleri Türkiye saatine göre gösterilsin)
date_default_timezone_set('Europe/Istanbul');

// Sadece mutfak erişebilsin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mutfak') {
    header("Location: index.php?error=Yetkisiz erişim!");
    exit;
}

// Siparişleri çekme fonksiyonu
function getOrders($conn) {
    $sql = "
    SELECT a.id AS adisyon_id, 
           m.masa_no, 
           u.urun_adi, 
           d.adet, 
           d.aciklama,
           a.acilis_tarihi
    FROM adisyonlar a
    JOIN masalar m ON a.masa_id = m.id
    JOIN adisyon_detay d ON a.id = d.adisyon_id
    JOIN urunler u ON d.urun_id = u.id
    WHERE a.kapali = 0
    ORDER BY a.acilis_tarihi DESC
    ";
    $res = $conn->query($sql);
    if (!$res) {
        return "SQL Hatası: " . $conn->error;
    }

    $orders = [];
    while ($row = $res->fetch_assoc()) {
        $orders[$row['adisyon_id']]['masa_no'] = $row['masa_no'];
        $orders[$row['adisyon_id']]['tarih'] = $row['acilis_tarihi'];
        $orders[$row['adisyon_id']]['urunler'][] = [
            'urun_adi' => $row['urun_adi'],
            'adet' => $row['adet'],
            'aciklama' => $row['aciklama']
        ];
    }

    if (empty($orders)) {
        return "<p>📭 Şu anda aktif sipariş bulunmamaktadır.</p>";
    }

    $html = "";
    foreach ($orders as $adisyon_id => $siparis) {
        // Açılış saatini Türkiye saatine göre güvenli şekilde formatla
        // Eğer veritabanı zaman damgaları UTC olarak saklanıyorsa, aşağıdaki UTC satırını kullan:
        // $dt = new DateTime($siparis['tarih'], new DateTimeZone('UTC'));
        // $dt->setTimezone(new DateTimeZone('Europe/Istanbul'));

        // Aksi durumda (çoğu senaryoda yeterli): doğrudan DateTime kullan (date_default_timezone_set ile İstanbul oldu)
        $dt = new DateTime($siparis['tarih']);
        $dt->setTimezone(new DateTimeZone('Europe/Istanbul'));
        $time = $dt->format('H:i');

        $html .= '<div class="fis" id="fis-'.$adisyon_id.'">';
        $html .= '<h3>🪑 Masa ' . $siparis['masa_no'] . ' <span>#' . $adisyon_id . '</span></h3>';
        $html .= '<small>📌 Açılış: ' . $time . '</small>';
        $html .= '<ul>';
        foreach ($siparis['urunler'] as $urun) {
            $aciklamaText = $urun['aciklama'] ? "📝 " . htmlspecialchars($urun['aciklama']) : "❌ Açıklama yok";
            $html .= "<li><strong>{$urun['adet']}</strong> x {$urun['urun_adi']} <br><em>{$aciklamaText}</em></li>";
        }
        $html .= '</ul>';
        $html .= '<button onclick="printOrder(\'fis-'.$adisyon_id.'\')">🖨 Yazdır</button>';
        $html .= '</div>';
    }
    return $html;
}

// AJAX istekleri için sadece içerik döndür
if (isset($_GET['ajax'])) {
    echo getOrders($conn);
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>🍽 Siparişler</title>
  <style>
    body { font-family: "Courier New", monospace; background:#f9f9f9; margin:0; padding:20px; }
    h2 { background:#3498db; color:#fff; padding:15px; margin:0 0 20px 0; border-radius:8px; display:flex; justify-content:space-between; align-items:center; }
    #clock { font-size:16px; font-weight:bold; }
    #orders { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
    .fis { background:#fff; border:2px dashed #333; padding:15px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    .fis h3 { margin:0 0 10px; font-size:18px; border-bottom:1px dashed #999; padding-bottom:5px; }
    .fis h3 span { font-size:14px; color:#666; float:right; }
    .fis small { display:block; margin-bottom:10px; color:#888; font-size:13px; }
    .fis ul { list-style:none; margin:0; padding:0; }
    .fis li { padding:4px 0; font-size:16px; border-bottom:1px dotted #ccc; }
    .fis li:last-child { border-bottom:none; }
    .fis em { font-size:13px; color:#555; }
    .fis button { margin-top:10px; padding:6px 10px; background:#2ecc71; border:none; color:#fff; font-weight:bold; cursor:pointer; border-radius:5px; }
    .fis button:hover { background:#27ae60; }
  </style>
</head>
<body>
  <h2>
    📋 Sipariş Listesi
    <span id="clock"></span>
  </h2>
  <div id="orders">
    <?= getOrders($conn) ?>
  </div>

  <script>
  setInterval(()=>{
    fetch("mutfak.php?ajax=1")
      .then(r=>r.text())
      .then(html=>{
        document.getElementById("orders").innerHTML = html;
      });
  }, 5000);

  function updateClock() {
    const now = new Date();
    const saat = String(now.getHours()).padStart(2, '0');
    const dakika = String(now.getMinutes()).padStart(2, '0');
    const saniye = String(now.getSeconds()).padStart(2, '0');
    document.getElementById("clock").innerText = saat + ":" + dakika + ":" + saniye;
  }
  setInterval(updateClock, 1000);
  updateClock();

  // ✅ Termal yazıcıya uygun yazdır
  function printOrder(id) {
    const div = document.getElementById(id).innerHTML;
    const printWindow = window.open('', '', 'height=600,width=400');
    printWindow.document.write('<html><head><title>Fiş</title>');
    printWindow.document.write(`
      <style>
        @page { size: 80mm auto; margin: 5mm; }
        body { font-family: "Courier New", monospace; font-size:14px; width:80mm; }
        h3 { font-size:14px; margin:0 0 5px; border-bottom:1px dashed #000; padding-bottom:3px; }
        small { font-size:12px; display:block; margin-bottom:5px; }
        ul { list-style:none; padding:0; margin:0; }
        li { border-bottom:1px dotted #000; padding:3px 0; }
        li:last-child { border-bottom:none; }
        em { font-size:11px; color:#000; }
        button { display:none; } /* yazdır butonu görünmesin */
      </style>
    `);
    printWindow.document.write('</head><body>');
    printWindow.document.write(div);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
  }
  </script>
</body>
</html>