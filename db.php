<?php
$host = 'localhost';
$dbname = 'kutuphane_db'; // phpMyAdmin'de oluşturduğun veritabanı
$user = 'root'; // XAMPP için varsayılan kullanıcı adı
$pass = ''; // XAMPP için varsayılan şifre (boştur)

try {
    // PDO (PHP Data Objects) ile güvenli bağlantı
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Hata modunu ayarla
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Bağlantı hatası olursa programı durdur ve mesaj ver
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>