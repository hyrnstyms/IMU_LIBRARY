<?php
session_start(); // Oturumu başlat
session_destroy(); // Oturumu sonlandır (tüm session verilerini sil)
header("Location: index.php"); // Ana sayfaya (login) yönlendir
exit;
?>