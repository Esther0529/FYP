<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}
include 'Header.php';
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');
$user_id = intval($_SESSION['user_id']);
$result = mysqli_query($conn, "SELECT message, created_at, is_read FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<main style="max-width:600px;margin:40px auto;padding:32px;background:#fff;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.06);min-height:60vh;">
    <h1 style="font-size:2rem;font-weight:700;margin-bottom:24px;">Notifications</h1>
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <ul style="list-style:none;padding:0;">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <li style="padding:18px 0;border-bottom:1px solid #eee;<?= !$row['is_read'] ? 'font-weight:bold;' : '' ?>">
                <div><?= htmlspecialchars($row['message']) ?></div>
                <div style="color:#888;font-size:0.98rem;margin-top:4px;"><?= htmlspecialchars($row['created_at']) ?></div>
            </li>
        <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <div style="color:#888;font-size:1.15rem;margin-top:32px;">You have no notifications.</div>
    <?php endif; ?>
</main>
<?php include 'Footer.php'; ?>
</body>
</html> 