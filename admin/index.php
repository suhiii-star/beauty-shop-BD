<?php
$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../config/config.php';
require_admin();
include __DIR__ . '/../includes/header.php';

$product_count = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$order_count = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$customer_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'customer'")->fetch_assoc()['total'];
$pending_count = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status = 'Pending'")->fetch_assoc()['total'];
?>
<section class="page-header">
    <h1>Admin Dashboard</h1>
    <p>Manage products, stock and customer orders.</p>
</section>
<div class="stat-grid">
    <div class="stat-card"><span>Products</span><strong><?= (int)$product_count ?></strong></div>
    <div class="stat-card"><span>Orders</span><strong><?= (int)$order_count ?></strong></div>
    <div class="stat-card"><span>Customers</span><strong><?= (int)$customer_count ?></strong></div>
    <div class="stat-card"><span>Pending</span><strong><?= (int)$pending_count ?></strong></div>
</div>
<div class="admin-actions">
    <a class="btn" href="<?= url('admin/products.php') ?>">Manage Products</a>
    <a class="btn btn-outline" href="<?= url('admin/orders.php') ?>">View Orders</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
