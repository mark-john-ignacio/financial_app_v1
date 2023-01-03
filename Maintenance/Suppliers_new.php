<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Suppliers_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
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

<body style="padding:5px; min-height:700px">
<form name="frmITEM" id="frmITEM" method="post">
	<fieldset>
    	<legend>New Supplier</legend>
<table width="100%" border="0">
  <tr>
    <td width="150"><b>Supplier Code</b></td>
    <td width="310" colspan="2" style="padding:2px">
    <div class="col-xs-12 nopadding">
           <div class="col-xs-4 nopadding">
            <input type="text" class="form-control input-sm" id="txtccode" name="txtccode" tabindex="1" placeholder="Input Supplier Code.." required autocomplete="off" />
           </div>
    
           <div class="col-xs-4 nopadwleft">		
            	 <div id="itmcode_err" style="padding: 5px 10px;"></div>
           </div>
    </div>
    </td>
  </tr>
  <tr>
    <td><b>Supplier Name</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Supplier Name.." required autocomplete="off" /></div></td>
  </tr>
</table>

<p>&nbsp;</p>
  <ul class="nav nav-tabs">
    <li class="active"><a href="#home">General</a></li>
    <li><a href="#menu1">Contact Details</a></li>
	<li><a href="#menu2">Product Details</a></li>
  </ul>
  
<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 30vh;text-align: left;overflow: auto">
    <div class="tab-content">
    
         <div id="home" class="tab-pane fade in active" style="padding-left:10px">
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
                        <input type="text" class="form-control input-sm" id="txtsalesacct" name="txtsalesacct"  tabindex="5" placeholder="Search Acct Title.." required autocomplete="off" />
                           </div>
                        
                            <div class="col-xs-3 nopadwleft">
                                <input type="text" id="txtsalesacctD" name="txtsalesacctD" class="form-control input-sm" readonly>
                            </div>	

                    </div>
                </div>

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
                		<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="9" />
                    </div>
                    
                    <div class="col-xs-3 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="10" />
                    </div>
                </div>

            
             </p>
         </div>

         <div id="menu1" class="tab-pane fade" style="padding-left:10px">
             <p>

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
    <td><button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (CTRL+S)</button></td>
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

<form name="frmedit" id="frmedit" action="Suppliers_edit.php" method="POST">
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
											
				addpurchcost(item.id,item.value);


									$("#txtcitmdesc").val("").change();
									$("#txtcitmdesc").focus();

			}
		
		});

		$("#frmITEM").on('submit', function (e) {
			e.preventDefault();
			var form = $("#frmITEM");
			var formdata = form.serialize();
			$.ajax({
				url: 'Suppliers_newsave.php',
				type: 'POST',
				async: false,
				data: formdata,
				beforeSend: function(){
					$("#AlertMsg").html("<b>SAVING NEW SUPPLIER: </b> Please wait a moment...");
					$("#AlertModal").modal('show');
				},
				success: function(data) {

					if(data.trim()=="True"){
						
						var x = saveprodz();						
						
						if(x.trim()=="True"){
							
							$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved! <br><br> Loading new supplier... <br> Please wait!");
						}
						else{
							$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved!<br><b>ERROR: </b>Supplier Details saving... <br><br> Loading new supplier... <br> Please wait!");
						}
						
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
									
					$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to save new supplier!");
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

					$("#txtccode").on("keyup", function() {
						
						//	$.post('itemcode_checker.php', {'id': $(this).val() }, function(data) {
						if($(this).val()!=""){
							$.ajax ({
							url: "suppcode_checker.php",
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
							url: "suppcode_checker.php",
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
	  	  e.preventDefault();
		 if(document.getElementById("btnSave").className=="btn btn-success btn-sm"){
		  $("#btnSave").click();
		 }
	  }

});

function addpurchcost(codez,namez){
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
		
	var crem = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><input type=\"text\" class=\"form-control input-xs\" id=\"txtcremarks\" name=\"txtcremarks\" placeholder=\"Enter Remarks...\" autocomplete=\"off\" /></td>";
	
	var cstat = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><button class=\"form-input btn btn-xs btn-danger\">Remove</button></div></td>";
	
	$('#myPurchTable > tbody:last-child').append('<tr>' + itmcode + itmname + crem + cstat + '</tr>');


	$("#itmerradd").attr("class","");
	$("#itmerradd").html("");
	$("#itmerradd").hide();
	
}
}

function saveprodz(){

	var custcode = $("#txtccode").val();
	var result = "True";
						
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

}

</script>