<?php
if(!isset($_SESSION)){
	session_start();
}

$_SESSION['pageid'] = "Salesman_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/jquery.numeric.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../../Bootstrap/js/moment.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

</head>

<body style="padding:5px; height:700px">
<form name="frmITEM" id="frmITEM" method="post">
	<fieldset>
    	<legend>New Salesman</legend>
<table width="100%" border="0">
  <tr>
    <td width="150"><b>Salesman Code</b></td>
    <td width="310" colspan="2" style="padding:2px">
    <div class="col-xs-7 nopadding">
    
           <div class="col-xs-4 nopadding">
            <input type="text" class="form-control input-sm" id="txtccode" name="txtccode" tabindex="1" placeholder="Input Customer Code.." required autocomplete="off" />
           </div>
    
           <div class="col-xs-5 nopadwleft">		
            	 <div id="itmcode_err" style="padding: 5px 10px;"></div>
           </div>
   
    </div>
    
    </td>
  </tr>
  <tr>
    <td><b>Salesman Name: </b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm text-uppercase" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Customer Name.." required autocomplete="off" /></div></td>
  </tr>

  <tr>
    <td><b>Address</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  tabindex="3" /></div></td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off" tabindex="4" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off" tabindex="5" />
                    </div></div></td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-9 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="6" />
                    </div>
                    
                    <div class="col-xs-3 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="7" />
                    </div></div></td>
  </tr>
  <tr>
    <td><b>Contact No.</b></td>
<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtcontact" name="txtcontact" tabindex="8" placeholder="Contact No.." autocomplete="off" /></div></td>
  </tr>
  <tr>
    <td><b>Email Add</b></td>
<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtcEmail" name="txtcEmail" tabindex="9" placeholder="Email Address.." autocomplete="off" /></div></td>
  </tr>
  

  
  
</table>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td><button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (F2)</button></td>
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



<form name="frmedit" id="frmedit" action="Salesman_edit.php" method="POST">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="">
</form>

</body>
</html>


<script type="text/javascript">

$(function() {
		
		$("#frmITEM").on('submit', function (e) {
		e.preventDefault();								
														  
			var form = $("#frmITEM");
			var formdata = form.serialize();

			$.ajax({
			url: 'Salesman_newsave.php',
			type: 'POST',
			async: false,
			data: formdata,
			beforeSend: function(){
				$("#AlertMsg").html("<b>SAVING NEW SALESMAN: </b> Please wait a moment...");
				$("#AlertModal").modal('show');
			},
			success: function(data) {
				if(data.trim()=="True"){
					$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved! <br><br> Loading new salesman... <br> Please wait!");
												
					setTimeout(function() {
						$("#AlertMsg").html("");
						  $('#AlertModal').modal('hide');
												  
							$("#txtcitemno").val($("#txtccode").val());
								$("#frmedit").submit();
							}, 3000); // milliseconds = 3seconds
												
					}
				else{
					$("#AlertMsg").html(data);	
				}
			},
			error: function(){
				$("#AlertMsg").html("");
				$("#AlertModal").modal('hide');
									
				$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to save new salesman!");
				$("#itmcode_err").show();
								  
			}
	    	});							
	});
	
		$("#txtcEmail").on("blur", function() {
			var sEmail = $(this).val();
			
			var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
			
			if(sEmail!=""){
				if (filter.test(sEmail)) {
					//wlang gagawin
				}
				else {
					$("#txtcEmail").val("").change();
					$("#txtcEmail").attr("placeholder","You entered and invalid email!");
					$("#txtcEmail").focus();
				}
			}
			else{
				$("#txtcEmail").attr("placeholder","Email Address...");
			}

		});

		
						$("#txtccode").on("keyup", function() {
						
						//	$.post('itemcode_checker.php', {'id': $(this).val() }, function(data) {
						if($(this).val()!=""){
							$.ajax ({
							url: "salesman_codechecker.php",
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
							url: "salesman_codechecker.php",
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
	if(e.keyCode == 113){//F2
			if(document.getElementById("btnSave").className=="btn btn-success btn-sm"){
				$("#btnSave").click();
			}
	}
});

</script>