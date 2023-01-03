<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PurchRet_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$ctranno = $_REQUEST['txtctranno'];
$company = $_SESSION['companyid'];

$sqlhead = mysqli_query($con,"select a.*,b.cname from purchreturn a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.ctranno = '$ctranno'");

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
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
	});
	
	//proddesc searching	
	$("#txtsinum").autocomplete("get_rrnum.php?id=nme", {
		width: 500,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#txtsinum").result(function(event, data, formatted) {
		$("#txtcustchkr").val(data[1]);
	
	if(document.getElementById("txtcustid").value==""){
		$("#txtcustid").val(data[1]);
		$("#txtcust").val(data[2]);
		//$("#seltype").val(data[3]);
	}
	else{
	  if(document.getElementById("txtcustid").value==data[1]){
		$("#txtcustid").val(data[1]);
		$("#txtcust").val(data[2]);
		//$("#seltype").val(data[3]);
	  }
	  else{
		  alert("RR's Supplier didn't match.");
	  }
	}
	
	});
	
	$("#txtsinum").keyup(function(event){
    if(event.keyCode == 13){
		if(document.getElementById("txtcustid").value==""){
		}
		else{
		  if(document.getElementById("txtcustid").value==document.getElementById("txtcustchkr").value){
			  
			 if (document.getElementById("txtsinum").value != "") {
				var code = $("#txtsinum").val();
				var left = (screen.width/2)-(800/2);
				var top = (screen.height/2)-(400/2);
				var sFeatures="dialogHeight: 400px; dialogWidth: 800px; dialogTop: " + top + "px; dialogLeft: " + left + "px;";
				
				var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
				var lastRow = tbl.length-1;
				var z;
				var itm="";
				if(lastRow>=1){
					for (z=1; z<=lastRow; z++){
						if(document.getElementById('txtcreference' + z).value==code){
							if(itm!=""){
								itm = itm + ",";
							}
							
							itm = itm + document.getElementById('txtnrefident' + z).value;
						}
					}
				}

				var url = "PurchRet_RRDetSearch.php?id="+code+"&itmn="+itm;
				
				window.showModalDialog(url, "", sFeatures)
				
			 }
		  
		  }
			 
		}
		
	$("#txtsinum").val("");
	
    }
	});
	
});

function deleteRow(r) {
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('MyTable').deleteRow(i);
	 document.getElementById('hdnrowcnt').value = lastRow - 2;
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempcreference = document.getElementById('txtcreference' + z);
			var tempnrefident = document.getElementById('txtnrefident' + z);
			var tempcitemno = document.getElementById('txtitemcode' + z);
			var tempcdesc = document.getElementById('txtitemdesc' + z);
			var tempnqty= document.getElementById('txtnqty' + z);
			var tempnqtyOrig= document.getElementById('txtnqtyOrig' + z);
			var tempcunit= document.getElementById('txtcunit' + z);
			var tempnprice = document.getElementById('txtnprice' + z);
			var tempnamount= document.getElementById('txtnamount' + z);
			var tempcfactor= document.getElementById('txtnfactor' + z);
			
			var x = z-1;
			tempcreference.id = "txtcreference" + x;
			tempcreference.name = "txtcreference" + x;
			tempnrefident.id = "txtnrefident" + x;
			tempnrefident.name = "txtnrefident" + x;			
			tempcitemno.id = "txtitemcode" + x;
			tempcitemno.name = "txtitemcode" + x;
			tempcdesc.id = "txtitemdesc" + x;
			tempcdesc.name = "txtitemdesc" + x;
			tempnqty.id = "txtnqty" + x;
			tempnqty.name = "txtnqty" + x;
			tempnqtyOrig.id = "txtnqtyOrig" + x;
			tempnqtyOrig.name = "txtnqtyOrig" + x;
			tempcunit.id = "txtcunit" + x;
			tempcunit.name = "txtcunit" + x;
			tempnprice.id = "txtnprice" + x;
			tempnprice.name = "txtnprice" + x;
			tempnamount.id = "txtnamount" + x;
			tempnamount.name = "txtnamount" + x;
			tempcfactor.id = "txtnfactor" + x;
			tempcfactor.name = "txtnfactor" + x;
			
			//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

		}
computeGross();

if(lastRow==1){
	document.getElementById('txtcust').readOnly=false;
}

}

function computeamt(valz,str,keyCode){
	var r = parseInt(str.slice(-1));
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	
	if(keyCode==38 ||keyCode==40){
		if(keyCode==38 && r!=1){
			var z = r - 1;
			document.getElementById("txtnqty"+z).focus();
		}
		
		if(keyCode==40 && r!=lastRow){
			var z = r + 1;
			document.getElementById("txtnqty"+z).focus();
		}
		
	}
	else{
		var txtprice = document.getElementById("txtnprice" + r).value;
	
		var pattern = /^-?[0-9]+(.[0-9]{1,4})?$/; 
		var text = valz;
	
		if (text.match(pattern)==null) 
		{
			//alert("Invalid Quantity");
			//with (document.forms[0].elements[nqtyname])
			//{
			//value = 1
			//}
			document.getElementById("txtnamount"+r).value = "0";
			//document.getElementById("txtnqty"+r).value = "0";

		}
		else {
			var Tot = parseFloat(txtprice) * parseFloat(valz);
			document.getElementById("txtnamount" + r).value = Tot.toFixed(2);
			
			computeGross();
			
		}
	}

	
}
function computeGross(){
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	var TotAmt = 0;
	for (z=1; z<=lastRow; z++){
		TotAmt += +document.getElementById("txtnamount" + z).value;
	}
	
	document.getElementById("txtnGross").value = TotAmt.toFixed(2);

}

function isNumber(keyCode) {
	return ((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode == 189 || keyCode == 37 || keyCode == 110 || keyCode == 190 || keyCode == 39 || (keyCode >= 96 && keyCode <= 105 || keyCode == 9))
}


function chkdecimal(valz,r){
		var nqtyname = "txtnqty" + r
	
		var pattern = /^-?[0-9]+(.[0-9]{1,2})?$/; 
		var text = valz;
	
		if (text.match(pattern)==null) 
		{
			alert("Invalid Quantity.\nNote: 2 decimal places only");
			with (document.forms[0].elements[nqtyname])
			{
				value = document.getElementById("txtnqtyOrig" + r).value;
				computeamt(value,r,0);
			}

		}
}

function addqty(){
	
	var itmcode = document.getElementById("txtprodid").value;
	//var itmdesc = document.getElementById("txtprodnme").value;
	//
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	var TotQty = 0;
	var TotAmt = 0;
	
	for (z=1; z<=lastRow; z++){
		if(document.getElementById("txtitemcode"+z).value==itmcode){
			var itmqty = document.getElementById("txtnqty"+z).value;
			var itmprice = document.getElementById("txtnprice"+z).value;
			
			TotQty = parseFloat(itmqty) + 1;
			document.getElementById("txtnqty"+z).value = TotQty;
			
			TotAmt = parseFloat(document.getElementById("txtnamount" + z).value) + parseFloat(itmprice);
			document.getElementById("txtnamount" + z).value = TotAmt.toFixed(2);
		}

	}
	
computeGross();

}

function chkform(){
	var ISOK = "YES";
	
	if(document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value==""){
		alert("Supplier Required!");
		document.getElementById("txtcust").focus();
		return false;
		
		ISOK = "NO";
	}
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	
	if(lastRow == 0){
		alert("No details found!");
		return false;
		ISOK = "NO";
	}
	else{
		var msgz = "";
		for (z=1; z<=lastRow; z++){
			if(document.getElementById("txtnqty"+z).value == 0 || document.getElementById("txtnqty"+z).value == ""){
				msgz = msgz + "\n Zero or blank qty is not allowed: row " + z;	
			}
		}
		
		if(msgz!=""){
			alert("Details Error: "+msgz);
			return false;
			ISOK = "NO";
		}
	}
	
	if(ISOK == "YES"){
		document.getElementById("hdnrowcnt").value = lastRow;
		document.getElementById("frmpos").submit();
	}

}

function openinv(){
	var left = (screen.width/2)-(800/2);
	var top = (screen.height/2)-(400/2);
	var sFeatures="dialogHeight: 400px; dialogWidth: 800px; dialogTop: " + top + "px; dialogLeft: " + left + "px;";

	var code = document.getElementById('txtcustid').value;
	
	if(code != ''){
	var url = "Received_POList.php?id="+code;
	
	window.showModalDialog(url, "", sFeatures)
	}
	else{
		alert("Supplier Required!");
	}

}

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "PurchRet_edit.php";
		document.getElementById(frm).submit();
	}
}

function disabled(){
	document.getElementById("txtctranno").readOnly = false;
	
	//document.getElementById("txtcust").disabled = true;
	//document.getElementById("txtcustid").disabled = true;
	document.getElementById("date_delivery").disabled = true; 
	document.getElementById("rec_delivery").disabled = true;
	document.getElementById("txtremarks").disabled = true;
	//document.getElementById("seltype").disabled = true;
	document.getElementById("txtsinum").disabled = true;
	//document.getElementById("txtprodnme").disabled = true;
	document.getElementById("txtnGross").disabled = true;

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	for (z=1; z<=lastRow; z++){
		document.getElementById("txtnqty"+z).disabled = true;
		document.getElementById("txtnprice"+z).disabled = true;
		document.getElementById("txtnfactor"+z).disabled = true;
		document.getElementById("row_"+z+"_delete").className = "btn btn-danger btn-xs disabled";
	}
	
	document.getElementById("btnSave").className = "btn btn-success btn-sm disabled";
	document.getElementById("btnUndo").className = "btn btn-danger btn-sm disabled";
	document.getElementById("btnAdd").className = "btn btn-default btn-sm disabled";
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
		
		document.getElementById("txtctranno").readOnly = true; 
		
		document.getElementById("txtctranno").value = document.getElementById("hdnOrigNo").value;
		
		//document.getElementById("txtcust").disabled = false;
		//document.getElementById("txtcustid").disabled = true;
		document.getElementById("date_delivery").disabled = false;
		document.getElementById("rec_delivery").disabled = false;
		document.getElementById("txtremarks").disabled = false;
		//document.getElementById("seltype").disabled = false;
		//document.getElementById("txtprodid").disabled = false;
		document.getElementById("txtsinum").disabled = false;
		document.getElementById("txtnGross").disabled = false;
	
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
	
		for (z=1; z<=lastRow; z++){
			document.getElementById("txtnqty"+z).disabled = false;
			document.getElementById("txtnprice"+z).disabled = false;
			document.getElementById("txtnfactor"+z).disabled = false;
			document.getElementById("row_"+z+"_delete").className = "btn btn-danger btn-xs";
		}
		
		document.getElementById("btnMain").className = "btn btn-primary btn-sm disabled";
		document.getElementById("btnNew").className = "btn btn-default btn-sm disabled";
		document.getElementById("btnPrint").className = "btn btn-info btn-sm disabled";
		document.getElementById("btnEdit").className = "btn btn-warning btn-sm disabled";
		document.getElementById("btnSave").className = "btn btn-success btn-sm";
		document.getElementById("btnUndo").className = "btn btn-danger btn-sm";
		document.getElementById("btnAdd").className = "btn btn-default btn-sm";	
	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{

		var url = "PurchRet_confirmprint.php?x="+x;
		var left = (screen.width/2)-(800/2);
		var top = (screen.height/2)-(400/2);
		var sFeatures="dialogHeight: 400px;  dialogWidth: 800px; dialogTop: "+top+"px; dialogLeft: "+left+"px; resizable: no;";
		window.showModalDialog(url, "", sFeatures)
		
	}
}

</script>

  <style type='text/css'>

.deleterow{cursor:pointer}
  </style>

</head>

<body style="padding:5px" onLoad="disabled(); document.getElementById('txtctranno').focus();">
<?php
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		$RecDate = $row['dreturned'];
		$SalesType = $row['creturntype'];
		$Gross = $row['ngross'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
?>
<form action="PurchRet_editsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Purchase Return</legend>	
        <table width="100%" border="0">
  <tr>
    <tH width="100">PR NO.:</tH>
    <td colspan="2" style="padding:2px"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
    
      <input type="hidden" name="hdnOrigNo" id="hdnOrigNo" value="<?php echo $ctranno;?>">
      
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
    
    </td>
    <td style="padding:2px;"><div id="salesstat">
      <?php
	if($lCancelled==1){
		echo "<font color='#FF0000'><b>CANCELLED</b></font>";
	}
	
	if($lPosted==1){
		echo "<font color='#FF0000'><b>POSTED</b></font>";
	}
	?>
    </div>
    </td>
  </tr>
  <tr>
    <tH width="100">SUPPLIER:</tH>
    <td style="padding:2px"><div class="col-xs-5">
      <input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..." value="<?php echo $CustName;?>" readonly>
    </div>
  &nbsp;&nbsp;
  <input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px" readonly value="<?php echo $CustCode;?>">
  <input type="hidden" id="txtcustchkr" name="txtcustchkr"></td>
    <tH width="150" style="padding:2px">DATE:</tH>
    <td style="padding:2px"><div class="col-xs-5"> <a href="javascript:NewCal('date_delivery','mmddyyyy')">
      <input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y", strtotime($Date)); ?>" readonly/>
    </a> </div></td>
  </tr>
  <tr>
    <tH>REMARKS:</tH>
    <td style="padding:2px"><div class="col-xs-8">
      <input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value="<?php echo $Remarks;?>">
    </div></td>
    <tH style="padding:2px">RETURN DATE:</th>
    <td style="padding:2px">
      <div class="col-xs-5"> <a href="javascript:NewCal('rec_delivery','mmddyyyy')">
        <input type='text' class="form-control input-sm" id="rec_delivery" name="rec_delivery" value="<?php echo date("m/d/Y", strtotime($RecDate)); ?>" readonly/>
      </a> </div>
      
      
      </td>
  </tr>
  <tr>
    <tH>&nbsp;</tH>
    <td style="padding:2px">&nbsp;</td>
    <tH style="padding:2px">RETURN TYPE:</th>
    <td style="padding:2px"><div class="col-xs-5">
        <select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="3">
          <option value="Grocery">Grocery</option>
          <option value="Cripples">Cripples</option>
        </select>
   </div></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
<tr>
    <td colspan="2">
 <div class="form-group">
  <div class="col-lg-9">
    <div class="form-inline">
      <div class="form-group ">
        <div class="col-lg-30">
          <input type="text" id="txtsinum" name="txtsinum" class="form-control input-sm	" placeholder="Search Receiving No..." size="80" tabindex="5">
        </div>
      </div>
    </div>
  </div>
</div>
		<input type="hidden" name="hdnprice" id="hdnprice">
        <input type="hidden" name="hdnunit" id="hdnunit">
    </td>
    <td><b>TOTAL AMOUNT : </b></td>
    <td><input type="text" id="txtnGross" name="txtnGross" readonly value="<?php echo $Gross;?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></td>

</tr>
</table>
         <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 250px;
					text-align: left;
					overflow: auto">
	
            <table id="MyTable" class="MyTable" cellpadding"3px" width="100%" border="0">

					<tr>
						<th style="border-bottom:1px solid #999">Code</th>
						<th style="border-bottom:1px solid #999">Description (Convertion)</th>
                        <th style="border-bottom:1px solid #999">UOM</th>
						<th style="border-bottom:1px solid #999">Qty</th>
						<th style="border-bottom:1px solid #999">Price</th>
						<th style="border-bottom:1px solid #999">Amount</th>
                        <th style="border-bottom:1px solid #999">Conv. Factor</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
					<tbody class="tbody">
                     <?php 
						$sqlbody = mysqli_query($con,"select a.*,b.citemdesc,b.cunit as mainunit from purchreturn_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$ctranno' order by nident");

						if (mysqli_num_rows($sqlbody)!=0) {
							$cntr = 0;
							while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
								$cntr = $cntr + 1;
						
					?>
                    <tr>
                    	<td><input type='hidden' value='<?php echo $rowbody['creference'];?>' name='txtcreference<?php echo $cntr;?>' id='txtcreference<?php echo $cntr;?>'> <input type='hidden' value='<?php echo $rowbody['nrefidentity'];?>' name='txtnrefident<?php echo $cntr;?>' id='txtnrefident<?php echo $cntr;?>'> <input type='hidden' value='<?php echo $rowbody['citemno'];?>' name='txtitemcode<?php echo $cntr;?>' id='txtitemcode<?php echo $cntr;?>'><?php echo $rowbody['citemno'];?></td>
                        <td><input type='hidden' value='<?php echo $rowbody['citemdesc'];?>' name='txtitemdesc<?php echo $cntr;?>' id='txtitemdesc<?php echo $cntr;?>'><?php echo $rowbody['citemdesc']." (".$rowbody['nfactor']." ".$rowbody['mainunit']."/".$rowbody['cunit'].")";?></td>
                        <td><input type='hidden' value='<?php echo $rowbody['cunit'];?>' name='txtcunit<?php echo $cntr;?>' id='txtcunit<?php echo $cntr;?>'><?php echo $rowbody['cunit'];?></td>
                        <td style="width:100px; padding:1px"><input type='text' value='<?php echo ($rowbody['nqty']);?>' class='form-control input-xs' style='text-align:right' name='txtnqty<?php echo $cntr;?>' id='txtnqty<?php echo $cntr;?>' onKeyup="computeamt(this.value,this.name,event.keyCode);" onkeydown="return isNumber(event.keyCode)" onBlur="chkdecimal(this.value,<?php echo $cntr;?>);" > <input type='hidden' value='<?php echo ($rowbody['nqtyorig']);?>' name='txtnqtyOrig<?php echo $cntr;?>' id='txtnqtyOrig<?php echo $cntr;?>'></td>
                        <td style="width:100px; padding:1px"><input type='text' value='<?php echo $rowbody['nprice'];?>' class='form-control input-xs' style='text-align:right' name='txtnprice<?php echo $cntr;?>' id='txtnprice<?php echo $cntr;?>' readonly></td>
                        <td style="width:100px; padding:1px"><input type='text' value='<?php echo $rowbody['namount'];?>' class='form-control input-xs' style='text-align:right' name='txtnamount<?php echo $cntr;?>' id='txtnamount<?php echo $cntr;?>' readonly> <input type='hidden' value='<?php echo $rowbody['ncost'];?>' name='txtncost<?php echo $cntr;?>' id='txtncost<?php echo $cntr;?>'> <input type='hidden' value='<?php echo $rowbody['nretail'];?>' name='txtnretail<?php echo $cntr;?>' id='txtnretail<?php echo $cntr;?>'></td>
                         <td style="width:100px; padding:1px; padding-left:10px">
                         <div class='col-xs-12'><input type='text' value='<?php echo $rowbody['nfactor'];?>' name='txtnfactor<?php echo $cntr;?>' id='txtnfactor<?php echo $cntr;?>' class='nqty form-control input-xs' style='text-align:right'></div>
                         </td>
                        <td style="width:80px;" align="right"><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr;?>_delete' class='delete' value='delete' onClick="deleteRow(this);"/></td>
                    </tr>
                    <?php
							}
						}
					?>
                   </tbody>
                    
			</table>

</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
        <input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
 
<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PurchRet.php';" id="btnMain" name="btnMain">
  <table align="center">
    <tr>
      <td><img src="../images/back.gif" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>Back to Main</td>
    </tr>
  </table>
</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='PurchRet_new.php';" id="btnNew" name="btnNew">
  <table align="center">
    <tr>
      <td><img src="../images/New.gif" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>New</td>
    </tr>
  </table>
</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
  <table align="center">
    <tr>
      <td><img src="../images/undo.png" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>Undo Edit</td>
    </tr>
  </table>

    </button>

    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $ctranno;?>');" id="btnPrint" name="btnPrint">
  <table align="center">
    <tr>
      <td><img src="../images/Bprint.gif" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>Print</td>
    </tr>
  </table>

    </button>
    
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
   <table align="center">
    <tr>
      <td><img src="../images/edit2.png" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>Edit</td>
    </tr>
  </table>
    </button>
    
       <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="openinv();" id="btnAdd" name="btnAdd">
   <table align="center">
    <tr>
      <td><img src="../images/dataAdd.png" width="30" height="20"/></td>
    </tr>
    <tr>
    <td>ADD ITEM</td>
    </tr>
  </table>
    </button>

    
   <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
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
    
    <td>&nbsp;</td>
  </tr>
</table>

    </fieldset>
</form>

<?php
}
else{
?>
<form action="PurchRet_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
  <legend>Purchase Return</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">PR NO.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>PR No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>

</body>
</html>