<?php
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-2 p-4 rounded-3">
    <div class="row justify-content-center">
        <div class="col-md-6 p-4 rounded-3 border border-2 border-primary">
            <button type="button" class="btn btn-secondary w-25" onclick="window.location.href='<?= base_url('attendees') ?>'">
                <i class="fas fa-arrow-left"></i> Attendees
            </button>
            <h2 class="text-center mt-4">Add Attendee</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('attendees/store') ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['name'] ?? '') ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Mobile Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['phone'] ?? '') ?>" required>
                    <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="nid" class="form-label">NID Number</label>
                    <input type="text" name="nid" id="nid" class="form-control <?= isset($errors['nid']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['nid'] ?? '') ?>" required>
                    <?php if (isset($errors['nid'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['nid']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary w-100">Add Attendee</button>
            </form>
        </div>
    </div>
</div>

<script>
// Validate form submission
document.querySelector('form').addEventListener('submit', function(event) {
    const name = document.getElementById('name').value.trim();
    const number = document.getElementById('number').value.trim();
    const nid = document.getElementById('nid').value.trim();

    if (!name || !number || !nid) {
        event.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>

<?php
require __DIR__ . '/../includes/footer.php';
?>