<?php
// Sertakan file koneksi database
require_once 'includes/koneksi.php';

// Inisialisasi variabel untuk pesan error atau sukses
$error = '';
$success = '';

// Cek apakah formulir telah disubmit menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data dari formulir
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi sederhana: pastikan semua kolom tidak kosong
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Semua kolom harus diisi.";
    } else {
        // Cek apakah username atau email sudah ada di database
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = "Username atau email sudah terdaftar.";
        } else {
            // Hash password untuk keamanan sebelum disimpan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Siapkan query untuk memasukkan data user baru
            $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'fan')");
            $stmt_insert->bind_param("sss", $username, $email, $hashed_password);

            // Eksekusi query
            if ($stmt_insert->execute()) {
                $success = "Pendaftaran berhasil! Silakan login.";
                // Redirect ke halaman login setelah 2 detik
                header("refresh:2;url=login.php");
            } else {
                $error = "Terjadi kesalahan saat pendaftaran.";
            }

            $stmt_insert->close();
        }
        $stmt_check->close();
    }
    // Tutup koneksi database
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | EXO Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">Daftar Akun Baru</h2>
            
            <?php if ($error): ?>
                <div class="message message-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="message message-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="register-form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" class="form-input" required>
                        <span class="password-toggle" id="togglePassword">
                            <i class="fa fa-eye-slash"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="form-button">Daftar</button>
            </form>
            <p style="margin-top: 20px;">Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</html>