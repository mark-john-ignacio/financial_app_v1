<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PurchSummary.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?><html>
<head>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>

<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coop Financials</title>
</head>

<body style="padding-left:50px;">
<center>
  <font size="+1"><b><u>Purchased Summary</u></b></font>
</center>
<br>

<form action="Purchases/PurchSumItem.php" method="post" name="frmrep" id="frmrep" target="_blank">
<table width="100%" border="0" cellpadding="2">
  <tr>
    <td rowspan="3" valign="top" width="50" style="padding:2px">
    <button type="submit" class="btn btn-danger navbar-btn" id="btnsales">
    <span class="glyphicon glyphicon-search"></span> View Report
    </button>
    </td>
    <td width="150" style="padding-left:10px"><b>Product: </b></td>
    <td style="padding:2px">
			<div class="col-xs-6 nopadding">	
			<SELECT name="seltyp" id="seltyp" class="form-control input-sm" onChange="setact(this.value);">
            
            	<option value="Purchases/PurchSumItem.php">Per Item</option>
                <option value="Purchases/PurchSumCust.php">Per Customer</option>
                <option value="Purchases/PurchSumInv.php">Per Transaction</option>
                <option value="Purchases/PurchSumMonth.php">Per Month</option>
                
            </SELECT>
            </div>	
   </td>
  </tr>
  <tr>
    <td style="padding-left:10px"><b>Date Range: </b></td>
    <td style="padding:2px">
    <div class="col-xs-12 nopadding" id="datezpick">
        <div class="col-xs-3 nopadding">

		<input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" />

		</div>
        
        <div class="col-xs-1 nopadding" style="vertical-align:bottom;" align="center">
        	<label style="padding:1px;">TO</label>
        </div>
 
         <div class="col-xs-3 nopadding">

		<input type='text' class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>" />

		</div>

     </div>   

        
         <div class="col-xs-3 nopadding" id="monthpick" style="display:none">
			<select name="selmonth" id="id" class="form-control input-sm">
            	<?php 
					$now = date("Y");
					//$varyr = $now - 2014;
					
					for ($x=2015; $x<=$now; $x++){
				?>
                	<option value="<?php echo $x;?>" <?php if($x==$now){echo "selected";}?>><?php echo $x;?></option>
                <?php } ?>
            </select>
     	 </div>

    </td>
  </tr>
</table>
</form>
</body>
</html>
<script type="text/javascript">
$(function(){

	        $('.datepick').datetimepicker({
                 format: 'MM/DD/YYYY'
           });
});

function setact(x){
	document.getElementById("frmrep").action = x;
	
	if(x=="Purchases/PurchSumMonth.php"){
		document.getElementById("datezpick").style.display = "none";
		document.getElementById("monthpick").style.display = "inline";
	}
	else{
		document.getElementById("monthpick").style.display = "none";
		document.getElementById("datezpick").style.display = "inline";
	}
}
</script>
