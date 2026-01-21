<?php
session_name("garson_session");
session_start();
date_default_timezone_set('Europe/Istanbul');
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'garson') {
    header("Location: index.php");
    exit;
}

$masa_id = intval($_GET['masa']);

// Adisyon kontrol
$adisyon = $conn->query("SELECT * FROM adisyonlar WHERE masa_id=$masa_id AND kapali=0")->fetch_assoc();
if (!$adisyon) {
    $conn->query("INSERT INTO adisyonlar (masa_id) VALUES ($masa_id)");
    $adisyon_id = $conn->insert_id;
    $adisyon = $conn->query("SELECT * FROM adisyonlar WHERE id=$adisyon_id")->fetch_assoc();
} else {
    $adisyon_id = $adisyon['id'];
}

// Ürün ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['urun_id'])) {
    $urun_id = intval($_POST['urun_id']);
    $adet = max(1, intval($_POST['adet']));
    $aciklama = isset($_POST['aciklama']) ? $conn->real_escape_string($_POST['aciklama']) : NULL;

    $conn->query("INSERT INTO adisyon_detay (adisyon_id, urun_id, adet, aciklama) 
                  VALUES ($adisyon_id, $urun_id, $adet, " . ($aciklama ? "'$aciklama'" : "NULL") . ")");
    $conn->query("UPDATE masalar SET durum=1 WHERE id=$masa_id");
}

// Ürün silme
if (isset($_GET['sil'])) {
    $sil_id = intval($_GET['sil']);
    $conn->query("DELETE FROM adisyon_detay WHERE id=$sil_id");

    $kalan = $conn->query("SELECT COUNT(*) as sayi FROM adisyon_detay WHERE adisyon_id=$adisyon_id")->fetch_assoc();
    if ($kalan['sayi'] == 0) {
        $conn->query("UPDATE masalar SET durum=0 WHERE id=$masa_id");
    }

    header("Location: adisyon.php?masa=$masa_id&kat=" . ($_GET['kat'] ?? ''));
    exit;
}

// Siparişi onayla
if (isset($_POST['onayla'])) {
    $adetKontrol = $conn->query("SELECT COUNT(*) as sayi FROM adisyon_detay WHERE adisyon_id=$adisyon_id")->fetch_assoc();
    
    if ($adetKontrol['sayi'] > 0) {
        $conn->query("UPDATE adisyonlar SET onayli=1 WHERE id=$adisyon_id");
        $adisyon['onayli'] = 1;
        $conn->query("UPDATE masalar SET durum=1 WHERE id=$masa_id");
    } else {
        $conn->query("UPDATE masalar SET durum=0 WHERE id=$masa_id");
        $adisyon['onayli'] = 0;
    }
}

// Kategoriler
$kategoriler = $conn->query("SELECT DISTINCT kategori FROM urunler");
$seciliKategori = $_GET['kat'] ?? ($kategoriler->num_rows > 0 ? $kategoriler->fetch_assoc()['kategori'] : null);

// Ürünler
$urunler = $conn->query("SELECT * FROM urunler WHERE kategori='$seciliKategori'");

// Sipariş Detay
$detay = $conn->query("SELECT adisyon_detay.*, urunler.urun_adi, urunler.fiyat 
                       FROM adisyon_detay 
                       JOIN urunler ON urunler.id=adisyon_detay.urun_id
                       WHERE adisyon_id=$adisyon_id");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Adisyon - Masa <?=$masa_id?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="adisyon-container">
    <h2>🍽 Masa <?=$masa_id?> - Adisyon</h2>

    <!-- Kategoriler -->
    <div class="kategori-bar">
    <?php
    $kategoriler->data_seek(0);
    while($k = $kategoriler->fetch_assoc()) { ?>
        <a href="adisyon.php?masa=<?=$masa_id?>&kat=<?=$k['kategori']?>" 
           class="kategori-btn <?=($k['kategori']==$seciliKategori?'aktif':'')?>"><?=$k['kategori']?></a>
    <?php } ?>
    </div>

    <!-- Ürünler -->
    <div class="urun-grid">
    <?php while($u = $urunler->fetch_assoc()) { ?>
        <button type="button" 
                class="urun-btn" 
                onclick="openNumpad(<?=$u['id']?>, '<?=htmlspecialchars($u['urun_adi'], ENT_QUOTES)?>')">
          <?=$u['urun_adi']?><br><small><?=$u['fiyat']?>₺</small>
        </button>
    <?php } ?>
    </div>

    <!-- Siparişler -->
    <div class="siparis-box">
      <h3>🧾 Mevcut Siparişler <?=($adisyon['onayli'] ? '(Onaylı)' : '(Taslak)')?></h3>
      <ul class="siparis-list">
      <?php 
      $toplam = 0;
      while($d=$detay->fetch_assoc()) { 
          $tutar = $d['fiyat']*$d['adet'];
          $toplam += $tutar;
      ?>
        <li>
            <span><?=$d['urun_adi']?></span>
            <span><?=$d['adet']?> x <?=$d['fiyat']?>₺</span>
            <strong><?=$tutar?>₺</strong>
            <div style="font-size:12px;color:#666;">
              <?= $d['aciklama'] ? "📝 ".htmlspecialchars($d['aciklama']) : "❌ Açıklama yok" ?>
            </div>
            <?php if ($adisyon['onayli'] == 0) { ?>
              <a href="adisyon.php?masa=<?=$masa_id?>&kat=<?=$seciliKategori?>&sil=<?=$d['id']?>" class="delete-btn">❌</a>
            <?php } ?>
        </li>
      <?php } ?>
      </ul>
      <div class="toplam-box">
        <h3>Toplam: <?=$toplam?>₺</h3>
      </div>
    </div>

    <!-- Siparişi Onayla -->
    <?php if ($adisyon['onayli'] == 0) { ?>
      <form method="post">
        <button type="submit" name="onayla" class="btn-confirm">✅ Siparişi Onayla</button>
      </form>
    <?php } ?>

    <a href="masalar.php" class="btn-back">← Masalara Dön</a>
</div>

<!-- Adet Seçim Modal -->
<div id="adetModal" class="modal">
<div class="modal-content">
  <h3 id="urunTitle">Adet Seç</h3>
  <div class="adet-display" id="adetDisplay">0</div>

  <div class="numpad">
    <button onclick="addDigit(1)">1</button>
    <button onclick="addDigit(2)">2</button>
    <button onclick="addDigit(3)">3</button>
    <button onclick="addDigit(4)">4</button>
    <button onclick="addDigit(5)">5</button>
    <button onclick="addDigit(6)">6</button>
    <button onclick="addDigit(7)">7</button>
    <button onclick="addDigit(8)">8</button>
    <button onclick="addDigit(9)">9</button>
    <button onclick="clearAdet()">C</button>
    <button onclick="addDigit(0)">0</button>
    <button onclick="backspace()">←</button>
  </div>

  <textarea id="urunAciklama" placeholder="Açıklama (opsiyonel)" style="width:100%;margin-top:10px;"></textarea>

  <div class="modal-actions">
    <button class="btn-cancel" onclick="closeModal()">İptal</button>
    <button class="btn-confirm" onclick="confirmAdet()">Onayla</button>
  </div>
</div>
</div>

<script>
let selectedUrunId = null;

function openNumpad(urunId, urunAdi) {
    selectedUrunId = urunId;
    document.getElementById("urunTitle").innerText = urunAdi + " - Adet Seç";
    document.getElementById("adetDisplay").innerText = "0";
    document.getElementById("urunAciklama").value = "";
    document.getElementById("adetModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("adetModal").style.display = "none";
    selectedUrunId = null;
}

function addDigit(num) {
    let display = document.getElementById("adetDisplay");
    let current = display.innerText === "0" ? "" : display.innerText;
    display.innerText = current + num;
}

function backspace() {
    let display = document.getElementById("adetDisplay");
    let current = display.innerText;
    display.innerText = current.length > 1 ? current.slice(0, -1) : "0";
}

function clearAdet() {
    document.getElementById("adetDisplay").innerText = "0";
}

function confirmAdet() {
    let adet = parseInt(document.getElementById("adetDisplay").innerText);
    let aciklama = document.getElementById("urunAciklama").value.trim();
    if (!adet || adet <= 0) { alert("Lütfen geçerli bir adet girin."); return; }

    let form = document.createElement("form");
    form.method = "post"; form.action = "";

    let urunInput = document.createElement("input"); urunInput.type = "hidden"; urunInput.name = "urun_id"; urunInput.value = selectedUrunId;
    let adetInput = document.createElement("input"); adetInput.type = "hidden"; adetInput.name = "adet"; adetInput.value = adet;
    let aciklamaInput = document.createElement("input"); aciklamaInput.type = "hidden"; aciklamaInput.name = "aciklama"; aciklamaInput.value = aciklama;

    form.appendChild(urunInput); form.appendChild(adetInput); form.appendChild(aciklamaInput);
    document.body.appendChild(form); form.submit();
}
</script>
</body>
</html>