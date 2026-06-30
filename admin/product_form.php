<?php
$page_title = 'Product Form';
require_once __DIR__ . '/../config/config.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$product = [
    'name' => '', 'category' => 'Skincare', 'price' => '', 'stock' => '', 'image' => 'assets/images/placeholder.svg',
    'description' => '', 'suitable_for' => ''
];

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    if ($found) $product = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $image = trim($_POST['image'] ?? 'assets/images/placeholder.svg');
    $description = trim($_POST['description'] ?? '');
    $suitable_for = trim($_POST['suitable_for'] ?? '');

    if ($name === '' || $category === '' || $price <= 0) {
        set_flash('danger', 'Name, category and valid price are required.');
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, stock=?, image=?, description=?, suitable_for=? WHERE product_id=?");
            $stmt->bind_param('ssdisssi', $name, $category, $price, $stock, $image, $description, $suitable_for, $id);
            $stmt->execute();
            set_flash('success', 'Product updated.');
        } else {
            $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock, image, description, suitable_for) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssdisss', $name, $category, $price, $stock, $image, $description, $suitable_for);
            $stmt->execute();
            set_flash('success', 'Product added.');
        }
        redirect('admin/products.php');
    }
}
include __DIR__ . '/../includes/header.php';
$cats = ['Skincare', 'Makeup', 'Haircare', 'Perfume', 'Personal Care'];
?>
<section class="page-header">
    <h1><?= $id > 0 ? 'Edit Product' : 'Add Product' ?></h1>
    <p>Fill product information clearly for customers.</p>
</section>
<form method="post" class="form-card wide-form">
    <?php csrf_field(); ?>
    <div class="form-grid">
        <div>
            <label>Product Name</label>
            <input type="text" name="name" value="<?= e($product['name']) ?>" required>
        </div>
        <div>
            <label>Category</label>
            <select name="category" required>
                <?php foreach ($cats as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= $product['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Price</label>
            <input type="number" step="0.01" name="price" value="<?= e($product['price']) ?>" required>
        </div>
        <div>
            <label>Stock</label>
            <input type="number" name="stock" value="<?= e($product['stock']) ?>" required>
        </div>
    </div>
    <label>Image Path</label>
    <input type="text" name="image" value="<?= e($product['image']) ?>" placeholder="assets/images/placeholder.svg">
    <label>Suitable For</label>
    <input type="text" name="suitable_for" value="<?= e($product['suitable_for']) ?>" placeholder="Dry skin, oily skin, daily use...">
    <label>Description</label>
    <textarea name="description" rows="5" required><?= e($product['description']) ?></textarea>
    <button class="btn" type="submit">Save Product</button>
    <a class="btn btn-outline" href="<?= url('admin/products.php') ?>">Cancel</a>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
