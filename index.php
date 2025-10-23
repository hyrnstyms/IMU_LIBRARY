<?php
// Oturumu başlat (Hata mesajlarını veya "zaten giriş yapmışsın" bilgisini okumak için)
session_start();

// Eğer kullanıcı zaten giriş yapmışsa, onu ilgili sayfaya yönlendir
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_rol'] == 'admin') {
        header("Location: admin.php");
        exit;
    } else {
        header("Location: uye.php");
        exit;
    }
}
?>
<html>
	<head>
		<title>İMÜ Kütüphanesi - Ana Sayfa</title>
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
			<h1 class="content-title">
				İMÜ Kütüphanesi'ne Hoş Geldiniz
			</h1>
			<p>Gerekli bilgilere ve hizmetlere ulaşmak için lütfen üstteki menüyü kullanınız.</p>
			<hr style="border:0; border-top: 1px solid #ccc; margin: 30px 0;">

			<div class="form-container" style="margin-top: 10px;">
				<h2>Personel & Üye Girişi</h2>

                <?php
                // PHP: Eğer bir giriş hatası varsa (login_check.php'den gelen)
                if (isset($_SESSION['login_error'])) {
                    // Hatayı kırmızı renkte göster
                    echo '<p style="color: red; text-align: center; font-weight: bold;">' . $_SESSION['login_error'] . '</p>';
                    // Mesajı gösterdikten sonra sil (sayfa yenilenince kaybolsun)
                    unset($_SESSION['login_error']);
                }
                ?>

				<form action="login_check.php" method="POST">
					<div class="form-group">
						<label for="kullanici">Kullanıcı Adı:</label>
						<input type="text" id="kullanici" name="kullanici" required>
					</div>
					<div class="form-group">
						<label for="sifre">Şifre:</label>
						<input type="password" id="sifre" name="sifre" required>
					</div>
					<button type="submit" class="btn">Giriş Yap</button>
				</form>
                
                <p style="text-align:center; margin-top:15px;">
                    Hesabınız yok mu? <a href="register.php">Kayıt Olun</a>
                </p>
                </div>
		</div>
		
		<div id="footer">
			© 2025 İstanbul Medeniyet Üniversitesi - Kütüphane ve Dokümantasyon Daire Başkanlığı
		</div>
	</div>
	</body>
</html>