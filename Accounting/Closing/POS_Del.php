<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

//POST RECORD
$company = $_SESSION['companyid'];
$ctranno = "";
$ddate = "";
$cmodule = "";
$cnt = 0;
$sqlhead = mysqli_query($con,"select * from trialbaltrans where cremarks = 'N' order by ddate, csortval");

if (mysqli_num_rows($sqlhead)!=0) {
	
	$cnt = mysqli_num_rows($sqlhead);
	
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		
		$ctranno = $row["ctranno"];
		$ddate = $row["ddate"];
		$cmodule = $row["cmodule"];
	}
}

mysqli_close($con);


if ($ctranno!=""){
	echo $cnt;
?>


<form name="frmact" id="frmact" action="MScript.php" method="POST">
	<input name="ctranno" id="ctranno" value="<?php echo $ctranno;?>" type="hidden"/>
    <input name="ddate" id="ddate" value="<?php echo $ddate;?>"  type="hidden"/>
    <input name="cmodule" id="cmodule" value="<?php echo $cmodule;?>"  type="hidden" />
</form>

<script>
	document.getElementById("frmact").submit();
</script>

<?php

}

else{
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Coop Financials</title>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="../../Bootstrap/css/bootstrap.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="../../global/plugins/bootstrap-datepicker/css/datepicker.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
</head>
<body>
<center><font size="+2" style="font-weight:300; color:#093">
DONE PROCESSING
</font>
</center><br><br>
                    	<button type="button" class="btn btn-danger btn-sm btn-block" id="btnmonthcloseX" name="btnmonthcloseX">CLOSE</button>


<script src="../../global/plugins/jquery.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../../global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="../../bootstrap/js/bootstrap.js" type="text/javascript"></script>
<script src="../../global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script> 

<script>
	  
	  $(function(){
		  $("#btnmonthcloseX").on("click", function() {
				top.location.href = "../../main.php"
       	});
			  
	  });
	  
   </script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>

<?php
}
?>