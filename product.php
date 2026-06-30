<?php
$page_title = 'Product Details';
include __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo '<div class="empty-state">Product not found.</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}
?>
<section class="product-detail">
    <div class="detail-image">
        <img src="<?= url($product['image'] ?: 'assets/images/placeholder.svg') ?>" alt="<?= e($product['name']) ?>">
    </div>
    <div class="detail-info">
        <span class="tag"><?= e($product['category']) ?></span>
        <h1><?= e($product['name']) ?></h1>
        <p class="price-big"><?= money($product['price']) ?></p>
        <p><?= nl2br(e($product['description'])) ?></p>
        <div class="detail-list">
            <p><strong>Suitable for:</strong> <?= e($product['suitable_for'] ?: 'General use') ?></p>
            <p><strong>Available stock:</strong> <?= (int)$product['stock'] ?></p>
        </div>
        <form method="post" action="<?= url('cart.php') ?>" class="add-cart-box">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
            <label>Quantity</label>
            <input type="number" name="quantity" min="1" max="<?= max(1, (int)$product['stock']) ?>" value="1">
            <button class="btn" type="submit" <?= ((int)$product['stock'] <= 0) ? 'disabled' : '' ?>>Add to Cart</button>
        </form>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
