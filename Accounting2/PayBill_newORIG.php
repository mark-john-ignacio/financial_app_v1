<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PayBill_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
$company = $_SESSION['companyid'];
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>COOPERATIVE SYSTEM</title>
    
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min34.css">
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap-select.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-datetimepicker.min.css">
    
  <script type="text/javascript" src="../js/jquery.js"></script>
  
  <script type="text/javascript" src="lib/js/bootstrap-select.js"></script>
  <script src="../js/bootstrap.min.js"></script>


<script type='text/javascript' src='../js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />


<script language="javascript" type="text/javascript" src="../js/datetimepicker.js"></script>

<script type="text/javascript">
$(function(){
	
	$("#txtcust").autocomplete("get_supplier.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#txtcust").result(function(event, data, formatted) {
		$("#txtcustid").val(data[1]);
		
		document.getElementById("txtpayee").value = document.getElementById("txtcust").value;
	
		$.ajax({
				type: "GET", 
				url: "PayBill_getDet.php",
				data: "id="+data[1],
				success: function(html) {
					$("#tableContainer").html(html);
				}
			});
		});
	
	
});


function isNumber(keyCode) {
	return ((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode == 189 || keyCode == 37 || keyCode == 110 || keyCode == 190 || keyCode == 39 || (keyCode >= 96 && keyCode <= 105 || keyCode == 9))
}

function chkdecimal(valz,nme,initials){
		var numberPattern = /\d+/g;
		var r = nme.match(numberPattern);

		var nname = initials + r;
	
		var pattern = /^-?[0-9]+(.[0-9]{1,2})?$/; 
		var text = valz;
	
		if (text.match(pattern)==null) 
		{
			alert("Invalid Quantity.\nNote: 2 decimal places only");
			with (document.forms[0].elements[nname])
			{
			 value = 0.00;
			}

		}
		
		if(initials=="nDiscount"){
			computeowed(nme);
		}
		
		if(initials=="nApplied"){
			totGross();
		}
}

function setPosi(nme,keyCode,namez){
		var numberPattern = /\d+/g;
		var r = nme.match(numberPattern);

		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		

		if(namez=="nDiscount"){
			//alert(keyCode);
			if(keyCode==38 && r!=1){//Up
				var z = parseInt(r) - parseInt(1);
				document.getElementById("nDiscount"+z).focus();
			}
			
			if(keyCode==40 && r!=lastRow){//Down
				var z = parseInt(r) + parseInt(1);
				document.getElementById("nDiscount"+z).focus();
			}
			
			if(keyCode==39){ //To Right
				document.getElementById("nApplied"+r).focus();
			}

		}

		if(namez=="nApplied"){
			//alert(keyCode);
			if(keyCode==38 && r!=1){//Up
				var z = parseInt(r) - parseInt(1);
				document.getElementById("nApplied"+z).focus();
			}
			
			if(keyCode==40 && r!=lastRow){//Down
				var z = parseInt(r) + parseInt(1);
				document.getElementById("nApplied"+z).focus();
			}
			
			if(keyCode==37){ //To Left
				document.getElementById("nDiscount"+r).focus();
			}

		}

}

function computeowed(nme){
		var numberPattern = /\d+/g;
		var r = nme.match(numberPattern);
		
		var disc = document.getElementById("nDiscount"+r).value;
		var amt = document.getElementById("nAmount"+r).value;
		
		var totowe = parseFloat(amt) - parseFloat(disc);
		
		document.getElementById("cTotOwed"+r).value = totowe.toFixed(2);
		
}

function chkRows(){
	
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		document.getElementById("hdnrowcnt").value = lastRow;
		
		if(document.getElementById("txtnGross").value == 0){
			alert("No Amount Applied!");
			return false;
		}

}

function totGross(){
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		var z;
		var gross = 0;
		
		for (z=1; z<=lastRow; z++){
			gross = parseFloat(gross) + parseFloat(document.getElementById("nApplied"+z).value);
		}
		
		document.getElementById("txtnGross").value = gross.toFixed(2);

}
</script>

  <style type='text/css'>

.deleterow{cursor:pointer}

		.container{
			width: 800px;
			margin: 0 auto;
		}



		ul.tabs{
			margin: 0px;
			padding: 0px;
			list-style: none;
		}
		ul.tabs li{
			background: none;
			color: #222;
			display: inline-block;
			padding: 10px 15px;
			cursor: pointer;
		}

		ul.tabs li.current{
			background: #ededed;
			color: #222;
		}

		.tab-content{
			display: none;
			background: #ffffff;
			padding: 15px;
		}

		.tab-content.current{
			display: inherit;
		}

  </style>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="PayBill_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return chkRows();">
	<fieldset>
    	<legend>Pay Bills</legend>	
        <table width="100%" border="0">
  <tr>
    <tH width="100">Account:</tH>
    <td style="padding:2px;" width="500">
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
  
    <div class="col-xs-8">
        	<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>">
</div> 

        	<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
        
    </td>
    <tH width="150">Balance:</tH>
    <td style="padding:2px;">&nbsp;</td>
  </tr>
  <tr>
    <tH width="100" valign="top">SUPPLIER:</tH>
    <td valign="top" style="padding:2px">
    
        <div class="col-xs-8">
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..." required>
</div> 

        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly>
            
    </td>
    <tH width="150" style="padding:2px">CHECK NO.:</tH>
    <td style="padding:2px">
    <?php
    	$sqlchk = mysqli_query($con,"select * from paybill where compcode='$company' Order By ctranno desc LIMIT 1");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$cChkNoDef = $row['cchkno'];
		}
	}else{
		$cChkNoDef = "";
	}
	?>
    
    <div class="col-xs-8">
      <input type="text" class="form-control input-sm" id="txtchkNo" name="txtchkNo" width="20px" tabindex="1" required value="<?php echo $cChkNoDef;?>">
    </div></td>
  </tr>
  <tr>
    <tH width="100" valign="top">PAYEE:</tH>
    <td valign="top" style="padding:2px"><div class="col-xs-10">
      <input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee">
    </div></td>
    <tH style="padding:2px">DATE:</tH>
    <td style="padding:2px"><div class="col-xs-5"> <a href="javascript:NewCal('date_delivery','mmddyyyy')">
      <input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" readonly/>
    </a></div></td>
  </tr>
  <tr>
    <tH width="100" valign="top">MEMO:</tH>
    <td valign="top" style="padding:2px">
    <div class="col-xs-10"> 
      <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"></textarea>
    </div> 
      </td>
    <th valign="top" style="padding:2px">AMOUNT :</th>
    <td valign="top" style="padding:2px"><div class="col-xs-5">
      <input type="text" id="txtnGross" name="txtnGross" readOnly style="border:none; background:#FFF; font-weight:bold; color:#F00" value="0.00">
    </div></td>
    </tr>
</table>
<br>
	  <div id="tableContainer" class="alt2" dir="ltr" style="
                        margin: 0px;
                        padding: 3px;
                        border: 1px solid #919b9c;
                        width: 100%;
                        height: 250px;
                        text-align: left;
                        overflow: auto">
<table width="100%" border="0" cellpadding="0" id="MyTable">
  <tr>
    <th scope="col">APV No</th>
    <th scope="col">Status</th>
    <th scope="col">Date</th>
    <th scope="col">Amount</th>
    <th scope="col">Discount</th>
    <th scope="col">Total Owed</th>
    <th scope="col">Amount Applied</th>
  </tr>
</table>
</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">
   <input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
   <button type="submit" class="btn btn-success btn-sm" id="btnSave" name="btnSave">
   <table align="center">
    <tr>
      <td><img src="../images/diskette.jpg" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>Save</td>
    </tr>
  </table>
   </button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>
</form>


</body>
</html>