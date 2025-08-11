<?php
require_once 'includes/koneksi.php';

$member_id = $_GET['id'] ?? 0;
$member = null;

if ($member_id) {
    $stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil <?php echo htmlspecialchars($member['stage_name'] ?? 'Member'); ?> | EXO Cafe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    <main class="main-content">
        <?php if ($member): ?>
        <div class="member-profile-detail">
            <img src="assets/images/members/<?php echo htmlspecialchars($member['profile_photo']); ?>" ...>
            <h1 class="profile-name"><?php echo htmlspecialchars($member['stage_name']); ?></h1>
            <h2 class="profile-real-name">(<?php echo htmlspecialchars($member['name']); ?>)</h2>
            <p class="profile-info">Posisi: **<?php echo htmlspecialchars($member['position']); ?>**</p>
            <p class="profile-info">Tanggal Lahir: **<?php echo htmlspecialchars($member['birth_date']); ?>**</p>
            <div class="profile-description">
                <h3>Deskripsi</h3>
                <p><?php echo nl2br(htmlspecialchars($member['description'])); ?></p>
            </div>
            <div class="profile-socials">
                <?php if ($member['instagram_url']): ?><a href="<?php echo htmlspecialchars($member['instagram_url']); ?>" target="_blank">Instagram</a><?php endif; ?>
                <?php if ($member['twitter_url']): ?><a href="<?php echo htmlspecialchars($member['twitter_url']); ?>" target="_blank">Twitter</a><?php endif; ?>
                <?php if ($member['youtube_url']): ?><a href="<?php echo htmlspecialchars($member['youtube_url']); ?>" target="_blank">Youtube</a><?php endif; ?>
                <?php if ($member['tiktok_url']): ?><a href="<?php echo htmlspecialchars($member['tiktok_url']); ?>" target="_blank">Tiktok</a><?php endif; ?>
                <?php if ($member['weibo_url']): ?><a href="<?php echo htmlspecialchars($member['weibo_url']); ?>" target="_blank">Weibo</a><?php endif; ?>
                </div>
        </div>
        <?php else: ?>
        <div class="message message-error">
            Profil member tidak ditemukan.
        </div>
        <?php endif; ?>
    </main>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>