<?php
// Mulai sesi
session_start();

// Hapus semua variabel sesi
$_SESSION = array();

// Hancurkan sesi
session_destroy();

// Alihkan pengguna ke halaman beranda
header("Location: index.php");
exit;
?>