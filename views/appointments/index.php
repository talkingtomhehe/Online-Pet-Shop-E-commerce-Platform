<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Appointments</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Pet</th>
                        <th>Service</th>
                        <th>Date/Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments && $appointments->num_rows > 0): ?>
                        <?php while($appt = $appointments->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $appt['id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($appt['username']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($appt['phone']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($appt['pet_name']); ?></td>
                                <td><?php echo ucfirst($appt['service_type']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($appt['appointment_date'])); ?></td>
                                <td>
                                    <select class="form-select form-select-sm status-select" 
                                            data-id="<?php echo $appt['id']; ?>"
                                            style="width: 120px;">
                                        <option value="pending" <?php echo $appt['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $appt['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="completed" <?php echo $appt['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $appt['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" title="<?php echo htmlspecialchars($appt['notes']); ?>">
                                        <i class="bi bi-info-circle"></i> Notes
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">No appointments found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php include VIEWS_PATH . 'shared/pagination.php'; ?>
    </div>
</div>

<script>
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const id = this.dataset.id;
        const status = this.value;
        
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);
        
        fetch('<?php echo SITE_URL; ?>admin/update-appointment-status', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) alert('Status updated');
            else alert('Failed to update');
        });
    });
});
</script>