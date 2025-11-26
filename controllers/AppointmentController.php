<?php
require_once 'models/Appointment.php';
require_once 'includes/SessionManager.php';

class AppointmentController {
    private $appointmentModel;

    public function __construct() {
        $this->appointmentModel = new Appointment();
    }

    public function index() {
        // Check login
        if (!SessionManager::isUserLoggedIn()) {
            $_SESSION['redirect_after_login'] = SITE_URL . 'appointments';
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }

        $pageTitle = 'Book Appointment';
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'appointments/book.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }

    public function book() {
        if (!SessionManager::isUserLoggedIn()) {
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $petName = trim($_POST['pet_name']);
            $serviceType = $_POST['service_type'];
            $date = $_POST['date'];
            $time = $_POST['time'];
            $notes = trim($_POST['notes']);

            $appointmentDateTime = $date . ' ' . $time . ':00';

            // 1. Basic Validation
            if (empty($petName) || empty($date) || empty($time)) {
                $error = "Please fill in all required fields.";
                include VIEWS_PATH . 'layouts/header.php';
                include VIEWS_PATH . 'appointments/book.php';
                include VIEWS_PATH . 'layouts/footer.php';
                return;
            }

            // 2. Date Validation (Cannot be in past)
            if (strtotime($appointmentDateTime) < time()) {
                $error = "You cannot book an appointment in the past.";
                include VIEWS_PATH . 'layouts/header.php';
                include VIEWS_PATH . 'appointments/book.php';
                include VIEWS_PATH . 'layouts/footer.php';
                return;
            }

            // 3. Staff Availability Check (Check if slot is taken)
            if ($this->appointmentModel->isSlotTaken($appointmentDateTime)) {
                $error = "Sorry, that time slot is already booked. Please choose another time.";
                include VIEWS_PATH . 'layouts/header.php';
                include VIEWS_PATH . 'appointments/book.php';
                include VIEWS_PATH . 'layouts/footer.php';
                return;
            }

            // 4. Save to DB
            $data = [
                'user_id' => $_SESSION['user_id'],
                'pet_name' => $petName,
                'service_type' => $serviceType,
                'appointment_date' => $appointmentDateTime,
                'notes' => $notes
            ];

            if ($this->appointmentModel->create($data)) {
                $success = "Appointment booked successfully!";
                // Clear form
                $petName = ''; $notes = '';
                include VIEWS_PATH . 'layouts/header.php';
                include VIEWS_PATH . 'appointments/book.php';
                include VIEWS_PATH . 'layouts/footer.php';
            } else {
                $error = "Something went wrong. Please try again.";
                include VIEWS_PATH . 'layouts/header.php';
                include VIEWS_PATH . 'appointments/book.php';
                include VIEWS_PATH . 'layouts/footer.php';
            }
        } else {
            header('Location: ' . SITE_URL . 'appointments');
        }
    }
    
    public function myAppointments() {
        if (!SessionManager::isUserLoggedIn()) {
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }
        
        $appointments = $this->appointmentModel->getByUserId($_SESSION['user_id']);
        $pageTitle = 'My Appointments';
        
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'appointments/list.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }
}