<?php
session_start();
// Unset all session variables
$_SESSION = array();
// Destroy the session.
session_destroy();
// Normally, you might send a response back to the client here
echo json_encode(["success" => true]);