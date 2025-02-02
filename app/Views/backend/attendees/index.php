<?php
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-2 p-4 rounded-3">
    <h2 class="text-center">Attendees</h2>

    <div class="text-end mb-3">
        <a href="<?= base_url('attendees/create') ?>" class="btn btn-success">+ Add Attendee</a>
    </div>
    
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="name" placeholder="Search by name" value="<?= isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="phone" placeholder="Search by mobile number" value="<?= isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : '' ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="nid" placeholder="Search by NID" value="<?= isset($_GET['nid']) ? htmlspecialchars($_GET['nid']) : '' ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Search</button>
                    <a href="<?= base_url('attendees') ?>" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Serial</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>NID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendees)): ?>
                    <?php 
                    $index = 0;
                    foreach ($attendees as $attendee): 
                        $index++;
                    ?>
                        <tr>
                            <td><?= ($currentPage - 1) * $perPage + $index ?></td>
                            <td><?= htmlspecialchars($attendee['name']); ?></td>
                            <td><?= htmlspecialchars($attendee['phone']); ?></td>
                            <td><?= htmlspecialchars($attendee['nid']); ?></td>
                            <td>
                                <a href="<?= base_url('attendees/edit/' . $attendee['id']) ?>" title="Edit Attendee" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="<?= base_url('attendees/delete/' . $attendee['id'] . '?page=' . $currentPage) ?>" 
                                   title="Delete Attendee" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this attendee?')">
                                   <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No attendees found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation" class="mt-3">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= base_url('attendees?' . http_build_query(array_merge($_GET, ['page' => $currentPage - 1]))) ?>">Previous</a>
            </li>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= base_url('attendees?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= base_url('attendees?' . http_build_query(array_merge($_GET, ['page' => $currentPage + 1]))) ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<?php
require __DIR__ . '/../includes/footer.php';
?>