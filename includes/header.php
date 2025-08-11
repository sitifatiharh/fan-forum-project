<?php
// Pastikan sesi sudah dimulai di setiap halaman yang menyertakan file ini
if (!isset($_SESSION)) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $is_logged_in ? $_SESSION['role'] : '';
?>

<header class="main-header">
    <div class="header-container">
        <a href="index.php" class="logo">EXO Cafe</a>
        <nav class="main-nav">
            <ul>
                <?php if ($user_role === 'admin'): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="manage_user.php">Kelola Pengguna</a></li>
                    <li><a href="manage_member.php">Kelola Member</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                <?php elseif ($user_role === 'fan'): ?>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="forum.php">Forum</a></li>
                    <li><a href="profile.php?user=<?php echo htmlspecialchars($_SESSION['username']); ?>">Profil</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="member_list.php">Member</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>