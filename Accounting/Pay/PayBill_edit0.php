<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PayBill_edit.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$ccvno = $_REQUEST['txtctranno'];
$company = $_SESSION['companyid'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>


</head>

<body style="padding:5px" onLoad="disabled(); document.getElementById('txtctranno').focus();">

      <?php
    	$sqlchk = mysqli_query($con,"Select a.cacctno, c.cacctdesc, a.ccode, a.cbankcode, a.ccheckno, e.cname as cbankname, a.cpayee, DATE_FORMAT(a.ddate,'%m/%d/%Y') as ddate, DATE_FORMAT(a.dcheckdate,'%m/%d/%Y') as dcheckdate, a.ngross, a.npaid, a.lapproved, a.lcancelled, a.lprintposted, b.cname, d.cname as custname, c.cacctdesc
		From paybill a 
		left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode 
		left join accounts c on a.cacctno=c.cacctno 
		left join customers d on a.compcode=d.compcode and a.ccode=d.cempid 
		left join bank e on a.compcode=e.compcode and a.cbankcode=e.ccode 
		where a.compcode='$company' and a.ctranno='$ccvno'");
				
if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$cCode = $row['ccode'];
			if($row['cname']!=""){
				$cName = $row['cname'];
			}else{
				$cName = $row['custname'];
			}
			
			$cBank = $row['cbankcode'];
			$cBankName = $row['cbankname'];
			$cCheckNo = $row['ccheckno'];
			$cAcctID = $row['cacctno'];
			$cAcctDesc = $row['cacctdesc'];
			
			$cPayee = $row['cpayee'];
			$dDate = $row['ddate'];
			$dCheckDate = $row['dcheckdate'];
			$nAmount = $row['ngross'];
			$nPaid = $row['npaid'];
			
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
			$lPrintPost = $row['lprintposted'];
		}

	?>

<form action="PayBill_editsave.php" name="frmpos" id="frmpos" method="post" >
	<fieldset>
    	<legend>Check Issuance Details</legend>	
<table width="100%" border="0">
  <tr>
    <tH>PV No.:</tH>
    <td colspan="3" style="padding:2px;">
    <div class="col-xs-2 nopadding"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
      
      <input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $ccvno;?>">
      
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
      <input type="hidden" name="hdnprintpost" id="hdnprintpost" value="<?php echo $lPrintPost;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
    </td>
    </tr>
          <tr>
            <tH width="150" style="padding:2px">Payment Account:</tH>
            <td style="padding:2px;" width="500">
            <div class="col-xs-12 nopadding">
    <div class="col-xs-6 nopadding">
        	<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>">
	</div> 
	<div class="col-xs-6 nopadwleft">
        	<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
    </div>
  </div> </td>
            <tH width="150" style="padding:2px">Balance:</tH>
            <td style="padding:2px;"><div class="col-xs-6 nopadding">
        	<input type="text" class="form-control input-sm" id="txtnbalance" name="txtnbalance" readonly value="<?php echo $nDebitDBalz;?>" style="text-align:right">
    </div></td>
          </tr>
          <tr>
            <tH width="150" valign="top"><span style="padding:2px">Paid To:</span></tH>
            <td valign="top" style="padding:2px"><div class="col-xs-6 nopadding">
        <input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..." required value="<?php echo $cName;?>">
      </div>
      <div class="col-xs-6 nopadwleft">
        <input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly value="<?php echo $cCode;?>">
      </div>
    </div></td>
            <tH width="150" style="padding:2px">Payment Date:</tH>
            <td style="padding:2px"><div class="col-xs-6 nopadding">
      <input type='text' class="datepick form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $dDate; ?>" />
    </div></td>
          </tr>
          <tr>
            <tH width="150" valign="top" style="padding:2px">Payee:</tH>
            <td valign="top" style="padding:2px"><div class="col-xs-10 nopadding">
              <input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee" value="<?php echo $cPayee; ?>">
            </div></td>
            <tH colspan="2" rowspan="5" style="padding:2px; padding-top:5px"> 
            <?php
											$Bacctno = "";
											$Bacctdesc = "";
											$BDateCheck = "";
											$BCheck = "";
											
					if($cPayType=="Cheque"){
						
						$sqlbody = mysqli_query($con,"select a.cbank, DATE_FORMAT(a.ddate,'%m/%d/%Y') as ddate, a.ccheckno, b.cname from paybill_check_t a left join bank b on a.cbank=b.ccode where a.compcode='$company' and a.ctranno = '$ccvno'");
			
									if (mysqli_num_rows($sqlbody)!=0) {
										$cntr = 0;
										while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
											$Bacctno = $rowbody['cbank'];
											$Bacctdesc = $rowbody['cname'];
											$BDateCheck = $rowbody['ddate'];;
											$BCheck = $rowbody['ccheckno'];
										}
									}
					}
				?>
            <fieldset class="fieldset1">
              <legend class="legend1">Cheque Details</legend>
              <div class='col-xs-12' style="padding-bottom:2px">
                <div class='col-xs-4 nopadding'> <b>Bank Name</b> </div>
                <div class='col-xs-8 nopadwleft'>
                  <input type='text' class='form-control input-sm' name='txtBankName' id='txtBankName' readonly value="<?php echo $Bacctdesc;?>" />
                  <input type='hidden' name='txtBank' id='txtBank' value="<?php echo $Bacctno;?>" />
                </div>
              </div>
              <div class='col-xs-12' style="padding-bottom:2px">
                <div class='col-xs-4 nopadding'> <b>Cheque Date</b> </div>
                <div class='col-xs-8 nopadwleft'>
                  <input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate" value="<?php echo $BDateCheck; ?>" />
                </div>
              </div>
              <div class='col-xs-12' style="padding-bottom:2px">
                <div class='col-xs-4 nopadding'> <b>Cheque No.</b> </div>
                <div class='col-xs-8 nopadwleft'>
                  <input type='text' class='form-control input-sm' name='txtCheckNo' id='txtCheckNo' readonly value="<?php echo $BCheck;?>" />
                </div>
              </div>
              <div class='col-xs-12' style="padding-bottom:2px">
                <div class='col-xs-4 nopadding'> <b>&nbsp;</b> </div>
                <div class='col-xs-8 nopadwleft'>
                  <button type="button" class="btn btn-danger btn-sm" name="btnVoid" id="btnVoid">VOID CHECK NO. </button>
                </div>
              </div>
            </fieldset>
            </tH>
          </tr>
          <tr>
            <tH width="150" valign="top" style="padding:2px">Memo:</tH>
            <td rowspan="2" valign="top" style="padding:2px"><div class="col-xs-10 nopadding">
              <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"><?php echo $cMemo; ?></textarea>
            </div></td>
          </tr>
          <tr>
            <tH valign="top">&nbsp;</tH>
          </tr>
          <tr>
            <tH valign="top"><span style="padding:2px">Total Paid :</span></tH>
            <td valign="top" style="padding:2px"><div class="col-xs-6 nopadding">
      <input type="hidden" id="txtnGross" name="txtnGross" value="<?php echo $nAmount; ?>">
      <input type="text" id="txttotpaid" name="txttotpaid" class="numericchkamt form-control input-sm" value="<?php echo $nPaid; ?>" style="font-weight:bold; color:#F00; text-align:right" >
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
<?php
//get CREDIT ACCOUNT CODE
    $sqlchk = mysqli_query($con,"Select cvalue From parameters where compcode='$company' and ccode='CVCREDIT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nCredit = $row['cvalue'];
		}
	}else{
		$nCredit = "";
	}

$sql = "Select A.cacctno, a.capvno,DATE_FORMAT(a.dapvdate,'%m/%d/%Y') as dapvdate, a.namount, a.ndiscount, a.nowed, a.napplied, sum(c.napplied) as napplied2
From paybill_t a 
left join 
	(	
		select a.napplied, a.capvno, a.ctranno
		from paybill_t a
		left join paybill b on a.compcode=b.compcode and a.ctranno=b.ctranno
		where a.compcode='$company' and b.lcancelled=0 and a.ctranno<>'$ccvno'
	) c on a.capvno=c.capvno
where a.compcode='$company' and a.ctranno='$ccvno'
group by a.ctranno,a.dapvdate,a.namount
order by a.nident";

//echo $sql;

$rsd = mysqli_query($con,$sql);

?>
<table width="100%" border="0" cellpadding="0" id="MyTable">
  <tr>
    <th scope="col">APV No</th>
    <!--<th scope="col">Status</th>-->
    <th scope="col">Date</th>
    <th scope="col">Amount(PHP)</th>
    <th scope="col">Payed(PHP)</th>
    <th scope="col" width="150px">Discount(PHP)</th>
    <th scope="col" width="150px">Total Owed(PHP)</th>
    <th scope="col" width="150px">Amount Applied(PHP)</th>
  </tr>
<?php
$cntr = 0;
$amtapv = 0;
$amtapplied = 0;

while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
$cntr = $cntr + 1;

$amtapv = $rs['namount'];
$amtapplied = $rs['napplied2'];

$totowed = $amtapv - $amtapplied;

	$amtapv = number_format($amtapv,4,".",""); 
	$amtapplied = number_format($amtapplied,4,".",""); 
	$totowed = number_format($totowed,4,".","");

?>
  <tr>
    <td><?php echo $rs['capvno']?><input type="hidden" name="cTranNo<?php echo $cntr;?>" id="cTranNo<?php echo $cntr;?>" value="<?php echo $rs['capvno']?>" /> <input type="hidden" name="cacctno<?php echo $cntr;?>" id="cacctno<?php echo $cntr;?>" value="<?php echo $rs['cacctno']?>" /></td>
    
    <!--<td>&nbsp;</td>-->
    
    <td><?php echo $rs['dapvdate']?><input type="hidden" name="dApvDate<?php echo $cntr;?>" id="dApvDate<?php echo $cntr;?>" value="<?php echo $rs['dapvdate']?>" /></td>
    
    <td align="right"><?php echo $amtapv; ?><input type="hidden" name="nAmount<?php echo $cntr;?>" id="nAmount<?php echo $cntr;?>" value="<?php echo $rs['namount']?>" />&nbsp;&nbsp;&nbsp;</td>

    <td align="right"><?php echo $amtapplied; ?>&nbsp;&nbsp;&nbsp;</td>
    
    <td style="padding:2px" align="center"><input type="text" class="numeric form-control input-sm" name="nDiscount<?php echo $cntr;?>" id="nDiscount<?php echo $cntr;?>" value="<?php echo $rs['ndiscount']?>" style="text-align:right"  autocomplete="off" /></td>
    
    <td style="padding:2px" align="center"><input type="text" class="form-control input-sm" name="cTotOwed<?php echo $cntr;?>" id="cTotOwed<?php echo $cntr;?>"  value="<?php echo $rs['nowed']?>" style="text-align:right" readonly></td>
    
    <td style="padding:2px" align="center"><input type="text" class="numeric form-control input-sm" name="nApplied<?php echo $cntr;?>" id="nApplied<?php echo $cntr;?>"  value="<?php echo $rs['napplied']?>" style="text-align:right" autocomplete="off" /></td>
  </tr>	
<?php
}
?>
</table>
</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">
   <input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PayBill.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='PayBill_new.php';" id="btnNew" name="btnNew">
New<br>(F1)</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>
<!--
    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php// echo $ctranno;?>');" id="btnPrint" name="btnPrint">
Print<br>(F4)
    </button>
-->    
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(CTRL+E)    </button>
    
    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
Save<br>(CTRL+S)    </button>

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



<?php
}
else{
?>

<form action="PayBill_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Pay Bills</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">PV NO:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>PV No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>

</body>
</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	
	 if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='PayBill_new.php';
		}
	  }
	  else if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){ 
			e.preventDefault();
			return chkform();
		}
	  }
	  else if(e.keyCode == 69 && e.ctrlKey){//CTRL E
		if($("#btnEdit").is(":disabled")==false){
			e.preventDefault();
			enabled();
		}
	  }
	  else if(e.keyCode == 80 && e.ctrlKey){//CTRL+P
		if($("#btnPrint").is(":disabled")==false){
			e.preventDefault();
			printchk('<?php echo $ccvno;?>');
		}
	  }
	  else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
		if($("#btnUndo").is(":disabled")==false){
			e.preventDefault();
			chkSIEnter(13,'frmpos');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			$("#btnMain").click();
		}
	  }
	});

$(function(){
	
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY'
    });

								$("input.numeric").numeric({decimalPlaces: 4});
								$("input.numeric").on("focus", function () {
									$(this).select();
								});
														
								$("input.numeric").on("keyup", function (e) {
									CompOwed($(this).attr('name'));
									GoToComp();
									setPosi($(this).attr('name'),e.keyCode);
								});


	$('#txtcacct').typeahead({
	
		source: function (query, process) {
			return $.getJSON(
				'th_accounts.php',
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
					query: $("#txtcust").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		autoSelect: true,
		displayText: function (item) {
			 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
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
	
	$("input.numericchkamt").numeric({decimalPlaces: 4});	
	
		
	$("#btnVoid").on("click", function(){
		alert($("#txtBank").val());
		var rems = prompt("Please enter your reason...", "");
		if (rems == null || rems == "") {
			alert("No remarks entered!\nCheque cannot be void!");
		}
		else{
			//alert( "id="+ $("#txtBank").val()+"&chkno="+ $("#txtCheckNo").val()+"&rem="+ rems);
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

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "PayBill_edit.php";
		document.getElementById(frm).submit();
	}
}

function disabled(){

	$("#frmpos :input").attr("disabled", true);
	
	
	$("#txtctranno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){
	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
			var msgsx = "POSTED"
		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{

		$("#frmpos :input").attr("disabled", false);
		
			
			$("#txtctranno").attr("readonly", true);
			$("#txtctranno").val($("#hdnorigNo").val());
			
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);		
	}

}


function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{
		  var url = "PayBill_confirmprint.php?x="+x;
		  
		  $("#myprintframe").attr('src',url);


		$("#PrintModal").modal('show');
		

	}
}

</script>
