<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');

// All redirect logic BEFORE including header.php
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$remove_id]);
    header('Location: Cart.php');
    exit;
}
if (isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $pid => $qty) {
        $qty = max(1, min(999, intval($qty)));
        $_SESSION['cart'][$pid] = $qty;
    }
    header('Location: Cart.php');
    exit;
}

include 'Header.php';

// Fetch products in cart
$cart = $_SESSION['cart'] ?? [];
$products = [];
$subtotal = 0;
if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
    while ($row = mysqli_fetch_assoc($result)) {
        $row['qty'] = $cart[$row['id']];
        $row['total'] = $row['qty'] * $row['price'];
        $products[] = $row;
        $subtotal += $row['total'];
    }
}

function encodeImagePath($path) {
    $parts = explode('/', str_replace('\\', '/', $path));
    $parts = array_map('rawurlencode', $parts);
    return implode('/', $parts);
}
?>
<main style="min-height:80vh;background:#fff;">
    <div style="max-width:900px;margin:0 auto;padding:32px 0;display:flex;flex-direction:column;align-items:center;">
        <div style="width:100%;display:flex;justify-content:center;align-items:center;">
            <h1 style="font-size:2rem;font-weight:700;text-align:center;margin:0;">Your Cart</h1>
        </div>
        <?php if (!$products): ?>
            <div style="text-align:center;margin-top:48px;max-width:700px;">
                <div style="font-size:1.15rem;margin-bottom:18px;">Your cart is empty.</div>
                <a href="Product.php" style="display:inline-block;background:#222;color:#fff;padding:14px 36px;border-radius:6px;font-size:1.12rem;font-weight:600;text-decoration:none;margin-top:18px;">CONTINUE SHOPPING</a>
            </div>
        <?php else: ?>
            <form method="post" action="Cart.php">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1.5px solid #eee;font-size:1.08rem;color:#888;text-align:left;">
                        <th style="padding:8px 0;width:120px;">Product</th>
                        <th style="padding:8px 0;">Name</th>
                        <th style="padding:8px 0;">Price</th>
                        <th style="padding:8px 0;">Qty</th>
                        <th style="padding:8px 0;">Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $p): ?>
                    <tr style="border-bottom:1px solid #f2f2f2;vertical-align:middle;">
                        <td style="padding:16px 0;"><img src="<?= htmlspecialchars(trim($p['image_url'])) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:80px;height:80px;object-fit:contain;border-radius:8px;background:#fafafa;"></td>
                        <td style="padding:16px 0;">
                            <div style="font-weight:600;"> <?= htmlspecialchars($p['name']) ?> </div>
                        </td>
                        <td style="padding:16px 0;">RM<?= number_format($p['price'],2) ?></td>
                        <td style="padding:16px 0;">
                            <input type="number" name="qty[<?= $p['id'] ?>]" value="<?= $p['qty'] ?>" min="1" max="999" style="width:56px;padding:6px 8px;border-radius:4px;border:1px solid #ddd;">
                        </td>
                        <td style="padding:16px 0;">RM<?= number_format($p['total'],2) ?></td>
                        <td style="padding:16px 0;">
                            <a href="Cart.php?remove=<?= $p['id'] ?>" style="color:#e53935;font-size:1.2rem;text-decoration:none;" title="Remove">&times;</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top:18px;display:flex;gap:18px;align-items:center;">
                <button type="submit" name="update_qty" style="background:#222;color:#fff;padding:8px 16px;border-radius:4px;font-weight:600;border:none;">Update Cart</button>
                <a href="Product.php" style="background:#fff;color:#111;padding:8px 16px;border-radius:4px;text-decoration:none;display:inline-block;border:1.5px solid #111;font-weight:600;">Continue Shopping</a>
                <a href="Checkout.php" style="background:#000;color:#fff;padding:8px 16px;border-radius:4px;text-decoration:none;display:inline-block;font-weight:600;">Checkout Now</a>
            </div>
            </form>
            <div style="margin-top:32px;font-size:1.15rem;font-weight:600;">Subtotal: RM<?= number_format($subtotal,2) ?></div>
        <?php endif; ?>
    </div>
</main>
<?php include 'Footer.php'; ?>
</body>
</html> 