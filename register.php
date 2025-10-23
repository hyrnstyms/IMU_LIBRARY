<?php
// Session'ı başlat (hata veya başarı mesajlarını göstermek için)
session_start();
?>
<html>
<head>
    <title>İMÜ Kütüphanesi - Kayıt Ol</title>
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
                <li><a href="index.php">Ana Sayfa</a></li>
                <li><a href="hakkimizda.php">Hakkımızda</a></li>
                <li><a href="misyon-vizyon.php">Misyon & Vizyon</a></li>
                <li><a href="iletisim.php">İletişim</a></li>
            </ul>
        </div>
    </div>
    
    <div id="content">
        <h1 class="content-title">Yeni Üye Kaydı</h1>
        <div class="form-container">
            <h2>Kayıt Formu</h2>

            <?php
            // PHP: Hata veya başarı mesajlarını göster
            if (isset($_SESSION['register_error'])) {
                echo '<p style="color: red; text-align: center; font-weight: bold;">' . $_SESSION['register_error'] . '</p>';
                unset($_SESSION['register_error']);
            }
            if (isset($_SESSION['register_success'])) {
                echo '<p style="color: green; text-align: center; font-weight: bold;">' . $_SESSION['register_success'] . '</p>';
                unset($_SESSION['register_success']);
            }
            ?>

            <form action="register_check.php" method="POST">
                <div class="form-group">
                    <label for="ad_soyad">Ad Soyad:</label>
                    <input type="text" id="ad_soyad" name="ad_soyad" required>
                </div>
                <div class="form-group">
                    <label for="kullanici">Kullanıcı Adı:</label>
                    <input type="text" id="kullanici" name="kullanici" required>
                </div>
                <div class="form-group">
                    <label for="sifre">Şifre:</label>
                    <input type="password" id="sifre" name="sifre" required>
                </div>
                <button type="submit" class="btn">Kayıt Ol</button>
            </form>
            <p style="text-align:center; margin-top:15px;">
                Zaten hesabınız var mı? <a href="index.php">Giriş Yapın</a>
            </p>
        </div>
    </div>
    
    <div id="footer">
        © 2025 İstanbul Medeniyet Üniversitesi - Kütüphane ve Dokümantasyon Daire Başkanlığı
    </div>
</div>
</body>
</html>