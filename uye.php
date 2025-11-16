<?php
session_start();
require 'db.php'; // Veritabanı bağlantımızı ekledik

// GÜVENLİK KONTROLÜ
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'uye') {
    $_SESSION['login_error'] = "Lütfen önce giriş yapın.";
    header("Location: index.php");
    exit;
}

$kullanici_id = $_SESSION['user_id'];

// 1. OKUMA (READ): Üyenin elindeki (iade etmediği) kitapları çek
// (İki tabloyu 'JOIN' ile birleştiriyoruz)
$stmt_mevcut = $db->prepare("
    SELECT k.kitap_adi, k.yazar, o.odunc_tarihi 
    FROM odunc_kayitlari o
    JOIN kitaplar k ON o.kitap_id = k.id
    WHERE o.kullanici_id = ? AND o.iade_tarihi IS NULL
");
$stmt_mevcut->execute([$kullanici_id]);
$mevcut_kitaplar = $stmt_mevcut->fetchAll(PDO::FETCH_ASSOC);

// 2. OKUMA (READ): Kütüphanedeki (stoğu olan) tüm kitapları çek
$stmt_diger = $db->prepare("SELECT * FROM kitaplar WHERE stok_adeti > 0");
$stmt_diger->execute();
$diger_kitaplar = $stmt_diger->fetchAll(PDO::FETCH_ASSOC);

// Mesajları al ve temizle
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

?>
<html>
	<head>
		<title>İMÜ Kütüphanesi - Üye Paneli</title>
		<link rel="stylesheet" href="css/genel.css">
        <style>
            .kitap-tablosu {
                width: 100%; margin-top: 20px; border-collapse: collapse; text-align: left;
            }
            .kitap-tablosu th, .kitap-tablosu td {
                padding: 10px; border: 1px solid #ddd; color: #333;
            }
            .kitap-tablosu th { background-color: #f9f9f9; font-weight: bold; }
            .btn-odunc-al {
                color: white; background-color: #007b5e; padding: 5px 10px;
                text-decoration: none; border-radius: 4px; font-size: 14px;
            }
        </style>
	</head>	
	<body class="body">
	<div id="container">	
		<div id="header">
			<div id="logo">
				<a target="_blank" href="http:www.medeniyet.edu.tr"><img style="margin-top:15px" src="logo.png" alt="imü logo"></a>
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
            
            <?php if ($message): ?>
                <p style="color: <?php echo $message_type == 'error' ? 'red' : 'green'; ?>; font-weight: bold; text-align: center;">
                    <?php echo $message; ?>
                </p>
            <?php endif; ?>

            <h2 class="content-subtitle" style="text-align: left;">Şu An Elinizde Bulunan Kitaplar</h2>
            <table class="kitap-tablosu">
                <thead>
                    <tr><th>Kitap Adı</th><th>Yazar</th><th>Ödünç Alma Tarihi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($mevcut_kitaplar as $kitap): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($kitap['kitap_adi']); ?></td>
                        <td><?php echo htmlspecialchars($kitap['yazar']); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($kitap['odunc_tarihi'])); // Tarihi formatla ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($mevcut_kitaplar)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #777;">Şu an elinizde ödünç kitap bulunmuyor.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <br>

            <h2 class="content-subtitle" style="text-align: left;">Kütüphanedeki Kitaplar (Ödünç Al)</h2>
            <table class="kitap-tablosu">
                <thead>
                    <tr><th>Kitap Adı</th><th>Yazar</th><th>Stok</th><th>İşlem</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($diger_kitaplar as $kitap): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($kitap['kitap_adi']); ?></td>
                        <td><?php echo htmlspecialchars($kitap['yazar']); ?></td>
                        <td><?php echo $kitap['stok_adeti']; ?></td>
                        <td>
                            <a href="odunc_al.php?kitap_id=<?php echo $kitap['id']; ?>" class="btn-odunc-al" onclick="return confirm('Bu kitabı ödünç almak istediğinize emin misiniz?');">Ödünç Al</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($diger_kitaplar)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #777;">Kütüphanede stoğu olan kitap bulunamadı.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
		</div>
		
		<div id="footer">
			© 2025 İstanbul Medeniyet Üniversitesi - Kütüphane ve Dokümantasyon Daire Başkanlığı
		</div>	
	</div>
	</body>
</html>