<article class="product-card">
    <a href="<?= url('product.php?id=' . (int)$product['product_id']) ?>" class="product-image-wrap">
        <img src="<?= url($product['image'] ?: 'assets/images/placeholder.svg') ?>" alt="<?= e($product['name']) ?>">
    </a>
    <div class="product-info">
        <span class="tag"><?= e($product['category']) ?></span>
        <h3><a href="<?= url('product.php?id=' . (int)$product['product_id']) ?>"><?= e($product['name']) ?></a></h3>
        <?php if (!empty($product['suitable_for'])): ?>
            <p class="muted">Suitable for: <?= e($product['suitable_for']) ?></p>
        <?php endif; ?>
        <div class="card-bottom">
            <strong><?= money($product['price']) ?></strong>
            <span class="stock <?= ((int)$product['stock'] > 0) ? 'in' : 'out' ?>">
                <?= ((int)$product['stock'] > 0) ? 'In Stock' : 'Out of Stock' ?>
            </span>
        </div>
        <form method="post" action="<?= url('cart.php') ?>" class="quick-cart-form">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn btn-full" <?= ((int)$product['stock'] <= 0) ? 'disabled' : '' ?>>Add to Cart</button>
        </form>
    </div>
</article>
