<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesReg.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];				

				$sql = "select * From company where compcode='$company'";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$compname =  $row['compname'];
				}

?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">  
<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../Bootstrap/switch/css/bootstrap-switch.css" rel="stylesheet">

 
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/switch/js/highlight.js"></script>
<script src="../../Bootstrap/switch/js/bootstrap-switch.js"></script>
<script src="../../Bootstrap/switch/js/main.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Income Statement</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Profit &amp; Loss Statement</h2>
<h3>SETUP</h3><br>
</center>

<?php

$sqlgenerals = "select * From accounts where compcode='$company' and cFinGroup='Income Statement' order By cacctno";
$result=mysqli_query($con,$sqlgenerals);

?>
<table class="table table-condensed">
	
    <?php
    	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
					$nlvl = intval($row['nlevel']);
					
					$indnt = 0;
					if($nlvl>1){
						$indnt = (5 * $nlvl) + ($nlvl * 2);
					}


		if($row['ctype']=="General"){
			

	?>
    <tr>
    	<td colspan="3" style="text-indent:<?php echo $indnt; ?>px"><b><?php echo $row['cacctdesc'];?></b></td>
    </tr>
    
    <?php
		}
		else{
	?>
     <tr>
    	<td style="text-indent:<?php echo $indnt; ?>px"><?php echo $row['cacctdesc'];?></td>
        <td width="250px" align="right">

        <input id="switch-state" type="checkbox" checked="checked" data-on-text="ADD" data-off-text="LESS" data-size="mini" data-off-color="warning" data-handle-width="80">

        </td>
        <td width="150" align="right">
        <i class="up fa fa-angle-up fa-sm fa-border"></i>
        <i class="down fa fa-angle-down fa-sm fa-border"></i>
        <i class="top fa fa-angle-double-up fa-sm fa-border"></i>
        <i class="bottom fa fa-angle-double-down fa-sm fa-border"></i>
        </td>
    </tr>
   
    <?php
		}
		}
	?>
</table>
<hr>

</body>
</html>
<script>
$(document).ready(function(){
    $(".up,.down,.top,.bottom").click(function(){
        var row = $(this).parents("tr:first");
        if ($(this).is(".up")) {
            row.insertBefore(row.prev());
        } else if ($(this).is(".down")) {
            row.insertAfter(row.next());
        } else if ($(this).is(".top")) {
            //row.insertAfter($("table tr:first"));
            row.insertBefore($("table tr:first"));
        }else {
            row.insertAfter($("table tr:last"));
        }
    });
});
</script>