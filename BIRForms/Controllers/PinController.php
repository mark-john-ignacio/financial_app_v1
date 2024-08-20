<?php
class PinController {
    private $correctPin = '1234'; // Ideally, this should be stored securely or fetched from a database

    public function __construct() {
        //session_start();
    }

    public function verifyPin($pin) {
        if ($pin === $this->correctPin) {
            $_SESSION['is_authenticated'] = true;
            return true;
        } else {
            $_SESSION['is_authenticated'] = false;
            return false;
        }
    }

    public function checkAuthentication() {
        if (!isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true) {
            header('Location: pin_access_view.php');
            exit;
        }
    }

    public function logout() {
        $_SESSION = array();
        session_destroy();
        echo json_encode(["success" => true]);
    }
}