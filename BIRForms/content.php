<?php
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    switch ($page) {
        case 'home':
            echo "<h1>Welcome Home</h1>";
            break;
        case 'manageForms':
            require_once 'Controllers/FormsController.php';
            $controller = new FormsController();
            $controller->manageForms();
            break;
        default:
            echo "<h1>Page Not Found</h1>";
    }
} else {
    echo "<h1>Welcome</h1>";
}