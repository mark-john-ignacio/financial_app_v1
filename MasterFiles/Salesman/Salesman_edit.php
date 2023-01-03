<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Salesman_edit.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

			if(isset($_REQUEST["txtcitemno"])){
				$citemno = $_REQUEST['txtcitemno'];
			}else{
				$citemno = $_REQUEST['txtccode'];
			}
				
			$company = $_SESSION['companyid'];
				
				if($citemno <> ""){
					
					$sql = "select A.* from salesman A where A.compcode='$company' and A.ccode='$citemno'";
				}else{
					header('Salesman.php');
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
						$HouseNo = $row['chouseno']; 
						$City = $row['ccity']; 
						$State = $row['cstate'];
						$Country = $row['ccountry'];
						$ZIP = $row['czip'];
						
						$ctelno = $row['ctelno'];
						$cemailadd = $row['cemailadd'];

						$Status = $row['cstatus'];

					}
				}
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
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../../Bootstrap/js/moment.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 


</head>

<body style="padding:5px; height:700px">

<form name="frmCust" id="frmCust" method="post" enctype="multipart/form-data">
	<fieldset>
    	<legend>Salesman Details  (<b>Status: <?php echo $Status; ?></b>)</legend>
<table width="100%" border="0">
  <tr>
    <td width="150" height="150" rowspan="5"  style="vertical-align:top">
    <?php 
	if(!file_exists("../../imgsman/".$citemno.".jpg") and !file_exists("../../imgsupp/".$citemno.".jpeg") and !file_exists("../../imgsman/".$citemno.".png")){
		$imgsrc = "../../images/emp.jpg";
	}
	else{
		if(file_exists("../../imgsman/".$citemno.".jpg")){
			$imgsrc = "../../imgsman/".$citemno.".jpg";
		}

		if(file_exists("../../imgsman/".$citemno.".jpeg")){
			$imgsrc = "../../imgsman/".$citemno.".jpeg";
		}

		if(file_exists("../../imgsman/".$citemno.".png")){
			$imgsrc = "../../imgsman/".$citemno.".png";
		}
	}
	?>

    <img src="<?php echo $imgsrc;?>" width="145" height="145" id="previewing">
    </td>
    <td width="150">&nbsp;<b>Salesman Code</b></td>
    <td style="padding:2px"><div class="col-xs-4 nopadding"><input type="text" class="form-control input-sm" id="txtccode" name="txtccode" tabindex="1" placeholder="Input Customer Code.." required value="<?php echo $cCustCode;?>" autocomplete="off" onKeyUp="chkSIEnter(event.keyCode,'frmCust');" /></div><span id="user-result"></span></td>
  </tr>
  <tr>
    <td>&nbsp;<b>Salesman Name</b></td>
    <td style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Customer Name.." required  value="<?php echo $cCustName;?>" autocomplete="off" /></div></td>
  </tr>   

  <tr>
    <td>&nbsp;<b>Address</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  tabindex="3"  value="<?php echo $HouseNo; ?>" /></div></td>
  </tr>
  
  <tr>
		<td>&nbsp;</td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off" tabindex="4"  value="<?php echo $City; ?>" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off" tabindex="5"  value="<?php echo $State; ?>" />
                    </div></div></td>
  </tr>
  
  <tr>
    <td style="vertical-align:top" align="center">&nbsp;</td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-9 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="6" value="<?php echo $Country; ?>" />
                    </div>
                    
                    <div class="col-xs-3 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="7" value="<?php echo $ZIP; ?>" />
                    </div></div></td>
  </tr>
  <tr>
    <td align="center"><div class="col-xs-12 nopadwtop2x">
      <label class="btn btn-warning btn-xs"> Browse Image&hellip;
        <input type="file" name="file" id="file" style="display: none;">
      </label>
    </div></td>
    <td>&nbsp;<strong>Contact No.</strong></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtcontact" name="txtcontact" tabindex="8" placeholder="Contact No.." autocomplete="off" value="<?php echo $ctelno; ?>" /></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;<strong>Email Add</strong></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtcEmail" name="txtcEmail" tabindex="9" placeholder="Email Address.." autocomplete="off" value="<?php echo $cemailadd; ?>" /></div></td>
  </tr>
  
   <tr>
    <td colspan="3" style="vertical-align:top"><div class="err" id="add_err"></div></td>
    </tr>
  <tr>
    <td colspan="3" style="vertical-align:top">&nbsp;
		<div class="err" id="add_err"></div>
    </td>
  </tr>
</table>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
		<button type="button" class="btn btn-primary btn-sm" onClick="window.location.href='Salesman.php';" id="btnMain" name="btnMain">Back to Main<br>(ESC)</button>

    	<button type="button" class="btn btn-default btn-sm" onClick="window.location.href='Salesman_new.php';" id="btnNew" name="btnNew">New<br>(F1)</button>
 
     <button type="button" class="btn btn-danger btn-sm" onClick="chkSIEnter(13,'frmedit');" id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>
   
        <button type="button" class="btn btn-warning btn-sm" onClick="enabled();" id="btnEdit" name="btnEdit"> Edit<br>(CTRL+E) </button>

    	<button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (CTRL+S)</button>
    
    </td>
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
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $cCustCode;?>">
</form>

</body>
</html>

<script type="text/javascript">
$(document).ready(function(){
	$("#itmcode_err").hide();
	$("#txtccode").focus();

	disabled();
});
	$(function(){

		
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

						$("#frmCust").on('submit', function (e) {
								e.preventDefault();
						
							  var formx = document.getElementById("frmCust");
								var formData = new FormData(formx);
							//alert(formData.serialize());
							  $.ajax({
								type: 'post',
								url: 'Salesman_editsave.php',
								data: formData,
								contentType: false,
								processData: false,
								async:false,
								beforeSend: function(){
								  	$("#AlertMsg").html("<b>UPDATING SALESMAN: </b> Please wait a moment...");
									$("#AlertModal").modal('show');
								},
								success: function(data) {

										if(data.trim()=="True" || data.trim()=="Size" || data.trim()=="NO"){
											if(data.trim()=="True"){
									 			$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated! <br><br> Loading salesman details... <br> Please wait!");				
											}else if(data.trim()=="Size"){
												$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated<br><br> Invalid Image Type or Size is too big! <br><br> Loading supplier details... <br> Please wait!");				
											}
											else if(data.trim()=="NO"){
												$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated <br><br> NO new image to be uploaded! <br><br> Loading supplier details... <br> Please wait!");				
											}
											
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
									
							  		$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to update customer!");
									$("#itmcode_err").show();
								  
								}
							  });							

						});

		//Checking of uploaded file.. must be image
		$("#file").change(function() {
			$("#add_err").empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
				$('#previewing').attr('src','../../imgusers/preview.jpg');
				
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<div class='alert alert-danger nopadwleft' role='alert'>Please Select A valid Image File. <b>Note: </b>Only jpeg, jpg and png Images type allowed</div>");
				return false;
			}
			else
			{
				var reader = new FileReader();
				reader.onload = imageIsLoaded;
				reader.readAsDataURL(this.files[0]);
			}
		});
		
	});

	$(document).keydown(function(e) {	 

		 if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Salesman_new.php';
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
		  else if(e.keyCode == 90 && e.ctrlKey){//F3
			if($("#btnUndo").is(":disabled")==false){
				e.preventDefault();
				chkSIEnter(13,'frmedit');
			}
		  }
		  else if(e.keyCode == 27){//ESC	  
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Salesman.php';
			}
		  }

	});


function disabled(){

	$("#frmCust :input, label").attr("disabled", true);
	
	
	$("#txtccode").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){

		$("#frmCust :input, label").attr("disabled", false);
		
			
			$("#txtccode").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
			
			$("#txtcdesc").focus();

}

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "Salesman_edit.php";
		document.getElementById(frm).submit();
	}
}


	//preview of image
	function imageIsLoaded(e) {
		$("#file").css("color","green");
		$('#image_preview').css("display", "block");
		$('#previewing').attr('src', e.target.result);
		$('#previewing').attr('width', '145px');
		$('#previewing').attr('height', '145px');
	};



</script>
