<?php
session_start();
require_once 'includes/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki peran 'fan'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'fan') {
    header("Location: index.php");
    exit;
}

$message = '';
$upload_dir = 'assets/images/posts/'; // Direktori penyimpanan foto

// Logika untuk membuat postingan baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_post'])) {
    $user_id = $_SESSION['user_id'];
    $content = trim($_POST['content']);
    $image_url = NULL;

    // Logika Unggah File
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_file_name = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = $new_file_name;
            } else {
                $message = "Gagal mengunggah gambar.";
            }
        } else {
            $message = "Tipe file tidak valid atau ukuran file terlalu besar (maks 5MB).";
        }
    }

    if (empty($message)) {
        $stmt_insert = $conn->prepare("INSERT INTO posts (user_id, content, image_url) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iss", $user_id, $content, $image_url);
        if ($stmt_insert->execute()) {
            $message = "Postingan berhasil dibuat!";
        } else {
            $message = "Gagal membuat postingan.";
        }
        $stmt_insert->close();
    }
}

// Logika untuk membuat komentar baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_comment'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];
    $content = trim($_POST['content']);

    // Cek apakah post_id valid
    $stmt_check_post = $conn->prepare("SELECT id FROM posts WHERE id = ?");
    $stmt_check_post->bind_param("i", $post_id);
    $stmt_check_post->execute();
    $result_check_post = $stmt_check_post->get_result();

    if ($result_check_post->num_rows > 0 && !empty($content)) {
        $stmt_insert = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iis", $post_id, $user_id, $content);
        if ($stmt_insert->execute()) {
            $message = "Komentar berhasil ditambahkan!";
        } else {
            $message = "Gagal menambahkan komentar.";
        }
        $stmt_insert->close();
    } else {
        $message = "Postingan tidak valid atau isi komentar kosong.";
    }
}

// Ambil semua postingan dan komentar terkait
$result_posts = $conn->query("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum | EXO Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">Forum EXO Cafe</h2>
            <?php if ($message): ?>
                <div class="message message-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="create-post-section">
                <h3>Buat Postingan Baru</h3>
                <form action="forum.php" method="POST" class="register-form" enctype="multipart/form-data">
                    <input type="hidden" name="create_post" value="1">
                    <div class="form-group">
                        <textarea id="content" name="content" class="form-input" rows="3" placeholder="Apa yang sedang kamu pikirkan?" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Unggah Gambar (opsional):</label>
                        <input type="file" id="image" name="image" class="form-input" accept="image/*">
                    </div>
                    <button type="submit" class="form-button">Posting</button>
                </form>
            </div>
            
            <hr style="margin: 40px 0;">

            <div class="posts-feed">
                <?php 
                // Pengecekan ini memastikan perulangan tidak berjalan jika tidak ada postingan
                if ($result_posts->num_rows > 0): ?>
                    <?php 
                    // Perulangan ini akan menampilkan setiap postingan yang ada
                    while ($post = $result_posts->fetch_assoc()): ?>
                    
                    <div class="tweet-card">
                        <div class="tweet-header">
                            <span class="tweet-author"><a href="user.php?id=<?php echo $post['user_id']; ?>">@<?php echo htmlspecialchars($post['username']); ?></a></span>
                            <span class="tweet-date"><?php echo htmlspecialchars($post['created_at']); ?></span>
                        </div>
                        <div class="tweet-body">
                            <p class="tweet-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <?php if ($post['image_url']): ?>
                                <img src="assets/images/posts/<?php echo htmlspecialchars($post['image_url']); ?>" alt="Gambar Postingan" class="tweet-image">
                            <?php endif; ?>
                        </div>
                    
                        <div class="tweet-comments-section">
                            <hr>
                            <h4>Komentar</h4>
                            <?php
                            $stmt_comments = $conn->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC");
                            $stmt_comments->bind_param("i", $post['id']);
                            $stmt_comments->execute();
                            $result_comments = $stmt_comments->get_result();

                            if ($result_comments->num_rows > 0): ?>
                                <div class="comments-list">
                                    <?php while ($comment = $result_comments->fetch_assoc()): ?>
                                        <div class="comment-item">
                                            <span class="comment-author"><a href="user.php?id=<?php echo $post['user_id']; ?>">@<?php echo htmlspecialchars($post['username']); ?></a></span>
                                            <span class="comment-date"><?php echo htmlspecialchars($comment['created_at']); ?></span>
                                            <p class="comment-content"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-comments">Belum ada komentar.</p>
                            <?php endif; ?>
                            <?php $stmt_comments->close(); ?>

                            <form action="forum.php" method="POST" class="comment-form">
                                <input type="hidden" name="create_comment" value="1">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <textarea name="content" placeholder="Tulis komentar..." required></textarea>
                                <button type="submit">Kirim</button>
                            </form>
                        </div>
                    </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Belum ada postingan. Jadilah yang pertama!</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>