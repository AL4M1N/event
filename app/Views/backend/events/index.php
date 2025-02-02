<?php
require __DIR__ . '/../includes/header.php';
?>

<div class="container main-container mt-2 p-4 rounded-3">
    <h2 class="text-center">Events</h2>
    <div class="text-end mb-3">
        <a href="<?= base_url('events/new') ?>" class="btn btn-success">+ Add Event</a>
    </div>

    <div class="row">
        <!-- Filters Column -->
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Filters</h5>
                    <form method="GET">
                        <div class="mb-3">
                            <label for="name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="capacity" class="form-label">Minimum Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" value="<?= isset($_GET['capacity']) ? htmlspecialchars($_GET['capacity']) : '' ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="date" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date') ? 'selected' : '' ?>>Date</option>
                                <option value="name" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : '' ?>>Name</option>
                                <option value="capacity" <?= (isset($_GET['sort']) && $_GET['sort'] == 'capacity') ? 'selected' : '' ?>>Capacity</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="order" class="form-label">Order</label>
                            <select class="form-select" id="order" name="order">
                                <option value="desc" <?= (isset($_GET['order']) && $_GET['order'] == 'desc') ? 'selected' : '' ?>>Descending</option>
                                <option value="asc" <?= (isset($_GET['order']) && $_GET['order'] == 'asc') ? 'selected' : '' ?>>Ascending</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="<?= base_url('events') ?>" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Events Column -->
        <div class="col-md-9">
            <?php if (!empty($data['events'])): ?>
                <div class="row g-4">
                    <?php foreach ($data['events'] as $event): ?>
                        <div class="col-12">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($event['name']) ?></h5>
                                    <div class="d-flex gap-1">
                                        <a href="<?= base_url('events/export/' . $event['id']) ?>" title="Export CSV" class="btn btn-info btn-sm">
                                            <i class="fas fa-file-csv"></i>
                                        </a>
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#attendeeModal<?= $event['id'] ?>" title="Register Attendee">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <a href="<?= base_url('events/edit/' . $event['id']) ?>" title="Edit Event" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                        <a href="<?= base_url('events/delete/' . $event['id'] . '?page=' . $currentPage) ?>" 
                                           title="Delete Event" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this event?')">
                                           <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                                    <div class="mb-3 d-flex justify-content-between">
                                        <div><strong>Date:</strong> <?= htmlspecialchars($event['date']) ?></div>
                                        <div id="attendee-number"><strong>Attendees:</strong> <?= htmlspecialchars($event['attendee_count']) ?>/<?= htmlspecialchars($event['capacity']) ?></div>
                                    </div>
                                    
                                    <?php if (!empty($event['attendees'])): ?>
                                        <div class="attendees-list">
                                            <h6>Attendees:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Name</th>
                                                            <th>Mobile Number</th>
                                                            <th>NID</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $attendees = explode(';;', $event['attendees']);
                                                        $index = 0;
                                                        foreach ($attendees as $attendee):
                                                            if (empty($attendee)) continue;
                                                            list($name, $phone, $nid) = explode('|', $attendee);
                                                            $index++;
                                                        ?>
                                                            <tr>
                                                                <td><?= $index ?></td>
                                                                <td><?= htmlspecialchars($name) ?></td>
                                                                <td><?= htmlspecialchars($phone) ?></td>
                                                                <td><?= htmlspecialchars($nid) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No attendees registered yet.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('events?' . http_build_query(array_merge($_GET, ['page' => $currentPage - 1]))) ?>">Previous</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('events?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('events?' . http_build_query(array_merge($_GET, ['page' => $currentPage + 1]))) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="alert alert-warning text-center">No events found.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php foreach ($data['events'] as $event): ?>
        <!-- Attendee Registration Modal -->
        <div class="modal fade" id="attendeeModal<?= $event['id'] ?>" tabindex="-1" aria-labelledby="attendeeModalLabel<?= $event['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="attendeeModalLabel<?= $event['id'] ?>">Register for <?= htmlspecialchars($event['name']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="p-4 border-bottom">
                        <h6>Add Existing Attendee</h6>
                        <div class="row">
                            <div class="col-md-8 mb-2">
                                <select class="form-select" id="existingAttendee<?= $event['id'] ?>">
                                    <option value="">Loading attendees...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-100" onclick="addExistingAttendee(<?= $event['id'] ?>)">Add</button>
                            </div>
                        </div>
                    </div>
                    <form id="attendeeForm<?= $event['id'] ?>" onsubmit="registerAttendee(event, <?= $event['id'] ?>)">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="name<?= $event['id'] ?>" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name<?= $event['id'] ?>" name="name" required>
                                <div class="invalid-feedback" id="nameError<?= $event['id'] ?>"></div>
                            </div>
                            <div class="mb-3">
                                <label for="phone<?= $event['id'] ?>" class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" id="phone<?= $event['id'] ?>" name="phone" required>
                                <div class="invalid-feedback" id="phoneError<?= $event['id'] ?>"></div>
                            </div>
                            <div class="mb-3">
                                <label for="nid<?= $event['id'] ?>" class="form-label">NID Number</label>
                                <input type="text" class="form-control" id="nid<?= $event['id'] ?>" name="nid" required>
                                <div class="invalid-feedback" id="nidError<?= $event['id'] ?>"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>

// Replace the existing modal show event listener with this updated version
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('[id^="attendeeModal"]');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const eventId = this.id.replace('attendeeModal', '');
            loadExistingAttendees(eventId);
        });
    });
});

// Update the loadExistingAttendees function to include error handling and loading state
function loadExistingAttendees(eventId) {
    const select = document.getElementById(`existingAttendee${eventId}`);
    select.innerHTML = '<option value="">Loading attendees...</option>';
    select.disabled = true;

    fetch(`<?= base_url('attendees/list-available/') ?>${eventId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(response => {
        select.innerHTML = '<option value="">Select an attendee</option>';
        
        if (response.success && Array.isArray(response.data) && response.data.length > 0) {
            response.data.forEach(attendee => {
                select.innerHTML += `<option value="${attendee.id}">${attendee.name} (${attendee.phone})</option>`;
            });
        } else {
            select.innerHTML = '<option value="">No attendees available</option>';
        }
    })
    .catch(error => {
        console.error('Error loading attendees:', error);
        select.innerHTML = '<option value="">Error loading attendees</option>';
    })
    .finally(() => {
        select.disabled = false;
    });
}

// Add Existing Attendee
function addExistingAttendee(eventId) {
    const select = document.getElementById(`existingAttendee${eventId}`);
    const attendeeId = select.value;
    
    if (!attendeeId) {
        alert('Please select an attendee');
        return;
    }
    
    // Add Existing Attendee
    fetch(`<?= base_url('attendees/add-existing/') ?>${eventId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ attendee_id: attendeeId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById(`attendeeModal${eventId}`)).hide();
            
            // Use the attendee data from the server response
            updateAttendeesList(eventId, data.attendee);
            
            // Show success message
            alert('Attendee added successfully!');
        } else {
            alert(data.message || 'An error occurred while adding the attendee.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request.');
    });
}

function registerAttendee(event, eventId) {
    event.preventDefault();
    
    const form = document.getElementById(`attendeeForm${eventId}`);
    const formData = new FormData(form);
    
    // Reset previous error states
    resetFormErrors(eventId);
    
    // Register Attendee
    fetch(`<?= base_url('attendees/register/') ?>${eventId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById(`attendeeModal${eventId}`)).hide();
            
            // Reset form
            form.reset();
            
            // Update attendees list with just the new attendee
            updateAttendeesList(eventId, data.attendee);
            
            // Show success message
            alert('Attendee registered successfully!');
        } else {
            if (data.errors) {
                // Show validation errors under each field
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(`${field}${eventId}`);
                    const errorDiv = document.getElementById(`${field}Error${eventId}`);
                    if (input && errorDiv) {
                        input.classList.add('is-invalid');
                        errorDiv.textContent = data.errors[field];
                    }
                });
            } else {
                // Show general error
                alert(data.message || 'An error occurred while registering the attendee.');
            }
        }
    })
    .catch(error => {
        alert('An error occurred while processing your request.');
    });
}

// Reset Form Errors
function resetFormErrors(eventId) {
    const fields = ['name', 'phone', 'nid'];
    fields.forEach(field => {
        const input = document.getElementById(`${field}${eventId}`);
        const errorDiv = document.getElementById(`${field}Error${eventId}`);
        if (input && errorDiv) {
            input.classList.remove('is-invalid');
            errorDiv.textContent = '';
        }
    });
}

// Update Attendees List
function updateAttendeesList(eventId, attendee) {
    // Find the correct card that contains the attendees list for this event
    const eventCard = document.querySelector(`.card:has([data-bs-target="#attendeeModal${eventId}"])`);
    if (!eventCard) {
        console.error('Event card not found');
        return;
    }

    let attendeesList = eventCard.querySelector('.attendees-list');
    
    // If attendees-list doesn't exist, create it
    if (!attendeesList) {
        const cardBody = eventCard.querySelector('.card-body');
        attendeesList = document.createElement('div');
        attendeesList.className = 'attendees-list';
        cardBody.appendChild(attendeesList);
    }

    // If there's no table yet, create it
    if (!attendeesList.querySelector('table')) {
        attendeesList.innerHTML = `
            <h6>Attendees:</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Mobile Number</th>
                            <th>NID</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        `;
    }

    // Remove "No attendees" message if it exists
    const noAttendeesMsg = attendeesList.querySelector('p.text-muted');
    if (noAttendeesMsg) {
        noAttendeesMsg.remove();
    }

    // Get the current number of rows to determine the new ID
    const tbody = attendeesList.querySelector('tbody');
    const newId = tbody.children.length + 1;

    // Create and append the new row
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${newId}</td>
        <td>${attendee.name}</td>
        <td>${attendee.phone}</td>
        <td>${attendee.nid}</td>
    `;
    
    tbody.appendChild(newRow);
    
    // Update the attendee count
    const attendeeCountDiv = eventCard.querySelector('#attendee-number');
    if (attendeeCountDiv && attendeeCountDiv.textContent.includes('Attendees:')) {
        const countText = attendeeCountDiv.textContent;
        const matches = countText.match(/(\d+)\/(\d+)/);
        if (matches) {
            const [, current, capacity] = matches;
            const newCount = parseInt(current) + 1;
            attendeeCountDiv.innerHTML = `<strong>Attendees:</strong> ${newCount}/${capacity}`;
        }
    }
}
</script>

<?php
require __DIR__ . '/../includes/footer.php';
?>