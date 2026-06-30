<?php
$page_title = 'My Orders';
require_once __DIR__ . '/config/config.php';
require_login();
include __DIR__ . '/includes/header.php';

$user_id = (int)$_SESSION['user']['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_id DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<section class="page-header">
    <h1>My Orders</h1>
    <p>Track your previously submitted orders.</p>
</section>
<div class="table-card">
    <?php if ($orders->num_rows === 0): ?>
        <div class="empty-state">No orders yet.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th><th>Details</th></tr>
            </thead>
            <tbody>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?= (int)$order['order_id'] ?></td>
                    <td><?= e($order['order_date']) ?></td>
                    <td><?= money($order['total_amount']) ?></td>
                    <td><span class="status-pill"><?= e($order['status']) ?></span></td>
                    <td><a href="<?= url('order_view.php?id=' . (int)$order['order_id']) ?>">View</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
