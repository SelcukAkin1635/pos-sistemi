<?php
include "db.php";

// Ürünleri kategorilere göre çekelim
$res = $conn->query("SELECT * FROM urunler ORDER BY kategori, urun_adi");
$urunler = [];
while ($row = $res->fetch_assoc()) {
    $urunler[$row['kategori']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>📱 QR Menü</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

  <div class="max-w-5xl mx-auto p-6">
    <h1 class="text-5xl font-extrabold text-center mb-12 text-gradient bg-clip-text text-transparent from-amber-500 via-orange-500 to-red-500">
      🍽️ QR Menü
    </h1>

    <?php foreach ($urunler as $kategori => $liste): ?>
      <section class="mb-12">
        <h2 class="text-3xl font-semibold text-white bg-gradient-to-r from-amber-500 via-orange-400 to-red-500 rounded-xl px-6 py-3 mb-6 shadow-lg">
          <?= htmlspecialchars($kategori ?: "Diğer") ?>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          <?php foreach ($liste as $u): ?>
            <div class="bg-white rounded-2xl shadow-xl p-6 flex flex-col justify-between hover:scale-105 hover:shadow-2xl transition-transform duration-300">
              <div>
                <h3 class="text-2xl font-bold mb-3"><?= htmlspecialchars($u['urun_adi']) ?></h3>
                <p class="text-gray-800 text-lg mb-2 font-medium"><?= number_format($u['fiyat'], 2) ?> ₺</p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>

  </div>

</body>
</html>