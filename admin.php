<?php
session_name("admin_session");
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=Yetkisiz erişim!");
    exit;
}

// admin.php - Tam, çalışır halde admin paneli (Ciro sayfası dahil)
// Not: geliştirme sırasında hataları görmeye yardımcı olmak için error display açık.
// Üretime alırken bu satırları kapatın.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "db.php"; // içinde $conn (mysqli) olmalı

// sadece admin erişsin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$page = $_GET['page'] ?? 'dashboard';

// helper: tablo sütunu var mı?
function column_exists($conn, $table, $column) {
    $tableEsc = $conn->real_escape_string($table);
    $colEsc = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `{$tableEsc}` LIKE '{$colEsc}'");
    return ($res && $res->num_rows > 0);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Admin Paneli</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* küçük ek stil */
    .sidebar a.active { background:#374151; }
    .card { background:#fff; border-radius:8px; padding:16px; box-shadow:0 4px 16px rgba(2,6,23,0.06); }
  </style>
</head>
<body class="bg-gray-100">
  <!-- Top Navbar -->
  <header class="flex items-center justify-between bg-white shadow px-4 py-3 md:px-6 md:py-4">
    <div class="flex items-center gap-2">
      <!-- Hamburger Button -->
      <button id="sidebarToggle" class="md:hidden mr-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400" aria-label="Menüyü Aç/Kapat">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      <span class="text-xl md:text-2xl font-semibold">Admin Paneli</span>
    </div>
<div class="hidden md:flex gap-2 items-center">
  <a href="admin.php?page=dashboard" class="py-1 px-2 rounded hover:bg-gray-100 <?= $page==='dashboard' ? 'bg-gray-200 font-bold':'' ?>">Dashboard</a>
  <a href="admin.php?page=masalar" class="py-1 px-2 rounded hover:bg-gray-100 <?= $page==='masalar' ? 'bg-gray-200 font-bold':'' ?>">Masalar</a>
  <a href="admin.php?page=urunler" class="py-1 px-2 rounded hover:bg-gray-100 <?= $page==='urunler' ? 'bg-gray-200 font-bold':'' ?>">Ürünler</a>
  <a href="admin.php?page=adisyonlar" class="py-1 px-2 rounded hover:bg-gray-100 <?= $page==='adisyonlar' ? 'bg-gray-200 font-bold':'' ?>">Adisyonlar</a>
  <a href="admin.php?page=ciro" class="py-1 px-2 rounded hover:bg-gray-100 <?= $page==='ciro' ? 'bg-gray-200 font-bold':'' ?>">Ciro</a>
  <a href="admin.php?page=personel" class="py-1 px-2 rounded hover:bg-gray-100 <?= $page==='personel' ? 'bg-gray-200 font-bold':'' ?>">Personel Yönetimi</a>
  <a href="logout.php" class="py-1 px-2 rounded hover:bg-gray-100">Çıkış</a>
</div>
  </header>
  <div class="flex min-h-screen">
    <!-- Sidebar (responsive) -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-800 text-white p-6 transform -translate-x-full transition-transform duration-200 ease-in-out md:relative md:translate-x-0 md:block hidden md:w-64 md:bg-gray-800 md:text-white md:p-6">
      <div class="md:hidden flex justify-end mb-4">
        <button id="sidebarClose" class="text-gray-300 hover:text-white" aria-label="Menüyü Kapat">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
  <nav class="space-y-2 sidebar">
    <a href="admin.php?page=dashboard" class="block py-2 px-3 rounded hover:bg-gray-700 <?= $page==='dashboard' ? 'active':'' ?>">📊 Dashboard</a>
    <a href="admin.php?page=masalar" class="block py-2 px-3 rounded hover:bg-gray-700 <?= $page==='masalar' ? 'active':'' ?>">📋 Masa Yönetimi</a>
    <a href="admin.php?page=urunler" class="block py-2 px-3 rounded hover:bg-gray-700 <?= $page==='urunler' ? 'active':'' ?>">🍽️ Ürün Yönetimi</a>
    <a href="admin.php?page=adisyonlar" class="block py-2 px-3 rounded hover:bg-gray-700 <?= $page==='adisyonlar' ? 'active':'' ?>">🧾 Adisyonlar</a>
    <a href="admin.php?page=ciro" class="block py-2 px-3 rounded hover:bg-gray-700 <?= $page==='ciro' ? 'active':'' ?>">💰 Ciro</a>
    <a href="admin.php?page=personel" class="block py-2 px-3 rounded hover:bg-gray-700 <?= $page==='personel' ? 'active':'' ?>">👥 Personel Yönetimi</a>
    <a href="logout.php" class="block py-2 px-3 rounded hover:bg-gray-700">🚪 Çıkış Yap</a>
</nav>
    </aside>
    <!-- Overlay for mobile sidebar -->
    <div id="sidebarOverlay" class="fixed inset-0 z-30 bg-black bg-opacity-40 hidden md:hidden"></div>
    <!-- Main Content -->
    <main class="flex-1 p-3 sm:p-6 transition-all duration-200">
      <?php
      // PAGE SWITCH
      switch ($page) {
// ------------------- DASHBOARD -------------------
// ------------------- DASHBOARD -------------------
case 'dashboard':
  // Açık adisyon sayısı
  $acikAdisyon = $conn->query("SELECT COUNT(*) as s FROM adisyonlar WHERE kapali=0")->fetch_assoc()['s'] ?? 0;

  // Masa sayısı
  $masaSayisi = $conn->query("SELECT COUNT(*) as s FROM masalar")->fetch_assoc()['s'] ?? 0;

  // Bugünkü ciro
  $bugunCiroSql = "
    SELECT SUM(d.adet*u.fiyat) as toplam
    FROM adisyon_detay d
    JOIN urunler u ON u.id=d.urun_id
    JOIN adisyonlar a ON a.id=d.adisyon_id
    WHERE a.kapali=1 AND DATE(a.acilis_tarihi)=CURDATE()
  ";
  $bugunCiro = $conn->query($bugunCiroSql)->fetch_assoc()['toplam'] ?? 0;

  // Tüm zamanların toplam kazancı
  $toplamKazancSql = "
    SELECT SUM(d.adet*u.fiyat) as toplam
    FROM adisyon_detay d
    JOIN urunler u ON u.id=d.urun_id
    JOIN adisyonlar a ON a.id=d.adisyon_id
    WHERE a.kapali=1
  ";
  $toplamKazanc = $conn->query($toplamKazancSql)->fetch_assoc()['toplam'] ?? 0;

  // Günlük cirolar (sadece son 10 gün)
  $sqlCiroGunluk = "
    SELECT DATE(a.acilis_tarihi) as gun, SUM(d.adet*u.fiyat) as toplam
    FROM adisyon_detay d
    JOIN urunler u ON u.id=d.urun_id
    JOIN adisyonlar a ON a.id=d.adisyon_id
    WHERE a.kapali=1
    GROUP BY DATE(a.acilis_tarihi)
    ORDER BY DATE(a.acilis_tarihi) DESC
    LIMIT 10
  ";
  $resCiro = $conn->query($sqlCiroGunluk);
  $ciroGunluk = [];
  while ($row = $resCiro->fetch_assoc()) {
      $ciroGunluk[] = $row;
  }
  $ciroGunluk = array_reverse($ciroGunluk); // grafik için eski → yeni

  // Ödeme yöntemleri (bugün & toplam)
  $sqlOdeme = "
    SELECT odeme_yontemi, 
           SUM(CASE WHEN DATE(tarih)=CURDATE() THEN odeme_tutari ELSE 0 END) as bugun,
           SUM(odeme_tutari) as toplam
    FROM odemeler
    GROUP BY odeme_yontemi
  ";
  $resOdeme = $conn->query($sqlOdeme);
  $odemeData = [];
  while ($o = $resOdeme->fetch_assoc()) {
      $odemeData[$o['odeme_yontemi']] = $o;
  }
  $nakitBugun = $odemeData['Nakit']['bugun'] ?? 0;
  $nakitToplam = $odemeData['Nakit']['toplam'] ?? 0;
  $kartBugun = $odemeData['Kart']['bugun'] ?? 0;
  $kartToplam = $odemeData['Kart']['toplam'] ?? 0;
  ?>

  <h1 class="text-2xl font-bold mb-6">📊 Dashboard</h1>

  <!-- Üst kartlar -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 shadow rounded text-center">
      <div class="text-sm text-gray-500">Bugünkü Ciro</div>
      <div class="text-2xl font-bold text-green-600"><?= number_format($bugunCiro,2) ?> ₺</div>
    </div>
    <div class="bg-white p-4 shadow rounded text-center">
      <div class="text-sm text-gray-500">Toplam Kazanç</div>
      <div class="text-2xl font-bold text-blue-600"><?= number_format($toplamKazanc,2) ?> ₺</div>
    </div>
    <div class="bg-white p-4 shadow rounded text-center">
      <div class="text-sm text-gray-500">Açık Adisyonlar</div>
      <div class="text-2xl font-bold text-yellow-600"><?= (int)$acikAdisyon ?></div>
    </div>
    <div class="bg-white p-4 shadow rounded text-center">
      <div class="text-sm text-gray-500">Masa Sayısı</div>
      <div class="text-2xl font-bold text-purple-600"><?= (int)$masaSayisi ?></div>
    </div>
  </div>

  <!-- Alt kısım -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Günlük Ciro Bildirimleri -->
    <div class="bg-white p-4 shadow rounded overflow-x-auto">
      <h3 class="text-lg font-semibold mb-3">📢 Günlük Ciro Bildirimleri (Son 10 Gün)</h3>
      <ul class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
        <?php if (count($ciroGunluk) === 0): ?>
          <li class="py-2 text-gray-500">Henüz kapalı adisyon yok.</li>
        <?php else: ?>
          <?php foreach ($ciroGunluk as $g): ?>
            <li class="py-2 flex justify-between">
              <span><?= date("d.m.Y", strtotime($g['gun'])) ?></span>
              <span class="font-semibold"><?= number_format($g['toplam'],2) ?> ₺</span>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Grafik -->
    <div class="bg-white p-4 shadow rounded overflow-x-auto">
      <h3 class="text-lg font-semibold mb-3">📈 Son 7 Gün Ciro Grafiği</h3>
      <canvas id="ciroChart" height="150"></canvas>
    </div>
  </div>

  <!-- Ödeme Yöntemleri -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Nakit Ödeme -->
    <div class="bg-white p-4 shadow rounded text-center overflow-x-auto">
      <h3 class="text-lg font-semibold mb-3">💵 Nakit Ödemeler</h3>
      <p class="text-sm text-gray-500">Bugün</p>
      <p class="text-xl font-bold text-green-600"><?= number_format($nakitBugun,2) ?> ₺</p>
      <p class="text-sm text-gray-500 mt-2">Toplam</p>
      <p class="text-xl font-bold text-blue-600"><?= number_format($nakitToplam,2) ?> ₺</p>
    </div>

    <!-- Kart Ödeme -->
    <div class="bg-white p-4 shadow rounded text-center overflow-x-auto">
      <h3 class="text-lg font-semibold mb-3">💳 Kart Ödemeler</h3>
      <p class="text-sm text-gray-500">Bugün</p>
      <p class="text-xl font-bold text-green-600"><?= number_format($kartBugun,2) ?> ₺</p>
      <p class="text-sm text-gray-500 mt-2">Toplam</p>
      <p class="text-xl font-bold text-blue-600"><?= number_format($kartToplam,2) ?> ₺</p>
    </div>
  </div>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('ciroChart').getContext('2d');
    const ciroChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_column($ciroGunluk, 'gun')) ?>,
        datasets: [{
          label: '₺ Ciro',
          data: <?= json_encode(array_column($ciroGunluk, 'toplam')) ?>,
          backgroundColor: '#4e73df'
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } }
      }
    });
  </script>
  <?php
  break;
// ------------------- MASALAR -------------------
        case 'masalar':
          $has_masa_adi = column_exists($conn, 'masalar', 'masa_adi');
          $has_kat = column_exists($conn, 'masalar', 'kat');

          // masa ekleme
          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ekle'])) {
              $masa_no = intval($_POST['masa_no'] ?? 0);
              $masa_adi = $conn->real_escape_string($_POST['masa_adi'] ?? '');
              $kat = $conn->real_escape_string($_POST['kat'] ?? '');
              if ($masa_no > 0) {
                  $exists = $conn->query("SELECT id FROM masalar WHERE masa_no=$masa_no");
                  if ($exists && $exists->num_rows > 0) {
                      echo "<div class='mb-4 p-3 bg-yellow-100 text-yellow-800 rounded'>⚠️ Bu masa zaten var.</div>";
                  } else {
                      if ($has_masa_adi && $has_kat) {
                          $sql = "INSERT INTO masalar (masa_no,durum,masa_adi,kat) VALUES ($masa_no,0,'$masa_adi','$kat')";
                      } elseif ($has_masa_adi) {
                          $sql = "INSERT INTO masalar (masa_no,durum,masa_adi) VALUES ($masa_no,0,'$masa_adi')";
                      } elseif ($has_kat) {
                          $sql = "INSERT INTO masalar (masa_no,durum,kat) VALUES ($masa_no,0,'$kat')";
                      } else {
                          $sql = "INSERT INTO masalar (masa_no,durum) VALUES ($masa_no,0)";
                      }
                      if ($conn->query($sql)) echo "<div class='mb-4 p-3 bg-green-100 text-green-800 rounded'>✅ Masa eklendi.</div>";
                  }
              } else {
                  echo "<div class='mb-4 p-3 bg-red-100 text-red-800 rounded'>❌ Geçerli bir masa numarası girin.</div>";
              }
          }

          // masa silme
          if (isset($_GET['sil'])) {
              $id = intval($_GET['sil']);
              $check = $conn->query("SELECT COUNT(*) as s FROM adisyonlar WHERE masa_id=$id")->fetch_assoc()['s'] ?? 0;
              if ($check > 0) {
                  echo "<div class='mb-4 p-3 bg-red-100 text-red-800 rounded'>❌ Bu masaya bağlı $check adisyon var, silinemez.</div>";
              } else {
                  if ($conn->query("DELETE FROM masalar WHERE id=$id")) {
                      echo "<div class='mb-4 p-3 bg-green-100 text-green-800 rounded'>✅ Masa silindi.</div>";
                  }
              }
          }
          ?>
          <h1 class="text-2xl font-bold mb-4">Masa Yönetimi</h1>

          <form method="post" class="flex flex-wrap gap-2 items-center mb-6">
            <input type="number" name="masa_no" required placeholder="Masa No" class="border p-2 rounded w-28">
            <?= $has_masa_adi ? "<input type='text' name='masa_adi' placeholder='Masa Adı' class='border p-2 rounded w-40'>" : "" ?>
            <?= $has_kat ? "<input type='text' name='kat' placeholder='Kat' class='border p-2 rounded w-28'>" : "" ?>
            <button type="submit" name="ekle" class="bg-green-500 text-white px-3 py-1 rounded">➕ Ekle</button>
          </form>

          <?php
          $resMasalar = $conn->query("SELECT * FROM masalar ORDER BY masa_no ASC");
          echo "<div class='card overflow-x-auto'><table class='min-w-full text-sm'>";
          echo "<thead class='bg-gray-50'><tr><th class='p-2 text-left'>ID</th><th class='p-2 text-left'>Masa No</th>";
          if ($has_masa_adi) echo "<th class='p-2 text-left'>Masa Adı</th>";
          if ($has_kat) echo "<th class='p-2 text-left'>Kat</th>";
          echo "<th class='p-2 text-left'>Durum</th><th class='p-2 text-left'>İşlem</th></tr></thead><tbody>";
          while ($m = $resMasalar->fetch_assoc()) {
              $durum = $m['durum'] ? "DOLU" : "BOŞ";
              echo "<tr class='border-t'><td class='p-2'>{$m['id']}</td><td class='p-2'>{$m['masa_no']}</td>";
              if ($has_masa_adi) echo "<td class='p-2'>".htmlspecialchars($m['masa_adi'])."</td>";
              if ($has_kat) echo "<td class='p-2'>".htmlspecialchars($m['kat'])."</td>";
              echo "<td class='p-2'>{$durum}</td><td class='p-2'><a href='admin.php?page=masalar&sil={$m['id']}' class='bg-red-500 text-white px-2 py-1 rounded'>Sil</a></td></tr>";
          }
          echo "</tbody></table></div>";
          break;
// ------------------- ADISYONLAR-------------------
        case 'adisyonlar':
    $filtre = $_GET['filtre'] ?? 'gunluk';
    $sayfaNo = max(1, intval($_GET['s'] ?? 1));
    $limit = 25;
    $offset = ($sayfaNo - 1) * $limit;

    // Filtreye göre WHERE
    if ($filtre == "gunluk") {
        $where = "DATE(a.acilis_tarihi)=CURDATE()";
    } elseif ($filtre == "aylik") {
        $where = "YEAR(a.acilis_tarihi)=YEAR(CURDATE()) AND MONTH(a.acilis_tarihi)=MONTH(CURDATE())";
    } elseif ($filtre == "yillik") {
        $where = "YEAR(a.acilis_tarihi)=YEAR(CURDATE())";
    } else {
        $where = "1";
    }

    // --- AUTO-CLOSE (kapali=1) adisyonlar: onayli=1, kapali=0, toplam_odeme=0
    $sql = "
        SELECT a.id, a.kapali, a.onayli,
               COALESCE(SUM(o.odeme_tutari),0) as toplam_odeme
        FROM adisyonlar a
        LEFT JOIN odemeler o ON o.adisyon_id=a.id
        WHERE $where
        GROUP BY a.id, a.kapali, a.onayli
    ";
    $autoCloseResult = $conn->query($sql);
    if ($autoCloseResult) {
        while ($r = $autoCloseResult->fetch_assoc()) {
            if ($r['onayli'] && !$r['kapali'] && floatval($r['toplam_odeme']) == 0.0) {
                $adisyon_id = intval($r['id']);
                $conn->query("UPDATE adisyonlar SET kapali=1 WHERE id=$adisyon_id");
            }
        }
        $autoCloseResult->free();
    }

    // --- TOPLAM KAYIT (0 ₺ adisyonları hariç)
    $toplamKayitRes = $conn->query("
        SELECT a.id
        FROM adisyonlar a
        LEFT JOIN odemeler o ON o.adisyon_id=a.id
        WHERE $where
        GROUP BY a.id
        HAVING COALESCE(SUM(o.odeme_tutari),0) > 0
    ");
    $toplamKayit = $toplamKayitRes->num_rows;
    $toplamSayfa = max(1, ceil($toplamKayit / $limit));

    // --- ASIL ADİSYON LİSTESİ
    $sql = "
        SELECT a.id, a.acilis_tarihi, a.kapali, a.onayli, m.masa_no,
               COALESCE(SUM(o.odeme_tutari),0) as toplam_odeme
        FROM adisyonlar a
        JOIN masalar m ON m.id=a.masa_id
        LEFT JOIN odemeler o ON o.adisyon_id=a.id
        WHERE $where
        GROUP BY a.id, a.acilis_tarihi, a.kapali, a.onayli, m.masa_no
        HAVING toplam_odeme > 0
        ORDER BY a.acilis_tarihi DESC
        LIMIT $limit OFFSET $offset
    ";
    $adisyonlar = $conn->query($sql);
    ?>
    <h1 class="text-2xl font-bold mb-4">Adisyonlar</h1>
    <div class="mb-4 space-x-2">
        <a href="admin.php?page=adisyonlar&filtre=gunluk" class="px-3 py-1 bg-gray-200 rounded">Günlük</a>
        <a href="admin.php?page=adisyonlar&filtre=aylik" class="px-3 py-1 bg-gray-200 rounded">Aylık</a>
        <a href="admin.php?page=adisyonlar&filtre=yillik" class="px-3 py-1 bg-gray-200 rounded">Yıllık</a>
        <a href="admin.php?page=adisyonlar&filtre=hepsi" class="px-3 py-1 bg-gray-200 rounded">Tümü</a>
    </div>
    <div class="card overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-2 text-left">ID</th>
                    <th class="p-2 text-left">Masa</th>
                    <th class="p-2 text-left">Açılış</th>
                    <th class="p-2 text-left">Onaylı</th>
                    <th class="p-2 text-left">Kapalı</th>
                    <th class="p-2 text-left">Toplam Ödeme</th>
                    <th class="p-2 text-left">Detay</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($a = $adisyonlar->fetch_assoc()) { ?>
                    <tr class="border-t">
                        <td class="p-2"><?= $a['id'] ?></td>
                        <td class="p-2">Masa <?= $a['masa_no'] ?></td>
                        <td class="p-2"><?= $a['acilis_tarihi'] ?></td>
                        <td class="p-2"><?= $a['onayli'] ? "✅" : "❌" ?></td>
                        <td class="p-2"><?= $a['kapali'] ? "✅" : "❌" ?></td>
                        <td class="p-2"><?= number_format($a['toplam_odeme'],2) ?> ₺</td>
                        <td class="p-2">
                            <a href="admin.php?page=adisyon_detay&id=<?= $a['id'] ?>" class="bg-blue-500 text-white px-2 py-1 rounded">Görüntüle</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        <?php for ($i=1; $i<=$toplamSayfa; $i++): 
            $active = $i==$sayfaNo ? 'bg-blue-500 text-white' : 'bg-gray-200'; ?>
            <a href="admin.php?page=adisyonlar&filtre=<?=htmlspecialchars($filtre)?>&s=<?=$i?>" class="px-3 py-1 rounded <?=$active?>"><?=$i?></a>
        <?php endfor; ?>
    </div>
<?php
break;
   // ------------------- ADISYON DETAY -------------------
   case 'adisyon_detay':
    $id = intval($_GET['id'] ?? 0);
    ?>
    <h1 class="text-2xl font-bold mb-4">Adisyon #<?= $id ?> Detayları</h1>
    <?php
    // Sadece tutarı > 0 olan ürünleri çek
    $sql = "
        SELECT d.adet, u.urun_adi, u.fiyat, (d.adet*u.fiyat) as tutar
        FROM adisyon_detay d
        JOIN urunler u ON u.id=d.urun_id
        WHERE d.adisyon_id=$id
        HAVING tutar > 0
    ";
    $detaylar = $conn->query($sql);
    $genel = 0;
    ?>
    <div class="card overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-2 text-left">Ürün</th>
                    <th class="p-2 text-left">Adet</th>
                    <th class="p-2 text-left">Fiyat</th>
                    <th class="p-2 text-left">Tutar</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($d = $detaylar->fetch_assoc()): 
                $genel += $d['tutar']; ?>
                <tr class="border-t">
                    <td class="p-2"><?= htmlspecialchars($d['urun_adi']) ?></td>
                    <td class="p-2"><?= (int)$d['adet'] ?></td>
                    <td class="p-2"><?= number_format($d['fiyat'],2) ?> ₺</td>
                    <td class="p-2"><?= number_format($d['tutar'],2) ?> ₺</td>
                </tr>
            <?php endwhile; ?>
                <tr class="bg-gray-100 font-bold">
                    <td colspan="3" class="p-2 text-right">Toplam</td>
                    <td class="p-2"><?= number_format($genel,2) ?> ₺</td>
                </tr>
            </tbody>
        </table>
    </div>
    <a href="admin.php?page=adisyonlar" class="inline-block mt-4 bg-gray-500 text-white px-3 py-1 rounded">⬅️ Geri</a>
<?php
break;
          // ------------------- PERSONEL YÖNETİMİ -------------------
case 'personel':
  // Yeni personel ekleme
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ekle'])) {
      $username = $conn->real_escape_string($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';
      $role = $conn->real_escape_string($_POST['role'] ?? 'garson');

      if ($username && $password) {
          // Aynı kullanıcı var mı?
          $check = $conn->query("SELECT id FROM users WHERE username='$username'");
          if ($check && $check->num_rows > 0) {
              echo "<div class='mb-4 p-3 bg-yellow-100 text-yellow-800 rounded'>⚠️ Bu kullanıcı adı zaten kullanılıyor.</div>";
          } else {
              // MD5 şifreleme (senin tablona uyumlu)
              $hashed = md5($password);
              $sql = "INSERT INTO users (username,password,role) VALUES ('$username','$hashed','$role')";
              if ($conn->query($sql)) {
                  echo "<div class='mb-4 p-3 bg-green-100 text-green-800 rounded'>✅ Personel eklendi.</div>";
              } else {
                  echo "<div class='mb-4 p-3 bg-red-100 text-red-800 rounded'>❌ Hata: {$conn->error}</div>";
              }
          }
      } else {
          echo "<div class='mb-4 p-3 bg-red-100 text-red-800 rounded'>❌ Tüm alanları doldurun.</div>";
      }
  }

  // Personel silme
  if (isset($_GET['sil'])) {
      $id = intval($_GET['sil']);
      if ($conn->query("DELETE FROM users WHERE id=$id")) {
          echo "<div class='mb-4 p-3 bg-green-100 text-green-800 rounded'>✅ Personel silindi.</div>";
      }
  }
  ?>
  <h1 class="text-2xl font-bold mb-4">👥 Personel Yönetimi</h1>

  <form method="post" class="flex flex-wrap gap-2 items-center mb-6">
    <input type="text" name="username" required placeholder="Kullanıcı Adı" class="border p-2 rounded w-32">
    <input type="password" name="password" required placeholder="Şifre" class="border p-2 rounded w-32">
    <select name="role" class="border p-2 rounded">
      <option value="admin">Admin</option>
      <option value="kasiyer">Kasiyer</option>
      <option value="garson">Garson</option>
    </select>
    <button type="submit" name="ekle" class="bg-green-500 text-white px-3 py-1 rounded">➕ Ekle</button>
  </form>

  <?php
  $resUsers = $conn->query("SELECT * FROM users ORDER BY id DESC");
  echo "<div class='card overflow-x-auto'><table class='min-w-full text-sm'>";
  echo "<thead class='bg-gray-50'><tr>
          <th class='p-2 text-left'>ID</th>
          <th class='p-2 text-left'>Kullanıcı Adı</th>
          <th class='p-2 text-left'>Rol</th>
          <th class='p-2 text-left'>İşlem</th>
        </tr></thead><tbody>";
  while ($u = $resUsers->fetch_assoc()) {
      echo "<tr class='border-t'>
              <td class='p-2'>{$u['id']}</td>
              <td class='p-2'>".htmlspecialchars($u['username'])."</td>
              <td class='p-2'>{$u['role']}</td>
              <td class='p-2'>
                <a href='admin.php?page=personel&sil={$u['id']}' class='bg-red-500 text-white px-2 py-1 rounded'>Sil</a>
              </td>
            </tr>";
  }
  echo "</tbody></table></div>";
  break;
                // ------------------- ÜRÜNLER -------------------
        case 'urunler':
          ?>
          <h1 class="text-2xl font-bold mb-4">🍽️ Ürün Yönetimi</h1>

          <!-- Ürün ekleme formu -->
          <form method="post" class="flex flex-wrap gap-2 items-center mb-6">
            <input type="text" name="urun_adi" required placeholder="Ürün Adı" class="border p-2 rounded w-40">
            <input type="number" step="0.01" name="fiyat" required placeholder="Fiyat" class="border p-2 rounded w-32">
            <input type="text" name="kategori" placeholder="Kategori" class="border p-2 rounded w-40">
            <button type="submit" name="ekle" class="bg-green-500 text-white px-3 py-1 rounded">➕ Ekle</button>
          </form>
          <?php

          // Ekleme
          if (isset($_POST['ekle'])) {
              $adi = $conn->real_escape_string($_POST['urun_adi']);
              $fiyat = floatval($_POST['fiyat']);
              $kategori = $conn->real_escape_string($_POST['kategori']);
              if ($adi && $fiyat > 0) {
                  $conn->query("INSERT INTO urunler (urun_adi,fiyat,kategori) VALUES ('$adi',$fiyat,'$kategori')");
                  echo "<div class='mb-4 p-3 bg-green-100 text-green-800 rounded'>✅ Ürün eklendi.</div>";
              }
          }

          // Silme
          if (isset($_GET['sil'])) {
              $id = intval($_GET['sil']);
              if ($conn->query("DELETE FROM urunler WHERE id=$id")) {
                  echo "<div class='mb-4 p-3 bg-green-100 text-green-800 rounded'>✅ Ürün silindi.</div>";
              }
          }

          // Güncelleme
          if (isset($_POST['guncelle'])) {
              $id = intval($_POST['id']);
              $adi = $conn->real_escape_string($_POST['urun_adi']);
              $fiyat = floatval($_POST['fiyat']);
              $kategori = $conn->real_escape_string($_POST['kategori']);
              $conn->query("UPDATE urunler SET urun_adi='$adi', fiyat=$fiyat, kategori='$kategori' WHERE id=$id");
              echo "<div class='mb-4 p-3 bg-green-100 text-green-800 rounded'>✅ Ürün güncellendi.</div>";
          }

          // Liste
          $resUrunler = $conn->query("SELECT * FROM urunler ORDER BY id DESC");
          ?>
          <div class="card overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="p-2 text-left">ID</th>
                  <th class="p-2 text-left">Ürün Adı</th>
                  <th class="p-2 text-left">Fiyat</th>
                  <th class="p-2 text-left">Kategori</th>
                  <th class="p-2 text-left">İşlem</th>
                </tr>
              </thead>
              <tbody>
                <?php while($u = $resUrunler->fetch_assoc()): ?>
                  <tr class="border-t">
                    <td class="p-2"><?= $u['id'] ?></td>
                    <td class="p-2"><?= htmlspecialchars($u['urun_adi']) ?></td>
                    <td class="p-2"><?= number_format($u['fiyat'],2) ?> ₺</td>
                    <td class="p-2"><?= htmlspecialchars($u['kategori']) ?></td>
                    <td class="p-2 flex gap-2">
                      <button onclick="openEditModal(<?= $u['id'] ?>,'<?= htmlspecialchars($u['urun_adi'],ENT_QUOTES) ?>',<?= $u['fiyat'] ?>,'<?= htmlspecialchars($u['kategori'],ENT_QUOTES) ?>')" class="bg-blue-500 text-white px-2 py-1 rounded">✏️ Düzenle</button>
                      <a href="admin.php?page=urunler&sil=<?= $u['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Silmek istediğine emin misin?')">🗑️ Sil</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

          <!-- Düzenleme Modal -->
          <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
            <div class="bg-white p-6 rounded shadow-lg max-w-full sm:w-96">
              <h3 class="text-lg font-semibold mb-4">Ürün Düzenle</h3>
              <form method="post">
                <input type="hidden" name="id" id="editId">
                <div class="mb-2">
                  <label class="block text-sm">Ürün Adı</label>
                  <input type="text" name="urun_adi" id="editAdi" class="border p-2 w-full rounded" required>
                </div>
                <div class="mb-2">
                  <label class="block text-sm">Fiyat</label>
                  <input type="number" step="0.01" name="fiyat" id="editFiyat" class="border p-2 w-full rounded" required>
                </div>
                <div class="mb-4">
                  <label class="block text-sm">Kategori</label>
                  <input type="text" name="kategori" id="editKategori" class="border p-2 w-full rounded">
                </div>
                <div class="flex justify-end gap-2">
                  <button type="button" onclick="closeEditModal()" class="bg-gray-400 text-white px-3 py-1 rounded">İptal</button>
                  <button type="submit" name="guncelle" class="bg-green-500 text-white px-3 py-1 rounded">Kaydet</button>
                </div>
              </form>
            </div>
          </div>

          <script>
            function openEditModal(id, adi, fiyat, kategori) {
              document.getElementById('editId').value = id;
              document.getElementById('editAdi').value = adi;
              document.getElementById('editFiyat').value = fiyat;
              document.getElementById('editKategori').value = kategori;
              document.getElementById('editModal').style.display = 'flex';
            }
            function closeEditModal() {
              document.getElementById('editModal').style.display = 'none';
            }
          </script>
          <?php
          break;
        // ------------------- CİRO -------------------
        case 'ciro':
          ?>
          <h1 class="text-2xl font-bold mb-4">📊 Ciro Analizi</h1>
          <?php
          // kategori bazlı satış (sadece kapalı adisyonlar)
          $sqlKategori = "
            SELECT COALESCE(u.kategori,'Diğer') AS kategori, SUM(d.adet * u.fiyat) AS toplam
            FROM adisyon_detay d
            JOIN urunler u ON u.id = d.urun_id
            JOIN adisyonlar a ON a.id = d.adisyon_id
            WHERE a.kapali = 1
            GROUP BY u.kategori
            ORDER BY toplam DESC
          ";
          $res1 = $conn->query($sqlKategori);
          $kategoriLabels = $kategoriTotals = [];
          if ($res1) {
              while ($row = $res1->fetch_assoc()) {
                  $kategoriLabels[] = $row['kategori'];
                  $kategoriTotals[] = (float)$row['toplam'];
              }
          }

          // zaman filtresi: gunluk / haftalik / aylik
          $filtre = $_GET['filtre'] ?? 'haftalik';
          $ciroLabels = $ciroTotals = [];

          if ($filtre === 'gunluk') {
              $sqlCiro = "
                SELECT DATE(a.acilis_tarihi) AS label, SUM(d.adet * u.fiyat) AS toplam
                FROM adisyon_detay d
                JOIN urunler u ON u.id = d.urun_id
                JOIN adisyonlar a ON a.id = d.adisyon_id
                WHERE a.kapali = 1
                GROUP BY DATE(a.acilis_tarihi)
                ORDER BY DATE(a.acilis_tarihi) ASC
              ";
              $labelTitle = "Günlük";
          } elseif ($filtre === 'aylik') {
              $sqlCiro = "
                SELECT CONCAT(YEAR(a.acilis_tarihi), '-', LPAD(MONTH(a.acilis_tarihi),2,'0')) AS label, SUM(d.adet * u.fiyat) AS toplam
                FROM adisyon_detay d
                JOIN urunler u ON u.id = d.urun_id
                JOIN adisyonlar a ON a.id = d.adisyon_id
                WHERE a.kapali = 1
                GROUP BY YEAR(a.acilis_tarihi), MONTH(a.acilis_tarihi)
                ORDER BY YEAR(a.acilis_tarihi), MONTH(a.acilis_tarihi)
              ";
              $labelTitle = "Aylık";
          } else { // haftalik default
              $sqlCiro = "
                SELECT CONCAT(YEAR(a.acilis_tarihi), '-W', LPAD(WEEK(a.acilis_tarihi,1),2,'0')) AS label, SUM(d.adet * u.fiyat) AS toplam
                FROM adisyon_detay d
                JOIN urunler u ON u.id = d.urun_id
                JOIN adisyonlar a ON a.id = d.adisyon_id
                WHERE a.kapali = 1
                GROUP BY YEAR(a.acilis_tarihi), WEEK(a.acilis_tarihi,1)
                ORDER BY YEAR(a.acilis_tarihi), WEEK(a.acilis_tarihi,1)
              ";
              $labelTitle = "Haftalık";
          }

          $res2 = $conn->query($sqlCiro);
          if ($res2) {
              while ($r = $res2->fetch_assoc()) {
                  $ciroLabels[] = $r['label'];
                  $ciroTotals[] = (float)$r['toplam'];
              }
          }
          ?>

          <div class="mb-4 space-x-2">
            <a href="admin.php?page=ciro&filtre=gunluk" class="px-3 py-1 bg-gray-200 rounded <?= $filtre==='gunluk' ? 'bg-gray-400':'' ?>">Günlük</a>
            <a href="admin.php?page=ciro&filtre=haftalik" class="px-3 py-1 bg-gray-200 rounded <?= $filtre==='haftalik' ? 'bg-gray-400':'' ?>">Haftalık</a>
            <a href="admin.php?page=ciro&filtre=aylik" class="px-3 py-1 bg-gray-200 rounded <?= $filtre==='aylik' ? 'bg-gray-400':'' ?>">Aylık</a>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card overflow-x-auto">
              <h3 class="font-semibold mb-3">Kategori Bazlı Satış (₺)</h3>
              <?php if (count($kategoriLabels) === 0): ?>
                <p class="text-gray-500">Henüz kapalı adisyon yok veya veri yok.</p>
              <?php else: ?>
                <canvas id="kategoriChart" width="400" height="300"></canvas>
              <?php endif; ?>
            </div>

            <div class="card overflow-x-auto">
              <h3 class="font-semibold mb-3"><?= htmlspecialchars($labelTitle) ?> Ciro (₺)</h3>
              <?php if (count($ciroLabels) === 0): ?>
                <p class="text-gray-500">Seçilen dönemde veri yok.</p>
              <?php else: ?>
                <canvas id="ciroChart" width="400" height="300"></canvas>
              <?php endif; ?>
            </div>
          </div>

          <!-- Chart.js -->
          <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
          <script>
            // PHP -> JS veri
            const kategoriLabels = <?= json_encode($kategoriLabels, JSON_UNESCAPED_UNICODE) ?>;
            const kategoriTotals = <?= json_encode($kategoriTotals, JSON_UNESCAPED_UNICODE) ?>;
            const ciroLabels = <?= json_encode($ciroLabels, JSON_UNESCAPED_UNICODE) ?>;
            const ciroTotals = <?= json_encode($ciroTotals, JSON_UNESCAPED_UNICODE) ?>;

            if (document.getElementById('kategoriChart') && kategoriLabels.length) {
              new Chart(document.getElementById('kategoriChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                  labels: kategoriLabels,
                  datasets: [{
                    data: kategoriTotals,
                    backgroundColor: [
                      '#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b',
                      '#858796','#20c997','#6610f2','#fd7e14','#6f42c1'
                    ]
                  }]
                },
                options: {
                  responsive: true,
                  plugins: { legend: { position: 'bottom' } }
                }
              });
            }

            if (document.getElementById('ciroChart') && ciroLabels.length) {
              new Chart(document.getElementById('ciroChart').getContext('2d'), {
                type: 'bar',
                data: {
                  labels: ciroLabels,
                  datasets: [{
                    label: 'Ciro (₺)',
                    data: ciroTotals,
                    backgroundColor: '#4e73df'
                  }]
                },
                options: {
                  responsive: true,
                  scales: {
                    y: { beginAtZero: true }
                  },
                  plugins: { legend: { display: false } }
                }
              });
            }
          </script>
          <?php
          break;

        // ------------------- DEFAULT -------------------
        default:
          echo "<div class='card'><p>Sayfa bulunamadı.</p></div>";
      } // switch
      ?>
    </main>

  </div>
  <script>
    // Sidebar toggle for mobile
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    function openSidebar() {
      sidebar.classList.remove('hidden');
      sidebar.classList.remove('-translate-x-full');
      sidebarOverlay.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }
    function closeSidebar() {
      sidebar.classList.add('-translate-x-full');
      sidebarOverlay.classList.add('hidden');
      setTimeout(() => sidebar.classList.add('hidden'), 200);
      document.body.classList.remove('overflow-hidden');
    }
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', () => {
        sidebar.classList.remove('hidden');
        setTimeout(() => sidebar.classList.remove('-translate-x-full'), 10);
        sidebarOverlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      });
    }
    if (sidebarClose) {
      sidebarClose.addEventListener('click', closeSidebar);
    }
    if (sidebarOverlay) {
      sidebarOverlay.addEventListener('click', closeSidebar);
    }
    // On resize: hide sidebar on mobile if not already
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 768) {
        sidebar.classList.remove('-translate-x-full', 'hidden');
        sidebarOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      } else {
        sidebar.classList.add('hidden', '-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }
    });
  </script>
</body>
</html>