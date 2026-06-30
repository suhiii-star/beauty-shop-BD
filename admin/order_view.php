<?php
$page_title = 'Admin Order Details';
require_once __DIR__ . '/../config/config.php';
require_admin();

$order_id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

include __DIR__ . '/../includes/header.php';
if (!$order) {
    echo '<div class="empty-state">Order not found.</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}
$itemsStmt = $conn->prepare("SELECT od.*, p.name, p.image FROM order_details od JOIN products p ON od.product_id = p.product_id WHERE od.order_id = ?");
$itemsStmt->bind_param('i', $order_id);
$itemsStmt->execute();
$items = $itemsStmt->get_result();
?>
<section class="page-header row-between">
    <div>
        <h1>Order #<?= (int)$order['order_id'] ?></h1>
        <p><?= e($order['name']) ?> — <?= e($order['email']) ?></p>
    </div>
    <a class="btn btn-outline" href="<?= url('admin/orders.php') ?>">Back to Orders</a>
</section>
<div class="checkout-layout">
    <div class="table-card">
        <table>
            <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td class="product-mini"><img src="<?= url($item['image']) ?>" alt="<?= e($item['name']) ?>"><span><?= e($item['name']) ?></span></td>
                        <td><?= (int)$item['quantity'] ?></td>
                        <td><?= money($item['unit_price']) ?></td>
                        <td><?= money($item['unit_price'] * $item['quantity']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <aside class="summary-card">
        <h2>Order Information</h2>
        <p><strong>Status:</strong> <?= e($order['status']) ?></p>
        <p><strong>Phone:</strong> <?= e($order['phone']) ?></p>
        <p><strong>Address:</strong><br><?= nl2br(e($order['address'])) ?></p>
        <?php if (!empty($order['notes'])): ?><p><strong>Notes:</strong><br><?= nl2br(e($order['notes'])) ?></p><?php endif; ?>
        <p><strong>Total:</strong> <?= money($order['total_amount']) ?></p>
    </aside>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
