<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}
include 'Header.php';
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');
$user_id = intval($_SESSION['user_id']);
$result = mysqli_query($conn, "SELECT id, order_date, status, total FROM orders WHERE user_id = $user_id ORDER BY order_date DESC");
?>
<main style="max-width:900px;margin:40px auto;padding:32px;background:#fff;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.06);min-height:60vh;">
    <h1 style="font-size:2rem;font-weight:700;margin-bottom:24px;">My Orders</h1>
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1.5px solid #eee;font-size:1.08rem;color:#888;text-align:left;">
                    <th style="padding:8px 0;">Order ID</th>
                    <th style="padding:8px 0;">Date</th>
                    <th style="padding:8px 0;">Status</th>
                    <th style="padding:8px 0;">Total (RM)</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr style="border-bottom:1px solid #f2f2f2;vertical-align:middle;">
                    <td style="padding:12px 0;">#<?= $row['id'] ?></td>
                    <td style="padding:12px 0;"><?= htmlspecialchars($row['order_date']) ?></td>
                    <td style="padding:12px 0;"><?= htmlspecialchars($row['status']) ?></td>
                    <td style="padding:12px 0;"><?= number_format($row['total'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="color:#888;font-size:1.15rem;margin-top:32px;">You have no orders yet.</div>
    <?php endif; ?>
</main>
<?php include 'Footer.php'; ?>
</body>
</html> 