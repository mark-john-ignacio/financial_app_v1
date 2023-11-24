<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>COOPERATIVE SYSTEM</title>
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">

  	<script type="text/javascript" src="../js/jquery.js"></script>

	<script type='text/javascript' src='../js/jquery.autocomplete.js'></script>
    <link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />

 
<script type="text/javascript">
$(function(){
	
	$("#paydebit").autocomplete("getaccount.php", { 
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#paydebit").result(function(event, data, formatted) {
		$("#paydebitid").val(data[1]);
	});


	$("#paycredit").autocomplete("getaccount.php", { 
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#paycredit").result(function(event, data, formatted) {
		$("#paycreditid").val(data[1]);
	});
		
});
</script>       
</head>

<body style="padding:5px" onLoad="document.getElementById('paydebit').focus();">
<form method="post" name="frmSet" id="frmSet" action="PayBill_setsave.php">
<fieldset>
	<legend>Account Settings</legend>
<table width="95%" border="0" cellpadding="0" align="right">
  <tr>
    <th scope="row" width="170">Default Debit Account</th>
    <td style="padding:2px">
    
    <?php
    	$sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='CVDEBIT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
			$nDebitDesc = $row['cacctdesc'];
		}
	}else{
		$nDebitDef = "";
		$nDebitDesc =  "";
	}
	?>
    
    <div class="col-xs-10"><input type="text" class="form-control input-xs" name="paydebit" id="paydebit" placeholder="Search Account Description..." required tabindex="1" value="<?php echo $nDebitDesc;?>"> <input type="hidden" name="paydebitid" id="paydebitid"  value="<?php echo $nDebitDef;?>"> </div></td>
  </tr>
  <tr>
    <th scope="row">Credit Account (APV)</th>
    <td style="padding:2px">
    
	<?php
    	$sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='CVCREDIT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nCreditDef = $row['cvalue'];
			$nCreditDesc = $row['cacctdesc'];
		}
	}else{
		$nCreditDef = "";
		$nCreditDesc = "";
	}
	?>
    
    <div class="col-xs-10"><input type="text" class="form-control input-xs" name="paycredit" id="paycredit" placeholder="Search Account Description..." required tabindex="2" value="<?php echo $nCreditDesc;?>"> <input type="hidden" name="paycreditid" id="paycreditid" value="<?php echo $nCreditDef;?>"> </div></td>
  </tr>
</table>

</fieldset>
<br>
<fieldset>
	<legend>PrintOut Settings</legend>
  <table width="95%" border="0" cellpadding="0"  align="right">
      <tr>
        <th scope="row"  width="170">Prepared By</th>
        <td style="padding:2px">
    <?php
    	$sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVPREP'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$cprepared = $row['cvalue'];
		}
	}else{
		$cprepared = "";
	}
	?>

        <div class="col-xs-10"><input type="text" class="form-control input-xs" name="cprepared" id="cprepared" placeholder="Enter Name or Initials..."  tabindex="3" value="<?php echo $cprepared;?>"></div></td>
      </tr>
      <tr>
        <th scope="row">Reviewed By</th>
        <td style="padding:2px">
    <?php
    	$sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVREVW'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$creview = $row['cvalue'];
		}
	}else{
		$creview = "";
	}
	?>

        <div class="col-xs-10"><input type="text" class="form-control input-xs" name="creviewed" id="creviewed" placeholder="Enter Name or Initials..." tabindex="4" value="<?php echo $creview;?>"></div></td>
      </tr>
      <tr>
        <th scope="row">Verified By</th>
        <td style="padding:2px">
    <?php
    	$sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVVERI'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$cverify = $row['cvalue'];
		}
	}else{
		$cverify = "";
	}
	?>
        
        <div class="col-xs-10"><input type="text" class="form-control input-xs" name="cverified" id="cverified" placeholder="Enter Name or Initials..." tabindex="5" value="<?php echo $cverify;?>"></div></td>
      </tr>
      <tr>
        <th scope="row">Approved By</th>
        <td style="padding:2px">
    <?php
    	$sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVAPPR'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$capprv = $row['cvalue'];
		}
	}else{
		$capprv = "";
	}
	?>
        
        <div class="col-xs-10"><input type="text" class="form-control input-xs" name="capproved" id="capproved" placeholder="Enter Name or Initials..." tabindex="6" value="<?php echo $capprv;?>"></div></td>
      </tr>
  </table>
</fieldset>
<br><br>
<center>
<button type="submit" class="btn btn-success btn-sm"><span class="glyphicon glyphicon glyphicon-floppy-disk"></span> Save</button>
</center>
</form>
</body>
</html>