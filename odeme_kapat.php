<?php
session_name("kasiyer_session");
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kasiyer') {
    http_response_code(403);
    exit("error: yetkisiz erişim");
}

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['masa_id']) && isset($data['payments'])){
    $masa_id = intval($data['masa_id']);
    $payments = $data['payments'];

    // Açık ve onaylı adisyon bul
    $sorgu = $conn->prepare("SELECT id FROM adisyonlar WHERE masa_id=? AND kapali=0 AND onayli=1 LIMIT 1");
    $sorgu->bind_param("i", $masa_id);
    $sorgu->execute();
    $result = $sorgu->get_result();
    $adisyon = $result->fetch_assoc();

    if($adisyon){
        $adisyon_id = $adisyon['id'];

        // Ödemeleri kaydet
        $stmt = $conn->prepare("INSERT INTO odemeler (adisyon_id, odeme_tutari, odeme_yontemi) VALUES (?,?,?)");
        foreach($payments as $p){
            $tutar = floatval($p['amount']);
            $yontem = $p['method'];
            $stmt->bind_param("ids", $adisyon_id, $tutar, $yontem);
            $stmt->execute();
        }

        // Adisyon kapat
        $guncelle = $conn->prepare("UPDATE adisyonlar SET kapali=1 WHERE id=?");
        $guncelle->bind_param("i", $adisyon_id);
        $guncelle->execute();

        // Masa boşalt
        $masa = $conn->prepare("UPDATE masalar SET durum=0 WHERE id=?");
        $masa->bind_param("i", $masa_id);
        $masa->execute();

        echo "success";
    } else {
        echo "error: açık/onaylı adisyon bulunamadı!";
    }
} else {
    echo "error: eksik veri!";
}