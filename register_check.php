<?php
session_start();
require 'db.php'; // Veritabanı bağlantımızı dahil et

// Form gönderildi mi?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $ad_soyad = $_POST['ad_soyad'];
    $kulad = $_POST['kullanici'];
    $sifre = $_POST['sifre'];

    // GÜVENLİK: Şifreyi asla düz metin kaydetme! Hash'le.
    $hashed_sifre = password_hash($sifre, PASSWORD_DEFAULT);

    try {
        // 1. Önce bu kullanıcı adı daha önce alınmış mı diye kontrol et
        $stmt = $db->prepare("SELECT id FROM kullanicilar WHERE kulad = ?");
        $stmt->execute([$kulad]);
        
        if ($stmt->fetch()) {
            // Eğer fetch() bir sonuç döndürürse, bu kullanıcı adı DOLU demektir
            $_SESSION['register_error'] = "Bu kullanıcı adı zaten alınmış!";
            header("Location: register.php"); // Kayıt formuna geri dön
            exit;
        }

        // 2. Kullanıcı adı boşta. Şimdi YENİ KULLANICIYI EKLE (CREATE)
        // Yeni kayıtların rolünü varsayılan olarak 'uye' yapıyoruz
        $stmt = $db->prepare("INSERT INTO kullanicilar (ad_soyad, kulad, sifre, rol) VALUES (?, ?, ?, 'uye')");
        $stmt->execute([$ad_soyad, $kulad, $hashed_sifre]);

        // Başarılı olduğuna dair mesaj ver
        $_SESSION['register_success'] = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
        header("Location: register.php"); // Kayıt formuna geri dön
        exit;

    } catch (PDOException $e) {
        $_SESSION['register_error'] = "Veritabanı hatası: " . $e->getMessage();
        header("Location: register.php");
        exit;
    }

} else {
    // Forma basmadan direkt bu adrese gelirlerse, ana sayfaya at
    header("Location: index.php");
    exit;
}
?>