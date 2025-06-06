<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');

$id = intval($_GET['id'] ?? 0);
// Add to cart logic (must be before any output or includes that output)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['id'] ?? 0);
    $qty = max(1, intval($_POST['qty'] ?? 1));
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$post_id])) {
        $_SESSION['cart'][$post_id] += $qty;
    } else {
        $_SESSION['cart'][$post_id] = $qty;
    }
    if (isset($_POST['checkout_now'])) {
        header('Location: Checkout.php');
    } else {
        header('Location: Cart.php');
    }
    exit;
}

include 'Header.php';

$result = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$product = mysqli_fetch_assoc($result);
if (!$product) { echo "<p>Product not found.</p>"; exit; }

// Detect if racket (category_id or subcategory_id == 5)
$is_racket = ($product['category_id'] == 5 || $product['subcategory_id'] == 5);
// Detect if racket bag (by subcategory name or id, adjust as needed)
$is_racket_bag = false;
if (isset($product['subcategory_id'])) {
    $subcat_id = intval($product['subcategory_id']);
    // If racket bag subcategory id is 5 or name contains 'Racket Bag'
    if ($subcat_id == 5 || stripos($product['name'], 'Racket Bag') !== false) {
        $is_racket_bag = true;
    }
}
$racket_details = null;
if ($is_racket) {
    $racket_result = mysqli_query($conn, "SELECT * FROM racket_details WHERE id = $id");
    $racket_details = mysqli_fetch_assoc($racket_result);
}
?>
<main>
    <div class="product-details-flex">
        <div class="product-images-col">
            <div class="main-image-wrapper">
                <img src="<?= htmlspecialchars(str_replace('\\', '/', $product['image_url'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="main-image">
            </div>
            <div class="thumbnails-row">
                <img src="<?= htmlspecialchars(str_replace('\\', '/', $product['image_url'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="thumbnail active">
                <!-- Add more <img class="thumbnail"> here if you have more images -->
            </div>
        </div>
        <div class="product-info-col">
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            <div class="product-price-highlight" style="color:#222;">RM<?= number_format($product['price'], 2) ?></div>
            <form method="post" class="product-options-form">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <div class="option-group">
                    <label for="qty">Qty</label>
                    <div class="qty-row">
                        <button type="button" class="qty-btn" onclick="var q=document.getElementById('qty');if(q.value>1)q.value--">-</button>
                        <input type="number" name="qty" id="qty" value="1" min="1" max="999" oninput="if(this.value<1)this.value=1;if(this.value>999)this.value=999;">
                        <button type="button" class="qty-btn" onclick="var q=document.getElementById('qty');if(q.value<999)q.value++">+</button>
                    </div>
                </div>
                <div class="action-buttons-row">
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                    <button type="submit" name="checkout_now" class="checkout-now-btn">Checkout Now</button>
                </div>
            </form>
            <a href="Wishlist.php?add=<?= $product['id'] ?>" class="wishlist-icon" title="Add to Wishlist" style="background:none;border:none;padding:0;">
                <i class="far fa-heart"></i>
            </a>
        </div>
    </div>
</main>
<?php include 'Footer.php'; ?>
</body>
</html> 