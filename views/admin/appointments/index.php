<?php
// Admin appointments management view
?>

<div class="container-fluid px-4">
    <?php
    // Display success/error messages
    if (isset($_SESSION['admin_message'])) {
        $messageType = $_SESSION['admin_message']['type'];
        $messageText = $_SESSION['admin_message']['text'];
        echo "<div class='alert alert-{$messageType} alert-dismissible fade show' role='alert'>
                {$messageText}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        unset($_SESSION['admin_message']);
    }
    ?>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">Total Appointments</div>
                            <div class="h2 mb-0"><?= $totalAppointments ?></div>
                        </div>
                        <i class="bi bi-calendar-check fs-1 opacity"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">Pending Requests</div>
                            <div class="h2 mb-0"><?= $pendingCount ?></div>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">Confirmed</div>
                            <div class="h2 mb-0"><?= $confirmedCount ?></div>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-funnel me-1"></i>
            Filter Appointments
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="page" value="admin">
                <input type="hidden" name="action" value="appointments">
                
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= isset($_GET['status']) && $_GET['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="cancelled" <?= isset($_GET['status']) && $_GET['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        <option value="completed" <?= isset($_GET['status']) && $_GET['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>">
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="index.php?page=admin&action=appointments" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header" style="background-color: #b77c52; color: white;">
            <i class="bi bi-list-ul me-1"></i>
            Appointment List
        </div>
        <div class="card-body">
            <?php if (empty($appointments)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No appointments found.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Staff</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td>#<?= $appointment['id'] ?></td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($appointment['customer_name']) ?></strong>
                                        </div>
                                        <small class="text-muted"><?= htmlspecialchars($appointment['customer_email']) ?></small>
                                        <?php if (!empty($appointment['customer_phone'])): ?>
                                            <br><small class="text-muted"><i class="bi bi-telephone"></i> <?= htmlspecialchars($appointment['customer_phone']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><strong><?= htmlspecialchars($appointment['service_name']) ?></strong></div>
                                        <small class="text-muted">
                                            <?= $appointment['duration_minutes'] ?> min | $<?= number_format($appointment['price'], 2) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div><?= htmlspecialchars($appointment['staff_name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($appointment['staff_role']) ?></small>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-calendar"></i> <?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></div>
                                        <div><i class="bi bi-clock"></i> <?= date('g:i A', strtotime($appointment['appointment_time'])) ?></div>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'cancelled' => 'danger',
                                            'completed' => 'info'
                                        ];
                                        $statusColor = $statusColors[$appointment['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusColor ?>">
                                            <?= ucfirst($appointment['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= date('M d, Y', strtotime($appointment['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($appointment['status'] === 'pending'): ?>
                                            <form method="POST" action="index.php?page=admin&action=update-appointment-status" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="index.php?page=admin&action=update-appointment-status" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        <?php elseif ($appointment['status'] === 'confirmed'): ?>
                                            <form method="POST" action="index.php?page=admin&action=update-appointment-status" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-info btn-sm" title="Mark as Completed">
                                                    <i class="bi bi-check2-all"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="index.php?page=admin&action=update-appointment-status" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Cancel">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($appointment['customer_notes'])): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#genericNoteModal"
                                                    data-id="<?= $appointment['id'] ?>"
                                                    data-note="<?= htmlspecialchars($appointment['customer_notes']) ?>"
                                                    title="View Notes">
                                                <i class="bi bi-sticky"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="genericNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #b77c52; color: white;">
                <h5 class="modal-title" id="noteModalTitle">Customer Notes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="noteModalContent" style="white-space: pre-wrap;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .card-header { font-weight: bold; }
    .table th { font-weight: 600; color: #495057; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    .badge { padding: 0.35em 0.65em; font-weight: 500; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Logic for Status Update Confirmation
    document.querySelectorAll('form[action*="update-appointment-status"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const status = this.querySelector('input[name="status"]').value;
            const statusText = status.charAt(0).toUpperCase() + status.slice(1);
            
            if (!confirm(`Are you sure you want to ${statusText.toLowerCase()} this appointment?`)) {
                e.preventDefault();
            }
        });
    });

    // 2. Logic for Generic Note Modal
    const noteModal = document.getElementById('genericNoteModal');
    if (noteModal) {
        noteModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;
            
            // Extract info from data-* attributes
            const noteContent = button.getAttribute('data-note');
            const appointmentId = button.getAttribute('data-id');
            
            // Update the modal's content.
            const modalTitle = noteModal.querySelector('#noteModalTitle');
            const modalBody = noteModal.querySelector('#noteModalContent');

            modalTitle.textContent = `Customer Notes - Appointment #${appointmentId}`;
            modalBody.textContent = noteContent;
        });
    }
});
</script>