<?php
require_once 'Models/FormModel.php';

class FormsController {
    private $model;

    public function __construct() {
        $this->model = new FormModel();
    }

    public function manageForms() {
        if (!$this->model->isAuthenticated()) {
            include 'Views/pin_access_view.php';
            exit;
        }
        include 'Views/manage_bir_forms.php';
    }
}