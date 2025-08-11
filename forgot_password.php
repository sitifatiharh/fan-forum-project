<?php
require_once 'includes/koneksi.php';

$message = '';
$reset_link = ''; // Variabel untuk menyimpan tautan reset

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $message = "Email harus diisi.";
    } else {
        // Cek apakah email ada di tabel users
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Buat token unik
            $token = bin2hex(random_bytes(32));
            
            // Masukkan email dan token ke tabel password_resets
            $stmt_insert = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $email, $token);
            $stmt_insert->execute();

            // SINI PERUBAHANNYA: Tautan reset disimpan dan ditampilkan
            $reset_link = "http://localhost/exocafe/reset_password.php?token=" . $token;
            $message = "Tautan reset password telah berhasil dibuat. Silakan klik tautan di bawah ini.";
        } else {
            $message = "Email tidak terdaftar.";
        }
        $stmt->close();
        if(isset($stmt_insert)) $stmt_insert->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | EXO Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">Lupa Password</h2>
            <?php if ($message): ?>
                <div class="message message-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if ($reset_link): ?>
                <p>
                    <a href="<?php echo htmlspecialchars($reset_link); ?>">Klik di sini untuk reset password</a>
                </p>
            <?php else: ?>
                <form action="forgot_password.php" method="POST" class="register-form">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-input" required>
                    </div>
                    <button type="submit" class="form-button">Kirim Tautan Reset</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>