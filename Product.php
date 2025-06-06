<?php
include 'Header.php';
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');

// Fetch categories and brands for filter dropdowns
$category_options = mysqli_query($conn, "SELECT id, name FROM categories");
$brand_options = mysqli_query($conn, "SELECT id, name FROM brands");

// Get filters
$ids = isset($_GET['ids']) ? $_GET['ids'] : null;
$category_id = $_GET['category_id'] ?? null;
$subcategory_id = $_GET['subcategory_id'] ?? null;
$brand_id = $_GET['brand_id'] ?? null;
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : '';
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : '';
$sort = $_GET['sort'] ?? 'featured';

// Build query
$where = [];
if ($ids) {
    // Sanitize and build IN clause
    $id_array = array_filter(array_map('intval', explode(',', $ids)), function($v) { return $v > 0; });
    if (count($id_array) > 0) {
        $where[] = 'id IN (' . implode(',', $id_array) . ')';
    } else {
        $where[] = '0'; // No valid IDs, show nothing
    }
} else {
    if ($category_id) $where[] = "category_id = " . intval($category_id);
    if ($subcategory_id) $where[] = "subcategory_id = " . intval($subcategory_id);
    if ($brand_id) $where[] = "brand_id = " . intval($brand_id);
    if ($searchTerm !== '') $where[] = "name LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'";
    if ($min_price !== '') $where[] = "price >= " . floatval($min_price);
    if ($max_price !== '') $where[] = "price <= " . floatval($max_price);
}
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Sorting
$order_by = '';
switch ($sort) {
    case 'price_asc':
        $order_by = 'ORDER BY price ASC';
        break;
    case 'price_desc':
        $order_by = 'ORDER BY price DESC';
        break;
    case 'name_asc':
        $order_by = 'ORDER BY name ASC';
        break;
    case 'name_desc':
        $order_by = 'ORDER BY name DESC';
        break;
    default:
        $order_by = 'ORDER BY id DESC'; // Featured (latest)
}

$result = mysqli_query($conn, "SELECT * FROM products $where_sql $order_by");
?>
<main>
    <div style="padding:30px 0 10px 30px;">
        <div style="display:flex;align-items:center;gap:18px;margin-bottom:10px;min-height:38px;justify-content:flex-start;margin-left:0;">
            <button id="filterBtn" class="product-filter-btn" style="display:flex;align-items:center;gap:7px;padding:0 10px 0 0;background:none;border:none;cursor:pointer;font-size:1rem;font-weight:600;color:#111;min-height:32px;">
                <i class="fas fa-sliders-h" style="font-size:1.05em;"></i>
                <span style="font-weight:600;letter-spacing:0.01em;">FILTER</span>
            </button>
            <div style="height:22px;width:1px;background:#ddd;margin:0 6px;"></div>
            <form method="get" id="sortForm" style="display:flex;align-items:center;gap:6px;">
                <span style="color:#444;font-size:0.98rem;letter-spacing:0.01em;">SORT BY:</span>
                <select name="sort" id="sortSelect" style="font-weight:600;font-size:0.98rem;border:none;background:none;outline:none;cursor:pointer;color:#111;min-height:28px;">
                    <option value="featured" <?= $sort=='featured'?'selected':'' ?>>Featured</option>
                    <option value="price_asc" <?= $sort=='price_asc'?'selected':'' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort=='price_desc'?'selected':'' ?>>Price: High to Low</option>
                    <option value="name_asc" <?= $sort=='name_asc'?'selected':'' ?>>Name: A-Z</option>
                    <option value="name_desc" <?= $sort=='name_desc'?'selected':'' ?>>Name: Z-A</option>
                </select>
            </form>
        </div>
        <div id="filterPanel" style="display:none;margin-bottom:24px;padding:24px 18px;background:#fafafa;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            <form method="get" style="display:flex;gap:24px;flex-wrap:wrap;align-items:flex-end;">
                <div>
                    <label for="category_id" style="font-weight:600;">Category</label><br>
                    <select name="category_id" id="category_id" style="padding:8px 12px;border-radius:4px;border:1px solid #ccc;min-width:140px;">
                        <option value="">All</option>
                        <?php 
                        // Re-fetch for filter panel (since previous while loop exhausted the result)
                        $category_options2 = mysqli_query($conn, "SELECT id, name FROM categories");
                        while($cat = mysqli_fetch_assoc($category_options2)): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($category_id == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label for="brand_id" style="font-weight:600;">Brand</label><br>
                    <select name="brand_id" id="brand_id" style="padding:8px 12px;border-radius:4px;border:1px solid #ccc;min-width:140px;">
                        <option value="">All</option>
                        <?php 
                        $brand_options2 = mysqli_query($conn, "SELECT id, name FROM brands");
                        while($brand = mysqli_fetch_assoc($brand_options2)): ?>
                            <option value="<?= $brand['id'] ?>" <?= ($brand_id == $brand['id']) ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label for="min_price" style="font-weight:600;">Min Price</label><br>
                    <input type="number" step="0.01" name="min_price" id="min_price" value="<?= htmlspecialchars($min_price) ?>" style="padding:8px 12px;border-radius:4px;border:1px solid #ccc;width:110px;">
                </div>
                <div>
                    <label for="max_price" style="font-weight:600;">Max Price</label><br>
                    <input type="number" step="0.01" name="max_price" id="max_price" value="<?= htmlspecialchars($max_price) ?>" style="padding:8px 12px;border-radius:4px;border:1px solid #ccc;width:110px;">
                </div>
                <div>
                    <button type="submit" style="padding:10px 24px;border-radius:6px;background:#222;color:#fff;font-weight:600;border:none;font-size:1.08rem;">Apply</button>
                </div>
            </form>
        </div>
    </div>
    <div class="product-grid">
        <?php if (mysqli_num_rows($result) === 0): ?>
            <div style="padding:32px;font-size:1.2rem;color:#888;">No product result found.</div>
        <?php else: ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="product-card" style="position:relative;">
                    <a href="ProductDetails.php?id=<?= $row['id'] ?>">
                        <div class="product-image-wrapper">
                            <img src="<?= htmlspecialchars(str_replace('\\', '/', $row['image_url'])) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        </div>
                        <div class="product-swatches">
                            <span class="swatch" style="background:#ede9e3"></span>
                            <span class="swatch" style="background:#fff"></span>
                            <span class="swatch" style="background:#5a3a2e"></span>
                            <span class="swatch" style="background:#222"></span>
                        </div>
                        <div class="product-name" style="text-decoration:none;"><?= htmlspecialchars($row['name']) ?></div>
                        <div class="product-price" style="color:#111;">RM<?= number_format($row['price'], 2) ?></div>
                    </a>
                    <div style="position:absolute;bottom:16px;right:16px;display:flex;gap:12px;z-index:2;">
                        <a href="Wishlist.php" class="wishlist-icon" style="color:#e53935;font-size:1.3rem;text-decoration:none;display:flex;align-items:center;justify-content:center;">
                            <i class="far fa-heart"></i>
                        </a>
                        <a href="Cart.php?add=<?= $row['id'] ?>&qty=1" class="cart-icon" style="color:#111;font-size:1.3rem;text-decoration:none;display:flex;align-items:center;justify-content:center;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61l1.38-7.39H6"></path></svg>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</main>
<script>
document.getElementById('filterBtn').onclick = function() {
    var panel = document.getElementById('filterPanel');
    panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'block' : 'none';
};
document.getElementById('sortSelect').onchange = function() {
    document.getElementById('sortForm').submit();
};
</script>
<?php include 'Footer.php'; ?>
</body>
</html> 