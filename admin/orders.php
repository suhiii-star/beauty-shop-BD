<?php
$page_title = 'Manage Orders';
require_once __DIR__ . '/../config/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
    verify_csrf();
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? 'Pending';
    $allowed = ['Pending', 'Processing', 'Completed', 'Cancelled'];
    if (!in_array($status, $allowed, true)) $status = 'Pending';
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param('si', $status, $order_id);
    $stmt->execute();
    set_flash('success', 'Order status updated.');
    redirect('admin/orders.php');
}

include __DIR__ . '/../includes/header.php';
$orders = $conn->query("SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_id DESC");
?>
<section class="page-header">
    <h1>Customer Orders</h1>
    <p>Review orders and update their status.</p>
</section>
<div class="table-card">
    <table>
        <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <tr>
                <td><a href="<?= url('admin/order_view.php?id=' . (int)$order['order_id']) ?>">#<?= (int)$order['order_id'] ?></a></td>
                <td><?= e($order['name']) ?><br><small><?= e($order['email']) ?></small></td>
                <td><?= money($order['total_amount']) ?></td>
                <td><span class="status-pill"><?= e($order['status']) ?></span></td>
                <td><?= e($order['order_date']) ?></td>
                <td>
                    <form method="post" class="inline-form">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="action" value="status">
                        <input type="hidden" name="order_id" value="<?= (int)$order['order_id'] ?>">
                        <select name="status">
                            <?php foreach (['Pending', 'Processing', 'Completed', 'Cancelled'] as $status): ?>
                                <option value="<?= e($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-small" type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
