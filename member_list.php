<?php
require_once 'includes/koneksi.php';

$result_members = $conn->query("SELECT id, stage_name, position, profile_photo FROM members ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Member | EXO Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main class="main-content">
        <div class="member-profiles-section">
            <h2 class="section-title">Daftar Member EXO</h2>
            <div class="member-card-container">
                <?php while ($member = $result_members->fetch_assoc()): ?>
                <div class="member-card">
                    <img src="assets/images/members/<?php echo htmlspecialchars($member['profile_photo']); ?>" ...>
                    <h3 class="member-name"><?php echo htmlspecialchars($member['stage_name']); ?></h3>
                    <p class="member-position"><?php echo htmlspecialchars($member['position']); ?></p>
                    <a href="profile.php?id=<?php echo $member['id']; ?>" class="read-more-link">Lihat Profil</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>