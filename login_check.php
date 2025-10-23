<?php
// Session'ı (Oturum) başlat. Kullanıcı bilgilerini saklamak için bu şart.
session_start();

// Veritabanı bağlantı dosyamızı çağır
require 'db.php';

// Form POST metodu ile mi gönderildi?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $kulad = $_POST['kullanici'];
    $sifre = $_POST['sifre'];

    try {
        // SQL Injection saldırılarını önlemek için 'prepared statement' kullanıyoruz
        $stmt = $db->prepare("SELECT * FROM kullanicilar WHERE kulad = ?");
        $stmt->execute([$kulad]);
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        // 1. Kullanıcı bulundu mu?
        // 2. Bulunduysa, girilen şifre veritabanındaki hash'li şifre ile eşleşiyor mu?
        if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
            
            // BAŞARILI GİRİŞ!
            // Kullanıcı bilgilerini session'a kaydediyoruz (Hocanın istediği)
            $_SESSION['user_id'] = $kullanici['id'];
            $_SESSION['user_kulad'] = $kullanici['kulad'];
            $_SESSION['user_ad_soyad'] = $kullanici['ad_soyad'];
            $_SESSION['user_rol'] = $kullanici['rol'];

            // Rol'e göre farklı sayfalara yönlendir (Hocanın istediği)
            if ($kullanici['rol'] == 'admin') {
                header("Location: admin.php"); // Admin sayfasına git
                exit;
            } else {
                header("Location: uye.php"); // Üye sayfasına git
                exit;
            }

        } else {
            // HATALI GİRİŞ
            // Hata mesajını session'a kaydet ve login sayfasına geri dön
            $_SESSION['login_error'] = "Kullanıcı adı veya şifre hatalı!";
            header("Location: index.php");
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['login_error'] = "Bir veritabanı hatası oluştu: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
} else {
    // Eğer bu sayfaya form dışından (direkt URL yazarak) gelinirse
    header("Location: index.php");
    exit;
}
?>