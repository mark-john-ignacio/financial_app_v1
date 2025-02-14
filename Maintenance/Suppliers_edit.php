<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Suppliers_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');


			if(isset($_REQUEST['txtcitemno'])){
					$citemno = $_REQUEST['txtcitemno'];
			}
			else{
					$citemno = $_REQUEST['txtccode'];
				}
				
				if($citemno <> ""){
					
					$sql = "select suppliers.*, A1.cacctdesc as salescode from suppliers LEFT JOIN accounts A1 ON (suppliers.cacctcode = A1.cacctno) where suppliers.ccode='$citemno'";
				}else{
					header('Items.php');
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
						$GroceryID = $row['cacctcode'];
						$GroceryDesc = $row['salescode'];
						$Status = $row['cstatus'];
						
						$Type = $row['csuppliertype'];
						$Class = $row['csupplierclass'];
						$Terms = $row['cterms'];
						
						$HouseNo = $row['chouseno'];
						$City = $row['ccity'];
						$State = $row['cstate'];
						$Country = $row['ccountry'];
						$ZIP = $row['czip'];
					
						$Contact = $row['ccontactname'];
						$Desig = $row['cdesignation'];
						$Email = $row['cemail'];
						$PhoneNo = $row['cphone'];
						$Mobile = $row['cmobile'];

					}
				}

?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../Bootstrap/js/moment.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

</head>

<body style="padding:5px;">
<form name="frmSupp" id="frmSupp" method="post" enctype="multipart/form-data">
	<fieldset>
    	<legend>Suppliers Details  (<b>Status: <?php echo $Status; ?></b>)</legend>
<table width="100%" border="0">
  <tr>
    <td width="150" height="150" rowspan="5"  style="vertical-align:top">
    <?php 
	if(!file_exists("../imgsupp/".$cCustCode.".jpg") and !file_exists("../imgsupp/".$cCustCode.".jpeg") and !file_exists("../imgsupp/".$cCustCode.".png")){
		$imgsrc = "../images/emp.jpg";
	}
	else{
		if(file_exists("../imgsupp/".$cCustCode.".jpg")){
			$imgsrc = "../imgsupp/".$cCustCode.".jpg";
		}

		if(file_exists("../imgsupp/".$cCustCode.".jpeg")){
			$imgsrc = "../imgsupp/".$cCustCode.".jpeg";
		}

		if(file_exists("../imgsupp/".$cCustCode.".png")){
			$imgsrc = "../imgsupp/".$cCustCode.".png";
		}
	}
	?>
    <img src="<?php echo $imgsrc;?>" width="145" height="145" id="previewing">

    </td>
    <td width="150" style="vertical-align:middle"><b>Supplier Code</b></td>
    <td colspan="2" style="padding:2px;">
    <div class="col-xs-12 nopadding">
           <div class="col-xs-4 nopadding">
            <input type="text" class="form-control input-sm" id="txtccode" name="txtccode" tabindex="1" placeholder="Input Supplier Code.." required autocomplete="off" value="<?php echo $cCustCode;?>" onKeyUp="chkSIEnter(event.keyCode,'frmSupp');" />
           </div>
    
           <div class="col-xs-4 nopadwleft">		
            	 <div id="itmcode_err" style="padding: 5px 10px;"></div>
           </div>
    </div>
    </td>
  </tr>
  <tr>
    <td style="vertical-align:middle"><b>Supplier Name</b></td>
    <td colspan="2" style="padding:2px;"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Supplier Name.." required autocomplete="off" value="<?php echo $cCustName;?>" /></div></td>
  </tr>
  <tr>
    <td colspan="3" style="vertical-align:top">                
    <div class="col-xs-6 nopadwtop2x">
            <label class="btn btn-warning btn-xs">
                Browse Image&hellip; <input type="file" name="file" id="file" style="display: none;">
            </label>
    </div>
	</td>
  </tr>
  <tr>
    <td colspan="3" style="vertical-align:top"><div class="err" id="add_err"></div></td>
    </tr>
  <tr>
    <td colspan="3" style="vertical-align:top">&nbsp;
    </td>
    </tr>
</table>


  <ul class="nav nav-tabs">
    <li class="active"><a href="#home">General</a></li>
    <li><a href="#menu1">Contact Details</a></li>
    <li><a href="#menu2">Product Details</a></li>
  </ul>
  
<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 30vh;text-align: left;overflow: auto">
    <div class="tab-content">
    
         <div id="home" class="tab-pane fade in active" style="padding-left:30px">
             <p>

				<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadding">
                		<b>Type</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

                            <div class="col-xs-7 nopadding">
                                    <select id="seltyp" name="seltyp" class="form-control input-sm selectpicker"  tabindex="3">
                                        <?php
                                    $company = $_SESSION['companyid'];
                                        
                                    $sql = "select * from groupings where compcode='$company' and ctype='SUPTYP' and cstatus='ACTIVE' order by cdesc";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>   
                                        <option value="<?php echo $row['ccode'];?>" <?php if($row['ccode']==$Type){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
                                        <?php
                                            }
                                            
                            
                                        ?>     
                                    </select>
                                </div>
                    </div>
                </div>


				<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadding">
                		<b>Classification</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

                            <div class="col-xs-7 nopadding">
                                  <select id="selcls" name="selcls" class="form-control input-sm selectpicker"  tabindex="3">
									<?php
                                    $sql = $sql = "select * from groupings where compcode='$company' and ctype='SUPCLS' and cstatus='ACTIVE' order by cdesc";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>
                                    <option value="<?php echo $row['ccode'];?>" <?php if($row['ccode']==$Class){ echo "selected"; } ?> ><?php echo $row['cdesc']?></option>
                                    <?php
                                            }
                                            
                            
                                        ?>
                                  </select>
                                </div>
                    </div>
                </div>


				<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadding">
                		<b>Terms</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

                            <div class="col-xs-4 nopadding">
                                  <select id="selterms" name="selterms" class="form-control input-sm selectpicker"  tabindex="3">
									<?php
                                    $sql = "Select * From groupings where compcode='$company' and ctype='TERMS'";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>
                                    <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                                    <?php
                                            }
                                            
                            
                                        ?>
                                  </select>
                                </div>
                    </div>
                </div>

				<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadding">
                		<b>Account Code</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

                           <div class="col-xs-7 nopadding">
                        <input type="text" class="form-control input-sm" id="txtsalesacct" name="txtsalesacct"  tabindex="5" placeholder="Search Acct Title.." required autocomplete="off" value="<?php echo $GroceryDesc;?>" />
                           </div>
                        
                            <div class="col-xs-3 nopadwleft">
                                <input type="text" id="txtsalesacctD" name="txtsalesacctD" class="form-control input-sm" readonly value="<?php echo $GroceryID;?>">
                            </div>	

                    </div>
                </div>

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

            
             </p>
         </div>

         <div id="menu1" class="tab-pane fade" style="padding-left:30px">
             <p>

                <div class="col-xs-7 nopadding">
                	<input type="text" class="form-control input-sm" id="txtcperson" name="txtcperson" placeholder="Contact Person..." autocomplete="off" tabindex="11" value="<?php echo $Contact;?>" />
                </div>
 
              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcdesig" name="txtcdesig" placeholder="Designation..." autocomplete="off" tabindex="12" value="<?php echo $Desig;?>" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcEmail" name="txtcEmail" placeholder="Email Address..." autocomplete="off" tabindex="13" value="<?php echo $Email;?>" />
                    </div>
                </div>

              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcphone" name="txtcphone" placeholder="Phone No..." autocomplete="off" tabindex="14" value="<?php echo $PhoneNo;?>" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcmobile" name="txtcmobile" placeholder="Mobile No..." autocomplete="off" tabindex="15" value="<?php echo $Mobile;?>" />
                    </div>
                </div>

             </p>
         </div>
         
                  <div id="menu2" class="tab-pane fade" style="padding-left:10px">
             <p>
        
        <div class="col-xs-12 nopadwdown">
        <div class="col-xs-2 nopadwright2x">
			<input type='text' class="form-control input-sm" id="txtcitmno" name="txtcitmno" value="" placeholder="Enter product code..." autocomplete="off"/>
       		<input type='hidden' id="hdncunit" name="hdncunit" value="" />
        </div>
        <div class="col-xs-4 nopadding">
			<input type='text' class="form-control input-sm" id="txtcitmdesc" name="txtcitmdesc" value="" placeholder="Enter product description..." autocomplete="off" />
        </div>
        
        <div class="col-xs-5 nopadwleft">
                <div id="itmerradd"></div>
        </div>
    
        </div>

            
           	<table width="95%" border="0" id="myPurchTable" cellpadding="2">
             <thead>
              <tr>
                <th scope="col" width="150">Product Code</th>
                <th scope="col">Product Description</th>
                <th scope="col" width="220">Remarks</th>
                <th scope="col" width="100">&nbsp;</th>
              </tr>
             </thead>
             <tbody>
             
             </tbody>
            </table>
           	
			 </p>
         </div>

    
	</div>
</div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>		<button type="button" class="btn btn-primary btn-sm" onClick="window.location.href='Suppliers.php';" id="btnMain" name="btnMain">Back to Main<br>(ESC)</button>

    	<button type="button" class="btn btn-default btn-sm" onClick="window.location.href='Suppliers_new.php';" id="btnNew" name="btnNew">New<br>(F1)</button>
 
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

<form name="frmedit" id="frmedit" action="Suppliers_edit.php" method="POST">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $citemno; ?>">
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
	
	loaditemdet();
	disabled();
	
});

$(function() {

		$('#txtcitmdesc').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
					dataType: "json",
					data: {
						query: $("#txtcitmdesc").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return item.value;
			},
			highlighter: Object,
			afterSelect: function(item) { 					
											
				addpurchcost(item.id,item.value,"");


				$("#txtcitmdesc").val("").change();
				$("#txtcitmdesc").focus();

			}
		
		});

		$('#txtcitmno').on("keypress", function(event) {
			if(event.keyCode == 13){
						$.ajax({
							url:'get_productid.php',
							data: 'c_id='+ $(this).val(),                 
							success: function(value){
								var data = value.split(",");
								//$('#txtcitmno').val(data[0]);
								//$('#txtcitmdesc').val(data[1]);
								
								addpurchcost(data[0],data[1],"");
							}
						});
						
						$('#txtcitmno').val("");
			}
		});



						$("#frmSupp").on('submit', function (e) {
							
							e.preventDefault();
							 
							  var form = document.getElementById("frmSupp");
							  var formData = new FormData(form);

							  $.ajax({
								type: 'POST',
								url: 'Suppliers_editsave.php',
								data: formData,
								contentType: false,
								processData: false,
								async: false,
								beforeSend: function(){
								  	$("#AlertMsg").html("<b>UPDATING SUPPLIER DETAILS: </b> Please wait a moment...");
									$("#AlertModal").modal('show');
								},
								success: function (data) {
								//alert(data);
								
									var x = saveprodz();						
									
									//alert(x.trim());	
																
									if(x.trim()=="True"){

										
										
										if(data.trim()=="True" || data.trim()=="Size" || data.trim()=="NO"){
											if(data.trim()=="True"){
									 			$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated! <br><br> Loading supplier details... <br> Please wait!");				
											}else if(data.trim()=="Size"){
												$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated<br><br> Invalid Image Type or Size is too big! <br><br> Loading supplier details... <br> Please wait!");				
											}
											else if(data.trim()=="NO"){
												$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated <br><br> NO new image to be uploaded! <br><br> Loading supplier details... <br> Please wait!");				
											}
										}
										else{
											$("#AlertMsg").html(data);	
										}


									}
									else{
										$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved!<br><b>ERROR: </b>Product Details saving... <br><br> Loading new supplier... <br> Please wait!");
									}
									
								
											
											setTimeout(function() {
											  $("#AlertMsg").html("");
											  $('#AlertModal').modal('hide');
											  
											  $("#txtcitemno").val($("#txtccode").val());
											  $("#frmedit").submit();
											}, 2000); // milliseconds = 3seconds
											
											
								},
								error: function(){
									$("#AlertMsg").html("");
									$("#AlertModal").modal('hide');
									
							  		$("#AlertMsg").html("<b><font color='red'>ERROR: </font></b> Unable to update supplier!");
									$("#itmcode_err").show();
								  
								}
							  });							

							});
							
							
							
		$("#txtsalesacct").typeahead({						 
			autoSelect: true,
			source: function(request, response) {							
				$.ajax({
					url: "th_accounts.php",
					dataType: "json",
					data: { query: request },
					success: function (data) {
						response(data);
					}
				});
				},
				displayText: function (item) {
					return item.id + " : " + item.name;
				},
				highlighter: Object,
				afterSelect: function(item) { 					
					$('#txtsalesacct').val(item.name).change(); 
					$('#txtsalesacctD').val(item.id); 
							
				}
		});
		
		$("#txtsalesacct").on("blur", function() {
			if($('#txtsalesacctD').val()==""){
				$('#txtsalesacct').val("").change();
				$('#txtsalesacct').focus();
			}
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


		//Checking of uploaded file.. must be image
		$("#file").change(function() {
			$("#add_err").empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
				$('#previewing').attr('src','../imgusers/preview.jpg');
				
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
				window.location.href='Suppliers_new.php';
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
				window.location.href='Suppliers.php';
			}
		  }

	});


function addpurchcost(codez,namez,crem){
var isMERON = "";	
//CHECK IF CODE EXIST IN TABLE
$("#myPurchTable > tbody > tr").each(function() {
	var txtcitm = $(this).find("input[name='txtitmcode']").val();
	
	if(txtcitm==codez){
		isMERON = "TRUE";
	}
	
});

if(isMERON=="TRUE"){
	$("#itmerradd").attr("class","alert alert-danger nopadding");
	$("#itmerradd").html("<b>ERROR: </b> Item already added!");
	$("#itmerradd").show();

}
else{

	var tbl = document.getElementById('myPurchTable').getElementsByTagName('tr');
	var count = tbl.length;
	
	var itmcode = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadding\"><input type=\"hidden\" id=\"txtitmcode\" name=\"txtitmcode\" value=\""+codez+"\" />"+codez+"</div></td>";
	
	var itmname = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><input type=\"hidden\" id=\"txtitmname\" name=\"txtitmname\" value=\""+namez+"\" />"+namez+"</div></td>";
		
	var crem = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><input type=\"text\" class=\"form-control input-xs\" id=\"txtcremarks\" name=\"txtcremarks\" placeholder=\"Enter Remarks...\" autocomplete=\"off\" value=\""+crem+"\"/></td>";
	
	var cstat = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><button class=\"form-input btn btn-xs btn-danger\">Remove</button></div></td>";
	
	$('#myPurchTable > tbody:last-child').append('<tr>' + itmcode + itmname + crem + cstat + '</tr>');


	$("#itmerradd").attr("class","");
	$("#itmerradd").html("");
	$("#itmerradd").hide();
	
}
}

function disabled(){

	$("#frmSupp :input, label").attr("disabled", true);
	
	
	$("#txtccode").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){

		$("#frmSupp :input, label").attr("disabled", false);
		
			
			$("#txtccode").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
			
			$("#txtcdesc").focus();

}

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "Suppliers_edit.php";
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
	
function loaditemdet(){
	var custcode = $("#txtccode").val();
	
					$.ajax({
					url: "th_loadsupprod.php",
					dataType: "json",
					async: false,
					data: { id: custcode },
					success: function (data) {
                      console.log(data);
					  $.each(data,function(index,item){

						if(item.remarks==null){
							var crem="";
						}
						else{
							var crem=item.remarks;
						}
						
						addpurchcost(item.id,item.name,crem);

					  });
					}
				});

}


function saveprodz(){
//alert("Hello");

	var custcode = $("#txtccode").val();
	var result = "True"	
		//alert(custcode);
						
	$("#myPurchTable > tbody > tr").each(function() {
		
		var txtcitm = $(this).find("input[name='txtitmcode']").val();
		var txtcremarks = $(this).find("input[name='txtcremarks']").val();
							

			$.ajax ({
				url: "Suppliers_prodsave.php",
				data: { id: custcode, itm: txtcitm, rem: txtcremarks },
				async: false,
				success: function( data ) {
					 result = data;
				}
			});
			
														
	});
	
	
	return result;
	
	//alert(result);

}

</script>