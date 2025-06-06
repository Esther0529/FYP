<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');

// Example session cart: $_SESSION['cart'] = [product_id => qty, ...]
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$cart = $_SESSION['cart'];

// All redirect logic BEFORE including header.php
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$remove_id]);
    header('Location: Checkout.php');
    exit;
}
if (isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $pid => $qty) {
        $qty = max(1, min(999, intval($qty)));
        $_SESSION['cart'][$pid] = $qty;
    }
    header('Location: Checkout.php');
    exit;
}

include 'header.php';

// Fetch products
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
$shipping = isset($_POST['shipping']) ? floatval($_POST['shipping']) : 40.00;
$estimated_total = $subtotal + $shipping;

function encodeImagePath($path) {
    $parts = explode('/', str_replace('\\', '/', $path));
    $parts = array_map('rawurlencode', $parts);
    return implode('/', $parts);
}
?>
<main style="background:#fafafa;min-height:100vh;padding:32px 0;">
    <div style="max-width:1200px;margin:0 auto;display:flex;gap:40px;align-items:flex-start;">
        <!-- Left: Product List -->
        <div style="flex:2;background:#fff;border-radius:12px;padding:32px 24px;box-shadow:0 2px 12px rgba(0,0,0,0.07);">
            <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:24px;">Shopping Bag</h2>
            <?php if ($products): ?>
            <form method="post" action="Checkout.php">
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
                        <td style="padding:16px 0;"><img src="<?= htmlspecialchars(str_replace('\\', '/', $p['image_url'])) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:80px;height:80px;object-fit:contain;border-radius:8px;background:#fafafa;"></td>
                        <td style="padding:16px 0;">
                            <div style="font-weight:600;"> <?= htmlspecialchars($p['name']) ?> </div>
                        </td>
                        <td style="padding:16px 0;">RM<?= number_format($p['price'],2) ?></td>
                        <td style="padding:16px 0;">
                            <input type="number" name="qty[<?= $p['id'] ?>]" value="<?= $p['qty'] ?>" min="1" max="999" style="width:56px;padding:6px 8px;border-radius:4px;border:1px solid #ddd;" oninput="this.value = Math.max(1, Math.min(999, this.value))">
                        </td>
                        <td style="padding:16px 0;">RM<?= number_format($p['total'],2) ?></td>
                        <td style="padding:16px 0;">
                            <a href="Checkout.php?remove=<?= $p['id'] ?>" style="color:#e53935;font-size:1.2rem;text-decoration:none;" title="Remove">&times;</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top:18px;display:flex;gap:18px;align-items:center;">
                <a href="Product.php" style="background:#fff;color:#111;padding:8px 16px;border-radius:4px;text-decoration:none;display:inline-block;border:1.5px solid #111;font-weight:600;">Continue Shopping</a>
            </div>
            </form>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
        <!-- Right: Order Summary -->
        <div style="flex:1;background:#fff;border-radius:12px;padding:32px 24px;box-shadow:0 2px 12px rgba(0,0,0,0.07);min-width:320px;">
            <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:18px;">Order Summary</h2>
            <div style="margin-bottom:12px;display:flex;justify-content:space-between;">
                <span>Subtotal</span>
                <span>RM<?= number_format($subtotal,2) ?></span>
            </div>
            <form method="post" action="Checkout.php" style="margin-bottom:12px;">
                <label for="shipping" style="display:block;margin-bottom:6px;">Shipping</label>
                <select name="shipping" id="shipping" style="width:100%;padding:8px 12px;border-radius:4px;border:1px solid #ddd;">
                    <option value="40.00"<?= $shipping==40.00?' selected':'' ?>>Standard (RM40.00)</option>
                    <option value="0.00"<?= $shipping==0.00?' selected':'' ?>>Free (above RM200)</option>
                </select>
                <button type="submit" style="display:none;">Update Shipping</button>
            </form>
            <div style="margin-bottom:18px;display:flex;justify-content:space-between;font-weight:600;font-size:1.12rem;">
                <span>Estimated Total:</span>
                <span>RM<?= number_format($estimated_total,2) ?></span>
            </div>
            <a href="#" style="display:block;background:#19b600;color:#fff;text-align:center;padding:14px 0;border-radius:4px;font-size:1.12rem;font-weight:700;text-decoration:none;margin-bottom:18px;">PROCEED TO CHECKOUT</a>
            <div style="text-align:center;color:#888;font-size:0.98rem;margin-bottom:18px;">Or Express Checkout with</div>
            <div style="display:flex;gap:12px;justify-content:center;margin-bottom:18px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="GPay" style="height:28px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa" style="height:28px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png" alt="Mastercard" style="height:28px;">
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:center;align-items:center;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa" style="height:22px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png" alt="Mastercard" style="height:22px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Amex_logo.svg" alt="Amex" style="height:22px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Atome_logo.svg" alt="Atome" style="height:22px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/Boost_logo.svg" alt="Boost" style="height:22px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/GrabPay_Logo.png" alt="GrabPay" style="height:22px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/7e/Touch_%27n_Go_eWallet_logo.svg" alt="TNG" style="height:22px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/FPX_logo.svg" alt="FPX" style="height:22px;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Apple_Pay_logo.svg" alt="ApplePay" style="height:22px;">
            </div>
        </div>
    </div>
</main>
<?php include 'Footer.php'; ?>
</body>
</html> 