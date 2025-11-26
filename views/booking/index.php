<?php
// Ensure user is logged in
if (!isset($isLoggedIn)) {
    $isLoggedIn = false;
}

// Include header
$pageTitle = 'Book a Spa Appointment';
include 'views/layouts/header.php';
?>

<style>
    .booking-wizard {
        max-width: 800px;
        margin: 50px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .wizard-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
    }

    .wizard-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e0e0e0;
        z-index: 0;
    }

    .wizard-step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 10px;
        transition: all 0.3s;
    }

    .wizard-step.active .step-number {
        background: #b77c52;
        color: white;
    }

    .wizard-step.completed .step-number {
        background: #28a745;
        color: white;
    }

    .step-title {
        font-size: 14px;
        color: #666;
    }

    .wizard-step.active .step-title {
        color: #b77c52;
        font-weight: bold;
    }

    .wizard-content {
        display: none;
    }

    .wizard-content.active {
        display: block;
    }

    .service-card, .staff-card {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .service-card:hover, .staff-card:hover {
        border-color: #b77c52;
        box-shadow: 0 3px 10px rgba(183, 124, 82, 0.2);
    }

    .service-card.selected, .staff-card.selected {
        border-color: #b77c52;
        background: #fff9f5;
    }

    .service-card h5, .staff-card h5 {
        color: #b77c52;
        margin-bottom: 10px;
    }

    .service-price {
        font-size: 20px;
        font-weight: bold;
        color: #b77c52;
    }

    .time-slot {
        display: inline-block;
        padding: 10px 20px;
        margin: 5px;
        border: 2px solid #e0e0e0;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .time-slot:hover {
        border-color: #b77c52;
        background: #fff9f5;
    }

    .time-slot.selected {
        border-color: #b77c52;
        background: #b77c52;
        color: white;
    }

    .time-slot.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-wizard {
        background: #b77c52;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-wizard:hover {
        background: #a06842;
    }

    .btn-wizard:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .wizard-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .loading-slots {
        text-align: center;
        padding: 30px;
        color: #666;
    }

    .confirmation-details {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .confirmation-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .confirmation-row:last-child {
        border-bottom: none;
    }

    .confirmation-label {
        font-weight: bold;
        color: #666;
    }

    .confirmation-value {
        color: #333;
    }
</style>

<div class="booking-wizard">
    <h2 class="text-center mb-4">Book Your Pet Spa Appointment</h2>

    <!-- Wizard Steps -->
    <div class="wizard-steps">
        <div class="wizard-step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-title">Choose Service</div>
        </div>
        <div class="wizard-step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-title">Select Staff</div>
        </div>
        <div class="wizard-step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-title">Pick Date & Time</div>
        </div>
        <div class="wizard-step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-title">Confirm</div>
        </div>
    </div>

    <?php if (!$isLoggedIn): ?>
        <div class="alert-info">
            <strong>Note:</strong> You need to <a href="index.php?page=signin&redirect=booking">sign in</a> to complete your booking.
        </div>
    <?php endif; ?>

    <!-- Booking Form -->
    <form id="bookingForm" method="POST" action="index.php?page=booking&action=store">
        <!-- Step 1: Choose Service -->
        <div class="wizard-content active" data-step="1">
            <h4 class="mb-4">Select a Service</h4>
            <div id="servicesContainer">
                <?php foreach ($services as $service): ?>
                    <div class="service-card" data-service-id="<?= $service['id'] ?>" 
                         data-service-name="<?= htmlspecialchars($service['name']) ?>"
                         data-service-price="<?= number_format($service['price'], 2) ?>"
                         data-service-duration="<?= $service['duration_minutes'] ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5><?= htmlspecialchars($service['name']) ?></h5>
                                <p class="mb-2 text-muted"><?= htmlspecialchars($service['description']) ?></p>
                                <p class="mb-0"><small><i class="bi bi-clock"></i> <?= $service['duration_minutes'] ?> minutes</small></p>
                            </div>
                            <div class="text-end">
                                <div class="service-price">$<?= number_format($service['price'], 2) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="service_id" id="serviceId" required>
        </div>

        <!-- Step 2: Select Staff -->
        <div class="wizard-content" data-step="2">
            <h4 class="mb-4">Choose Your Groomer</h4>
            <div id="staffContainer">
                <?php foreach ($staffMembers as $staff): ?>
                    <div class="staff-card" data-staff-id="<?= $staff['id'] ?>"
                         data-staff-name="<?= htmlspecialchars($staff['name']) ?>"
                         data-staff-role="<?= htmlspecialchars($staff['role']) ?>">
                        <h5><?= htmlspecialchars($staff['name']) ?></h5>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($staff['role']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="staff_id" id="staffId" required>
        </div>

        <!-- Step 3: Pick Date & Time -->
        <div class="wizard-content" data-step="3">
            <h4 class="mb-4">Select Date and Time</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Appointment Date</label>
                    <input type="date" class="form-control" id="appointmentDate" name="appointment_date" 
                           min="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
            <div id="timeSlotsContainer">
                <p class="text-muted">Please select a date to view available time slots.</p>
            </div>
            <input type="hidden" name="appointment_time" id="appointmentTime" required>
        </div>

        <!-- Step 4: Confirm -->
        <div class="wizard-content" data-step="4">
            <h4 class="mb-4">Confirm Your Booking</h4>
            <div class="confirmation-details">
                <div class="confirmation-row">
                    <span class="confirmation-label">Service:</span>
                    <span class="confirmation-value" id="confirmService">-</span>
                </div>
                <div class="confirmation-row">
                    <span class="confirmation-label">Groomer:</span>
                    <span class="confirmation-value" id="confirmStaff">-</span>
                </div>
                <div class="confirmation-row">
                    <span class="confirmation-label">Date:</span>
                    <span class="confirmation-value" id="confirmDate">-</span>
                </div>
                <div class="confirmation-row">
                    <span class="confirmation-label">Time:</span>
                    <span class="confirmation-value" id="confirmTime">-</span>
                </div>
                <div class="confirmation-row">
                    <span class="confirmation-label">Duration:</span>
                    <span class="confirmation-value" id="confirmDuration">-</span>
                </div>
                <div class="confirmation-row">
                    <span class="confirmation-label">Price:</span>
                    <span class="confirmation-value" id="confirmPrice">-</span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Special Notes (Optional)</label>
                <textarea class="form-control" name="customer_notes" rows="3" 
                          placeholder="Any special requests or information about your pet..."></textarea>
            </div>

            <div class="alert alert-info">
                <strong>Please Note:</strong> Your appointment request will be reviewed by our team. 
                You will receive a confirmation once approved.
            </div>
        </div>

        <!-- Wizard Navigation -->
        <div class="wizard-actions">
            <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                Previous
            </button>
            <button type="button" class="btn btn-wizard" id="nextBtn">
                Next
            </button>
            <button type="submit" class="btn btn-wizard" id="submitBtn" style="display: none;">
                <?= $isLoggedIn ? 'Submit Booking' : 'Sign In to Book' ?>
            </button>
        </div>
    </form>
</div>

<script>
let currentStep = 1;
const totalSteps = 4;
let selectedService = null;
let selectedStaff = null;
let selectedDate = null;
let selectedTime = null;

// Service selection
document.querySelectorAll('.service-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        
        selectedService = {
            id: this.dataset.serviceId,
            name: this.dataset.serviceName,
            price: this.dataset.servicePrice,
            duration: this.dataset.serviceDuration
        };
        
        document.getElementById('serviceId').value = selectedService.id;
    });
});

// Staff selection
document.querySelectorAll('.staff-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.staff-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        
        selectedStaff = {
            id: this.dataset.staffId,
            name: this.dataset.staffName,
            role: this.dataset.staffRole
        };
        
        document.getElementById('staffId').value = selectedStaff.id;
    });
});

// Date selection - load available slots
document.getElementById('appointmentDate').addEventListener('change', function() {
    selectedDate = this.value;
    loadAvailableSlots();
});

function loadAvailableSlots() {
    if (!selectedStaff || !selectedDate) return;
    
    const container = document.getElementById('timeSlotsContainer');
    container.innerHTML = '<div class="loading-slots">Loading available time slots...</div>';
    
    // AJAX request to get available slots
    fetch('index.php?page=booking&action=check-availability', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `date=${selectedDate}&staff_id=${selectedStaff.id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.slots.length === 0) {
                container.innerHTML = '<p class="text-muted">No available slots for this date. Please select another date.</p>';
            } else {
                container.innerHTML = '<h5 class="mb-3">Available Time Slots</h5>';
                data.slots.forEach(slot => {
                    const slotBtn = document.createElement('button');
                    slotBtn.type = 'button';
                    slotBtn.className = 'time-slot';
                    slotBtn.textContent = slot.display;
                    slotBtn.dataset.time = slot.time;
                    
                    slotBtn.addEventListener('click', function() {
                        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                        this.classList.add('selected');
                        selectedTime = this.dataset.time;
                        document.getElementById('appointmentTime').value = selectedTime;
                    });
                    
                    container.appendChild(slotBtn);
                });
            }
        } else {
            container.innerHTML = `<p class="text-danger">${data.message}</p>`;
        }
    })
    .catch(error => {
        container.innerHTML = '<p class="text-danger">Error loading time slots. Please try again.</p>';
    });
}

// Wizard navigation
document.getElementById('nextBtn').addEventListener('click', function() {
    if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
            currentStep++;
            updateWizard();
        }
    }
});

document.getElementById('prevBtn').addEventListener('click', function() {
    if (currentStep > 1) {
        currentStep--;
        updateWizard();
    }
});

function validateStep(step) {
    switch(step) {
        case 1:
            if (!selectedService) {
                alert('Please select a service');
                return false;
            }
            break;
        case 2:
            if (!selectedStaff) {
                alert('Please select a groomer');
                return false;
            }
            break;
        case 3:
            if (!selectedDate) {
                alert('Please select a date');
                return false;
            }
            if (!selectedTime) {
                alert('Please select a time slot');
                return false;
            }
            break;
    }
    return true;
}

function updateWizard() {
    // Update steps
    document.querySelectorAll('.wizard-step').forEach(step => {
        const stepNum = parseInt(step.dataset.step);
        step.classList.remove('active', 'completed');
        
        if (stepNum === currentStep) {
            step.classList.add('active');
        } else if (stepNum < currentStep) {
            step.classList.add('completed');
        }
    });
    
    // Update content
    document.querySelectorAll('.wizard-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelector(`.wizard-content[data-step="${currentStep}"]`).classList.add('active');
    
    // Update buttons
    document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'inline-block';
    document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'inline-block';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-block' : 'none';
    
    // Update confirmation details
    if (currentStep === 4) {
        updateConfirmation();
    }
}

function updateConfirmation() {
    document.getElementById('confirmService').textContent = selectedService.name;
    document.getElementById('confirmStaff').textContent = `${selectedStaff.name} (${selectedStaff.role})`;
    document.getElementById('confirmDate').textContent = new Date(selectedDate).toLocaleDateString('en-US', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
    
    // Format time
    const timeObj = new Date(`2000-01-01 ${selectedTime}`);
    document.getElementById('confirmTime').textContent = timeObj.toLocaleTimeString('en-US', {
        hour: 'numeric', minute: '2-digit', hour12: true
    });
    
    document.getElementById('confirmDuration').textContent = `${selectedService.duration} minutes`;
    document.getElementById('confirmPrice').textContent = `$${selectedService.price}`;
}

// Form submission
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    <?php if (!$isLoggedIn): ?>
        e.preventDefault();
        window.location.href = 'index.php?page=signin&redirect=booking';
    <?php endif; ?>
});
</script>

<?php include 'views/layouts/footer.php'; ?>
