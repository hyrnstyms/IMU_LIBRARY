<?php
session_start();
require 'db.php'; // Veritabanı bağlantımız

// 1. GÜVENLİK: Kullanıcı giriş yapmış mı ve 'uye' mi?
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'uye') {
    // Giriş yapmamışsa ana sayfaya at
    header("Location: index.php");
    exit;
}

// 2. ÖDÜNÇ ALINACAK KİTABIN ID'SİNİ AL
// Linkten gelen veriyi alıyoruz (örn: odunc_al.php?kitap_id=5)
if (!isset($_GET['kitap_id'])) {
    header("Location: uye.php"); // Kitap ID'si yoksa üye sayfasına dön
    exit;
}

$kitap_id = $_GET['kitap_id'];
$kullanici_id = $_SESSION['user_id'];

try {
    // 3. STOK KONTROLÜ (UPDATE)
    // Kitabın stoğunu kontrol et VE aynı anda 1 azalt (atomik işlem)
    // Eğer stok 0 ise, UPDATE komutu 0 satırı etkiler
    $stmt = $db->prepare("UPDATE kitaplar SET stok_adeti = stok_adeti - 1 WHERE id = ? AND stok_adeti > 0");
    $stmt->execute([$kitap_id]);

    // 4. İŞLEM KONTROLÜ
    // rowCount(), UPDATE işleminden kaç satırın etkilendiğini söyler
    if ($stmt->rowCount() > 0) {
        // Stok vardı ve 1 azaltıldı, şimdi kaydı oluştur (CREATE)
        $insert_stmt = $db->prepare("INSERT INTO odunc_kayitlari (kullanici_id, kitap_id, odunc_tarihi) VALUES (?, ?, ?)");
        $insert_stmt->execute([$kullanici_id, $kitap_id, date('Y-m-d')]); // date('Y-m-d') bugünün tarihini verir

        $_SESSION['message'] = "Kitap başarıyla ödünç alındı!";
        $_SESSION['message_type'] = "success";
    } else {
        // Stok 0'dı veya kitap bulunamadı
        $_SESSION['message'] = "Hata: Bu kitabın stoğu tükenmiş!";
        $_SESSION['message_type'] = "error";
    }

} catch (PDOException $e) {
    $_SESSION['message'] = "Veritabanı hatası: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
}

// 5. SONUÇ
// İşlem bittikten sonra üye sayfasına geri dön
header("Location: uye.php");
exit;
?>