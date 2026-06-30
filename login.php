<?php
$page_title = 'Login';
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT user_id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'user_id' => (int)$user['user_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        set_flash('success', 'Welcome, ' . $user['name'] . '!');
        redirect($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
    } else {
        set_flash('danger', 'Invalid email or password.');
    }
}
include __DIR__ . '/includes/header.php';
?>
<section class="auth-wrap">
    <form method="post" class="auth-card">
        <?php csrf_field(); ?>
        <h1>Login</h1>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button class="btn btn-full" type="submit">Login</button>
        <p>New customer? <a href="<?= url('register.php') ?>">Create account</a></p>
        <div class="demo-box">
            <strong>Admin Demo</strong><br>
            Email: admin@beautyshop.test<br>
            Password: admin123
        </div>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
