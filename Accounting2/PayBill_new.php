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
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>
<script src="../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="PayBill_newsave.php" name="frmpos" id="frmpos" method="post">
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
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..." required autocomplete="off">
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
    
    <div class="col-xs-6">
      <input type="text" class="form-control input-sm" id="txtchkNo" name="txtchkNo" width="20px" tabindex="1" required value="<?php echo $cChkNoDef;?>">
    </div></td>
  </tr>
  <tr>
    <tH width="100" valign="top">PAYEE:</tH>
    <td valign="top" style="padding:2px"><div class="col-xs-10">
      <input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee">
    </div></td>
    <tH style="padding:2px">DATE:</tH>
    <td style="padding:2px"><div class="col-xs-6">
      <input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" />
    </div></td>
  </tr>
  <tr>
    <tH width="100" valign="top">MEMO:</tH>
    <td valign="top" style="padding:2px">
    <div class="col-xs-10"> 
      <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"></textarea>
    </div> 
      </td>
    <th valign="top" style="padding:2px">AMOUNT :</th>
    <td valign="top" style="padding:2px"><div class="col-xs-6">
      <input type="text" id="txtnGross" name="txtnGross" readOnly class="form-control input-sm" style="font-weight:bold; color:#F00; text-align:right" value="0.00">
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

  <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (F2)</button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>
</form>


</body>
</html>

<script type="text/javascript">
$(function(){
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY'
    });

	$('#txtcust').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "th_customer.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		autoSelect: true,
		displayText: function (item) {
			 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.typ + " " + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
			$("#txtpayee").val(item.value);
			
			$.ajax({
				type: "GET", 
				url: "PayBill_getDet.php",
				data: "id="+item.id,
				success: function(html) {
					$("#tableContainer").html(html);
					
								$("input.numeric").numeric({decimalPlaces: 4});
								$("input.numeric").on("focus", function () {
									$(this).select();
								});
														
								$("input.numeric").on("keyup", function (e) {
									CompOwed($(this).attr('name'));
									GoToComp();
									setPosi($(this).attr('name'),e.keyCode);
								});

				}
			});


		}
	});

});
		




function setPosi(nme,keyCode){
		var r = nme.replace(/\D/g,'');
		var namez = nme.replace(/[0-9]/g, '');
		
		
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

function CompOwed(nme){
		var numberPattern = /\d+/g;
		var r = nme.match(numberPattern);
		
		var disc = document.getElementById("nDiscount"+r).value;
		var amt = document.getElementById("nAmount"+r).value;
		
		var totowe = parseFloat(amt) - parseFloat(disc);
		
		document.getElementById("cTotOwed"+r).value = totowe.toFixed(4);
		
}

function chkform(){
	
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		if(document.getElementById("txtnGross").value == 0){
			alert("No Amount Applied!");
			return false;
		}
		else{
			document.getElementById("hdnrowcnt").value = lastRow;
			$("#frmpos").submit();
		}

}

function GoToComp(){
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
