<?php 
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-2 p-4 rounded-3">
    <div class="row justify-content-center">
        <div class="col-md-6 p-4 rounded-3 border border-2 border-primary">
            <button type="button" class="btn btn-secondary w-25" onclick="window.location.href='<?= base_url('events') ?>'">
                <i class="fas fa-arrow-left"></i> Events
            </button>
            <h2 class="text-center">Add Event</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('events/store') ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Event Name</label>
                    <input type="text" name="name" id="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['name'] ?? '') ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="5" 
                              class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                              required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" 
                           class="form-control <?= isset($errors['date']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['date'] ?? '') ?>" required>
                    <?php if (isset($errors['date'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['date']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacity</label>
                    <input type="number" name="capacity" id="capacity" class="form-control <?= isset($errors['capacity']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['capacity'] ?? '') ?>" required>
                    <?php if (isset($errors['capacity'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['capacity']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary w-100">Add Event</button>
            </form>
        </div>
    </div>
</div>

<script>
// Validate form submission
document.querySelector('form').addEventListener('submit', function(event) {
    const name = document.getElementById('name').value.trim();
    const description = document.getElementById('description').value.trim();
    const date = document.getElementById('date').value;

    if (!name || !description || !date) {
        event.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>

<?php
require __DIR__ . '/../includes/footer.php';
?>