<?php
// Mulai sesi PHP
session_start();

// Sertakan koneksi database
require_once 'includes/koneksi.php';

// Cek apakah user sudah login
$is_logged_in = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda | EXO Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php 
    // Sertakan header.php
    require_once 'includes/header.php'; 
    ?>

    <main class="main-content">
        <?php if ($is_logged_in): ?>
            <div class="welcome-section">
                <h1 class="welcome-title">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p>Selamat datang kembali di EXO Cafe. Jelajahi forum atau lihat profil member favoritmu!</p>
                <a href="forum.php" class="form-button">Kunjungi Forum</a>
            </div>
            
        <?php else: ?>
            <div class="guest-section">
                <h1 class="welcome-title">Selamat Datang di EXO Cafe</h1>
                <p>Website komunitas untuk para EXO-L. Lihat profil member kami atau <a href="login.php">login</a> untuk bergabung dengan forum.</p>
            </div>
            <div class="member-profiles-section">
                <h2 class="section-title">Profil Member EXO</h2>
                <div class="member-card">
                    <img src="assets/images/suho.jpg" alt="Foto Suho" class="member-photo">
                    <h3 class="member-name">Suho</h3>
                    <a href="member_list.php" class="read-more-link">Lihat Profil</a>
                </div>
                </div>
            
        <?php endif; ?>
    </main>

    <?php 
    // Sertakan footer.php
    require_once 'includes/footer.php'; 
    ?>
    <script src="assets/js/script.js"></script>
</body>
</html>