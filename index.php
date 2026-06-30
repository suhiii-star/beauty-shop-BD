<?php
$page_title = 'Home';
include __DIR__ . '/includes/header.php';

$featured = $conn->query("SELECT product_id, name, category, price, stock, image, suitable_for FROM products WHERE stock > 0 ORDER BY product_id DESC LIMIT 8");
$categories = ['Skincare', 'Makeup', 'Haircare', 'Perfume', 'Personal Care'];
?>
<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">Fresh cosmetics • Easy ordering • Local seller friendly</p>
        <h1>Shop beauty products in one clean and organized website.</h1>
        <p>Browse skincare, makeup, haircare, perfume, and personal-care items with clear prices, descriptions, stock information, and suitability notes.</p>
        <div class="hero-actions">
            <a class="btn" href="<?= url('products.php') ?>">Browse Products</a>
            <a class="btn btn-outline" href="<?= url('register.php') ?>">Create Account</a>
        </div>
    </div>
    <div class="hero-card">
        <img src="<?= url('assets/images/hero.svg') ?>" alt="Beauty products illustration">
    </div>
</section>

<section class="section">
    <div class="section-heading">
        <h2>Shop by Category</h2>
        <p>Find products quickly from the main beauty categories.</p>
    </div>
    <div class="category-grid">
        <?php foreach ($categories as $cat): ?>
            <a class="category-card" href="<?= url('products.php?category=' . urlencode($cat)) ?>">
                <span><?= e($cat) ?></span>
                <small>View collection</small>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section-heading row-between">
        <div>
            <h2>Latest Products</h2>
            <p>Recently added items from the catalogue.</p>
        </div>
        <a class="link" href="<?= url('products.php') ?>">View all →</a>
    </div>
    <div class="product-grid">
        <?php while ($product = $featured->fetch_assoc()): ?>
            <?php include __DIR__ . '/includes/product_card.php'; ?>
        <?php endwhile; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
