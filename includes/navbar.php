<?php $user = current_user(); ?>
<header class="site-header">
    <div class="nav-wrap">
        <a class="logo" href="<?= url('index.php') ?>">Beauty<span>Shop</span></a>
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">☰</button>
        <nav class="nav-menu" id="navMenu">
            <a href="<?= url('index.php') ?>">Home</a>
            <a href="<?= url('products.php') ?>">Products</a>
            <a href="<?= url('cart.php') ?>">Cart <span class="badge"><?= cart_count() ?></span></a>
            <?php if ($user): ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= url('admin/index.php') ?>">Admin</a>
                <?php else: ?>
                    <a href="<?= url('my_orders.php') ?>">My Orders</a>
                <?php endif; ?>
                <a href="<?= url('logout.php') ?>">Logout</a>
            <?php else: ?>
                <a href="<?= url('login.php') ?>">Login</a>
                <a class="btn btn-small" href="<?= url('register.php') ?>">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
