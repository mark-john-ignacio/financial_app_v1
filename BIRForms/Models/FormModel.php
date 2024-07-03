<?php
class FormModel {
    public function isAuthenticated() {
        session_start();
        return isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'] === true;
    }
}