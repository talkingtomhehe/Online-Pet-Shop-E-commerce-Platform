<?php
// User appointments view
$pageTitle = 'My Appointments';
include 'views/layouts/header.php';
?>

<style>
    .appointments-container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 30px;
    }

    .appointment-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 25px;
        margin-bottom: 20px;
        transition: transform 0.3s;
    }

    .appointment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .appointment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .appointment-id {
        font-size: 14px;
        color: #666;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .status-completed {
        background: #d1ecf1;
        color: #0c5460;
    }

    .appointment-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .detail-item {
        display: flex;
        align-items: start;
    }

    .detail-icon {
        color: #b77c52;
        font-size: 20px;
        margin-right: 12px;
        margin-top: 2px;
    }

    .detail-content h6 {
        margin: 0;
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-content p {
        margin: 5px 0 0 0;
        font-size: 16px;
        color: #333;
        font-weight: 500;
    }

    .appointment-notes {
        margin-top: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
        border-left: 4px solid #b77c52;
    }

    .appointment-notes h6 {
        margin: 0 0 10px 0;
        color: #b77c52;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .empty-state i {
        font-size: 64px;
        color: #b77c52;
        margin-bottom: 20px;
    }

    .btn-book {
        background: #b77c52;
        color: white;
        padding: 12px 30px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
        transition: background 0.3s;
    }

    .btn-book:hover {
        background: #a06842;
        color: white;
    }
</style>

<div class="appointments-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Spa Appointments</h2>
        <a href="index.php?page=booking" class="btn-book">
            <i class="bi bi-plus-circle me-2"></i>Book New Appointment
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (empty($appointments)): ?>
        <div class="empty-state">
            <i class="bi bi-calendar-x"></i>
            <h4>No Appointments Yet</h4>
            <p class="text-muted">You haven't booked any spa appointments. Book your first appointment to pamper your pet!</p>
        </div>
    <?php else: ?>
        <?php foreach ($appointments as $appointment): ?>
            <div class="appointment-card">
                <div class="appointment-header">
                    <div class="appointment-id">
                        Appointment #<?= $appointment['id'] ?> 
                        <small class="text-muted">• Booked on <?= date('M d, Y', strtotime($appointment['created_at'])) ?></small>
                    </div>
                    <?php
                    $statusClass = 'status-' . $appointment['status'];
                    $statusText = ucfirst($appointment['status']);
                    ?>
                    <div class="status-badge <?= $statusClass ?>">
                        <?= $statusText ?>
                    </div>
                </div>

                <div class="appointment-details">
                    <div class="detail-item">
                        <i class="bi bi-scissors detail-icon"></i>
                        <div class="detail-content">
                            <h6>Service</h6>
                            <p><?= htmlspecialchars($appointment['service_name']) ?></p>
                            <small class="text-muted">
                                <?= $appointment['duration_minutes'] ?> minutes • $<?= number_format($appointment['price'], 2) ?>
                            </small>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="bi bi-person-badge detail-icon"></i>
                        <div class="detail-content">
                            <h6>Groomer</h6>
                            <p><?= htmlspecialchars($appointment['staff_name']) ?></p>
                            <small class="text-muted"><?= htmlspecialchars($appointment['staff_role']) ?></small>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="bi bi-calendar-event detail-icon"></i>
                        <div class="detail-content">
                            <h6>Date & Time</h6>
                            <p><?= date('l, F j, Y', strtotime($appointment['appointment_date'])) ?></p>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> <?= date('g:i A', strtotime($appointment['appointment_time'])) ?>
                            </small>
                        </div>
                    </div>
                </div>

                <?php if (!empty($appointment['customer_notes'])): ?>
                    <div class="appointment-notes">
                        <h6><i class="bi bi-sticky me-2"></i>Your Notes</h6>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($appointment['customer_notes'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($appointment['status'] === 'pending'): ?>
                    <div class="mt-3 p-3" style="background: #fff9f5; border-radius: 5px;">
                        <i class="bi bi-info-circle text-warning me-2"></i>
                        <small class="text-muted">Your appointment request is pending review. We'll notify you once it's confirmed.</small>
                    </div>
                <?php elseif ($appointment['status'] === 'confirmed'): ?>
                    <div class="mt-3 p-3" style="background: #d4edda; border-radius: 5px;">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <small class="text-success">Your appointment has been confirmed! We look forward to seeing you and your pet.</small>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'views/layouts/footer.php'; ?>
