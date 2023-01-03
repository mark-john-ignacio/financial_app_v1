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

<form action="App_newsave.php" name="frmAppNew" id="frmAppNew" method="post">
	<fieldset>
    	<legend>Loan Application</legend>	
        <table width="100%" border="0">
  <tr>
    <tH>Payable Account:</tH>
    <td style="padding:2px;">
    <?php
    	$sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc,b.nbalance From parameters a left join accounts b on a.cvalue=b.cacctno where ccode='LOANAPACCT'");
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
		?>
  <div class="col-xs-12 nopadding">
    <div class="col-xs-6 nopadding">
        	<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>" autocomplete="off">
	</div> 
	<div class="col-xs-6 nopadwleft">
        	<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
    </div>
  </div>  
    </td>
    <tH>&nbsp;</tH>
    <td style="padding:2px;">&nbsp;</td>
  </tr>
  <tr>
    <tH width="150">
    	Loanee
    
    </tH>
    <td style="padding:2px;" width="500">
    
    <div class="col-xs-12 nopadding">
        <div class="col-xs-6 nopadding">
        	<input type="text" class="typeahead form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="2" placeholder="Search Loanee Name..." required autocomplete="off">
		</div> 
		<div class="col-xs-3 nopadwleft">
        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly>
        </div>
    </div>        


    </td>
    <tH width="150">Member Type:</tH>
    <td style="padding:2px;">
    <div class="col-xs-8 nopadding">
    	<input type="text" id="txtmember" name="txtmember" class="form-control input-sm" readonly value="">
        <input type="hidden" id="txtmemberid" name="txtmemberid" value="">
    </div>
    </td>
  </tr>
  <tr>
    <tH>Dept:</tH>
    <td style="padding:2px;">
    <div class="col-xs-6 nopadding">
    	<input type="text" id="txtdept" name="txtdept" class="form-control input-sm" readonly value="">
        <input type="hidden" id="txtdeptid" name="txtdeptid" value="">
    </div>
    </td>
    <tH>Capital Share:</tH>
    <td style="padding:2px;">
    <div class="col-xs-8 nopadding">
    	<input type="text" id="txtcap" name="txtcap" class="form-control input-sm" readonly value=""  style="text-align:right;">
    </div>    
    </td>
  </tr>
  <tr>
    <tH>Yrs of Service:</tH>
    <td style="padding:2px;">
    <div class="col-xs-6 nopadding">
    	<input type="text" id="txtyrs" name="txtyrs" class="form-control input-sm" readonly value="">
    </div>    
    </td>
    <tH><b><u>Deduction Period</u></b></tH>
    <td style="padding:2px;">&nbsp;</td>
  </tr>
  <tr>
    <tH>Purpose:</tH>
    <td rowspan="2" style="padding:2px;" valign="top"><div class="col-xs-12 nopadding">
      <div class="col-xs-10 nopadding">
        <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"></textarea>
      </div>
    </div></td>
    <td style="padding:2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Start Date:</b></td>
    <td style="padding:2px;"><div class="col-xs-8 nopadding">
        <input type="text" id="date_start" name="date_start" class="form-control input-sm" value="<?php echo date("m/d/Y");?>"  style="text-align:right;">
      </div></td>
    </tr>
  <tr>
    <tH style="padding:2px;">&nbsp;</tH>
    <td style="padding:2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>End Date:</b>
    </td>
    <td style="padding:2px;"> 
    <div class="col-xs-8 nopadding">
    	<?php
			$dte1 = date("m/d/Y");
			//$dte1 = date_format(date_create("11/26/2018"), "m/d/Y");
			$effectiveDate = date('m/d/Y', strtotime("+3 months", strtotime($dte1)));
			
			if($dedtyp=="Semi"){
				$effectiveDate = date('m/d/Y', strtotime("-15 days", strtotime($dte1)));
			}
			else{
				$effectiveDate = date('m/d/Y', strtotime("-1 months", strtotime($dte1)));
			}
			//$effectiveDate = date_format(date_create($effectiveDate), "m/d/Y");
		?>
        <input type="text" id="date_end" name="date_end" class="form-control input-sm" readonly value="<?php echo $effectiveDate;?>"  style="text-align:right;">
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
        	<option value="<?php echo $row["ccode"];?>"><?php echo $row["cdesc"];?></option>
        
      <?php
				}
	  ?>
      </select>
    </div>
    </td>
    <tH width="150" style="padding:2px">Approved Loan Amt:</tH>
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnGross" name="txtnGross" class="numericchkamt form-control input-sm" value="0.00" style="text-align:right;" autocomplete="off">
    </div></td>
  </tr>
  <tr>
    <tH style="padding:2px">Payment Type:</tH>
    <td valign="top" style="padding:2px">
   <div class="col-xs-6 nopadding">
      <select id="selpaymet" name="selpaymet" class="form-control input-sm selectpicker">
        <option value="Cash">Cash</option>
        <option value="Check">Check</option>
        <option value="None">For deduction only</option>
      </select>
    </div>

    </td>
    <tH width="150" style="padding:2px">Addt'l Fee:</tH>
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnadd" name="txtnadd" class="numericchkamt form-control input-sm" value="0.00" style="text-align:right;" autocomplete="off">
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
        <option value="<?php echo $row["ccode"];?>"><?php echo $row["cdesc"];?></option>
        <?php
				}
	  ?>
      </select>
    </div></td>
    <tH width="150" style="padding:2px">Tot Interest:</tH>
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnIntRate" name="txtnIntRate" value="0.00" style="text-align:right;" readonly class="form-control input-sm">
    </div></td>
    </tr>
  <tr>
    <th style="padding:2px">Interest Rate:</th>
    <td style="padding:2px">
    
    <div class="col-xs-12 nopadding">
     <div class="col-xs-3 nopadding">
      <select id="selintrate" name="selintrate" class="form-control input-sm selectpicker">
        <?php
				
				
				$sql = "select * from groupings where compcode='$company' and ctype='LOANINT' and cstatus='ACTIVE' Order by CAST(cdesc as UNSIGNED)";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
      	
	  ?>
        <option value="<?php echo $row["ccode"];?>"><?php echo $row["cdesc"];?></option>
        <?php
				}
	  ?>
      </select>
     </div>
     <div class="col-xs-5 nopadleft">
       <div class="checkbox">
     	<label><input type="checkbox" value="YES" name="chkautoded" id="chkautoded" checked>Auto Deduct</label>
       </div>
     </div>
    </div></td>
    <tH width="150" style="padding:2px">Total Amt Loan:</tH> 
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnPayAmt" name="txtnPayAmt" value="0.00" style="text-align:right;" readonly class="form-control input-sm">
    </div></td>
  </tr>
  <tr>
    <th style="padding:2px">Total Amt Credited:</th>
    <td style="padding:2px"><div class="col-xs-6 nopadding">
      <input type="text" id="txtnObtain" name="txtnObtain" value="0.00" style="text-align:right;" readonly class="form-control input-sm">
    </div></td>
    <tH style="padding:2px">Deduction:</tH>
    <td style="padding:2px"><div class="col-xs-8 nopadding">
      <input type="text" id="txtnDeduct" name="txtnDeduct" value="0.00" style="text-align:right;" readonly class="form-control input-sm">
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

   
  <button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave">Save<br> (CTRL+S)</button>

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



<form method="post" name="frmedit" id="frmedit" action="App_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>

</body>
</html>


<script type="text/javascript">
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  $("#btnSave").click();
	  }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("App.php");

	  }
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
	
	var dtx = dte1.split("/");
	
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

	
	if(dtx[0]=="02" && (dtx[1]=="28" || dtx[1]=="29")){
		if(dtx[1]=="28"){
			current.setDate(current.getDate()+2);
		}else if(dtx[1]=="29"){
			current.setDate(current.getDate()+1);
		}
		
	}
	
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
			url: "App_newsavehdr.php",
			data: $('#frmSet').serialize(),
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW Loan Application: </b> Please wait a moment...");
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


</script>


