<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= $this->renderSection('title') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
</head>

<?php 
    $loggedInUser = getCurrentUser();
?>
<body>
    <nav>
        <a href='<?= url_to('/') ?>'>Home</a>
        <?php if($loggedInUser->inGroup('superadmin', 'admin')): ?>| <a href='<?= url_to('\Admin\Controllers\Users::index') ?>'>Users</a><?php endif; ?>
        | <a href='<?= url_to('logout') ?>'>Logout</a>
    </nav>

    <?php if (session()->has('message')): ?>
        <p class="success"><b><?= session('message') ?></b></p>   
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <p class="error"><b><?= session('error') ?></b></p>
    <?php endif; ?>
    
    <?= $this->renderSection('content') ?>
</body>

</html>