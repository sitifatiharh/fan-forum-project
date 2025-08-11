<?php
// Mulai sesi PHP
session_start();

// Sertakan file koneksi database
require_once 'includes/koneksi.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = trim($_POST['username_or_email']);
    $password = trim($_POST['password']);

    if (empty($username_or_email) || empty($password)) {
        $error = "Username/Email dan password harus diisi.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password cocok, buat sesi
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Cek peran user, jika admin, redirect ke dashboard
                if ($_SESSION['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    // Jika bukan admin, redirect ke halaman utama
                    header("Location: index.php");
                }
                // -----------------------------
                exit();
            } else {
                $error = "Username/Email atau password salah.";
            }
        } else {
            $error = "Username/Email atau password salah.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | EXO Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">Login</h2>
            
            <?php if ($error): ?>
                <div class="message message-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="register-form">
                <div class="form-group">
                    <label for="username_or_email">Username atau Email:</label>
                    <input type="text" id="username_or_email" name="username_or_email" class="form-input" required>
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
                <p style="margin-top: 20px;"><a href="forgot_password.php">Lupa password</a></p>
                
                <button type="submit" class="form-button">Login</button>
            </form>
            <p style="margin-top: 20px;">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</html>