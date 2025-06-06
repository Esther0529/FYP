<?php
session_start();
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');
include 'Header.php';

// Fetch latest 12 products, assuming 'created_at' exists. If not, fallback to id DESC.
$result = mysqli_query($conn, "SELECT id, name, price, image_url FROM products ORDER BY id DESC LIMIT 12");
$products = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
// Helper to check if product is new (added in last 14 days)
function isNewProduct($created_at) {
    return false;
}
?>
<main style="background:#fff;min-height:80vh;">
    <div style="max-width:1200px;margin:0 auto;padding:40px 0;">
        <h1 style="font-size:2rem;font-weight:700;margin-bottom:32px;text-align:center;">New In</h1>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" style="position:relative;">
                    <a href="ProductDetails.php?id=<?= $product['id'] ?>">
                        <img src="<?= htmlspecialchars(trim($product['image_url'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="product-price">
                            <?php
                            if (!empty($product['price']) && is_numeric($product['price'])) {
                                echo 'RM ' . number_format($product['price'], 2);
                            } else {
                                echo '<span style="color:#888;">Price Unavailable</span>';
                            }
                            ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php include 'Footer.php'; ?> 