<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PayBill_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$ccvno = $_REQUEST['txtctranno'];
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

<body style="padding:5px" onLoad="disabled(); document.getElementById('txtctranno').focus();">

      <?php
    	$sqlchk = mysqli_query($con,"Select a.cacctno, a.ccode, a.cpayee, a.cpaymentfor, a.cchkno, DATE_FORMAT(a.dcvdate,'%m/%d/%Y') as dcvdate, a.ngross, a.lapproved, a.lcancelled, a.lprintposted, b.cname, c.cacctdesc From paybill a left join suppliers b on a.ccode=b.ccode left join accounts c on a.cacctno=c.cacctno where a.ctranno='$ccvno'");
if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cacctno'];
			$nDebitDesc = $row['cacctdesc'];
			$cCode = $row['ccode'];
			$cName = $row['cname'];
			$cPayee = $row['cpayee'];
			$cMemo = $row['cpaymentfor'];
			$cChkNo = $row['cchkno'];
			$dDate = $row['dcvdate'];
			$nAmount = $row['ngross'];
			
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
			$lPrintPost = $row['lprintposted'];
		}

	?>

<form action="PayBill_editsave.php" name="frmpos" id="frmpos" method="post"  onSubmit="return false;">
	<fieldset>
    	<legend>Pay Bills</legend>	
        <table width="100%" border="0">
  <tr>
    <tH>CV No.:</tH>
    <td colspan="3" style="padding:2px;">
    <div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
      
      <input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $ccvno;?>">
      
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
      <input type="hidden" name="hdnprintpost" id="hdnprintpost" value="<?php echo $lPrintPost;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
    </td>
    </tr>
  <tr>
    <tH width="100">Account:</tH>
    <td style="padding:2px;" width="500">  
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
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..." required value="<?php echo $cName;?>">
</div> 

        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly value="<?php echo $cCode;?>">
            
                    
    </td>
    <tH width="150" style="padding:2px">CHECK NO.:</tH>
    <td style="padding:2px">    
    <div class="col-xs-6">
      <input type="text" class="form-control input-sm" id="txtchkNo" name="txtchkNo" width="20px" tabindex="1" required value="<?php echo $cChkNo;?>">
    </div></td>
  </tr>
  <tr>
    <tH width="100" valign="top">PAYEE:</tH>
    <td valign="top" style="padding:2px"><div class="col-xs-10">
      <input type="text" class="form-control input-sm" id="txtpayee" name="txtpayee" value="<?php echo $cPayee; ?>">
    </div></td>
    <tH style="padding:2px">DATE:</tH>
    <td style="padding:2px"><div class="col-xs-6">
      <input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $dDate; ?>" />
    </div></td>
  </tr>
  <tr>
    <tH width="100" valign="top">MEMO:</tH>
    <td valign="top" style="padding:2px">
    <div class="col-xs-10"> 
      <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"><?php echo $cMemo; ?></textarea>
    </div> 
      </td>
    <th valign="top" style="padding:2px">AMOUNT :</th> 
    <td valign="top" style="padding:2px">
      <div class="col-xs-6">
      <input type="text" id="txtnGross" name="txtnGross" readOnly class="form-control input-sm" style="font-weight:bold; color:#F00; text-align:right" value="<?php echo $nAmount; ?>">
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
<?php
//get CREDIT ACCOUNT CODE
    $sqlchk = mysqli_query($con,"Select cvalue From parameters where ccode='CVCREDIT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nCredit = $row['cvalue'];
		}
	}else{
		$nCredit = "";
	}

$sql = "Select a.capvno,DATE_FORMAT(a.dapvdate,'%m/%d/%Y') as dapvdate, a.namount, a.ndiscount, a.nowed, a.napplied, sum(c.napplied) as napplied2
From paybill_t a 
left join 
	(	
		select a.napplied, a.capvno, a.ctranno
		from paybill_t a
		left join paybill b on a.ctranno=b.ctranno
		where b.lcancelled=0 and a.ctranno<>'$ccvno'
	) c on a.capvno=c.capvno
where a.ctranno='$ccvno'
group by a.ctranno,a.dapvdate,a.namount
order by a.nidentity";

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
    <td><?php echo $rs['capvno']?><input type="hidden" name="cTranNo<?php echo $cntr;?>" id="cTranNo<?php echo $cntr;?>" value="<?php echo $rs['capvno']?>" /></td>
    
    <!--<td>&nbsp;</td>-->
    
    <td><?php echo $rs['dapvdate']?><input type="hidden" name="dApvDate<?php echo $cntr;?>" id="dApvDate<?php echo $cntr;?>" value="<?php echo $rs['dapvdate']?>" /></td>
    
    <td align="right"><?php echo $amtapv; ?><input type="hidden" name="nAmount<?php echo $cntr;?>" id="nAmount<?php echo $cntr;?>" value="<?php echo $rs['namount']?>" />&nbsp;&nbsp;&nbsp;</td>

    <td align="right"><?php echo $amtapplied; ?>&nbsp;&nbsp;&nbsp;</td>
    
    <td style="padding:2px" align="center"><input type="text" class="numeric form-control input-sm" name="nDiscount<?php echo $cntr;?>" id="nDiscount<?php echo $cntr;?>" value="<?php echo $rs['ndiscount']?>" style="text-align:right" /></td>
    
    <td style="padding:2px" align="center"><input type="text" class="form-control input-sm" name="cTotOwed<?php echo $cntr;?>" id="cTotOwed<?php echo $cntr;?>"  value="<?php echo $rs['nowed']?>" style="text-align:right" readonly></td>
    
    <td style="padding:2px" align="center"><input type="text" class="numeric form-control input-sm" name="nApplied<?php echo $cntr;?>" id="nApplied<?php echo $cntr;?>"  value="<?php echo $rs['napplied']?>" style="text-align:right" /></td>
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
Undo Edit<br>(F3)
    </button>
<!--
    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php// echo $ctranno;?>');" id="btnPrint" name="btnPrint">
Print<br>(F4)
    </button>
-->    
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(F8)    </button>
    
    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
Save<br>(F2)    </button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>
</form>
<?php
}
else{
?>

<form action="PayBill_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Pay Bills</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">CV NO:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>CV No. DID NOT EXIST!</b></font></tH>
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

	$('#txtcust').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "th_supplier.php",
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
		var ISOK = "YES";
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		

		if(lastRow == 0){
			alert("No details found!");
			return false;
			ISOK = "NO";
		}
		
		if(document.getElementById("txtnGross").value == 0){
			alert("No Amount Applied!");
			return false;
			ISOK = "NO";
		}
		
		if(ISOK=="YES"){
			document.getElementById("hdnrowcnt").value = lastRow;
			document.getElementById("frmpos").submit();
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
		  var url = "APV_confirmprint.php?x="+x;
		  
		  $("#myprintframe").attr('src',url);


		$("#PrintModal").modal('show');
		

	}
}

</script>
