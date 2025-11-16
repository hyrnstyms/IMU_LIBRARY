<?php
// Oturumu başlat
session_start();

// GÜVENLİK KONTROLÜ: 
// Kullanıcı giriş yapmış mı VE rolü 'admin' mi?
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    // Eğer giriş yapmamışsa veya admin değilse, login sayfasına at
    $_SESSION['login_error'] = "Bu sayfaya erişim yetkiniz yok!";
    header("Location: index.php");
    exit;
}
?>
<html>
	<head>
		<title>İMÜ Kütüphanesi - Admin Paneli</title>
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
                    <li><a href="profil.php">Profilim</a></li>
					<li><a href="logout.php" style="background-color: #c9302c; color:white;">Güvenli Çıkış</a></li>
				</ul>
			</div>
		</div>
		
		<div id="content">
            <h1 class="content-title">
                Yönetim Paneli (Hoşgeldin, <?php echo htmlspecialchars($_SESSION['user_ad_soyad']); ?>!)
            </h1>

			<p>Burası güvenli admin panelidir. Sadece admin rolüne sahip kullanıcılar görebilir.</p>
			
			<style>
				.admin-buttons { margin-top: 30px; text-align: center; } 
				.admin-buttons a {
					display: inline-block; width: 200px; padding: 20px;
					margin: 10px; background-color: #f4f4f4; border: 1px solid #ddd;
					text-align: center; text-decoration: none; color: #333;
					font-size: 16px; font-weight: bold; border-radius: 5px;
					transition: background-color 0.3s;
				}
				.admin-buttons a:hover { background-color: #e0e0e0; border-color: #ccc; }
			</style>
			
			<div class="admin-buttons">
				<a href="kitap_yonet.php">Kitap Ekle / Sil/Güncelle</a>
				<a href="uyeler_yonet.php">Üyeleri Yönet(Sil/Engelle)</a>
				<a href="iade_yonet.php">Ödünç/İade Yönetimi</a>
				
                <a href="#">Duyuru Yayınla</a>
				<a href="#">Raporlar</a>
				<a href="#">Ayarlar</a>
			</div>
		</div>
		
		<div id="footer">
			© 2025 İstanbul Medeniyet Üniversitesi - Kütüphane ve Dokümantasyon Daire Başkanlığı
		</div>
	</div>
	</body>
</html>