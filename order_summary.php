<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();
include "db.php";

header('Content-Type: application/json');

$masa = intval($_GET['masa'] ?? 0);
if ($masa <= 0) {
    echo json_encode(["items" => [], "total" => 0]);
    exit;
}

$res = $conn->query("SELECT id FROM adisyonlar WHERE masa_id=$masa AND kapali=0 LIMIT 1");
$ad = $res->fetch_assoc();
if (!$ad) {
    echo json_encode(["items" => [], "total" => 0]);
    exit;
}
$adisyon_id = $ad['id'];

$q = $conn->query("
    SELECT d.id, d.adet, u.urun_adi, u.fiyat
    FROM adisyon_detay d
    JOIN urunler u ON u.id = d.urun_id
    WHERE d.adisyon_id = $adisyon_id
");

$items = [];
$total = 0;

while ($row = $q->fetch_assoc()) {
    $tutar = $row['adet'] * $row['fiyat'];
    $items[] = [
        "id" => (int)$row['id'],
        "urun" => $row['urun_adi'],
        "adet" => (int)$row['adet'],
        "fiyat" => (float)$row['fiyat'],
        "tutar" => $tutar
    ];
    $total += $tutar;
}

echo json_encode([
    "items" => $items,
    "total" => $total
]);