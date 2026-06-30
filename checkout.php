<?php
$page_title = 'Checkout';
require_once __DIR__ . '/config/config.php';
require_login();

if (empty($_SESSION['cart'])) {
    set_flash('warning', 'Your cart is empty.');
    redirect('products.php');
}

$user_id = (int)$_SESSION['user']['user_id'];
$profile = null;
$stmt = $conn->prepare("SELECT phone, address FROM users WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($phone === '' || $address === '') {
        set_flash('danger', 'Phone and delivery address are required.');
    } else {
        try {
            $conn->begin_transaction();
            $total = 0;
            $cart = $_SESSION['cart'];

            foreach ($cart as $item) {
                $pid = (int)$item['product_id'];
                $qty = (int)$item['quantity'];
                $stockStmt = $conn->prepare("SELECT price, stock FROM products WHERE product_id = ? FOR UPDATE");
                $stockStmt->bind_param('i', $pid);
                $stockStmt->execute();
                $dbProduct = $stockStmt->get_result()->fetch_assoc();
                if (!$dbProduct || (int)$dbProduct['stock'] < $qty) {
                    throw new Exception('Not enough stock for ' . $item['name']);
                }
                $total += (float)$dbProduct['price'] * $qty;
            }

            $status = 'Pending';
            $orderStmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, phone, address, notes, status) VALUES (?, ?, ?, ?, ?, ?)");
            $orderStmt->bind_param('idssss', $user_id, $total, $phone, $address, $notes, $status);
            $orderStmt->execute();
            $order_id = $conn->insert_id;

            foreach ($cart as $item) {
                $pid = (int)$item['product_id'];
                $qty = (int)$item['quantity'];
                $price = (float)$item['price'];

                $detailStmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
                $detailStmt->bind_param('iiid', $order_id, $pid, $qty, $price);
                $detailStmt->execute();

                $updateStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
                $updateStock->bind_param('ii', $qty, $pid);
                $updateStock->execute();
            }

            $conn->commit();
            unset($_SESSION['cart']);
            set_flash('success', 'Order placed successfully.');
            redirect('order_success.php?id=' . $order_id);
        } catch (Exception $ex) {
            $conn->rollback();
            set_flash('danger', $ex->getMessage());
        }
    }
}
include __DIR__ . '/includes/header.php';
?>
<section class="page-header">
    <h1>Checkout</h1>
    <p>Confirm your delivery information and place the order.</p>
</section>

<div class="checkout-layout">
    <form method="post" class="form-card">
        <?php csrf_field(); ?>
        <h2>Delivery Information</h2>
        <label>Phone Number</label>
        <input type="text" name="phone" value="<?= e($profile['phone'] ?? '') ?>" required>
        <label>Delivery Address</label>
        <textarea name="address" rows="4" required><?= e($profile['address'] ?? '') ?></textarea>
        <label>Order Notes</label>
        <textarea name="notes" rows="3" placeholder="Optional message for seller"></textarea>
        <button class="btn btn-full" type="submit">Place Order</button>
    </form>
    <aside class="summary-card">
        <h2>Order Summary</h2>
        <?php foreach ($_SESSION['cart'] as $item): ?>
            <div class="summary-row">
                <span><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?></span>
                <strong><?= money($item['price'] * $item['quantity']) ?></strong>
            </div>
        <?php endforeach; ?>
        <hr>
        <div class="summary-row total">
            <span>Total</span>
            <strong><?= money(get_cart_total()) ?></strong>
        </div>
    </aside>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
