<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PP_New";

include('../../Connection/connection_string.php');
include('../../include/accessinner.php');
?>
<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css"> 
    
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/jquery.numeric.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>MYX Financials</title>

</head>

<body style="padding:5px;" onLoad="document.getElementById('txtcust').focus();">

<form name="frmITEM" id="frmITEM" method="post">
	<fieldset>
    	<legend>New Price Matrix</legend>
        
 	<div class="col-xs-12 nopadwdown">
        <div class="col-xs-1 nopadding">
        	<b>Supplier: </b>
        </div>   
        <div class="col-xs-7 nopadding"> 	
            <div class="col-xs-5 nopadwright2x">
                <input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier's Name..." autocomplete="off">
            </div> 
        &nbsp;&nbsp;
        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px" readonly>

        </div>    	

        <div class="col-xs-1 nopadding">
        	<b>Effect Date: </b>
        </div>
        <div class="col-xs-3 nopadwright2x">
          <div class="col-xs-8"> 
			<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery"  placeholder="Pick Effect Date..." required />
          </div>
        </div>

    </div>
    
 	<div class="col-xs-12 nopadwdown">
    
        <div class="col-xs-1 nopadding">
        	<b>Description: </b>
        </div>
        <div class="col-xs-5 nopadding">
        	<input type='text' class="form-control input-sm" id="txtcdescription" name="txtcdescription" value="" autocomplete="off" placeholder="Enter description or remarks..." />
        </div>
    </div>

    <div class="col-xs-12 nopadwdown">
    	&nbsp;
    </div>
   
    <div class="col-xs-12 nopadwdown">
        <div class="col-xs-2 nopadwright2x">
			<input type='text' class="form-control input-sm" id="txtcitmno" name="txtcitmno" value="" placeholder="Enter product code..." autocomplete="off"/>
       		<input type='hidden' id="hdncunit" name="hdncunit" value="" />
        </div>
        <div class="col-xs-4 nopadding">
			<input type='text' class="form-control input-sm" id="txtcitmdesc" name="txtcitmdesc" value="" placeholder="(CTRL+F) Enter product description..." autocomplete="off" />
        </div>
        
        <div class="col-xs-5 nopadwleft">
                <div id="itmerradd"></div>
        </div>
    
        </div>


         <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 350px;
					text-align: left;
					overflow: auto">
    
             <table width="100%" border="0" class="table table-hover nopadding" id="myTable">
             <thead>
                  <tr>
                    <th scope="col" width="50">&nbsp;</th>
                    <th scope="col" width="120"><b>Item Code</b></th>
                    <th scope="col"><b>Item Desc</b></th>
                    <th scope="col" width="100"><b>UOM</b></th>
                    <th scope="col" width="95"><b>Price</b></th>
    				<th scope="col" width="10">&nbsp;</th>
                  </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            
         </div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>

       <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (F2)</button>
 
    </td>
    
    </tr>
</table>

        </fieldset>
</form>


<!-- 1) Alert Modal -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div>
               <div class="alert alert-modal-danger">
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

<!-- 2) UOM -->
<div class="modal fade" id="UOMModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">

    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="UOMListHdr">UOM List</h5>
            </div>
            
            <div class="modal-body" style="height:30vh">
            	<table width="100%" border="0" class="table table-small table-highlight" id="unittbls">
                 <thead>
                  <tr>
                    <th scope="col" width="80">UOM</th>
                    <th scope="col" width="130">Desc</th>
                    <th scope="col" width="100">Factor</th>
                    <th scope="col">&nbsp;</th>
                  </tr>
                 </thead>
                 <tbody>
                 </tbody>
                </table>

			</div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>


<form method="post" name="frmedit" id="frmedit" action="PP_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>

</body>
</html>

<script type="text/javascript">
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
		e.preventDefault();
		$('#txtcitmdesc').focus();
      }

	});


$(function() {              
           // Bootstrap DateTimePicker v4
	        $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY',
				 useCurrent: false,
			   	 minDate: moment(),
			     defaultDate: moment(),

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
		}
	});
		   
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
	
				$('#txtcitmdesc').val(item.value).change(); 
				$('#txtcitmno').val(item.id); 
				$("#hdncunit").val(item.cunit);
				
				additm('txtcitmdesc');
			}
		
		});
		
		$("#txtcitmno").on("keydown", function (e){
			if(e.keyCode==13){
				e.preventDefault();
				
				$.ajax({
					url: "th_productid.php",
					dataType: "json",
					async: false,
					data: { query: $("#txtcitmno").val() },
					success: function (data) {
                      console.log(data);
					  $.each(data,function(index,item){
						// alert(item.id); 
						//$('#txtcitmno').val(item.id).change(); 
						$('#txtcitmdesc').val(item.value); 
						$("#hdncunit").val(item.cunit);
						
						additm('txtcitmno');

					  });
					}
				});

			}
		});
		

});

function additm(xid){

	var count = $('#myTable tr').length;
	var uomchk = "";
	var cnt = 0;
								$("#myTable > tbody > tr").each(function(index) {	

										var varxitm = $(this).find("input[type='hidden'][name='txtcprtno']").val();
										var varxunt = $(this).find("input[name='txtcprtunit']").val();
										
										
										if(varxitm==$('#txtcitmno').val()){
											cnt = cnt + 1;
											if(cnt > 1){
												uomchk = uomchk + ",";
											}
											
											uomchk = uomchk + varxunt;
										}
									
								});

								$.ajax({
									async: false,
									type: "POST",
									dataType: 'text',
									url: "th_mainuom.php",
									data: { id: $('#txtcitmno').val(), uomzx: uomchk },
									success: function (data) {

										if(data.trim()=="0"){
											$('#hdncunit').val("");
										}
										else{
											$('#hdncunit').val(data);
										}
									}
								});

if($('#hdncunit').val()!=""){
	
	var itm = "<td><input type=\"hidden\" name=\"txtcprtno\" id=\"txtcprtno\" value=\""+$('#txtcitmno').val()+"\" />" + $('#txtcitmno').val() + "</td>";
	var cdesc = "<td>" + $('#txtcitmdesc').val() + "</td>";
	var cunit = "<td><div class=\"input-group nopadding\"><input type=\"text\" class=\"form-control input-xs\" name=\"txtcprtunit\" id=\"txtcprtunit"+count+"\" value=\""+$('#hdncunit').val()+"\" readonly><span class=\"input-group-addon input-xs primary\"> <i class=\"glyphicon glyphicon-refresh\" data-id=\""+$('#txtcitmno').val()+"\" data-desc=\""+$('#txtcitmdesc').val()+"\" data-unt=\""+$('#hdncunit').val()+"\" name=\"uomaddon\" id=\"uomaddon"+count+"\"> </i></span></div></td>";
	var del = "<td><input type=\"button\" class=\"btn btn-xs btn-danger\" value=\"Delete\" id=\"del"+$('#txtcitmno').val()+"\" /></td>";
	var detz = "<td> <input type=\"text\" class=\"numeric form-control input-xs\" name=\"txtnprice\" id=\"txtnprice\" required autocomplete=\"off\" value=\"0.0000\" style=\"text-align: right\" /></td>";
	var del = "<td width=\90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + $('#txtcitmno').val() + "' value='delete' />";
	
	
	$('#myTable > tbody:last-child').append('<tr>' + "<td>"+count+". </td>" +itm + cdesc + cunit + detz + del + '</tr>');	
					
				$("#btndel"+count).on('click', function() {
					$(this).closest('tr').remove();
				
					$('#myTable td:first-child').each(function(index){
					  //alert($(this).text() + " to " + index);
					  var indx = parseInt(index) + 1;
					  $(this).text(indx + ".");
					});

					$("#myTable > tbody > tr").each(function(index) {	
						var indx = parseInt(index) + 1;
						
						$(this).find("input[type='hidden'][name='txtcprtno']").attr("id", "txtcprtno"+indx);
						$(this).find("input[name='txtcprtunit']").attr("id", "txtcprtunit"+indx);
						$(this).find("button[name='btndel']").attr("id", "btndel"+indx);
						
					});
					
				});
				
				$("#uomaddon"+count).on("mouseover", function(index) {
					$(this).css('cursor','pointer');
				});
				
				$("#uomaddon"+count).on("click", function() {
					var idxchk = $(this).closest('td').parent()[0].sectionRowIndex;
					var x = $(this).attr("data-id");
					var y = $(this).attr("data-desc");
					var u = $(this).attr("data-unt");
					var zun = "";
					
					$('#UOMListHdr').html("UOM List: " + x + " - " + y);
					$('#unittbls tbody').empty();
					
					$.ajax({
						async: false,
						type: "POST",
						dataType: 'json',
						url: "th_loaduomperitm.php",
						data: { id: x },
						success: function (data) {
							
							console.log(data);
                       		$.each(data,function(index,item){
							var rmkschk = "";
								
								$("#myTable > tbody > tr").each(function(index) {	
									
										var varxitm = $(this).find("input[type='hidden'][name='txtcprtno']").val();
										var varxunt = $(this).find("input[name='txtcprtunit']").val();
										
										if(varxitm==x && varxunt==item.id){
											var ux = item.id;
											u = u.trim();
											ux = ux.trim();
											
											if(u == ux){
												rmkschk = "<i>Selected!</i>";
											}
											else{
												rmkschk = "<i>Existing in details!</i>";
											}
											
											return false;
										}
									
								});
								
								
								if(rmkschk==""){
									var tdval = "<a href=\"javascript:;\" onclick=\"setuom('"+count+"', '"+item.id+"')\">" + item.id + "</a>";
								}
								else{
									var tdval = item.id;
								}
								
								$("<tr>").append(
								$("<td>").html(tdval),
								$("<td>").text(item.name), 
								$("<td>").text(item.fact), 
								$("<td>").html(rmkschk)
								).appendTo("#unittbls tbody");
							});

						}
					});


					$('#UOMModal').modal('show');
				});
				

				$("input.numeric").numeric({decimalPlaces: 4, negative: false});
				$("input.numeric").on("click", function () {
					$(this).select();
				});
								
				$("#itmerradd").attr("class","");
				$("#itmerradd").html("");
				$("#itmerradd").hide();

}
else{
	$("#AlertMsg").html("NO more available UOM to add!");
	$('#alertbtnOK').show();
	$("#AlertModal").modal('show');

}
				$('#txtcitmdesc').val("").change(); 
				$('#txtcitmno').val(""); 
				$("#hdncunit").val("");
				
				$('#'+xid).focus();

	
}

function setuom(x, y){
	$("#txtcprtunit"+x).val(y);
	$("#uomaddon"+x).attr("data-unt", y);
	$('#UOMModal').modal('hide');
	
}

function chkform(){
var rowCount = $('#myTable tr').length
var trancode = "";
var issaved = "";
if($('#txtcustid').val()=="" || $('#date_delivery').val()==""){
	
	$("#AlertMsg").html("<b>ERROR: </b> Supplier Code and Effectivity Date is mandatory!");
	$('#alertbtnOK').show();
	$('#AlertModal').modal('show');

}else{
	if(rowCount==1){
		$("#AlertMsg").html("Transaction cannot be saved without details.");
		$('#alertbtnOK').show();
		$("#AlertModal").modal('show');
	}else{
		
	//check for numeric textboxes without value
	var valuenull = "";
	$("input.numeric").each(function() {
		if($(this).val()==""){
			valuenull = "False";
		}
	});
	
		if(valuenull == "False"){
			$("#AlertMsg").html("Blank price is not allowed");
			$('#alertbtnOK').show();
			$("#AlertModal").modal('show');
		}
		else{
				var txtdeffect = $("#date_delivery").val();
				var txtcdesc = $("#txtcdescription").val();
				var txtccode = $("#txtcustid").val();
				
						$.ajax ({
							url: "th_savepp.php",
							data: { deffect: txtdeffect, desc: txtcdesc, ccode: txtccode },
							async: false,
							beforeSend: function(){
								$("#AlertMsg").html("<b>SAVING PRICE MATRIX: </b> Please wait a moment...");
								$('#alertbtnOK').hide();
								$("#AlertModal").modal('show');
							},
							success: function( data ) {
								if(data.trim()!="False"){
									trancode = data.trim();
						
									//INSERTING DETAILS
									var nident = 0;
									
									$("#myTable > tbody > tr").each(function() {	
									
										nident = nident + 1;
										var txtcitm = $(this).find("input[type='hidden'][name='txtcprtno']").val();
										var txtcuom = $(this).find("input[name='txtcprtunit']").val();
										var valz = $(this).find("input[name='txtnprice']").val();
										
											//alert("code="+ txtcitm + "&uom=" + txtcuom + "&val="+ valz + "&tran="+ trancode + "&ident="+ nident);
										
												$.ajax ({
													url: "th_saveppt.php",
													data: { code: txtcitm, uom: txtcuom, val: valz, tran: trancode, ident: nident },
													async: false,
													success: function( data ) {
														if(data.trim()!="True"){
															issaved = data.trim()+"\n";
														}
													}
												});
										 
									});
	
	
								}
							}
						});
	
	
		if(issaved==""){
			$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved! <br><br> Loading pm list... <br> Please wait!");
			$('#alertbtnOK').hide();
												
			setTimeout(function() {
				$("#AlertMsg").html("");
				$('#alertbtnOK').hide();
				$('#AlertModal').modal('hide');
	
					$("#txtctranno").val(trancode);
					$("#frmedit").submit();
	
			}, 3000); // milliseconds = 3seconds
			
	
		}else{
			$("#AlertMsg").html(issaved);
		}
		
		}
				
	 }
}
}
	   
</script>