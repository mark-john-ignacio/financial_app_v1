<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
    // print_r($_POST);
    // print_r($_SESSION);

    $_SESSION['pageid'] = "BIRForms";

    include("../../Connection/connection_string.php");
    include('../../include/denied.php');
    include('../../include/access.php');

    $company = $_SESSION['companyid'];

    $sql = "select * From company where compcode='$company'";
    $result=mysqli_query($con,$sql);
    
    if (!mysqli_query($con, $sql)) {
        printf("Errormessage: %s\n", mysqli_error($con));
    } 
        
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $comprdo = $row;
    }

    $month = str_pad($_POST['selmonth'], 2, "0", STR_PAD_LEFT);
    $year = $_POST['years'];

    $UrlBase = str_replace("Components/assets/","",$AttachUrlBase);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="../../include/select2/select2.min.css">

    <link rel="stylesheet" type="text/css" href="../../global/plugins/icheck/skins/all.css?x=<?php echo time();?>">

    <link href="../../global/css/components.css?x=<?=time()?>" rel="stylesheet" type="text/css"/>

    <link href="css/style.css?x=<?=time()?>" rel="stylesheet" type="text/css"/>

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../include/select2/select2.full.min.js"></script>

    <script src="../../global/plugins/icheck/icheck.min.js"></script>

    <script src="../../include/autoNumeric.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4/dist/autoNumeric.min.js"></script>
	<script src="../../include/FormatNumber.js"></script>

    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MyxFinancials</title>
</head>