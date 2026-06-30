<?php
$page_title = 'Order Details';
require_once __DIR__ . '/config/config.php';
require_login();

$order_id = (int)($_GET['id'] ?? 0);
$user_id = (int)$_SESSION['user']['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param('ii', $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

include __DIR__ . '/includes/header.php';
if (!$order) {
    echo '<div class="empty-state">Order not found.</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}
$itemsStmt = $conn->prepare("SELECT od.*, p.name, p.image FROM order_details od JOIN products p ON od.product_id = p.product_id WHERE od.order_id = ?");
$itemsStmt->bind_param('i', $order_id);
$itemsStmt->execute();
$items = $itemsStmt->get_result();
?>
<section class="page-header">
    <h1>Order #<?= (int)$order['order_id'] ?></h1>
    <p>Status: <span class="status-pill"><?= e($order['status']) ?></span></p>
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
        <h2>Delivery</h2>
        <p><strong>Phone:</strong> <?= e($order['phone']) ?></p>
        <p><strong>Address:</strong><br><?= nl2br(e($order['address'])) ?></p>
        <p><strong>Total:</strong> <?= money($order['total_amount']) ?></p>
    </aside>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
