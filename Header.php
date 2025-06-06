<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kulai Sport</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php
// Dynamic trending suggestions
if (file_exists('includes/db.php')) {
    include_once 'includes/db.php';
} elseif (file_exists('db.php')) {
    include_once 'db.php';
}
$trendingProducts = $brands = $categories = $subcategories = [];
if (isset($conn)) {
    $trendingProducts = mysqli_query($conn, "SELECT name FROM products LIMIT 3");
    $brands = mysqli_query($conn, "SELECT name FROM brands LIMIT 3");
    $categories = mysqli_query($conn, "SELECT name FROM categories LIMIT 3");
    $subcategories = mysqli_query($conn, "SELECT name FROM subcategories LIMIT 3");
}
?>
<script>
window.userProfile = {
  name: "<?php echo isset($_SESSION['user_name']) ? addslashes($_SESSION['user_name']) : 'User'; ?>",
  email: "<?php echo isset($_SESSION['user_email']) ? addslashes($_SESSION['user_email']) : 'user@email.com'; ?>"
};
</script>
<header class="main-header">
    <div class="toolbar">
        <div class="nav-row" style="justify-content:flex-start;">
            <a href="#" class="search-icon" id="searchIcon"><i class="fas fa-search"></i></a>
            <a href="Home.php" class="nav-link">Home</a>
            <a href="#" id="shopSidebarBtn" class="nav-link">Shop</a>
            <a href="NewIn.php" class="nav-link">New In</a>
            <!-- Example highlighted link -->
            <!-- <a href="#" class="nav-link highlighted" style="color:#1565c0;font-weight:bold;">SUMMER'S CALLING</a> -->
        </div>
        <div class="logo">KULAI SPORT</div>
        <div class="icon-row" id="iconRow" style="justify-content:flex-end;">
            <a href="Wishlist.php" class="icon-btn" aria-label="Wishlist"><i class="far fa-heart"></i></a>
            <a href="Cart.php" id="cart-icon" class="icon-btn" aria-label="Cart"><i class="fas fa-shopping-cart"></i></a>
            <a href="#" id="profile-icon" class="icon-btn" aria-label="Profile"><i class="fas fa-user"></i></a>
        </div>
    </div>
</header>
<!-- Pop-out Search Bar Overlay -->
<div class="search-overlay" id="searchOverlay">
  <div class="search-overlay-bg" id="searchOverlayBg"></div>
  <div class="search-overlay-box">
    <div class="search-overlay-header">
      <div class="search-bar-container">
        <form class="search-overlay-form" id="search-form" autocomplete="off" style="width:100%;position:relative;display:flex;align-items:center;">
          <input type="text" id="search-input" class="search-overlay-input" placeholder="Search for shoes, bags and more..." autocomplete="off" />
          <button type="submit" class="search-overlay-search-btn-inside">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>
      <span class="search-overlay-close" id="closeSearchOverlay">&times;</span>
    </div>
    <div id="search-suggestions" class="search-suggestions"></div>
    <div class="search-overlay-columns single-col">
      <div class="search-trending-section">
        <div class="search-trending-title">TRENDING SEARCHES</div>
        <ul class="search-trending-list left-align" style="text-align:left;font-size:1.08rem;">
<?php 
$trending = [];
$result = mysqli_query($conn, "SELECT id, name FROM products WHERE name NOT LIKE '%WINDSTORM 72 - BLACK GOLD-AYPR042-4%' ORDER BY id DESC LIMIT 4");
while($row = mysqli_fetch_assoc($result)) {
    $trending[] = $row;
}
foreach ($trending as $row) {
    echo '<li><a href="ProductDetails.php?id=' . urlencode($row['id']) . '">' . htmlspecialchars($row['name']) . '</a></li>';
}
?>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- Shop Sidebar -->
<div id="shopSidebar" class="sidebar">
    <button id="closeShopSidebar" class="close-btn">&times;</button>
    <div class="sidebar-panels">
      <ul class="sidebar-categories">
        <li data-category="brands">Top Brands <span class="arrow">&rsaquo;</span></li>
        <?php
        $cat_result = mysqli_query($conn, "SELECT id, name FROM categories");
        while ($cat = mysqli_fetch_assoc($cat_result)) {
            $cat_slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $cat['name']));
            echo '<li data-category="' . htmlspecialchars($cat_slug) . '">' . htmlspecialchars($cat['name']) . ' <span class="arrow">&rsaquo;</span></li>';
        }
        ?>
      </ul>
      <div class="sidebar-subcategories">
        <div class="subcategory-panel" data-category="brands">
          <div class="subcategory-title">TOP BRANDS</div>
          <a href="product.php?brand_id=4">Victor</a>
          <a href="product.php?brand_id=5">Yonex</a>
          <a href="product.php?brand_id=6">Li-Ning</a>
        </div>
        <?php
        $cat_result = mysqli_query($conn, "SELECT id, name FROM categories");
        while ($cat = mysqli_fetch_assoc($cat_result)) {
            $cat_slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $cat['name']));
            echo '<div class="subcategory-panel" data-category="' . htmlspecialchars($cat_slug) . '">';
            echo '<div class="subcategory-title">' . strtoupper(htmlspecialchars($cat['name'])) . '</div>';
            $sub_result = mysqli_query($conn, "SELECT id, name FROM subcategories WHERE category_id = " . intval($cat['id']));
            while ($sub = mysqli_fetch_assoc($sub_result)) {
                echo '<a href="product.php?subcategory_id=' . intval($sub['id']) . '">' . htmlspecialchars($sub['name']) . '</a>';
            }
            echo '</div>';
        }
        ?>
      </div>
    </div>
</div>

    
<div id="shopSidebarOverlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.25);z-index:999;"></div>

</div>
<div id="profileSidebarOverlay"></div>
<div id="profile-sidebar" class="profile-sidebar">
<?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_email'])): ?>
    <button id="close-profile" class="close-btn">&times;</button>
    <div style="padding:32px 24px 0 24px;">
        <div style="font-weight:700;font-size:1.2rem;margin-bottom:2px;">
            <?= strtoupper(htmlspecialchars($_SESSION['user_name'])) ?>
        </div>
        <div style="color:#888;font-size:1rem;margin-bottom:2px;">
            <?= htmlspecialchars($_SESSION['user_email']) ?>
        </div>
        <?php if (!empty($_SESSION['user_phone'])): ?>
        <div style="color:#888;font-size:1rem;margin-bottom:18px;">
            <?= htmlspecialchars($_SESSION['user_phone']) ?>
        </div>
        <?php endif; ?>
        <div style="margin:24px 0 0 0;">
            <ul style="list-style:none;padding:0;margin:0;">
                <li style="border-bottom:1px solid #eee;">
                    <a href="Orders.php" style="display:flex;align-items:center;padding:18px 0;color:#111;text-decoration:none;font-size:1.08rem;">
                        <i class="fa fa-box" style="width:28px;font-size:1.2em;margin-right:12px;"></i> My Orders
                    </a>
                </li>
                <li style="border-bottom:1px solid #eee;">
                    <a href="Profile.php" style="display:flex;align-items:center;padding:18px 0;color:#111;text-decoration:none;font-size:1.08rem;">
                        <i class="fa fa-user" style="width:28px;font-size:1.2em;margin-right:12px;"></i> Profile
                    </a>
                </li>
                <li style="border-bottom:1px solid #eee;">
                    <a href="Notification.php" style="display:flex;align-items:center;padding:18px 0;color:#111;text-decoration:none;font-size:1.08rem;position:relative;">
                        <i class="fa fa-bell" style="width:28px;font-size:1.2em;margin-right:12px;"></i> Notification
                        <span style="background:#111;color:#fff;border-radius:50%;padding:2px 10px;font-size:0.95em;margin-left:10px;display:inline-block;position:relative;left:0;">2</span>
                    </a>
                </li>
                <li style="border-bottom:1px solid #eee;">
                    <a href="Wishlist.php" style="display:flex;align-items:center;padding:18px 0;color:#111;text-decoration:none;font-size:1.08rem;">
                        <i class="fa fa-heart" style="width:28px;font-size:1.2em;margin-right:12px;"></i> Wishlist
                    </a>
                </li>
                <li style="border-bottom:1px solid #eee;">
                    <a href="Addresses.php" style="display:flex;align-items:center;padding:18px 0;color:#111;text-decoration:none;font-size:1.08rem;">
                        <i class="fa fa-location-dot" style="width:28px;font-size:1.2em;margin-right:12px;"></i> Saved Addresses
                    </a>
                </li>
                <li>
                    <a href="Logout.php" style="display:flex;align-items:center;padding:18px 0;color:#e53935;text-decoration:none;font-size:1.08rem;">
                        <i class="fa fa-sign-out" style="width:28px;font-size:1.2em;margin-right:12px;"></i> Sign Out
                    </a>
                </li>
            </ul>
        </div>
    </div>
<?php else: ?>
    <button id="close-profile" class="close-btn">&times;</button>
    <div style="padding:32px 24px 0 24px;">
        <div style="font-weight:700;font-size:1.4rem;text-align:center;margin-bottom:8px;">SIGN IN</div>
        <div style="text-align:center;color:#888;margin-bottom:18px;">Sign in to check out faster and enjoy membership privileges</div>
        <form method="post" action="Login.php" style="display:flex;flex-direction:column;gap:12px;">
            <input type="email" name="email" placeholder="Email address" required style="padding:12px;border-radius:6px;border:1px solid #ccc;">
            <div class="password-input-wrapper" style="position:relative;width:100%;display:flex;align-items:center;">
                <input type="password" id="sidebar-password" name="password" placeholder="Password" required style="padding:12px;border-radius:6px;border:1px solid #ccc;width:100%;">
                <button type="button" id="toggleSidebarPassword" class="password-toggle-btn" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1rem;color:#888;padding:0 6px;">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
            <button type="submit" style="padding:12px;background:#222;color:#fff;border:none;border-radius:6px;font-weight:600;">SIGN IN</button>
        </form>
        <div style="text-align:center;margin:12px 0;">
            <a href="forgot_password.php" style="color:#888;text-decoration:underline;">Forgot your password?</a>
        </div>
        <div style="display:flex;align-items:center;gap:8px;margin:18px 0;">
            <hr style="flex:1;border:0;border-top:1px solid #eee;">
            <span style="color:#888;">Or continue with</span>
            <hr style="flex:1;border:0;border-top:1px solid #eee;">
        </div>
        <div style="display:flex;gap:12px;justify-content:center;">
            <a href="google_login.php" style="flex:1;display:flex;align-items:center;justify-content:center;padding:10px;border:1px solid #ccc;border-radius:6px;"><img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google" style="width:24px;height:24px;"></a>
            <a href="apple_login.php" style="flex:1;display:flex;align-items:center;justify-content:center;padding:10px;border:1px solid #ccc;border-radius:6px;"><i class="fab fa-apple" style="font-size:1.5em;"></i></a>
        </div>
        <div style="margin:24px 0 0 0;padding:18px 0 0 0;border-top:1px solid #eee;text-align:center;">
            <div style="font-weight:700;font-size:1.1rem;margin-bottom:6px;">CREATE AN ACCOUNT</div>
            <a href="register.php" style="display:block;padding:12px 0;border:1px solid #222;border-radius:6px;font-weight:600;text-decoration:none;color:#222;">CREATE AN ACCOUNT</a>
        </div>
    </div>
<?php endif; ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shop Sidebar logic
    const shopBtn = document.getElementById('shopSidebarBtn');
    const shopSidebar = document.getElementById('shopSidebar');
    const closeShopSidebar = document.getElementById('closeShopSidebar');
    const shopSidebarOverlay = document.getElementById('shopSidebarOverlay');
    function hideSwiperButtons() {
        document.querySelectorAll('.swiper-button-next, .swiper-button-prev').forEach(btn => {
            btn.style.display = 'none';
        });
    }
    function showSwiperButtons() {
        document.querySelectorAll('.swiper-button-next, .swiper-button-prev').forEach(btn => {
            btn.style.display = '';
        });
    }
    shopBtn.onclick = function(e) {
        e.preventDefault();
        shopSidebar.classList.add('open');
        shopSidebarOverlay.style.display = 'block';
        document.body.classList.add('sidebar-open');
        hideSwiperButtons();
    };
    closeShopSidebar.onclick = shopSidebarOverlay.onclick = function() {
        shopSidebar.classList.remove('open');
        shopSidebarOverlay.style.display = 'none';
        document.body.classList.remove('sidebar-open');
        showSwiperButtons();
    };
    // Pop-out Search Bar Overlay logic
    const searchIcon = document.getElementById('searchIcon');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchOverlayBg = document.getElementById('searchOverlayBg');
    const closeSearchOverlay = document.getElementById('closeSearchOverlay');
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    const iconRow = document.getElementById('iconRow');
    searchIcon.onclick = function(e) {
        e.preventDefault();
        searchOverlay.classList.add('active');
        iconRow.style.display = 'none';
        setTimeout(() => searchInput.focus(), 100);
    };
    closeSearchOverlay.onclick = searchOverlayBg.onclick = function() {
        searchOverlay.classList.remove('active');
        searchSuggestions.classList.remove('active');
        iconRow.style.display = '';
    };
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchOverlay.classList.remove('active');
            searchSuggestions.classList.remove('active');
            iconRow.style.display = '';
        }
    });
    // Dynamic search suggestions logic
    let lastQuery = '';
    searchInput.addEventListener('input', function() {
        const q = this.value.trim();
        if (!q) {
            searchSuggestions.innerHTML = '';
            searchSuggestions.classList.remove('active');
            return;
        }
        if (q === lastQuery) return;
        lastQuery = q;
        fetch('search_suggestions.php?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                let html = '';
                let hasResults = false;
                function renderSection(title, items, type) {
                    if (!items.length) return '';
                    hasResults = true;
                    return `<div class="suggestion-group"><div class="suggestion-title">${title}</div><ul class="search-suggestions-list">` +
                        items.map(item => `<li data-type="${type}" data-id="${item.id}"><a href="ProductDetails.php?id=${item.id}">${item.name}</a></li>`).join('') +
                        '</ul></div>';
                }
                html += renderSection('Products', data.products, 'product');
                html += renderSection('Categories', data.categories, 'category');
                html += renderSection('Subcategories', data.subcategories, 'subcategory');
                html += renderSection('Brands', data.brands, 'brand');
                searchSuggestions.innerHTML = html;
                if (hasResults) {
                    searchSuggestions.classList.add('active');
                } else {
                    searchSuggestions.classList.remove('active');
                }
            });
    });
    // Hide suggestions on click outside
    document.addEventListener('mousedown', function(e) {
        if (!searchSuggestions.contains(e.target) && e.target !== searchInput) {
            searchSuggestions.classList.remove('active');
        }
    });
    // Optional: handle click on suggestion (navigate or fill input)
    searchSuggestions.addEventListener('click', function(e) {
        const li = e.target.closest('li[data-type]');
        if (!li) return;
        const type = li.getAttribute('data-type');
        const id = li.getAttribute('data-id');
        const name = li.textContent;
        if (type === 'product') {
            window.location.href = `ProductDetails.php?id=${id}`;
        } else if (type === 'category') {
            window.location.href = `Product.php?category_id=${id}`;
        } else if (type === 'subcategory') {
            window.location.href = `Product.php?subcategory_id=${id}`;
        } else if (type === 'brand') {
            window.location.href = `Product.php?brand_id=${id}`;
        }
    });
    // Search form submit: redirect to Product.php?q=searchterm
    const searchForm = document.getElementById('search-form');
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query) {
            window.location.href = 'Product.php?q=' + encodeURIComponent(query);
        }
    });
    // Profile sidebar logic
    const profileIcon = document.getElementById('profile-icon');
    const profileSidebarOverlay = document.getElementById('profileSidebarOverlay');
    const profileSidebar = document.getElementById('profile-sidebar');
    function openProfileSidebar() {
        profileSidebar.classList.add('open');
        profileSidebarOverlay.classList.add('active');
        document.body.classList.add('profile-sidebar-open');
    }
    function closeProfileSidebar() {
        profileSidebar.classList.remove('open');
        profileSidebarOverlay.classList.remove('active');
        document.body.classList.remove('profile-sidebar-open');
    }
    profileIcon.onclick = function(e) {
        e.preventDefault();
        openProfileSidebar();
    };
    profileSidebarOverlay.onclick = closeProfileSidebar;
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeProfileSidebar();
    });
    document.getElementById('close-profile').onclick = closeProfileSidebar;
    // Shop Sidebar two-panel logic
    document.querySelectorAll('.sidebar-categories li').forEach(li => {
        li.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.sidebar-categories li').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
            shopSidebar.classList.add('show-subcategories');
            const cat = this.getAttribute('data-category');
            document.querySelectorAll('.subcategory-panel').forEach(panel => {
                panel.classList.toggle('active', panel.getAttribute('data-category') === cat);
            });
        });
    });
    // On sidebar open, hide subcategories panel until a category is clicked
    shopSidebarBtn.addEventListener('click', function() {
        shopSidebar.classList.remove('show-subcategories');
        document.querySelectorAll('.sidebar-categories li').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.subcategory-panel').forEach(panel => panel.classList.remove('active'));
    });
    // When a category is clicked, show the subcategory panel for that category
    // (No auto-click of the first category on load)
    document.querySelectorAll('.sidebar-categories li').forEach(li => {
        li.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.sidebar-categories li').forEach(el => el.classList.remove('active'));
            this.classList.add('active');
            shopSidebar.classList.add('show-subcategories');
            const cat = this.getAttribute('data-category');
            document.querySelectorAll('.subcategory-panel').forEach(panel => {
                panel.classList.toggle('active', panel.getAttribute('data-category') === cat);
            });
        });
    });
    // Password show/hide toggle for sidebar sign-in form
    const sidebarPassword = document.getElementById('sidebar-password');
    const toggleSidebarPassword = document.getElementById('toggleSidebarPassword');
    if (sidebarPassword && toggleSidebarPassword) {
        toggleSidebarPassword.addEventListener('click', function () {
            const type = sidebarPassword.type === 'password' ? 'text' : 'password';
            sidebarPassword.type = type;
            this.innerHTML = type === 'password'
                ? '<i class="fa fa-eye"></i>'
                : '<i class="fa fa-eye-slash"></i>';
        });
    }
});
</script>

</body>
</html> 