<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "OR_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

	$sqlsemimo = "select * from parameters WHERE compcode='$company' and ccode='LOANDED'";

	$resemimo = mysqli_query ($con, $sqlsemimo); 

	if(mysqli_num_rows($resemimo)==0){
		//echo "True";

	}
	else {
		while($rowsemo = mysqli_fetch_array($resemimo, MYSQLI_ASSOC)){
			
			$dedtyp = $rowsemo['cvalue'];	
		
		}
	}



	//if(isset($_REQUEST['txtctranno'])){
			$txtctranno = $_REQUEST['txtctranno'];
	//}
	//else{
	//		$txtctranno = $_REQUEST['txtcsalesno'];
	//}


	$sqlmain = "select A.*, B.cname, C.cgroupdesc as cmemdesc, D.cgroupdesc as cdeptdesc from loans A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid left join customers_groups C on A.compcode=C.compcode and A.nmembertype=C.ccode and C.cgroupno='CustGroup2' left join customers_groups D on A.compcode=D.compcode and A.cdeptid=D.ccode and D.cgroupno='CustGroup1' WHERE A.compcode='$company' and A.ctranno='$txtctranno'";

	$resmain = mysqli_query ($con, $sqlmain); 

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../include/jquery-maskmoney.js" type="text/javascript"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
</head>

<body style="padding:5px; height:700px" onLoad="document.getElementById('txtcust').focus();">

<?php
	if(mysqli_num_rows($resmain)!=0){
		while($rowsemo = mysqli_fetch_array($resmain, MYSQLI_ASSOC)){
			
		
		$lCancelled = $rowsemo['lcancelled'];
		$lPosted = $rowsemo['lapproved'];

	$cCode =  $rowsemo['ccode'];
	$cName =  $rowsemo['cname'];
	$cMemType = $rowsemo['nmembertype'];
	$cMemDesc = $rowsemo['cmemdesc'];
	$cDeptID =  $rowsemo['cdeptid']; 
	$cDeptDesc =  $rowsemo['cdeptdesc']; 
	$ncapshare =  $rowsemo['ncapshare'];
	$nyrs =  $rowsemo['cyrs'];
	$cpurpose =  $rowsemo['cpurpose']; 
	$dbegin =  $rowsemo['dbegin']; 
	$dend = $rowsemo['dend']; 
	$lautoded = $rowsemo['lautoded'];
	
	$cLoanType =  $rowsemo['cloantype'];
	$cPayType = $rowsemo['cpaytype'];
	$cTerms = $rowsemo['cterms'];
	$nIntRate = $rowsemo['nintrate'];
	$nAmount = $rowsemo['namount'];
	$nLoanAmt = $rowsemo['nloaned'];
	$nAddFee = $rowsemo['naddfee'];
	$nTotInt = $rowsemo['ntotint'];
	$nTotAmtLoan = $rowsemo['npayamt'];
	$nDedAmt = $rowsemo['ndedamt'];
	

?>
<form action="App_editsave.php" name="frmpos" id="frmpos" method="post">
	<fieldset>
   	  <legend>Loan Application</legend>	
        <table width="100%" border="0">
  <tr>
    <tH>Trans. No.:</tH>
    <td style="padding:2px;">
    
    <div class="col-xs-3 nopadding">
    
    <input type="text" class="form-control input-sm" id="txtcsalesno" name="txtcsalesno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
      
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
      
    </td>
    <tH colspan="2"><div id="salesstat">
    <?php
	if($lCancelled==1){
		echo "<font color='#FF0000'><b>CANCELLED</b></font>";
	}
	
	if($lPosted==1){
		echo "<font color='#FF0000'><b>POSTED</b></font>";
	}
	?>
    </div></tH>
    </tr>
  <tr>
    <tH width="150">
    	Loanee
    
    </tH>
    <td style="padding:2px;" width="500">
    
    <div class="col-xs-12 nopadding">
        <div class="col-xs-6 nopadding">
        	<input type="text" class="typeahead form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="2" placeholder="Search Loanee Name..." required autocomplete="off" value="<?php echo $cName; ?>">
		</div> 
		<div class="col-xs-3 nopadwleft">
        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly value="<?php echo $cCode; ?>">
        </div>
    </div>        


    </td>
    <tH width="150">Member Type:</tH>
    <td style="padding:2px;">
    <div class="col-xs-8 nopadding">
    	<input type="text" id="txtmember" name="txtmember" class="form-control input-sm" readonly value="<?php echo $cMemDesc;?>">
        <input type="hidden" id="txtmemberid" name="txtmemberid" value="<?php echo $cMemType; ?>">
    </div>
    </td>
  </tr>
  <tr>
    <tH>Dept:</tH>
    <td style="padding:2px;">
    <div class="col-xs-6 nopadding">
    	<input type="text" id="txtdept" name="txtdept" class="form-control input-sm" readonly value="<?php echo $cDeptDesc; ?>">
        <input type="hidden" id="txtdeptid" name="txtdeptid" value="<?php echo $cDeptID; ?>">
    </div>
    </td>
    <tH>Capital Share:</tH>
    <td style="padding:2px;">
    <div class="col-xs-8 nopadding">
    	<input type="text" id="txtcap" name="txtcap" class="form-control input-sm" readonly value="<?php echo $ncapshare; ?>"  style="text-align:right;">
    </div>    
    </td>
  </tr>
  <tr>
    <tH>Yrs of Service:</tH>
    <td style="padding:2px;">
    <div class="col-xs-6 nopadding">
    	<input type="text" id="txtyrs" name="txtyrs" class="form-control input-sm" readonly value="<?php echo $nyrs; ?>">
    </div>    
    </td>
    <tH><b><u>Deduction Period</u></b></tH>
    <td style="padding:2px;">&nbsp;</td>
  </tr>
  <tr>
    <tH>Purpose:</tH>
    <td rowspan="2" style="padding:2px;" valign="top"><div class="col-xs-12 nopadding">
      <div class="col-xs-10 nopadding">
        <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"><?php echo $cpurpose; ?></textarea>
      </div>
    </div></td>
    <td style="padding:2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Start Date:</b></td>
    <td style="padding:2px;"><div class="col-xs-8 nopadding">
        <input type="text" id="date_start" name="date_start" class="form-control input-sm" value="<?php echo date_format(date_create($dbegin), "m/d/Y");?>"  style="text-align:right;">
      </div></td>
    </tr>
  <tr>
    <tH style="padding:2px;">&nbsp;</tH>
    <td style="padding:2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>End Date:</b>
    </td>
    <td style="padding:2px;"> 
    <div class="col-xs-8 nopadding">
        <input type="text" id="date_end" name="date_end" class="form-control input-sm" readonly value="<?php echo date_format(date_create($dend), "m/d/Y");?>"  style="text-align:right;">
      </div>
    </td>   
  </tr>
  <tr>
    <tH valign="top">&nbsp;</tH>
    <td valign="top" style="padding:2px">&nbsp;</td>
    <tH style="padding:2px">&nbsp;</tH>
    <td style="padding:2px">&nbsp;</td>
  </tr>
  <tr>
    <tH width="150" style="padding:2px">Loan Type:</tH>
    <td valign="top" style="padding:2px">
    
    <div class="col-xs-6 nopadding">
      <select id="selloantyp" name="selloantyp" class="form-control input-sm selectpicker">
      <?php
				
				
				$sql = "select * from groupings where compcode='$company' and ctype='LOANTYP' and cstatus='ACTIVE' order by nidentity";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
      	
	  ?>
        	<option value="<?php echo $row["ccode"];?>" <?php if ($row["ccode"]==$cLoanType) { echo "selected"; } ?>><?php echo $row["cdesc"];?></option>
        
      <?php
				}
	  ?>
      </select>
    </div>
    </td>
    <tH width="150" style="padding:2px">Approved Loan Amt:</tH>
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnGross" name="txtnGross" class="numericchkamt form-control input-sm" value="<?php echo $nLoanAmt; ?>" style="text-align:right;" autocomplete="off">
    </div></td>
  </tr>
  <tr>
    <tH style="padding:2px">Payment Type:</tH>
    <td valign="top" style="padding:2px">
   <div class="col-xs-6 nopadding">
      <select id="selpaymet" name="selpaymet" class="form-control input-sm selectpicker">
        <option value="Cash" <?php if ($cPayType=="Cash") { echo "selected"; } ?>>Cash</option>
        <option value="Check" <?php if ($cPayType=="Check") { echo "selected"; } ?>>Check</option>
        <option value="None" <?php if ($cPayType=="None") { echo "selected"; } ?>>For deduction only</option>
      </select>
    </div>

    </td>
    <tH width="150" style="padding:2px">Addt'l Fee:</tH>
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnadd" name="txtnadd" class="numericchkamt form-control input-sm" value="<?php echo $nAddFee; ?>" style="text-align:right;" autocomplete="off">
    </div></td>
  </tr>
  <tr>
    <th style="padding:2px">Terms:</th>
    <td style="padding:2px"><div class="col-xs-6 nopadding">
      <select id="selloantrm" name="selloantrm" class="form-control input-sm selectpicker">
        <?php
				
				
				$sql = "select * from groupings where compcode='$company' and ctype='LOANTRM' and cstatus='ACTIVE' Order by CAST(cdesc as UNSIGNED)";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
      	
	  ?>
        <option value="<?php echo $row["ccode"];?>" <?php if ($row["ccode"]==$cTerms) { echo "selected"; } ?>><?php echo $row["cdesc"];?></option>
        <?php
				}
	  ?>
      </select>
    </div></td>
    <tH width="150" style="padding:2px">Tot Interest:</tH>
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnIntRate" name="txtnIntRate" value="<?php echo $nTotInt; ?>" style="text-align:right;" readonly class="form-control input-sm">
    </div></td>
    </tr>
  <tr>
    <th style="padding:2px">Interest Rate:</th>
    <td style="padding:2px">
    
    <div class="col-xs-12 nopadding">
     <div class="col-xs-3 nopadding">
      <select id="selintrate" name="selintrate" class="form-control input-sm selectpicker">
        <?php
				
				
				$sql = "select ccode,cdesc from groupings where compcode='$company' and ctype='LOANINT' and cstatus='ACTIVE' Order by CAST(cdesc as UNSIGNED)";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$code2 = floatval($row["cdesc"]);
      	
	  ?>
        <option value="<?php echo $row["ccode"];?>" <?php if ($code2==$nIntRate) { echo "selected"; } ?>><?php echo $row["cdesc"];?></option>
        <?php
				}
	  ?>
      </select>
     </div>
     <div class="col-xs-5 nopadleft">
       <div class="checkbox">
       <?php
       	if($lautoded==1){
			$varchk = "checked";
		}else{
			$varchk = "";
		}
	   ?>
     	<label><input type="checkbox" value="YES" name="chkautoded" id="chkautoded" <?php echo $varchk; ?>>Auto Deduct</label>
       </div>
     </div>
    </div></td>
    <tH width="150" style="padding:2px">Total Amt Loan:</tH> 
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnPayAmt" name="txtnPayAmt" value="<?php echo $nTotAmtLoan; ?>" style="text-align:right;" readonly class="form-control input-sm">
    </div></td>
  </tr>
  <tr>
    <th style="padding:2px">Total Amt Credited:</th>
    <td style="padding:2px"><div class="col-xs-6 nopadding">
      <input type="text" id="txtnObtain" name="txtnObtain" value="<?php echo $nAmount; ?>" style="text-align:right;" readonly class="form-control input-sm">
    </div></td>
    <tH style="padding:2px">Deduction:</tH>
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnDeduct" name="txtnDeduct" value="<?php echo $nDedAmt; ?>" style="text-align:right;" readonly class="form-control input-sm">
    </div></td>
  </tr>
  <tr>
    <th style="padding:2px">&nbsp;</th>
    <td style="padding:2px">&nbsp;</td>
    <tH style="padding:2px">&nbsp;</tH>
    <td style="padding:2px">&nbsp;</td>
  </tr>
      </table>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">

<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='App.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='App_new.php';" id="btnNew" name="btnNew">
New<br>(F1)</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>
 
     <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(CTRL+E)    </button>
  
  <button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave">Save<br> (CTRL+S)</button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>
    
   
    
</form>

<?php
		}
	}
	else{
?>

<form action="App_edit.php" name="frmpos2" id="frmpos2">
  <fieldset>
   	<legend>Loan Application</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">Trans No.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>Loan No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>

<?php
	}
?>

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
	  if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='App.php';
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
	  else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
		if($("#btnUndo").is(":disabled")==false){
			e.preventDefault();
			chkSIEnter(13,'frmpos');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			window.location.href='App.php';
		}
	  }

	});

	$(document).ready(function(e) {
		  
		disabled();
		
    });
	
$(function() {              
    // Bootstrap DateTimePicker v4
    $('#date_start').datetimepicker({
       format: 'MM/DD/YYYY'
    });
	
	$('#date_start').on("blur", function () {
		computeEnddate();
	});
		   
	$("input.numericchkamt").numeric({decimalPlaces: 4});
	$("input.numericchkamt").on("click focus", function () {
		$(this).select();
	});
		
	$('#txtcust').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "../th_customer.php",
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
			return '<span class="dropdown-item-extra">' + item.id + '</span><br>' + item.value;
		},
		highlighter: Object,
		afterSelect: function(item) { 
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
		}
	});
	
	$("#txtcust").on('blur', function() {
		if($('#txtcustid').val() != "" && $('#txtcustid').val() != ""){
			$('#txtcust').attr('readonly', true);
			
			$.post('../th_getloanee.php',{ id:$('#txtcustid').val() },function( data ){ //send value to post request in ajax to the php page
				if(data.trim()!="True"){ 
				 var xy = data.trim();
				 var xyz = xy.split("|");
				 	
					//echo $mem."|".$memdesc."|".$dept."|".$deptdesc."|".$yrdesc."|".$ncap;
					
					$("#txtmemberid").val(xyz[0]);
				 	$("#txtmember").val(xyz[1]);
					

					$("#txtdeptid").val(xyz[2]);
				 	$("#txtdept").val(xyz[3]);
					
					$("#txtcap").val(xyz[5]);
					$("#txtyrs").val(xyz[4]);
				 
					$("#txtremarks").focus();
				}
				else{
					$("#AlertMsg").html("");
			
					$("#AlertMsg").html("&nbsp;&nbsp;Incomplete customer info!<br>Please complete customer details in the masterlist.");
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');

				}
			});
		}
	}); 
	
	$("#selloantrm").on("change", function() {
		computeEnddate();
		ComputeLoan();
	});
	
	$("#txtnGross").on("keyup", function() {
		ComputeLoan();
	});

	$("#selintrate").on("change", function() {
		ComputeLoan();
	});
	
	$("#txtnadd").on("keyup", function() {
		ComputeLoan();
	});
	
	$("#chkautoded").on("click", function() {
		ComputeLoan();
	});

});

function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}

function computeEnddate(){
	var dte1 = $("#date_start").val();
	var terms = $("#selloantrm").val();

	//alert(dte1);
	//alert(new Date(dte1));

	var r = terms.match(/-?\d+\.?\d*/);
	//alert(r % 1);
	//var date = new Date(dte1);
	//var sdate = $('#ReleaseDate').val();
	var current = new Date(dte1);
	//alert(current.getMonth()+ " + " + parseFloat(r));
	current.setMonth(current.getMonth()+parseFloat(r));
	
	//if decimal ung month ... like 3.5months add 15 days
	if((r % 1) != 0 ){
		current.setDate(current.getDate()+15);
	}
	
		var dedmo;
	
			   	$.ajax({
					url : "../th_chkparam.php",
					data: 'id='+ "LOANDED",
					type: "post",
					async: false,
					dataType: "text",
					success: function(data)
					{
						//alert("A:" + data.trim());	
						if(data.trim()!="True"){ 
						
							if(data.trim()=="Semi"){
								current.setDate(current.getDate()-15);
							}else{
								current.setMonth(current.getMonth()-1);
							}
						}
						else {
							current.setMonth(current.getMonth()-1);
						}
					}
				})

	
	
	var x = current.toLocaleDateString();
	
	$('#date_end').val(moment(x).format('L'));

}

function ComputeLoan(){
	var mon = $("#selloantrm").val();
	var int = $("#selintrate").val();
	var amt = $("#txtnGross").val();
	var nadd = $("#txtnadd").val();

	var r = int.match(/-?\d+\.?\d*/);
	var mo = mon.match(/-?\d+\.?\d*/);
	
	//alert(amt+":"+mo+":"+r);
	var ded;
	
			   	$.ajax({
					url : "../th_chkparam.php",
					data: 'id='+ "LOANDED",
					type: "post",
					async: false,
					dataType: "text",
					success: function(data)
					{
						//alert("A:" + data.trim());	
						if(data.trim()!="True"){ 
						
							if(data.trim()=="Semi"){
								ded = 2;
							}else{
								ded = 1;
							}
						}
						else {
							ded = 1;
						}
					}
				})

		
	//alert("(" + parseFloat(amt) + " * " + (parseFloat(r)/100) + ") * " + parseInt(mo));	
	var totInt = (parseFloat(amt) * (parseFloat(r)/100)) * parseFloat(mo);
	
	//alert($('input[name="chkautoded[]"]:checked').length);
	
	if($('input[name="chkautoded"]:checked').length > 0){
		var totPay = parseFloat(amt) + parseFloat(nadd);
		
		var totObtn = parseFloat(amt) - parseFloat(totInt);
	}
	else{
		var totPay = parseFloat(amt) + parseFloat(totInt) + parseFloat(nadd);
		
		var totObtn = totPay;
	}
	
	
	
	var totDed = parseFloat(totPay) / (parseFloat(mo) * parseInt(ded));
	
	$("#txtnIntRate").val(totInt.toFixed(2));
	$("#txtnPayAmt").val(totPay.toFixed(2));
	$("#txtnDeduct").val(totDed.toFixed(2));
	$("#txtnObtain").val(totObtn.toFixed(2));
	
	
}

function frmchk(){
		
	if($("#txtcustid").val()=="" || $("#txtcust").val()!=""){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>ERROR: </b> Valid applicant name required!");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');

	}
	else{
		$.ajax ({
			url: "App_editsave.php",
			data: $('#frmSet').serialize(),
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>Updating Loan Application: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				//alert(data.trim());
				if(data.trim()!="False"){
					trancode = data.trim();
				}
			},
            error: function (req, status, err) {
						//alert('Something went wrong\nStatus: '+status +"\nError: "+err);
				console.log('Something went wrong', status, err);

				VARHDRSTAT = status;
				VARHDRERR = err;

            }
			
		});
	}

}


function disabled(){

	$("#frmpos :input").attr("disabled", true);
	
	$("#txtcsalesno").attr("disabled", false);
	$("#txtctranno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
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
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
					
	}
}



</script>


