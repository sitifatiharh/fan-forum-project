<?php
require_once 'includes/koneksi.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

// Cek token saat halaman pertama kali dimuat
$stmt = $conn->prepare("SELECT email, created_at FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$token_data = $result->fetch_assoc();

if (!$token_data || strtotime($token_data['created_at']) < strtotime('-1 hour')) {
    $error = "Tautan reset tidak valid atau sudah kedaluwarsa.";
    $is_token_valid = false;
} else {
    $is_token_valid = true;
}

// Proses password baru jika form disubmit
if ($is_token_valid && $_SERVER["REQUEST_METHOD"] == "POST") {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($password) || empty($confirm_password)) {
        $error = "Password harus diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password di tabel users
        $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt_update->bind_param("ss", $hashed_password, $token_data['email']);
        $stmt_update->execute();

        // Hapus token dari tabel password_resets
        $stmt_delete = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt_delete->bind_param("s", $token);
        $stmt_delete->execute();
        
        $success = "Password berhasil diubah. Silakan login.";
        header("refresh:2;url=login.php");
    }
    if(isset($stmt_update)) $stmt_update->close();
    if(isset($stmt_delete)) $stmt_delete->close();
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | EXO Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">Reset Password</h2>
            <?php if ($error): ?>
                <div class="message message-error"><?php echo $error; ?></div>
            <?php elseif ($success): ?>
                <div class="message message-success"><?php echo $success; ?></div>
            <?php elseif ($is_token_valid): ?>
                <form action="reset_password.php?token=<?php echo $token; ?>" method="POST" class="register-form">
                    <div class="form-group">
                        <label for="password">Password Baru:</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" class="form-input" required>
                            <span class="password-toggle" id="togglePassword">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password:</label>
                        <div class="password-container">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                            <span class="password-toggle" id="toggleConfirmPassword">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="form-button">Ubah Password</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
    <?php require_once 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</body>
</html>