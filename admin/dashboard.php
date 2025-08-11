<?php
session_start();
require_once '../includes/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki peran 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Ambil data ringkasan
$total_users = 0;
$total_members = 0;

$result_users = $conn->query("SELECT COUNT(*) FROM users");
if ($result_users) {
    $total_users = $result_users->fetch_row()[0];
}

$result_members = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'fan'");
if ($result_members) {
    $total_members = $result_members->fetch_row()[0];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | EXO Cafe</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">Dashboard Admin</h2>
            <div class="dashboard-info">
                <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                <p>Total Pengguna Terdaftar: <strong><?php echo $total_users; ?></strong></p>
                <p>Total Member (Fan): <strong><?php echo $total_members; ?></strong></p>
            </div>
        </div>
    </main>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>