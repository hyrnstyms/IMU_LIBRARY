<?php
session_start();
require 'db.php'; // Veritabanı bağlantımız

// GÜVENLİK: Sadece adminler bu sayfayı görebilir
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    $_SESSION['login_error'] = "Bu sayfaya erişim yetkiniz yok!";
    header("Location: index.php");
    exit;
}

$admin_id = $_SESSION['user_id']; // Kendi kendini silmemesi/pasif yapmaması için
$message = '';
$message_type = 'success';

// İŞLEM 1: ÜYE SİLME (DELETE)
if (isset($_GET['sil_id'])) {
    $sil_id = $_GET['sil_id'];
    if ($sil_id == $admin_id) {
        $message = "Hata: Kendinizi silemezsiniz!";
        $message_type = "error";
    } else {
        try {
            $stmt = $db->prepare("DELETE FROM kullanicilar WHERE id = ?");
            $stmt->execute([$sil_id]);
            $message = "Kullanıcı başarıyla silindi!";
        } catch (PDOException $e) {
            $message = "Hata: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// İŞLEM 2: ÜYE DURUM GÜNCELLEME (UPDATE)
if (isset($_GET['durum_id']) && isset($_GET['yeni_durum'])) {
    $durum_id = $_GET['durum_id'];
    $yeni_durum = $_GET['yeni_durum'] == 'pasif' ? 'pasif' : 'aktif'; // Güvenlik
    
    if ($durum_id == $admin_id) {
        $message = "Hata: Kendi durumunuzu değiştiremezsiniz!";
        $message_type = "error";
    } else {
        try {
            $stmt = $db->prepare("UPDATE kullanicilar SET durum = ? WHERE id = ?");
            $stmt->execute([$yeni_durum, $durum_id]);
            $message = "Kullanıcı durumu güncellendi!";
        } catch (PDOException $e) {
            $message = "Hata: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// İŞLEM 3: ÜYELERİ LİSTELEME (READ)
// Kendi dışındaki tüm kullanıcıları listele
$kullanicilar = $db->prepare("SELECT id, kulad, ad_soyad, rol, durum FROM kullanicilar WHERE id != ?");
$kullanicilar->execute([$admin_id]);
$kullanicilar = $kullanicilar->fetchAll(PDO::FETCH_ASSOC);

?>
<html>
<head>
    <title>Üye Yönetimi - Admin Paneli</title>
    <link rel="stylesheet" href="css/genel.css">
    <style>
        .kitap-tablosu {
            width: 100%; margin-top: 20px; border-collapse: collapse; text-align: left;
        }
        .kitap-tablosu th, .kitap-tablosu td {
            padding: 10px; border: 1px solid #ddd; color: #333;
        }
        .kitap-tablosu th { background-color: #f9f9f9; font-weight: bold; }
        .btn-sil {
            color: white; background-color: #c9302c; padding: 5px 10px;
            text-decoration: none; border-radius: 4px; font-size: 14px;
        }
        .btn-durum {
            color: white; padding: 5px 10px; text-decoration: none;
            border-radius: 4px; font-size: 14px;
        }
        .btn-durum.aktif { background-color: #5cb85c; }
        .btn-durum.pasif { background-color: #f0ad4e; }
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
                <li><a href="admin.php">Admin Panele Dön</a></li>
                <li><a href="profil.php">Profilim</a></li>
                <li><a href="logout.php" style="background-color: #c9302c; color:white;">Güvenli Çıkış</a></li>
            </ul>
        </div>
    </div>

    <div id="content">
        <h1 class="content-title">Üye Yönetimi (Sil / Engelle)</h1>

        <?php if ($message): ?>
            <p style="color: <?php echo $message_type == 'error' ? 'red' : 'green'; ?>; font-weight: bold; text-align: center;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>
        
        <h2 class="content-subtitle" style="text-align: left;">Mevcut Üyeler</h2>
        <table class="kitap-tablosu">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı Adı</th>
                    <th>Ad Soyad</th>
                    <th>Rol</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kullanicilar as $kullanici): ?>
                <tr>
                    <td><?php echo $kullanici['id']; ?></td>
                    <td><?php echo htmlspecialchars($kullanici['kulad']); ?></td>
                    <td><?php echo htmlspecialchars($kullanici['ad_soyad']); ?></td>
                    <td><?php echo $kullanici['rol']; ?></td>
                    <td>
                        <span style="font-weight: bold; color: <?php echo $kullanici['durum'] == 'aktif' ? 'green' : 'red'; ?>;">
                            <?php echo ucfirst($kullanici['durum']); // Baş harfi büyük (Aktif/Pasif) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($kullanici['durum'] == 'aktif'): ?>
                            <a href="uyeler_yonet.php?durum_id=<?php echo $kullanici['id']; ?>&yeni_durum=pasif" class="btn-durum pasif">Engelle (Pasif Yap)</a>
                        <?php else: ?>
                            <a href="uyeler_yonet.php?durum_id=<?php echo $kullanici['id']; ?>&yeni_durum=aktif" class="btn-durum aktif">Aktif Yap</a>
                        <?php endif; ?>
                        
                        <a href="uyeler_yonet.php?sil_id=<?php echo $kullanici['id']; ?>" class="btn-sil" onclick="return confirm('Bu kullanıcıyı KALICI olarak silmek istediğinize emin misiniz?');">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if (empty($kullanicilar)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #777;">Yönetilecek başka kullanıcı bulunamadı.</td>
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