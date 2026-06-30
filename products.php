<?php
$page_title = 'Products';
include __DIR__ . '/includes/header.php';

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

$orderBy = 'product_id DESC';
if ($sort === 'price_low') $orderBy = 'price ASC';
if ($sort === 'price_high') $orderBy = 'price DESC';
if ($sort === 'name') $orderBy = 'name ASC';

$sql = "SELECT product_id, name, category, price, stock, image, suitable_for FROM products WHERE 1";
$params = [];
$types = '';

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR description LIKE ? OR suitable_for LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}
if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}
$sql .= " ORDER BY $orderBy";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();
$categories = $conn->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
?>
<section class="page-header">
    <h1>Product Catalogue</h1>
    <p>Search, filter and select beauty products from one organized catalogue.</p>
</section>

<form class="filter-bar" method="get">
    <input type="text" name="search" placeholder="Search products..." value="<?= e($search) ?>">
    <select name="category">
        <option value="">All Categories</option>
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= e($cat['category']) ?>" <?= $category === $cat['category'] ? 'selected' : '' ?>><?= e($cat['category']) ?></option>
        <?php endwhile; ?>
    </select>
    <select name="sort">
        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
        <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
        <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price Low to High</option>
        <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price High to Low</option>
    </select>
    <button class="btn" type="submit">Apply</button>
</form>

<div class="product-grid">
    <?php if ($products->num_rows > 0): ?>
        <?php while ($product = $products->fetch_assoc()): ?>
            <?php include __DIR__ . '/includes/product_card.php'; ?>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">No products found.</div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
