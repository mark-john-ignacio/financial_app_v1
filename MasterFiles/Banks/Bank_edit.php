<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Bank";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Bank_Edit'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	if(isset($_REQUEST['txtcitemno'])){
			$citemno = $_REQUEST['txtcitemno'];
	}
	else{
		$citemno = $_REQUEST['txtccode'];
	}
				
	if($citemno <> ""){
		
		$sql = "select A.*, B. cacctdesc from bank A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='$citemno'";
	}else{
		header('Bank.php');
		die();
	}
	
	$sqlhead=mysqli_query($con,$sql);
	
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
		
	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){

			$cCustCode = $row['ccode'];
			$cCustName = $row['cname'];
			$cCOANo = $row['cacctno'];
			$cCOA = $row['cacctdesc'];
			$cBankNo = $row['cbankacctno'];
			$cBank = $row['caccountname'];

			$cDoctype = $row['cdoctype'];
									
			$HouseNo = $row['caddress'];
			$City = $row['ccity'];
			$State = $row['cstate'];
			$Country = $row['ccountry'];
			$ZIP = $row['czip'];
		
			$Contact = $row['ccontact'];
			$Desig = $row['cdesignation'];
			$Email = $row['cemail'];
			$PhoneNo = $row['cphoneno'];
			$Mobile = $row['cmobile'];

		}
	}

?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>MYX Financials</title>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../../Bootstrap/js/moment.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

</head>

<body style="padding:5px; min-height:700px">
<form name="frmITEM" id="frmITEM" method="post">
	<fieldset>
    	<legend>Bank Details</legend>
<table width="100%" border="0">
  <tr>
    <td width="200"><b>Bank Code</b></td>
    <td width="310" colspan="2" style="padding:2px">
    <div class="col-xs-12 nopadding">
           <div class="col-xs-4 nopadding">
            <input type="text" class="form-control input-sm" id="txtccode" name="txtccode" tabindex="1" placeholder="Input Bank Code.." required autocomplete="off" value="<?php echo $cCustCode;?>" onKeyUp="chkSIEnter(event.keyCode,'frmITEM');"/>
           </div>
    
           <div class="col-xs-4 nopadwleft">		
            	 <div id="itmcode_err" style="padding: 5px 10px;"></div>
           </div>
    </div>
    </td>
  </tr>
  <tr>
    <td><b>Bank Name</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Bank Name.." required autocomplete="off" value="<?php echo $cCustName;?>" /></div></td>
  </tr>
  <tr>
    <td><b>Bank Account Number</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding">
      <input type="text" class="form-control input-sm" id="txtbankacct" name="txtbankacct" tabindex="2" placeholder="Input Bank Acct No.." required autocomplete="off" value="<?php echo $cBankNo;?>" />
    </div></td>
  </tr>
  <tr>
    <td><b>Bank Account Name</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding">
      <input type="text" class="form-control input-sm" id="txtbankacctnme" name="txtbankacctnme" tabindex="2" placeholder="Input Bank Acct No.." required autocomplete="off" value="<?php echo $cBank;?>" />
    </div></td>
  </tr>
  <tr>
    <td><b>Linked Account (COA)</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding">
      <input type="hidden" id="txtcoaacct" name="txtcoaacct" value="<?php echo $cCOANo;?>"/>
      <input type="text" class="form-control input-sm" id="txtcoa" name="txtcoa" tabindex="2" placeholder="Search Account Description.." autocomplete="off" value="<?php echo $cCOANo." - ".$cCOA;?>" />
    </div></td>
  </tr>

	<tr>
    <td><b>Check Doc Type</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding">
      <select class="form-control input-sm" name="seldoctype" id="seldoctype">
				<option value="1" <?=($cDoctype==1) ? "selected" : "" ?>>BDO/LANDBANK CHECK FORMAT</option>
				<option value="2" <?=($cDoctype==2) ? "selected" : "" ?>>METROBANK CHECK FORMAT</option>
				<option value="3" <?=($cDoctype==3) ? "selected" : "" ?>>EASTWEST CHECK FORMAT</option>
			</select>

    </div></td>
  </tr>

</table>

<p>&nbsp;</p>
  <ul class="nav nav-tabs">
    <li class="active"><a href="#home">Information</a></li>
    <li><a href="#menu1">Checkbook List</a></li>
  </ul>

<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 30vh;text-align: left;overflow: auto">

    <div class="tab-content">
    
        <div id="home" class="tab-pane fade in active" style="padding-left:30px">


		  <div class="col-xs-12 nopadding">
				<div class="col-xs-7 nopadding">
					<u><h4>ADDRESS</h4></u>
                </div>
          
             	<div class="col-xs-7 nopadwtop">
                	<input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  tabindex="6" value="<?php echo $HouseNo;?>" />
                </div>
 
              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off" tabindex="7" value="<?php echo $City;?>" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off" tabindex="8" value="<?php echo $State;?>" />
                    </div>
                </div>

              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-9 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="9" value="<?php echo $Country;?>" />
                    </div>
                    
                    <div class="col-xs-3 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="10" value="<?php echo $ZIP;?>" />
                    </div>
                </div>
</div>
 
 <div class="col-xs-12 nopadding">
				<div class="col-xs-7 nopadding">
					<u><h4>CONTACT DETAILS</h4></u>
                </div>

                <div class="col-xs-7 nopadding">
                	<input type="text" class="form-control input-sm" id="txtcperson" name="txtcperson" placeholder="Contact Person..." autocomplete="off" tabindex="11" value="<?php echo $Contact;?>"/>
                </div>
 
              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcdesig" name="txtcdesig" placeholder="Designation..." autocomplete="off" tabindex="12" value="<?php echo $Desig;?>"/>
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcEmail" name="txtcEmail" placeholder="Email Address..." autocomplete="off" tabindex="13" value="<?php echo $Email;?>"/>
                    </div>
                </div>

              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcphone" name="txtcphone" placeholder="Phone No..." autocomplete="off" tabindex="14" value="<?php echo $PhoneNo;?>"/>
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcmobile" name="txtcmobile" placeholder="Mobile No..." autocomplete="off" tabindex="15" value="<?php echo $Mobile;?>"/>
                    </div>
                </div>

		  </div>
            
        </div>
        
                <div id="menu1" class="tab-pane fade" style="padding-left:30px">
        	
            <p style="padding-top:10px">
            
            <input type="button" value="Add Checkbook" name="btnaddunit" id="btnaddunit" class="btn btn-primary btn-xs" onClick="addcheckbook();">
            
            <input name="hdnchkbkcnt" id="hdnchkbkcnt" type="hidden" value="0">
            <br>
                <table width="65%" border="0" cellpadding="2" id="myCheckBook">
                  <tr>
                    <th scope="col" width="80">Checkbook No.</th>
                    <th scope="col" width="80">CheckNo. (From)</th>
                    <th scope="col" width="80">CheckNo. (To)</th>
                    <th scope="col" width="80">Current Check No.</th>
                    <th scope="col" width="30">&nbsp;</th>
                  </tr>
            	</table>
         </p>
            
        </div>

    </div>
</div>

<?php
	if($poststat == "True"){
?>
<br>       
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td  style="padding-top:20px">		
		<button type="button" class="btn btn-primary btn-sm" onClick="window.location.href='Bank.php';" id="btnMain" name="btnMain">Back to Main<br>(ESC)</button>

    	<button type="button" class="btn btn-default btn-sm" onClick="window.location.href='Bank_new.php';" id="btnNew" name="btnNew">New<br>(F1)</button>
 
    	<button type="button" class="btn btn-danger btn-sm" onClick="chkSIEnter(13,'frmedit');" id="btnUndo" name="btnUndo">
			Undo Edit<br>(CTRL+Z)
    	</button>
   
        <button type="button" class="btn btn-warning btn-sm" onClick="enabled();" id="btnEdit" name="btnEdit"> Edit<br>(CTRL+E) </button>

    	<button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (CTRL+S)</button>
</td>
  </tr>
</table>
<?php
	}
?>
</fieldset>
</form>

<!-- SAVING MODAL -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div class="modal-content">
               <div class="alert-modal-danger">
                  <p id="AlertMsg"></p>
               </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->		

<form name="frmedit" id="frmedit" action="Bank_edit.php" method="POST">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $cCustCode;?>">
</form>

</body>
</html>


<script type="text/javascript">
$(document).ready(function() {
	$("#itmcode_err").hide();
	$("#txtccode").focus();
	
	
	$(".nav-tabs a").click(function(){
        $(this).tab('show');
    });

	
	loaditmfactor();
	disabled();
	
});

$(function() {


	$('#txtcoa').typeahead({
	
		source: function(request, response) {
			$.ajax({
				url: "../th_accounts.php",
				dataType: "json",
				data: {
					query: request
				},
				success: function (data) {
					response(data);
				}
			});
		},
		autoSelect: true,
		displayText: function (item) {
			 return item.id + ' - ' + item.name;
		},
		highlighter: Object,
		afterSelect: function(item) { 
			  
				$('#txtcoaacct').val(item.id);
				return item;
		}
	
	});

		$("#frmITEM").on('submit', function (e) {
			e.preventDefault();

			var tbl = document.getElementById('myCheckBook').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			$('#hdnchkbkcnt').val(lastRow);
			
			
			var form = $("#frmITEM");
			var formdata = form.serialize();
			
			
			$.ajax({
				url: 'Bank_editsave.php',
				type: 'POST',
				async: false,
				data: formdata,
				beforeSend: function(){
					$("#AlertMsg").html("<b>UPDATING NEW BANK: </b> Please wait a moment...");
					//$("#AlertMsg").html("<b>"+formdata+"");
					$("#AlertModal").modal('show');
				},
				success: function(data) {

					if(data.trim()=="True"){
												
							
							$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated! <br><br> Loading bank details... <br> Please wait!");
						
							setTimeout(function() {
								 $("#AlertMsg").html("");
								 $('#AlertModal').modal('hide');
												  
								 $("#txtcitemno").val($("#txtccode").val());
										$("#frmedit").submit();
							}, 2000); // milliseconds = 2seconds
												
					}

					else{
						$("#AlertMsg").html(data);	
					}
				},
				error: function(){
					$("#AlertMsg").html("");
					$("#AlertModal").modal('hide');
									
					$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to update bank details!");
					$("#itmcode_err").show();
								  
				}
			});		
			
								

		});
							
							
});

	$(document).keydown(function(e) {	 

		 if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Bank_new.php';
			}
		  }
		  else if(e.keyCode == 83 && e.ctrlKey){//F2
			if($("#btnSave").is(":disabled")==false){
				e.preventDefault();
				$("#btnSave").click();
			}
		  }
		  else if(e.keyCode == 69 && e.ctrlKey){//F8
			if($("#btnEdit").is(":disabled")==false){
				e.preventDefault();
				enabled();
			}
		  }
		  else if(e.keyCode == 90 && e.ctrlKey){//CTRL+Z
			if($("#btnUndo").is(":disabled")==false){
				e.preventDefault();
				chkSIEnter(13,'frmedit');
			}
		  }
		  else if(e.keyCode == 27){//ESC	  
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Bank.php';
			}
		  }

	});

function disabled(){

	$("#frmITEM :input, label").attr("disabled", true);
	
	$("#txtccode").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){

		$("#frmITEM :input, label").attr("disabled", false);
		
			
			$("#txtccode").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
			
			$("#txtcdesc").focus();

}

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "Bank_edit.php";
		document.getElementById(frm).submit();
	}
}

function addcheckbook(){
	var tbl = document.getElementById('myCheckBook').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('myCheckBook').insertRow(-1);
	var u=a.insertCell(0);
	u.align = "left";
	u.style.padding = "1px";
	var v=a.insertCell(1);
	v.align = "left";
	v.style.padding = "1px";
	var w=a.insertCell(2);
	w.align = "left";
	w.style.padding = "1px";
	var x=a.insertCell(3);
	x.align = "left";
	x.style.padding = "1px";
	var y=a.insertCell(4);
	y.align = "center";
	
	u.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='form-control input-sm' id='txtchkbookno"+lastRow+"' name='txtchkbookno"+lastRow+"' value='' required style=\"text-align: right\"> </div>";
	v.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='form-control input-sm' id='txtchkfrom"+lastRow+"' name='txtchkfrom"+lastRow+"' value='' required style=\"text-align: right\"> </div>";
	w.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='form-control input-sm' id='txtcheckto"+lastRow+"' name='txtcheckto"+lastRow+"' value='' required style=\"text-align: right\"> </div>";
	x.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='form-control input-sm' id='txtcurrentchk"+lastRow+"' name='txtcurrentchk"+lastRow+"' value='' required style=\"text-align: right\" readonly> </div>";
	y.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='Delete' onClick=\"deleteRow(this);\"/>";
	
		$("#txtchkfrom"+lastRow).on("keyup", function() {
			$("#txtcurrentchk"+lastRow).val($(this).val());
		});
	

}

function deleteRow(r) {
	var tbl = document.getElementById('myCheckBook').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('myCheckBook').deleteRow(i);
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempchkbkno = document.getElementById('txtchkbookno' + z);
			var tempchkfrom = document.getElementById('txtchkfrom' + z);
			var tempchkto= document.getElementById('txtcheckto' + z);
			var tempcurchk= document.getElementById('txtcurrentchk' + z);
			
			var x = z-1;
			tempchkbkno.id = "txtchkbookno" + x;
			tempchkbkno.name = "txtchkbookno" + x;
			tempchkfrom.id = "txtchkfrom" + x;
			tempchkfrom.name = "txtchkfrom" + x;
			tempchkto.id = "txtcheckto" + x;
			tempchkto.name = "txtcheckto" + x;
			tempcurchk.id = "txtcurrentchk" + x;
			tempcurchk.name = "txtcurrentchk" + x;

		}
}


function loaditmfactor(){
			var itmno = $("#txtccode").val();
	
			$.ajax ({
            url: "th_bankcheck.php",
			data: { id: itmno },
			dataType: 'json',
			async: false,
            success: function(data) {
				
				console.log(data);
				$.each(data,function(index,item){
							//var curstat = "";
							var delstat = "";
							var onbtnclk = "deleteRow(this);";
							
							if(item.chknocu>item.chknofr){
								//curstat = "readonly";
								delstat = "disabled";
								onbtnclk = "";
							}
							
							var tbl = document.getElementById('myCheckBook').getElementsByTagName('tr');
							var lastRow = tbl.length;
						
							var a=document.getElementById('myCheckBook').insertRow(-1);
							var u=a.insertCell(0);
							u.align = "left";
							u.style.padding = "1px";
							var v=a.insertCell(1);
							v.align = "left";
							v.style.padding = "1px";
							var w=a.insertCell(2);
							w.align = "left";
							w.style.padding = "1px";
							var x=a.insertCell(3);
							x.align = "left";
							x.style.padding = "1px";
							var y=a.insertCell(4);
							y.align = "center";
							
							u.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='form-control input-sm' id='txtchkbookno"+lastRow+"' name='txtchkbookno"+lastRow+"' value='"+item.chkbkno+"' required style=\"text-align: right\"> </div>";
							v.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='form-control input-sm' id='txtchkfrom"+lastRow+"' name='txtchkfrom"+lastRow+"' value='"+item.chknofr+"' required style=\"text-align: right\"> </div>";
							w.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='form-control input-sm' id='txtcheckto"+lastRow+"' name='txtcheckto"+lastRow+"' value='"+item.chknoto+"' required style=\"text-align: right\"> </div>";
							x.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='form-control input-sm' id='txtcurrentchk"+lastRow+"' name='txtcurrentchk"+lastRow+"' value='"+item.chknocu+"' required style=\"text-align: right\" readonly> </div>";
							y.innerHTML = "<input class='btn btn-danger btn-xs "+delstat+"' type='button' id='row_" + lastRow + "_delete' class='delete' value='Delete' onClick=\""+onbtnclk+"\" />";
						
							
				});

            }
    		});
	
}



</script>