<?php
session_start();
require 'db.php'; // Veritabanı bağlantımız

// GÜVENLİK: Sadece adminler bu sayfayı görebilir
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    $_SESSION['login_error'] = "Bu sayfaya erişim yetkiniz yok!";
    header("Location: index.php");
    exit;
}

$message = '';
$message_type = 'success';

// İŞLEM 1: KİTAP İADE ALMA (UPDATE)
// Linkten 'iade_id' (bu odunc_kayitlari tablosunun id'sidir) geldiyse...
if (isset($_GET['iade_id'])) {
    $odunc_id = $_GET['iade_id'];

    try {
        // 1. Önce, iade edilecek kayıttan 'kitap_id'yi bulmamız lazım (stoğu artırmak için)
        $stmt_kitap_id = $db->prepare("SELECT kitap_id FROM odunc_kayitlari WHERE id = ? AND iade_tarihi IS NULL");
        $stmt_kitap_id->execute([$odunc_id]);
        $kayit = $stmt_kitap_id->fetch();

        if ($kayit) {
            $kitap_id = $kayit['kitap_id'];

            // 2. odunc_kayitlari tablosunu güncelle (iade tarihini bugüne ayarla)
            $stmt_iade = $db->prepare("UPDATE odunc_kayitlari SET iade_tarihi = ? WHERE id = ?");
            $stmt_iade->execute([date('Y-m-d'), $odunc_id]);

            // 3. kitaplar tablosundaki stoğu 1 artır
            $stmt_stok = $db->prepare("UPDATE kitaplar SET stok_adeti = stok_adeti + 1 WHERE id = ?");
            $stmt_stok->execute([$kitap_id]);

            $message = "Kitap başarıyla iade alındı!";
        } else {
            $message = "Hata: İade edilecek kayıt bulunamadı veya zaten iade edilmiş.";
            $message_type = "error";
        }
    } catch (PDOException $e) {
        $message = "Hata: " . $e->getMessage();
        $message_type = "error";
    }
}

// İŞLEM 2: TÜM ÖDÜNÇLERİ LİSTELEME (READ)
// (3 tabloyu (odunc_kayitlari, kullanicilar, kitaplar) JOIN ile birleştiriyoruz)
$stmt_oduncler = $db->prepare("
    SELECT 
        o.id as odunc_id, 
        k.kitap_adi, 
        u.ad_soyad, 
        o.odunc_tarihi
    FROM odunc_kayitlari o
    JOIN kitaplar k ON o.kitap_id = k.id
    JOIN kullanicilar u ON o.kullanici_id = u.id
    WHERE o.iade_tarihi IS NULL
    ORDER BY o.odunc_tarihi ASC
");
$stmt_oduncler->execute();
$odunc_listesi = $stmt_oduncler->fetchAll(PDO::FETCH_ASSOC);

?>
<html>
<head>
    <title>Ödünç/İade Yönetimi - Admin Paneli</title>
    <link rel="stylesheet" href="css/genel.css">
    <style>
        .kitap-tablosu {
            width: 100%; margin-top: 20px; border-collapse: collapse; text-align: left;
        }
        .kitap-tablosu th, .kitap-tablosu td {
            padding: 10px; border: 1px solid #ddd; color: #333;
        }
        .kitap-tablosu th { background-color: #f9f9f9; font-weight: bold; }
        .btn-iade-al {
            color: white; background-color: #5cb85c; padding: 5px 10px;
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
                <li><a href="admin.php">Admin Panele Dön</a></li>
                <li><a href="logout.php" style="background-color: #c9302c; color:white;">Güvenli Çıkış</a></li>
            </ul>
        </div>
    </div>

    <div id="content">
        <h1 class="content-title">Ödünç/İade Yönetimi</h1>
        
        <?php if ($message): ?>
            <p style="color: <?php echo $message_type == 'error' ? 'red' : 'green'; ?>; font-weight: bold; text-align: center;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <h2 class="content-subtitle" style="text-align: left;">Dışarıdaki Kitaplar (İade Bekleyenler)</h2>
        <table class="kitap-tablosu">
            <thead>
                <tr>
                    <th>Kitap Adı</th>
                    <th>Üyenin Adı Soyadı</th>
                    <th>Ödünç Aldığı Tarih</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($odunc_listesi as $kayit): ?>
                <tr>
                    <td><?php echo htmlspecialchars($kayit['kitap_adi']); ?></td>
                    <td><?php echo htmlspecialchars($kayit['ad_soyad']); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($kayit['odunc_tarihi'])); ?></td>
                    <td>
                        <a href="iade_yonet.php?iade_id=<?php echo $kayit['odunc_id']; ?>" class="btn-iade-al" onclick="return confirm('Bu kitabı iade olarak işaretlemek istediğinize emin misiniz?');">İade Al</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($odunc_listesi)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #777;">Dışarıda ödünç kitap bulunmuyor.</td>
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