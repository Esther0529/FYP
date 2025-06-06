<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = require 'db.php';
if (!$conn) {
    die("Database connection failed.");
}

// Fetch categories and subcategories for the sidebar
$categories = [];
$stmt_categories = mysqli_query($conn, "SELECT id, name FROM categories");
if ($stmt_categories) {
    while ($row_category = mysqli_fetch_assoc($stmt_categories)) {
        $category_id = $row_category['id'];
        $category_name = $row_category['name'];
        $stmt_subcategories = mysqli_prepare($conn, "SELECT name FROM subcategories WHERE category_id = ?");
        mysqli_stmt_bind_param($stmt_subcategories, "i", $category_id);
        mysqli_stmt_execute($stmt_subcategories);
        $result = mysqli_stmt_get_result($stmt_subcategories);
        $subcategories = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $categories[$category_name] = $subcategories;
    }
}

// Fetch most-wanted products (top 5 for example)
$most_wanted = [];
$stmt = mysqli_query($conn, "SELECT id, name, price, image_url FROM products ORDER BY id LIMIT 5");
if ($stmt) {
    $most_wanted = mysqli_fetch_all($stmt, MYSQLI_ASSOC);
}

// Fetch 4 featured products for the big slider
$featured_slider = [];
$stmt_slider = mysqli_query($conn, "SELECT id, name, image_url FROM products ORDER BY id LIMIT 4");
if ($stmt_slider) {
    $featured_slider = mysqli_fetch_all($stmt_slider, MYSQLI_ASSOC);
}

include 'Header.php';
?>


<main>
    <section class="brand-banners">
        <div class="swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide banner-card">
                    <a href="ProductDetails.php?id=42" style="display:block;width:100%;height:100%;">
                        <img src="Image/yonex shoes.jpg" alt="YONEX POWER CUSHION AERUS Z2 WOMEN LIGHT PINK">
                        <div class="banner-content">
                            <h3>YONEX POWER CUSHION AERUS Z2 WOMEN LIGHT PINK</h3>
                            <p>Lightest Yonex shoes for speed and comfort</p>
                        </div>
                    </a>
                </div>
                <div class="swiper-slide banner-card">
                    <a href="Product.php?ids=53,54" style="display:block;width:100%;height:100%;">
                        <img src="Image/Victor thruster.webp" alt="Victor Thruster Ryuga">
                        <div class="banner-content">
                            <h3>Victor Thruster Ryuga</h3>
                            <p>Powerful smashes, ultimate control</p>
                        </div>
                    </a>
                </div>
                <div class="swiper-slide banner-card">
                    <a href="ProductDetails.php?id=58" style="display:block;width:100%;height:100%;">
                        <img src="Image/lining-windstorm.jpg" alt="Li-Ning Windstorm">
                        <div class="banner-content">
                            <h3>Li-Ning Windstorm</h3>
                            <p>Lightweight rackets for speed</p>
                        </div>
                    </a>
                </div>
                <div class="swiper-slide banner-card">
                    <a href="Product.php?ids=55,56,57" style="display:block;width:100%;height:100%;">
                        <img src="Image/Yonex Astrox.jpg" alt="Yonex Astrox 100 ZZ, 77 Pro, 88 D Pro">
                        <div class="banner-content">
                            <h3>Yonex Astrox Series</h3>
                            <p>Dominate the court with Astrox</p>
                        </div>
                    </a>
                </div>
            </div>
            <!-- Custom navigation buttons inside the banner -->
            <button class="custom-banner-arrow custom-banner-prev" aria-label="Previous">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
              </svg>
            </button>
            <button class="custom-banner-arrow custom-banner-next" aria-label="Next">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 6 15 12 9 18"></polyline>
              </svg>
            </button>
            <div class="swiper-pagination"></div>
        </div>
    </section>
    <!-- Product Category Slider Section -->
    <section class="most-wanted-section">
        <h2 class="section-title" style="text-align:center;">Most-Wanted Products</h2>
        <div class="most-wanted-grid-modern">
            <?php foreach ($most_wanted as $product): ?>
            <div class="most-wanted-modern-cell">
                <a href="Product_Details.php?id=<?= $product['id'] ?>" style="display:block;width:100%;height:100%;text-decoration:none;">
                    <div style="display:flex;align-items:center;justify-content:center;width:100%;padding:32px 0 0 0;">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width:80%;max-height:180px;object-fit:contain;">
                    </div>
                    <div class="most-wanted-modern-name" style="margin-top:32px;"><?= strtoupper(htmlspecialchars($product['name'])) ?></div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <div id="overlay" class="overlay"></div>
</main>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Swiper for the banner with custom navigation
        const bannerSwiper = new Swiper('.swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },
            speed: 800,
            effect: 'slide',
            navigation: {
                nextEl: '.custom-banner-next',
                prevEl: '.custom-banner-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            }
        });

        var productSwiper = new Swiper('.product-swiper', {
            slidesPerView: 4,
            spaceBetween: 30,
            navigation: {
                nextEl: '.product-swiper .swiper-button-next',
                prevEl: '.product-swiper .swiper-button-prev',
            },
            breakpoints: {
                1200: { slidesPerView: 4 },
                900: { slidesPerView: 3 },
                600: { slidesPerView: 2 },
                0: { slidesPerView: 1 }
            }
        });

        var mostWantedSwiper = new Swiper('.most-wanted-swiper', {
            slidesPerView: 4,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false
            },
            navigation: {
                nextEl: '.most-wanted-swiper .swiper-button-next',
                prevEl: '.most-wanted-swiper .swiper-button-prev',
            },
            breakpoints: {
                1200: { slidesPerView: 4 },
                900: { slidesPerView: 3 },
                600: { slidesPerView: 2 },
                0: { slidesPerView: 1 }
            }
        });
    });
</script>

<!-- Universal Sidebar Modal (added for profile icon/sidebar functionality) -->
<div id="universal-sidebar" class="side-modal-overlay" style="display:none;">
    <div class="side-modal-content" style="max-width:400px;">
        <button class="side-modal-close" onclick="closeUniversalSidebar()">&times;</button>
        <div id="universal-sidebar-content">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

</div>
<?php include 'Footer.php'; ?>
</body>
</html>