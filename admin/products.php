<?php
$page_title = 'Manage Products';
require_once __DIR__ . '/../config/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    $id = (int)($_POST['product_id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    set_flash('success', 'Product deleted.');
    redirect('admin/products.php');
}

include __DIR__ . '/../includes/header.php';
$products = $conn->query("SELECT * FROM products ORDER BY product_id DESC");
?>
<section class="page-header row-between">
    <div>
        <h1>Manage Products</h1>
        <p>Add, edit, delete and update product stock.</p>
    </div>
    <a class="btn" href="<?= url('admin/product_form.php') ?>">+ Add Product</a>
</section>
<div class="table-card">
    <table>
        <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
        <tbody>
        <?php while ($product = $products->fetch_assoc()): ?>
            <tr>
                <td class="product-mini"><img src="<?= url($product['image']) ?>" alt="<?= e($product['name']) ?>"><span><?= e($product['name']) ?></span></td>
                <td><?= e($product['category']) ?></td>
                <td><?= money($product['price']) ?></td>
                <td><?= (int)$product['stock'] ?></td>
                <td class="actions-cell">
                    <a class="btn btn-small" href="<?= url('admin/product_form.php?id=' . (int)$product['product_id']) ?>">Edit</a>
                    <form method="post" onsubmit="return confirm('Delete this product?');">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                        <button class="btn btn-small btn-danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
