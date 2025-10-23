<?php
session_start();
require 'db.php'; // Veritabanı bağlantısı

// GÜVENLİK: Giriş yapmamış kimse bu sayfayı göremez
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Giriş yapan kullanıcının ID'si

// Form gönderildi mi?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eski_sifre = $_POST['eski_sifre'];
    $yeni_sifre = $_POST['yeni_sifre'];

    try {
        // 1. Mevcut şifreyi kontrol etmek için kullanıcıyı DB'den oku (READ)
        $stmt = $db->prepare("SELECT sifre FROM kullanicilar WHERE id = ?");
        $stmt->execute([$user_id]);
        $kullanici = $stmt->fetch();

        // 2. Girilen 'eski şifre' doğru mu?
        if ($kullanici && password_verify($eski_sifre, $kullanici['sifre'])) {
            
            // 3. Eski şifre doğru. Yeni şifreyi hash'le
            $hashed_yeni_sifre = password_hash($yeni_sifre, PASSWORD_DEFAULT);

            // 4. Veritabanını yeni şifreyle güncelle (UPDATE)
            $update_stmt = $db->prepare("UPDATE kullanicilar SET sifre = ? WHERE id = ?");
            $update_stmt->execute([$hashed_yeni_sifre, $user_id]);

            $_SESSION['profil_success'] = "Şifreniz başarıyla güncellendi!";

        } else {
            // Eski şifre yanlıştı
            $_SESSION['profil_error'] = "Eski şifrenizi yanlış girdiniz!";
        }
    } catch (PDOException $e) {
        $_SESSION['profil_error'] = "Veritabanı hatası: " . $e->getMessage();
    }
    
    // Mesajı göstermek için sayfayı kendine yönlendir
    header("Location: profil.php");
    exit;
}
?>
<html>
<head>
    <title>İMÜ Kütüphanesi - Profilim</title>
    <link rel="stylesheet" href="css/genel.css">
</head>
<body class="body">
<div id="container">
    <div id="header">
        <div id="logo">
            <a target="_blank" href="http:www.medeniyet.edu.tr">   
                <img style="margin-top:15px" src="logo.png" alt="imü logo">
            </a>
        </div>
        <div id="menu">
             <ul>
                <?php if ($_SESSION['user_rol'] == 'admin'): ?>
                    <li><a href="admin.php">Admin Paneline Dön</a></li>
                <?php else: ?>
                    <li><a href="uye.php">Üye Paneline Dön</a></li>
                <?php endif; ?>
                <li><a href="logout.php" style="background-color: #c9302c; color:white;">Güvenli Çıkış</a></li>
            </ul>
        </div>
    </div>
    
    <div id="content">
        <h1 class="content-title">Profilim (Kullanıcı: <?php echo htmlspecialchars($_SESSION['user_kulad']); ?>)</h1>
        
        <div class="form-container">
            <h2>Şifre Değiştir</h2>
            
            <?php
            // Hata veya başarı mesajları
            if (isset($_SESSION['profil_error'])) {
                echo '<p style="color: red; text-align: center; font-weight: bold;">' . $_SESSION['profil_error'] . '</p>';
                unset($_SESSION['profil_error']);
            }
            if (isset($_SESSION['profil_success'])) {
                echo '<p style="color: green; text-align: center; font-weight: bold;">' . $_SESSION['profil_success'] . '</p>';
                unset($_SESSION['profil_success']);
            }
            ?>
            
            <form action="profil.php" method="POST">
                <div class="form-group">
                    <label for="eski_sifre">Eski Şifre:</label>
                    <input type="password" id="eski_sifre" name="eski_sifre" required>
                </div>
                <div class="form-group">
                    <label for="yeni_sifre">Yeni Şifre:</label>
                    <input type="password" id="yeni_sifre" name="yeni_sifre" required>
                </div>
                <button type="submit" class="btn">Şifremi Güncelle</button>
            </form>
        </div>
    </div>
    
    <div id="footer">
        © 2025 İstanbul Medeniyet Üniversitesi - Kütüphane ve Dokümantasyon Daire Başkanlığı
    </div>
</div>
</body>
</html>