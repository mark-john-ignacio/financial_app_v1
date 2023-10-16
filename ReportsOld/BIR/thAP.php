<?php
    if(!isset($_SESSION)){
        session_start();
    }
    require_once "../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $supplier = $_GET['name'];
    $trantype = intval($_GET['trantype']);
    $datefrom = $_GET['datefrom'];
    $dateto = $_GET['dateto'];
    
?>