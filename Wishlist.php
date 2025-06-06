<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle add to wishlist
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);
    if (!in_array($product_id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $product_id;
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Handle remove from wishlist
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    if (($key = array_search($id, $_SESSION['wishlist'])) !== false) {
        unset($_SESSION['wishlist'][$key]);
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
    }
    header('Location: Wishlist.php');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

include 'Header.php';
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');

$wishlist = $_SESSION['wishlist'];
$products = [];
if ($wishlist) {
    $ids = implode(',', array_map('intval', $wishlist));
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
    if (!$result) {
        echo '<div style="color:red;">SQL Error: ' . mysqli_error($conn) . '</div>';
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
$count = count($products);
?>
<main style="min-height:80vh;background:#fff;">
    <div style="max-width:900px;margin:0 auto;padding:32px 0;display:flex;flex-direction:column;align-items:center;">
        <div style="width:100%;display:flex;justify-content:center;align-items:center;">
            <h1 style="font-size:2rem;font-weight:700;text-align:center;margin:0;">WISHLIST</h1>
        </div>
        <?php if (!$products): ?>
            <div style="text-align:center;margin-top:48px;max-width:700px;">
                <div style="font-size:1.15rem;margin-bottom:18px;">Your wishlist is currently empty.</div>
                <a href="Product.php" style="display:inline-block;background:#222;color:#fff;padding:14px 36px;border-radius:6px;font-size:1.12rem;font-weight:600;text-decoration:none;margin-top:18px;">CONTINUE SHOPPING</a>
            </div>
        <?php else: ?>
            <table style="width:100%;border-collapse:collapse;margin-top:32px;">
                <thead>
                    <tr style="border-bottom:1.5px solid #eee;font-size:1.08rem;color:#888;text-align:left;">
                        <th style="padding:8px 0;width:120px;">Product</th>
                        <th style="padding:8px 0;">Name</th>
                        <th style="padding:8px 0;">Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $p): ?>
                    <tr style="border-bottom:1px solid #f2f2f2;vertical-align:middle;">
                        <td style="padding:16px 0;"><img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:80px;height:80px;object-fit:contain;border-radius:8px;background:#fafafa;">
                        </td>
                        <td style="padding:16px 0;">
                            <div style="font-weight:600;"> <?= htmlspecialchars($p['name']) ?> </div>
                        </td>
                        <td style="padding:16px 0;">RM<?= number_format($p['price'],2) ?></td>
                        <td style="padding:16px 0;">
                            <a href="Wishlist.php?remove=<?= $p['id'] ?>" style="color:#e53935;font-size:1.2rem;text-decoration:none;" title="Remove">&times;</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>
<?php include 'Footer.php'; ?>
</body>
</html> 