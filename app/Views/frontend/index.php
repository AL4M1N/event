<?php
require __DIR__ . '/includes/header.php';
?>

<div class="container text-center mt-5">
    <div>
        <img src="<?= base_url('image/logo.png') ?>" alt="App Logo" class="img-fluid mb-4" style="max-width: 200px;">
    </div>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <div>
            <a href="<?= base_url('login') ?>" class="btn btn-primary btn-lg me-3">Login</a>
            <a href="<?= base_url('register') ?>" class="btn btn-success btn-lg">Register</a>
        </div>
    <?php endif; ?>

    <h1 class="mt-4">Event Management Homepage</h1>
</div>

<?php
require __DIR__ . '/includes/footer.php';
?>