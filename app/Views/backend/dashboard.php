<?php
require __DIR__ . '/includes/header.php';
?>

<div class="container main-container mt-5 p-4 rounded-3">
    <h2 class="text-center">Dashboard</h2>
    <h3 class="text-center">Welcome, <?php echo $name; ?></h3>

    <div class="d-flex justify-content-center mt-5">
        <a href="<?= base_url('/events') ?>" class="btn btn-primary btn-lg me-3">Events</a>
        <a href="<?= base_url('/attendees') ?>" class="btn btn-primary btn-lg">Attendees</a>
    </div>
</div>

<?php
require __DIR__ . '/includes/footer.php';
?>