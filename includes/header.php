<?php
require_once __DIR__ . '/../config/config.php';
$page_title = $page_title ?? 'Beauty Shop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | Beauty Shop</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main class="main-container">
<?php show_flash(); ?>
