<?php
require_once 'config/database.php';
require_once 'models/SpaService.php';
require_once 'models/Staff.php';
require_once 'models/Appointment.php';
require_once 'includes/SessionManager.php';

class BookingController {
    private $db;
    private $spaService;
    private $staff;
    private $appointment;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->spaService = new SpaService($this->db);
        $this->staff = new Staff($this->db);
        $this->appointment = new Appointment($this->db);
    }

    /**
     * Display the booking wizard
     */
    public function index() {
        // Get all active services and staff for the form
        $services = $this->spaService->getAllActive();
        $staffMembers = $this->staff->getAllActive();
        
        // Check if user is logged in
        $isLoggedIn = SessionManager::isUserLoggedIn();
        
        include 'views/booking/index.php';
    }

    /**
     * AJAX endpoint to check availability for a specific date and staff
     */
    public function checkAvailability() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $date = isset($_POST['date']) ? trim($_POST['date']) : '';
        $staffId = isset($_POST['staff_id']) ? intval($_POST['staff_id']) : 0;
        
        // Validate inputs
        if (empty($date) || $staffId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid date or staff selection']);
            return;
        }
        
        // Validate date format and ensure it's not in the past
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        if (!$dateObj || $dateObj < $today) {
            echo json_encode(['success' => false, 'message' => 'Invalid date or date is in the past']);
            return;
        }
        
        // Get available slots
        $slots = $this->appointment->getAvailableSlots($date, $staffId);
        
        echo json_encode([
            'success' => true,
            'slots' => $slots
        ]);
    }

    /**
     * Store a new appointment (status: pending)
     */
    public function store() {
        // Ensure user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            $_SESSION['error_message'] = 'Please log in to book an appointment.';
            header('Location: index.php?page=signin&redirect=booking');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=booking');
            exit();
        }
        
        // Get form data
        $userId = $_SESSION['user_id'];
        $serviceId = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
        $staffId = isset($_POST['staff_id']) ? intval($_POST['staff_id']) : 0;
        $date = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';
        $time = isset($_POST['appointment_time']) ? trim($_POST['appointment_time']) : '';
        $notes = isset($_POST['customer_notes']) ? trim($_POST['customer_notes']) : '';
        
        // Validate inputs
        $errors = [];
        
        if ($serviceId <= 0) {
            $errors[] = 'Please select a service.';
        }
        
        if ($staffId <= 0) {
            $errors[] = 'Please select a staff member.';
        }
        
        if (empty($date)) {
            $errors[] = 'Please select an appointment date.';
        }
        
        if (empty($time)) {
            $errors[] = 'Please select an appointment time.';
        }
        
        // Validate date
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        if (!$dateObj || $dateObj < $today) {
            $errors[] = 'Invalid appointment date.';
        }
        
        // Validate time format
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $time)) {
            $errors[] = 'Invalid time format.';
        }
        
        // Check if slot is still available
        if (empty($errors) && !$this->appointment->isSlotAvailable($date, $time, $staffId)) {
            $errors[] = 'This time slot is no longer available. Please select another time.';
        }
        
        // If there are errors, redirect back with error messages
        if (!empty($errors)) {
            $_SESSION['error_message'] = implode('<br>', $errors);
            header('Location: index.php?page=booking');
            exit();
        }
        
        // Create appointment data
        $appointmentData = [
            'user_id' => $userId,
            'service_id' => $serviceId,
            'staff_id' => $staffId,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'customer_notes' => $notes
        ];
        
        // Save appointment
        $appointmentId = $this->appointment->create($appointmentData);
        
        if ($appointmentId) {
            $_SESSION['success_message'] = 'Your appointment request has been submitted successfully! We will review and confirm your booking shortly.';
            
            // Notify user about the new booking
            require_once 'models/Notification.php';
            // Use the existing DB connection from the controller instead of creating a new one
            $notification = new Notification(); 

            // 1. Define the specific message
            $message = "Your Pet Spa booking #{$appointmentId} has been placed successfully.";

            // 2. Define the exact link you asked for
            // (Using relative path is safer, but matches your request structure)
            $link = "index.php?page=user-appointments";

            // 3. Call create with the correct 3 arguments: (User, Message, Link)
            $notification->create($userId, $message, $link);

            header('Location: index.php?page=user-appointments');
        } else {
            $_SESSION['error_message'] = 'Failed to create appointment. Please try again.';
            header('Location: index.php?page=booking');
        }
        exit();
    }

    /**
     * View user's appointments
     */
    public function myAppointments() {
        // Ensure user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            header('Location: index.php?page=signin');
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $appointments = $this->appointment->getUserAppointments($userId);
        
        include 'views/user/appointments.php';
    }
}
