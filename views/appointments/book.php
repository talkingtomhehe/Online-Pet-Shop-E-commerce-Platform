<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="display-5 mb-3">Book an Appointment</h1>
                    <p class="lead text-muted">Schedule a grooming session, checkup, or boarding for your furry friend.</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success mb-4"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="post" action="<?php echo SITE_URL; ?>appointments/book">
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="pet_name" class="form-label">Pet Name *</label>
                                    <input type="text" id="pet_name" name="pet_name" class="form-control" required 
                                           value="<?php echo isset($_POST['pet_name']) ? htmlspecialchars($_POST['pet_name']) : ''; ?>"
                                           placeholder="e.g. Fluffy">
                                </div>
                                <div class="col-md-6">
                                    <label for="service_type" class="form-label">Service *</label>
                                    <select id="service_type" name="service_type" class="form-select" required>
                                        <option value="">Select a Service</option>
                                        <option value="grooming" <?php echo (isset($_POST['service_type']) && $_POST['service_type'] == 'grooming') ? 'selected' : ''; ?>>Grooming</option>
                                        <option value="checkup" <?php echo (isset($_POST['service_type']) && $_POST['service_type'] == 'checkup') ? 'selected' : ''; ?>>Veterinary Checkup</option>
                                        <option value="vaccination" <?php echo (isset($_POST['service_type']) && $_POST['service_type'] == 'vaccination') ? 'selected' : ''; ?>>Vaccination</option>
                                        <option value="boarding" <?php echo (isset($_POST['service_type']) && $_POST['service_type'] == 'boarding') ? 'selected' : ''; ?>>Pet Boarding</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="date" class="form-label">Date *</label>
                                    <input type="date" id="date" name="date" class="form-control" required 
                                           min="<?php echo date('Y-m-d'); ?>"
                                           value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="time" class="form-label">Time *</label>
                                    <select id="time" name="time" class="form-select" required>
                                        <option value="">Select Time</option>
                                        <option value="09:00">09:00 AM</option>
                                        <option value="10:00">10:00 AM</option>
                                        <option value="11:00">11:00 AM</option>
                                        <option value="13:00">01:00 PM</option>
                                        <option value="14:00">02:00 PM</option>
                                        <option value="15:00">03:00 PM</option>
                                        <option value="16:00">04:00 PM</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label">Special Notes (Optional)</label>
                                <textarea id="notes" name="notes" class="form-control" rows="4" 
                                          placeholder="Any allergies, behavioral issues, or special requests?"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Confirm Appointment</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?php echo SITE_URL; ?>appointments/my-appointments" class="text-decoration-none text-muted">
                        View My Appointments <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>