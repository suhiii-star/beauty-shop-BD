<?php
$page_title = 'Cart';
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        $stmt = $conn->prepare("SELECT product_id, name, price, stock, image FROM products WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if (!$product) {
            set_flash('danger', 'Product not found.');
        } elseif ((int)$product['stock'] <= 0) {
            set_flash('warning', 'This product is out of stock.');
        } else {
            $existing = $_SESSION['cart'][$product_id]['quantity'] ?? 0;
            $new_qty = min((int)$product['stock'], $existing + $quantity);
            $_SESSION['cart'][$product_id] = [
                'product_id' => (int)$product['product_id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'stock' => (int)$product['stock'],
                'image' => $product['image'],
                'quantity' => $new_qty
            ];
            set_flash('success', 'Product added to cart.');
        }
        redirect('cart.php');
    }

    if ($action === 'update') {
        foreach ($_POST['quantities'] ?? [] as $product_id => $quantity) {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } elseif (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] = min($quantity, (int)$_SESSION['cart'][$product_id]['stock']);
            }
        }
        set_flash('success', 'Cart updated.');
        redirect('cart.php');
    }

    if ($action === 'remove') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$product_id]);
        set_flash('success', 'Item removed.');
        redirect('cart.php');
    }

    if ($action === 'clear') {
        unset($_SESSION['cart']);
        set_flash('success', 'Cart cleared.');
        redirect('cart.php');
    }
}

include __DIR__ . '/includes/header.php';
$cart = $_SESSION['cart'] ?? [];
?>
<section class="page-header">
    <h1>Shopping Cart</h1>
    <p>Review selected items before checkout.</p>
</section>

<?php if (empty($cart)): ?>
    <div class="empty-state">
        <h3>Your cart is empty.</h3>
        <a class="btn" href="<?= url('products.php') ?>">Continue Shopping</a>
    </div>
<?php else: ?>
    <form method="post" class="table-card">
        <?php csrf_field(); ?>
        <input type="hidden" name="action" value="update">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($cart as $item): ?>
                <tr>
                    <td class="product-mini">
                        <img src="<?= url($item['image'] ?: 'assets/images/placeholder.svg') ?>" alt="<?= e($item['name']) ?>">
                        <span><?= e($item['name']) ?></span>
                    </td>
                    <td><?= money($item['price']) ?></td>
                    <td><input class="qty-input" type="number" min="0" max="<?= (int)$item['stock'] ?>" name="quantities[<?= (int)$item['product_id'] ?>]" value="<?= (int)$item['quantity'] ?>"></td>
                    <td><?= money($item['price'] * $item['quantity']) ?></td>
                    <td>
                        <button type="submit" formaction="<?= url('cart.php') ?>" formmethod="post" name="remove_id" class="link-button" onclick="event.preventDefault(); document.getElementById('remove-<?= (int)$item['product_id'] ?>').submit();">Remove</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cart-actions">
            <strong>Total: <?= money(get_cart_total()) ?></strong>
            <div>
                <button class="btn btn-outline" type="submit">Update Cart</button>
                <a class="btn" href="<?= url('checkout.php') ?>">Checkout</a>
            </div>
        </div>
    </form>
    <?php foreach ($cart as $item): ?>
        <form id="remove-<?= (int)$item['product_id'] ?>" method="post" action="<?= url('cart.php') ?>" class="hidden-form">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
        </form>
    <?php endforeach; ?>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
