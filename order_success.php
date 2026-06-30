<?php
$page_title = 'Order Confirmed';
include __DIR__ . '/includes/header.php';
$order_id = (int)($_GET['id'] ?? 0);
?>
<div class="success-page">
    <div class="success-icon">✓</div>
    <h1>Order Confirmed</h1>
    <p>Your order has been submitted successfully.</p>
    <p><strong>Order ID:</strong> #<?= $order_id ?></p>
    <div class="hero-actions center">
        <a class="btn" href="<?= url('my_orders.php') ?>">View My Orders</a>
        <a class="btn btn-outline" href="<?= url('products.php') ?>">Continue Shopping</a>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
