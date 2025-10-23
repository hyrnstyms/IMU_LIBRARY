<?php
// Oturumu başlat
session_start();

// GÜVENLİK KONTROLÜ: 
// Kullanıcı giriş yapmış mı VE rolü 'uye' mi?
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'uye') {
    // Eğer giriş yapmamışsa veya admin değilse, login sayfasına at
    $_SESSION['login_error'] = "Lütfen önce giriş yapın.";
    header("Location: index.php");
    exit;
}
?>
<html>
	<head>
		<title>İMÜ Kütüphanesi - Üye Paneli</title>
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
                Hoşgeldin, <?php echo htmlspecialchars($_SESSION['user_ad_soyad']); ?>!
            </h1>
			<p>Burası normal üye panelidir. Buradan kitap arayabilir ve ödünç aldığınız kitapları görebilirsiniz.</p>
		</div>
		
		<div id="footer">
			© 2025 İstanbul Medeniyet Üniversitesi - Kütüphane ve Dokümantasyon Daire Başkanlığı
		</div>	
	</div>
	</body>
</html>