<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PayBill_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');
$company = $_SESSION['companyid'];

$ddeldate = date("m/d/Y");
$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));

    	$sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc,b.nbalance From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='CVCRCASH'");
		if (mysqli_num_rows($sqlchk)!=0) {
			while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
				$nDebitDef = $row['cvalue'];
				$nDebitDesc = $row['cacctdesc'];
				$nDebitDBalz = $row['nbalance'];
			}
		}else{
			$nDebitDef = "";
			$nDebitDesc =  "";
			$nDebitDBalz = 0;
		}
		
		
		$sqlchk2 = mysqli_query($con,"Select A.ccode, A.cname, A.cnxtchkno from bank A where A.compcode='$company' and A.caccntno='$nDebitDef'");
		
		//echo "Select A.caccntno. B.cacctdesc, A.cnxtchkno from bank A left join accounts B on A.compcode=B.compcode and A.caccntno=B.cacctno where A.compcode='$company' and A.caccntno='$nDebitDef'";
		
		if (mysqli_num_rows($sqlchk2)!=0) {
			while($row2 = mysqli_fetch_array($sqlchk2, MYSQLI_ASSOC)){
				$Bacctno = $row2['ccode'];
				$Bacctdesc = $row2['cname'];
				$BCheck = $row2['cnxtchkno'];
			}
		}else{
			$Bacctno = "";
			$Bacctdesc =  "";
			$BCheck = "";
		}

		?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="PayBill_newsave.php" name="frmpos" id="frmpos" method="post">
	<fieldset>
   	  <legend>AP Payments</legend>	
        <table width="100%" border="0">
  <tr>
    <tH width="150" style="padding:2px">Payment Account:</tH>
    <td style="padding:2px;" width="500">
  <div class="col-xs-12 nopadding">
    <div class="col-xs-6 nopadding">
        	<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>" autocomplete="off">
	</div> 
	<div class="col-xs-6 nopadwleft">
        	<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
    </div>
  </div>    
    </td>
    <tH width="150" style="padding:2px">Balance:</tH>
    <td style="padding:2px;">
    <div class="col-xs-6 nopadding">
        	<input type="text" class="form-control input-sm" id="txtnbalance" name="txtnbalance" readonly value="<?php echo $nDebitDBalz;?>" style="text-align:right">
        </div></td>
  </tr>
  <tr>
    <tH width="150" valign="top"><span style="padding:2px">Paid To:</span></tH>
    <td valign="top" style="padding:2px">    
    <div class="col-xs-12 nopadding">
    <div class="col-xs-6 nopadding">
        <input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4">
      </div>
      <div class="col-xs-6 nopadwleft">
        <input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly>
      </div>
    </div>
    </td>
    <tH width="150" style="padding:2px">Payment Date:</tH>
    <td style="padding:2px"><div class="col-xs-6 nopadding">
      <input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" tabindex="3"  />
    </div></td>
  </tr>
  <tr>
    <tH width="150" valign="top" style="padding:2px">Payee:</tH>
    <td valign="top" style="padding:2px">
        <div class="col-xs-10 nopadding">
      <input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee" tabindex="5">
      
      
    </div>

    
      </td>
    <tH colspan="2" rowspan="5" style="padding:2px; padding-top:5px">
    <fieldset class="fieldset1">
      <legend class="legend1">Cheque Details</legend>
      
      <div class='col-xs-12' style="padding-bottom:2px">
         <div class='col-xs-4 nopadding'>
      		<b>Bank Name</b>
         </div>
         <div class='col-xs-8 nopadwleft'>
           <input type='text' class='form-control input-sm' name='txtBankName' id='txtBankName' readonly value="<?php echo $Bacctdesc;?>" />
           <input type='hidden' name='txtBank' id='txtBank' value="<?php echo $Bacctno;?>" /> 
         </div>
      </div> 
           
           
      <div class='col-xs-12' style="padding-bottom:2px">
         <div class='col-xs-4 nopadding'>
      		<b>Cheque Date</b>
         </div>
         <div class='col-xs-8 nopadwleft'>

              <input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate" value="<?php echo date("m/d/Y"); ?>" />
              
         </div>
      </div>
      
      <div class='col-xs-12' style="padding-bottom:2px">
         <div class='col-xs-4 nopadding'>
      		<b>Cheque No.</b>
         </div>
          <div class='col-xs-8 nopadwleft'>
          <input type='text' class='form-control input-sm' name='txtCheckNo' id='txtCheckNo' readonly value="<?php echo $BCheck;?>" />
          
          </div>
      </div>
      <div class='col-xs-12' style="padding-bottom:2px">
         <div class='col-xs-4 nopadding'>
      		<b>&nbsp;</b>
         </div>
          <div class='col-xs-8 nopadwleft'>
            <button type="button" class="btn btn-danger btn-sm" name="btnVoid" id="btnVoid">VOID CHECK NO. </button>
            </div>
       </div>       

    </fieldset>     
    
    
     </tH>
    </tr>
  <tr>
    <tH width="150" valign="top" style="padding:2px">Memo:</tH>
    <td rowspan="2" valign="top" style="padding:2px">
      <div class="col-xs-10 nopadding"> 
        <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks" tabindex="6"></textarea>
      </div>       </td>
    </tr>
  <tr>
    <tH valign="top">&nbsp;</tH>
    </tr>
  <tr>
    <tH valign="top"><span style="padding:2px">Total Paid :</span></tH>
    <td valign="top" style="padding:2px"><div class="col-xs-6 nopadding">
      <input type="hidden" id="txtnGross" name="txtnGross" value="0.0000">
      <input type="text" id="txttotpaid" name="txttotpaid" class="numericchkamt form-control input-sm" value="0.0000" style="font-weight:bold; color:#F00; text-align:right">
    </div></td>
    </tr>
  <tr>
    <tH valign="top" height="50px">&nbsp;</tH>
    <td valign="top" style="padding:2px">&nbsp;</td>
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
 <thead>
  <tr>
    <th scope="col">AP No</th>
    <th scope="col">Date</th>
    <th scope="col">Amount(PHP)</th>
    <th scope="col">Payed(PHP)</th>
    <th scope="col" width="150px">Total Owed(PHP)</th>
    <th scope="col" width="150px">Amount Applied(PHP)</th>
  </tr>
 </thead>
</table>
</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
    
   <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PayBill.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>

   
  <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>
    
    



</form>


<!-- 1) Alert Modal -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div class="modal-content">
               <div class="alert-modal-danger">
                  <p id="AlertMsg"></p>
                <p>
                    <center>
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("PayBill.php");

	  }
	});


$(document).ready(function() {
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY',
    });
	
});


$(function(){ 

	$('#txtcacct').typeahead({
	
		source: function (query, process) {
			return $.getJSON(
				'../th_accounts.php',
				{ query: query },
				function (data) {
					newData = [];
					map = {};
					
					$.each(data, function(i, object) {
						map[object.name] = object;
						newData.push(object.name);
					});
					
					process(newData);
				});
		},
		updater: function (item) {	
			  
				$('#txtcacctid').val(map[item].id);
				$('#txtnbalance').val(map[item].balance);
				return item;
		}
	
	});
	

	$('#txtcust').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "../th_csall.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val(), x: $("#selaptyp").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		autoSelect: true,
		displayText: function (item) {
			 return '<div style="border-top:1px solid gray; width: 300px"><span><b>' + item.typ + ": </b>"+ item.id + '</span><br><small>' + item.value + "</small></div>";
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
					async: false,
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
			
			GoToComp();

		}
	});
		
	$("#btnVoid").on("click", function(){
		var rems = prompt("Please enter your reason...", "");
		if (rems == null || rems == "") {
			alert("No remarks entered!\nCheque cannot be void!");
		}
		else{
			//alert( "id="+ $("#txtBankName").val()+"&chkno="+ $("#txtCheckNo").val()+"&rem="+ rems);
					$.ajax ({
					url: "PayBill_voidchkno.php",
					data: { id: $("#txtBank").val(), chkno: $("#txtCheckNo").val(), rem: rems },
					async: false,
					success: function( data ) {
						if(data.trim()!="False"){
							$("#txtCheckNo").val(data.trim());
							$("#btnVoid").attr("disabled", false);
						}
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
	var isOK = "True";
	//alert(isOK);
	
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
		if(document.getElementById("txttotpaid").value == 0){
			$("#AlertMsg").html("<b>ERROR: </b>Enter total paid!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			isOK="False";
			return false;
		}

			var npaid = document.getElementById("txttotpaid").value;
			var napplied = document.getElementById("txtnGross").value;
			
			var oob = parseFloat(npaid) - parseFloat(napplied);
			oob = oob.toFixed(4);
		
		if(parseFloat(oob) != 0){
			
			
			$("#AlertMsg").html("<b>ERROR: </b>Unbalanced amount!<br>Out of Balance: "+ Math.abs(oob));
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			isOK="False";
			return false;
		}
		
		
		if(isOK == "True"){
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
		document.getElementById("txttotpaid").value = gross.toFixed(2);

}
</script>
