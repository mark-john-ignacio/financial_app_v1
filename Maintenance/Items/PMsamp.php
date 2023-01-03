<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Items_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');


$company = $_SESSION['companyid'];
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../../Bootstrap/js/moment.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

</head>

<body style="padding:5px;">

  <ul class="nav nav-tabs">
    <li class="active"><a href="#home">General</a></li>
    <li><a href="#menu1">Account Codes</a></li>
  </ul>

<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 30vh;text-align: left;overflow: auto">

    <div class="tab-content">
    
        <div id="home" class="tab-pane fade in active" style="padding-left:30px">
         <p>
         Home
         </p>
        </div>
        
        <div id="menu1" class="tab-pane fade" style="padding-left:30px">
         <p>
         Codes
         </p>        
       </div>
 

    </div>

</div>
</body>
</html>



<script type="text/javascript">
$(document).ready(function(){
	
	$(".nav-tabs a").click(function(){
        $(this).tab('show');
    });

});
</script>
