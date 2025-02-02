<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../../../../vendor/autoload.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="row p-0 m-0">
            <div class="col-md-12">
                <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container">
                    <a class="navbar-brand" href="<?= base_url('/') ?>">
                        <img src="<?= base_url('image/logo.png') ?>" alt="App Logo" class="img-fluid" style="max-width: 200px; height: 60px;">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item">
                                    <a href="<?= base_url('logout') ?>" class="me-2 nav-link">Logout</a>
                                </li>
                            </ul>
                        <?php else: ?>
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item">
                                    <a href="<?= base_url('login') ?>" class="me-2 nav-link">Login</a>
                                </li>
                                <li>
                                    <a href="<?= base_url('register') ?>" class="nav-link">Register</a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                </nav>
            </div>
        </div>
    </header>