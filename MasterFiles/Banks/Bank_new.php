<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Bank_New";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access.php');
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
    	<legend>New Bank</legend>
<table width="100%" border="0">
  <tr>
    <td width="200"><b>Bank Code</b></td>
    <td width="310" colspan="2" style="padding:2px">
    <div class="col-xs-12 nopadding">
           <div class="col-xs-4 nopadding">
            <input type="text" class="form-control input-sm" id="txtccode" name="txtccode" tabindex="1" placeholder="Input Bank Code.." required autocomplete="off" />
           </div>
    
           <div class="col-xs-4 nopadwleft">		
            	 <div id="itmcode_err" style="padding: 5px 10px;"></div>
           </div>
    </div>
    </td>
  </tr>
  <tr>
    <td><b>Bank Name</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Bank Name.." required autocomplete="off" /></div></td>
  </tr>
  <tr>
    <td><b>Bank Account Number</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding">
      <input type="text" class="form-control input-sm" id="txtbankacct" name="txtbankacct" tabindex="3" placeholder="Input Bank Acct No.." required autocomplete="off" />
    </div></td>
  </tr>
  <tr>
    <td><b>Bank Account Name</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding">
      <input type="text" class="form-control input-sm" id="txtbankacctnme" name="txtbankacctnme" tabindex="4" placeholder="Input Bank Acct Name.." required autocomplete="off" />
    </div></td>
  </tr>
  <tr>
    <td><b>Linked Account (COA)</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding">
      <input type="hidden" id="txtcoaacct" name="txtcoaacct" />
      <input type="text" class="form-control input-sm" id="txtcoa" name="txtcoa" tabindex="5" placeholder="Search Account Description.." autocomplete="off" />
    </div></td>
  </tr>

	<tr>
    <td><b>Check Doc Type</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-4 nopadding">
      <select class="form-control input-sm" name="seldoctype" id="seldoctype">
				<option value="1">BDO/LANDBANK CHECK FORMAT</option>
				<option value="2">METROBANK CHECK FORMAT</option>
				<option value="3">EASTWEST CHECK FORMAT</option>
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
                	<input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  tabindex="6" />
                </div>
 
              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off" tabindex="7" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off" tabindex="8" />
                    </div>
                </div>

              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-9 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="9" value="PHILIPPINES" />
                    </div>
                    
                    <div class="col-xs-3 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="10" />
                    </div>
                </div>
		  </div>
  
		  <div class="col-xs-12 nopadding">
				<div class="col-xs-7 nopadding">
					<u><h4>CONTACT DETAILS</h4></u>
                </div>

                <div class="col-xs-7 nopadding">
                	<input type="text" class="form-control input-sm" id="txtcperson" name="txtcperson" placeholder="Contact Person..." autocomplete="off" tabindex="11" />
                </div>
 
              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcdesig" name="txtcdesig" placeholder="Designation..." autocomplete="off" tabindex="12" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcEmail" name="txtcEmail" placeholder="Email Address..." autocomplete="off" tabindex="13" />
                    </div>
                </div>

              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcphone" name="txtcphone" placeholder="Phone No..." autocomplete="off" tabindex="14" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcmobile" name="txtcmobile" placeholder="Mobile No..." autocomplete="off" tabindex="15" />
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

          
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td style="padding-top:20px"><button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (CTRL+S)</button></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
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
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="">
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
			
			//alert(formdata);
			$.ajax({
				url: 'Bank_newsave.php',
				type: 'POST',
				async: false,
				data: formdata,
				beforeSend: function(){
					$("#AlertMsg").html("<b>SAVING NEW BANK: </b> Please wait a moment...");
					//$("#AlertMsg").html("<b>"+formdata+"");
					$("#AlertModal").modal('show');
				},
				success: function(data) {

					if(data.trim()=="True"){
												
							
							$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved! <br><br> Loading new bank... <br> Please wait!");
						
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
									
					$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to save new bank!");
					$("#itmcode_err").show();
								  
				}
			});		
			
								

		});
							
					$("#txtccode").on("keyup", function() {
						
						//	$.post('itemcode_checker.php', {'id': $(this).val() }, function(data) {
						if($(this).val()!=""){
							$.ajax ({
							url: "bankcode_checker.php",
							data: { id: $(this).val() },
							async: false,
							dataType: 'text',
							success: function( data ) {

								if(data.trim()=="True"){

							  		$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Code Already In Use!");
									
									$("#itmcode_err").show();
								}
								else if(data.trim()=="False") {

							  		$("#itmcode_err").html("<b><font color='green'>VALID: </font></b> Valid Code!");
									
									$("#itmcode_err").show();
								}
							}
							});
						}
						else{
							$("#itmcode_err").html("");
							$("#itmcode_err").hide();
						}

					});


					$("#txtccode").on("blur", function() {
						
						//	$.post('itemcode_checker.php', {'id': $(this).val() }, function(data) {
							
							$.ajax ({
							url: "bankcode_checker.php",
							data: { id: $(this).val() },
							async: false,
							success: function( data ) {
								if(data.trim()=="True"){
									$("#txtccode").val("").change();
									$("#txtccode").focus();
								}
							}
							});
							
							$("#itmcode_err").html("");
							$("#itmcode_err").hide();


					});
							
});

$(document).keydown(function(e) {
	 if(e.keyCode == 83 && e.ctrlKey) { //CTRL S
		 if($("#btnSave").is(":disabled")==false){
		   e.preventDefault();
		   $("#btnSave").click();
		 }
	  }
});

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



</script>