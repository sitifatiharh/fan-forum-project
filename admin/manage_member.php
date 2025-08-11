<?php
session_start();
require_once '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = '';
$upload_dir = '../assets/images/members/'; // Direktori penyimpanan foto

// Logika untuk menambah member baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_member'])) {
    $name = trim($_POST['name']);
    $stage_name = trim($_POST['stage_name']);
    $position = trim($_POST['position']);
    $birth_date = trim($_POST['birth_date']);
    $description = trim($_POST['description']);
    $profile_photo = ''; // Inisialisasi variabel untuk nama file foto

    // --- LOGIKA UNGGAH FILE BARU ---
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (in_array($_FILES['profile_photo']['type'], $allowed_types) && $_FILES['profile_photo']['size'] <= $max_size) {
            // Buat nama file yang unik untuk menghindari konflik
            $file_extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $new_file_name = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_file_name;

            // Pindahkan file dari folder sementara ke folder tujuan
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                $profile_photo = $new_file_name;
            } else {
                $message = "Gagal mengunggah file.";
            }
        } else {
            $message = "Tipe file tidak valid atau ukuran file terlalu besar (maks 5MB).";
        }
    }
    // -----------------------------

    if (empty($message)) {
        $instagram_url = empty($_POST['instagram_url']) ? NULL : trim($_POST['instagram_url']);
        $twitter_url = empty($_POST['twitter_url']) ? NULL : trim($_POST['twitter_url']);
        $youtube_url = empty($_POST['youtube_url']) ? NULL : trim($_POST['youtube_url']);
        $tiktok_url = empty($_POST['tiktok_url']) ? NULL : trim($_POST['tiktok_url']);
        $weibo_url = empty($_POST['weibo_url']) ? NULL : trim($_POST['weibo_url']);

        $stmt_insert = $conn->prepare("INSERT INTO members (name, stage_name, position, birth_date, description, profile_photo, instagram_url, twitter_url, youtube_url, tiktok_url, weibo_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("sssssssssss", $name, $stage_name, $position, $birth_date, $description, $profile_photo, $instagram_url, $twitter_url, $youtube_url, $tiktok_url, $weibo_url);
        
        if ($stmt_insert->execute()) {
            $message = "Member berhasil ditambahkan!";
        } else {
            $message = "Gagal menambahkan member.";
        }
        $stmt_insert->close();
    }
}

// Logika untuk menghapus member (tidak berubah)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $member_id_to_delete = $_GET['id'];
    $stmt_delete = $conn->prepare("DELETE FROM members WHERE id = ?");
    $stmt_delete->bind_param("i", $member_id_to_delete);
    if ($stmt_delete->execute()) {
        $message = "Member berhasil dihapus!";
    } else {
        $message = "Gagal menghapus member.";
    }
    $stmt_delete->close();
    header("Location: manage_member.php");
    exit;
}

// Ambil semua data member EXO
$result_members = $conn->query("SELECT * FROM members ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Member EXO | EXO Cafe</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">Kelola Member EXO</h2>
            <?php if ($message): ?>
                <div class="message message-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <h3>Tambah Member Baru</h3>
            <form action="manage_member.php" method="POST" class="register-form" enctype="multipart/form-data">
                <input type="hidden" name="add_member" value="1">
                <div class="form-group">
                    <label for="name">Nama Asli:</label>
                    <input type="text" id="name" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="stage_name">Nama Panggung:</label>
                    <input type="text" id="stage_name" name="stage_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="position">Posisi:</label>
                    <input type="text" id="position" name="position" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="birth_date">Tanggal Lahir:</label>
                    <input type="date" id="birth_date" name="birth_date" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="profile_photo">Unggah Foto Profil:</label>
                    <input type="file" id="profile_photo" name="profile_photo" class="form-input" accept="image/*" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi:</label>
                    <textarea id="description" name="description" class="form-input" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="instagram_url">Instagram URL:</label>
                    <input type="url" id="instagram_url" name="instagram_url" class="form-input">
                </div>
                <div class="form-group">
                    <label for="twitter_url">Twitter URL:</label>
                    <input type="url" id="twitter_url" name="twitter_url" class="form-input">
                </div>
                <div class="form-group">
                    <label for="youtube_url">YouTube URL:</label>
                    <input type="url" id="youtube_url" name="youtube_url" class="form-input">
                </div>
                <div class="form-group">
                    <label for="tiktok_url">TikTok URL:</label>
                    <input type="url" id="tiktok_url" name="tiktok_url" class="form-input">
                </div>
                <div class="form-group">
                    <label for="weibo_url">Weibo URL:</label>
                    <input type="url" id="weibo_url" name="weibo_url" class="form-input">
                </div>
                <button type="submit" class="form-button">Tambah Member</button>
            </form>
            
            <hr style="margin: 40px 0;">

            <h3>Daftar Member</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Nama Panggung</th>
                        <th>Posisi</th>
                        <th>Tanggal Lahir</th>
                        <th>Deskripsi</th>
                        <th>Instagram</th>
                        <th>Twitter</th>
                        <th>YouTube</th>
                        <th>Tiktok</th>
                        <th>Weibo</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($member = $result_members->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($member['id']); ?></td>
                        <td><?php echo htmlspecialchars($member['name']); ?></td>
                        <td><?php echo htmlspecialchars($member['stage_name']); ?></td>
                        <td><?php echo htmlspecialchars($member['position']); ?></td>
                        <td><?php echo htmlspecialchars($member['birth_date']); ?></td>
                        <td><?php echo htmlspecialchars($member['description']); ?></td>
                        <td><?php echo htmlspecialchars($member['instagram_url']); ?></td>
                        <td><?php echo htmlspecialchars($member['twitter_url']); ?></td>
                        <td><?php echo htmlspecialchars($member['youtube_url']); ?></td>
                        <td><?php echo htmlspecialchars($member['tiktok_url']); ?></td>
                        <td><?php echo htmlspecialchars($member['weibo_url']); ?></td>
                        <td><img src="../assets/images/members/<?php echo htmlspecialchars($member['profile_photo']); ?>" alt="Foto <?php echo htmlspecialchars($member['stage_name']); ?>" class="member-table-photo"></td>
                        <td>
                            <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="action-link edit">Edit</a>
                            <a href="manage_member.php?action=delete&id=<?php echo $member['id']; ?>" class="action-link delete" onclick="return confirm('Yakin ingin menghapus member ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>