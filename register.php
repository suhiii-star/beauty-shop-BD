<?php
$page_title = 'Register';
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        set_flash('danger', 'Name, email and password are required.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'Please enter a valid email address.');
    } elseif (strlen($password) < 6) {
        set_flash('danger', 'Password must be at least 6 characters.');
    } elseif ($password !== $confirm) {
        set_flash('danger', 'Passwords do not match.');
    } else {
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            set_flash('warning', 'This email is already registered.');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'customer';
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssss', $name, $email, $hash, $phone, $address, $role);
            $stmt->execute();
            set_flash('success', 'Registration successful. Please login.');
            redirect('login.php');
        }
    }
}
include __DIR__ . '/includes/header.php';
?>
<section class="auth-wrap">
    <form method="post" class="auth-card" data-validate="register">
        <?php csrf_field(); ?>
        <h1>Create Account</h1>
        <label>Name</label>
        <input type="text" name="name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Phone</label>
        <input type="text" name="phone">
        <label>Address</label>
        <textarea name="address" rows="3"></textarea>
        <label>Password</label>
        <input type="password" name="password" minlength="6" required>
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" minlength="6" required>
        <button class="btn btn-full" type="submit">Register</button>
        <p>Already have an account? <a href="<?= url('login.php') ?>">Login</a></p>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
