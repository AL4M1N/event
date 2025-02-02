<?php
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-2 p-4 rounded-3">
    <div class="row justify-content-center">
        <div class="col-md-6 p-4 rounded-3 border border-2 border-primary">
            <button type="button" class="btn btn-secondary w-25" onclick="window.location.href='<?= base_url('attendees') ?>'">
                <i class="fas fa-arrow-left"></i> Attendees
            </button>
            <h2 class="text-center">Edit Attendee</h2>  

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('attendees/update/' . $attendee['id']) ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['name'] ?? $attendee['name']) ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Mobile Number</label>
                    <input type="text" name="phone" id="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['phone'] ?? $attendee['phone']) ?>" required>
                    <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="nid" class="form-label">NID</label>
                    <input type="text" name="nid" id="nid" class="form-control <?= isset($errors['nid']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($formData['nid'] ?? $attendee['nid']) ?>" required>
                    <?php if (isset($errors['nid'])): ?>
                        <div class="invalid-feedback"><?= $errors['nid'] ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>

<?php
require __DIR__ . '/../includes/footer.php';
?>