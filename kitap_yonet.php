<?php
session_start();
require 'db.php'; // Veritabanı bağlantımızı dahil et

// GÜVENLİK: Sadece adminler bu sayfayı görebilir
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    $_SESSION['login_error'] = "Bu sayfaya erişim yetkiniz yok!";
    header("Location: index.php");
    exit;
}

// MESAJLARI TEMİZLEMEK İÇİN
$message = '';
$message_type = '';

// İŞLEM 1: KİTAP EKLEME (CREATE)
// Formdan 'kitap_adi' verisi geldiyse...
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kitap_adi'])) {
    try {
        $stmt = $db->prepare("INSERT INTO kitaplar (kitap_adi, yazar, stok_adeti) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['kitap_adi'],
            $_POST['yazar'],
            $_POST['stok_adeti']
        ]);
        $message = "Kitap başarıyla eklendi!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Hata: " . $e->getMessage();
        $message_type = "error";
    }
}

// İŞLEM 2: KİTAP SİLME (DELETE)
// Adres çubuğundan 'sil_id' verisi geldiyse...
if (isset($_GET['sil_id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM kitaplar WHERE id = ?");
        $stmt->execute([$_GET['sil_id']]);
        $message = "Kitap başarıyla silindi!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Hata: " . $e->getMessage();
        $message_type = "error";
    }
}

// İŞLEM 3: KİTAPLARI LİSTELEME (READ)
// Sayfa her yüklendiğinde veritabanındaki tüm kitapları çek
$kitaplar = $db->query("SELECT * FROM kitaplar ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<html>
<head>
    <title>Kitap Yönetimi - Admin Paneli</title>
    <link rel="stylesheet" href="css/genel.css">
    <style>
        /* Bu sayfaya özel stiller */
        .form-container.inline-form {
            width: 100%;
            margin: 20px auto;
            text-align: left;
            background-color: #f0f0f0;
        }
        .form-container.inline-form form {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .form-container.inline-form .form-group {
            flex-grow: 1;
            margin-right: 10px;
        }
        .form-container.inline-form .btn {
            width: auto;
            flex-grow: 0;
            height: 38px; /* Input ile aynı yükseklik */
        }
        
        /* Kitap listesi tablosu */
        .kitap-tablosu {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: left;
        }
        .kitap-tablosu th, .kitap-tablosu td {
            padding: 10px;
            border: 1px solid #ddd;
            color: #333; /* İçerik açık mavi olunca yazı rengini düzelt */
        }
        .kitap-tablosu th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .kitap-tablosu .btn-sil {
            color: white;
            background-color: #c9302c;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
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
                <li><a href="profil.php">Profilim</a></li>
                <li><a href="logout.php" style="background-color: #c9302c; color:white;">Güvenli Çıkış</a></li>
            </ul>
        </div>
    </div>

    <div id="content">
        <h1 class="content-title">Kitap Yönetimi (Ekle / Sil / Listele)</h1>

        <?php if ($message): ?>
            <p style="color: <?php echo $message_type == 'error' ? 'red' : 'green'; ?>; font-weight: bold; text-align: center;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <div class="form-container inline-form">
            <h2>Yeni Kitap Ekle</h2>
            <form action="kitap_yonet.php" method="POST">
                <div class="form-group">
                    <label for="kitap_adi">Kitap Adı:</label>
                    <input type="text" id="kitap_adi" name="kitap_adi" required>
                </div>
                <div class="form-group">
                    <label for="yazar">Yazar:</label>
                    <input type="text" id="yazar" name="yazar" required>
                </div>
                <div class="form-group" style="flex-basis: 100px; flex-grow: 0;">
                    <label for="stok_adeti">Stok:</label>
                    <input type="number" id="stok_adeti" name="stok_adeti" value="1" required style="width: 100%;">
                </div>
                <button type="submit" class="btn">Ekle</button>
            </form>
        </div>
        
        <h2 class="content-subtitle" style="text-align: left;">Mevcut Kitaplar</h2>
        <table class="kitap-tablosu">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kitap Adı</th>
                    <th>Yazar</th>
                    <th>Stok</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kitaplar as $kitap): ?>
                <tr>
                    <td><?php echo $kitap['id']; ?></td>
                    <td><?php echo htmlspecialchars($kitap['kitap_adi']); ?></td>
                    <td><?php echo htmlspecialchars($kitap['yazar']); ?></td>
                    <td><?php echo $kitap['stok_adeti']; ?></td>
                    <td>
                        <a href="kitap_guncelle.php?id=<?php echo $kitap['id']; ?>" class="btn-durum aktif" style="margin-right: 5px;">Güncelle</a>
                        
                        <a href="kitap_yonet.php?sil_id=<?php echo $kitap['id']; ?>" class="btn-sil" onclick="return confirm('Bu kitabı silmek istediğinize emin misiniz?');">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                ```

**b) `kitap_guncelle.php` (Yeni Dosya)**
`IMU_LIBRARY` klasörünün içine **`kitap_guncelle.php`** adında **yeni bir dosya** oluştur ve aşağıdaki kodu içine yapıştır. Bu dosya, `UPDATE` işlemini yapacak.

```php
<?php
session_start();
require 'db.php'; // Veritabanı bağlantımız

// GÜVENLİK: Sadece adminler bu sayfayı görebilir
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    header("Location: index.php");
    exit;
}

$message = '';
$message_type = 'success';
$kitap = null;

// İŞLEM 1: FORM GÖNDERİLDİYSE (UPDATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $db->prepare("UPDATE kitaplar SET kitap_adi = ?, yazar = ?, stok_adeti = ? WHERE id = ?");
        $stmt->execute([
            $_POST['kitap_adi'],
            $_POST['yazar'],
            $_POST['stok_adeti'],
            $_POST['id'] // Formdaki gizli 'id' alanı
        ]);
        
        // Başarı mesajı ayarla ve listeleme sayfasına yönlendir
        $_SESSION['message'] = "Kitap başarıyla güncellendi!";
        header("Location: kitap_yonet.php");
        exit;

    } catch (PDOException $e) {
        $message = "Hata: " . $e->getMessage();
        $message_type = "error";
    }
}


// İŞLEM 2: SAYFA İLK YÜKLENDİĞİNDE (READ)
// Formu doldurmak için kitabın mevcut bilgilerini çek
if (isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("SELECT * FROM kitaplar WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $kitap = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$kitap) {
            // Kitap bulunamadıysa
            $_SESSION['message'] = "Hata: Kitap bulunamadı!";
            header("Location: kitap_yonet.php");
            exit;
        }
    } catch (PDOException $e) {
        $message = "Hata: " . $e->getMessage();
        $message_type = "error";
    }
} else {
    // ID yoksa sayfayı terk et
    header("Location: kitap_yonet.php");
    exit;
}

?>
<html>
<head>
    <title>Kitap Güncelle - Admin Paneli</title>
    <link rel="stylesheet" href="css/genel.css">
</head>
<body class="body">
<div id="container">
    <div id="header">
        <div id="logo">
            <a target="_blank" href="http:www.medeniyet.edu.tr"><img style="margin-top:15px" src="logo.png" alt="imü logo"></a>
        </div>
        <div id="menu">
            <ul>
                <li><a href="kitap_yonet.php">Kitap Yönetimine Dön</a></li>
                <li><a href="logout.php" style="background-color: #c9302c; color:white;">Güvenli Çıkış</a></li>
            </ul>
        </div>
    </div>

    <div id="content">
        <h1 class="content-title">Kitap Güncelle</h1>

        <?php if ($message): ?>
            <p style="color: <?php echo $message_type == 'error' ? 'red' : 'green'; ?>; font-weight: bold; text-align: center;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <div class="form-container" style="text-align: left; margin: 20px auto;">
            <h2>"<?php echo htmlspecialchars($kitap['kitap_adi']); ?>" Düzenle</h2>
            
            <form action="kitap_guncelle.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $kitap['id']; ?>">
                
                <div class="form-group">
                    <label for="kitap_adi">Kitap Adı:</label>
                    <input type="text" id="kitap_adi" name="kitap_adi" value="<?php echo htmlspecialchars($kitap['kitap_adi']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="yazar">Yazar:</label>
                    <input type="text" id="yazar" name="yazar" value="<?php echo htmlspecialchars($kitap['yazar']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="stok_adeti">Stok:</label>
                    <input type="number" id="stok_adeti" name="stok_adeti" value="<?php echo $kitap['stok_adeti']; ?>" required>
                </div>
                <button type="submit" class="btn">Güncelle</button>
            </form>
        </div>
    </div>
    
    <div id="footer">
        © 2025 İstanbul Medeniyet Üniversitesi - Kütüphane ve Dokümantasyon Daire Başkanlığı
    </div>
</div>
</body>
</html>