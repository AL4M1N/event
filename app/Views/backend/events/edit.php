<?php 
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-2 p-4 rounded-3">
    <div class="row justify-content-center">
        <div class="col-md-6 p-4 rounded-3 border border-2 border-primary">
            <button type="button" class="btn btn-secondary w-25" onclick="window.location.href='<?= base_url('events') ?>'">
                <i class="fas fa-arrow-left"></i> Events
            </button>
            <h2 class="text-center mt-4">Edit Event</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('events/update/' . $event['id']) ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Event Name</label>
                    <input type="text" name="name" id="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['name'] ?? $event['name']) ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="5" 
                              class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"><?= htmlspecialchars($formData['description'] ?? $event['description']) ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= $errors['description'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" 
                           class="form-control <?= isset($errors['date']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['date'] ?? $event['date']) ?>" required>
                    <?php if (isset($errors['date'])): ?>
                        <div class="invalid-feedback"><?= $errors['date'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacity</label>
                    <input type="number" name="capacity" id="capacity" 
                           class="form-control <?= isset($errors['capacity']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['capacity'] ?? $event['capacity']) ?>" required>
                    <?php if (isset($errors['capacity'])): ?>
                        <div class="invalid-feedback"><?= $errors['capacity'] ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Event</button>
            </form> 
        </div>
    </div>
</div>

<?php 
require __DIR__ . '/../includes/footer.php';
?>
