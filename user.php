<?php
session_start();
require_once 'includes/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki peran 'fan'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fan') {
    header("Location: index.php");
    exit;
}

$message = '';
$user_profile = null;
$user_posts = null;

// Ambil ID pengguna dari URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $profile_id = $_GET['id'];

    // Ambil detail pengguna
    $stmt_user = $conn->prepare("SELECT id, username, email, join_date FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $profile_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    
    if ($result_user->num_rows > 0) {
        $user_profile = $result_user->fetch_assoc();
        
        // Ambil semua postingan pengguna ini
        $stmt_posts = $conn->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.user_id = ? ORDER BY p.created_at DESC");
        $stmt_posts->bind_param("i", $profile_id);
        $stmt_posts->execute();
        $user_posts = $stmt_posts->get_result();
    } else {
        $message = "Pengguna tidak ditemukan.";
    }
    $stmt_user->close();
} else {
    $message = "ID pengguna tidak valid.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna | EXO Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main class="main-content">
        <div class="form-container">
            <?php if ($message): ?>
                <div class="message message-error"><?php echo $message; ?></div>
            <?php elseif ($user_profile): ?>
                <div class="profile-header">
                    <h2>@<?php echo htmlspecialchars($user_profile['username']); ?></h2>
                    <p>Bergabung sejak: <?php echo htmlspecialchars($user_profile['join_date']); ?></p>
                </div>
                
                <hr style="margin: 40px 0;">

                <h3>Postingan dari <?php echo htmlspecialchars($user_profile['username']); ?></h3>
                <div class="posts-feed">
                    <?php if ($user_posts && $user_posts->num_rows > 0): ?>
                        <?php while ($post = $user_posts->fetch_assoc()): ?>
                            <div class="tweet-card">
                                <div class="tweet-header">
                                    <span class="tweet-author">@<?php echo htmlspecialchars($post['username']); ?></span>
                                    <span class="tweet-date"><?php echo htmlspecialchars($post['created_at']); ?></span>
                                </div>
                                <div class="tweet-body">
                                    <p class="tweet-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                    <?php if ($post['image_url']): ?>
                                        <img src="assets/images/posts/<?php echo htmlspecialchars($post['image_url']); ?>" alt="Gambar Postingan" class="tweet-image">
                                    <?php endif; ?>
                                </div>
                                </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Pengguna ini belum membuat postingan.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>