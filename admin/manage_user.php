<?php
session_start();
require_once '../includes/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki peran 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Logika untuk menghapus user
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id_to_delete = $_GET['id'];
    // Cegah admin menghapus dirinya sendiri
    if ($user_id_to_delete != $_SESSION['user_id']) {
        $stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt_delete->bind_param("i", $user_id_to_delete);
        $stmt_delete->execute();
        $stmt_delete->close();
    }
    header("Location: manage_user.php");
    exit;
}

// Ambil semua data user
$result_users = $conn->query("SELECT id, username, email, role FROM users");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna | EXO Cafe</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    <main class="main-content">
        <div class="form-container">
            <h2 class="form-title">Kelola Semua Pengguna</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result_users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="action-link edit">Edit</a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="manage_user.php?action=delete&id=<?php echo $user['id']; ?>" class="action-link delete" onclick="return confirm('Yakin ingin menghapus pengguna ini?');">Hapus</a>
                            <?php endif; ?>
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