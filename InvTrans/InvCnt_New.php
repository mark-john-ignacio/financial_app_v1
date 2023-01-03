<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvTrans_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];

$sqlloc = mysqli_query($con,"select A.* from receive_putaway_location A where A.compcode='$company'");
$rowdetloc = $sqlloc->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
    
<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>
<script src="../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding-left:20px; padding-right:20px; padding-top:10px">
<fieldset>
    	<legend>New Inventory Transfer</legend>

<div class="col-xs-12 nopadding">
	<div class="col-xs-2 nopadding">
		<b>Source Warehouse: </b>
	</div>
	<div class="col-xs-3 nopadding">
		
		<select class="form-control input-sm" name="selwhfrom" id="selwhfrom">
			<option value="">-- Select Source Warehouse --</option>
		<?php
			foreach($rowdetloc as $localocs){
		?>
			<option value="<?php echo $localocs['nid'];?>"><?php echo $localocs['cdesc'];?></option>								
		
		<?php	
			}	
			
		?>
		</select>
	</div>
	
	<div class="col-xs-1 nopadding">
			&nbsp;
	</div>
    
	<div class="col-xs-2 nopadding">
		<b>Transaction Date: </b>
	</div>
	
	<div class="col-xs-2 nopadding">
		<input type="text" class="datepick form-control input-sm" name="txtdtrandate" id="txtdtrandate">
	</div>

</div>
	
<div class="col-xs-12 nopadwtop">
	<div class="col-xs-2 nopadding">
		<b>Destination Warehouse: </b>
	</div>
	<div class="col-xs-3 nopadding">
		<select class="form-control input-sm" name="selwhto" id="selwhto">
			<option value="">-- Select Destination Warehouse --</option>
		<?php
			foreach($rowdetloc as $localocs){
		?>
			<option value="<?php echo $localocs['nid'];?>"><?php echo $localocs['cdesc'];?></option>								
		
		<?php	
			}	
			
		?>
		</select>
	</div>
    

</div>

<div class="col-xs-12 nopadwtop">
	<div class="col-xs-2 nopadding">
		<b>Remarks: </b>
	</div>
	<div class="col-xs-10 nopadding">
		<input type="text" class="form-control input-sm" name="txtccrems" id="txtccrems" value="" placeholder="Enter Remarks...">
	</div>
    

</div>
	
</fieldset>	

<div class="col-xs-12 nopadwtop2x" id="divStatMsg">
	
</div>

<div class="col-xs-12 nopadwtop2x">
	
    <input type="text" class="form-control input-lg" name="txtscan" id="txtscan" value="" placeholder="Search Item Name...">

</div>


<form id="frmCount" name="frmCount" method="post" action="">
	<!--<input type="hidden" name="hdnmonth" id="hdnmonth" value="<?php// echo $_REQUEST["month"];?>">
    <input type="hidden" name="hdnyear" id="hdnyear" value="<?php// echo $_REQUEST["year"];?>">-->
 
                       
                  <table name='MyTbl' id='MyTbl' class="table table-scroll table-striped table-condensed">
                   <thead>
                    <tr>
                      <th width="50">&nbsp;</th>
                      <th width="150">Item Code</th>
                      <th>Item Description</th>
                      <th width="70">Unit</th>
                      <th width="100">Qty</th>
						<th width="100">Unit Cost</th>
                      <th width="200">Barcode</th>
                      <th width="200">Serial</th>
                      <th width="180">Loc</th>
                      <th width="100">Exprd</th>
                      <th width="50">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
				  </table>


<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
    <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Inv.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>

    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="window.location.href='Inv.php';" id="btnMain" name="btnMain">
Upload<br>(XLS)</button>

    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (CTRL+S)</button></td>

    </tr>
</table>

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

<div class="modal fade" id="SerialMod" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="InvSerDetHdr">Inventory Detail</h4>
								<input type="hidden" class="form-control input-sm" name="serdisitmcode" id="serdisitmcode"> 
								<input type="hidden" class="form-control input-sm" name="serdisrefident" id="serdisrefident">
            </div>
            
            <div class="modal-body" style="height:20vh">
							
								<div class="row">
										<div class="col-xs-2 nopadwtop"><b>&nbsp;&nbsp;&nbsp;Required Qty:</b></div>
										<div class="col-xs-1 nopadwtop" id="htmlserqtyneed"><input type="hidden" name="hdnserqtyneed" id="hdnserqtyneed"></div>
										<div class="col-xs-1 nopadwtop" id="htmlserqtyuom"><input type="hidden" name="hdnserqtyuom" id="hdnserqtyuom"></div>
								</div>
								
								<div class="row nopadwtop2x"><div class="col-xs-12">
										<table id="MyTableSerials" cellpadding="3px" width="100%" border="0">
		    							<thead>
		                        <tr>
		                            <th style="border-bottom:1px solid #999">Serial No.</th>	                            
		                            <th style="border-bottom:1px solid #999">Location</th>
		                            <th style="border-bottom:1px solid #999">Exp. Date</th>
		                            <th style="border-bottom:1px solid #999">Qty</th>
									<th style="border-bottom:1px solid #999">UOM</th>	
									<th style="border-bottom:1px solid #999">Qty Picked</th>
									
		                        </tr>
		                   </thead>
                   		 <tbody>
                   		 </tbody>
                        
                </table>
								</div></div>

						</div>

						<div class="modal-footer">
								<button class="btn btn-success btn-sm" name="btnInsSer" id="btnInsSer">Insert (Enter)</button>
								<button class="btn btn-danger btn-sm" name="btnClsSer" id="btnClsSer" data-dismiss="modal" >Close (Ctrl+X)</button>
						</div>
				</div>
		</div>
</div>
	
	
<form name="frmEdit" id="frmEdit" method="post" action="InvCnt_Edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		

</body>

</html>

<script type="text/javascript">

$("#txtscan").focus();

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){
			e.preventDefault();
			return chkform();
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			window.location.href='Inv.php';
		}
	  }

	});


$(function(){	
		$(".datepick").datetimepicker({
        	format: 'MM/DD/YYYY',
			useCurrent: false,
			minDate: moment(),
			defaultDate: moment(),
		});
	
	$('#txtscan').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "th_product_whse.php",
				dataType: "json",
				data: { query: $("#txtscan").val(), styp: "Goods", cwhse: $("#selwhfrom").val() },
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 900px"><span >'+item.desc+'</span</div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 	
		
			$('#MyTableSerials tbody').empty();

			$.ajax({
					url : "th_serialslist-manual.php",
					data: { itm: item.id, cuom: item.cunit, cwhse: $("#selwhfrom").val() },
					type: "POST",
					async: false,
					dataType: "JSON",
					success: function(data)
					{	
					   console.log(data);

             					$.each(data,function(index,item){

								$("<tr>").append(
									$("<td>").html("<input type='hidden' value='"+itmcode+"' name=\"lagyitmcode\" id=\"lagyitmcode\"><input type='hidden' value='"+item.cserial+"' name=\"lagyserial\" id=\"lagyserial\"><input type='hidden' value='"+item.nrefidentity+"' name=\"lagyrefident\" id=\"lagyrefident\"><input type='hidden' value='"+item.ctranno+"' name=\"lagyrefno\" id=\"lagyrefno\">"+item.cserial), 
									$("<td width=\"150x\" style=\"padding:1px\">").html("<input type='hidden' value='"+item.nlocation+"' name=\"lagylocas\" id=\"lagylocas\"><input type='hidden' value='"+item.locadesc+"' name=\"lagylocadesc\" id=\"lagylocadesc\">"+item.locadesc),
									$("<td width=\"100px\" style=\"padding:1px\">").html("<input type='hidden' value='"+item.dexpired+"' name=\"lagyexpd\" id=\"lagyexpd\">"+item.dexpired),
									$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+item.nqty+"' name=\"lagynqty\" id=\"lagynqty\">"+item.nqty),
									$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+item.cunit+"' name=\"lagycuom\" id=\"lagycuom\">"+item.cunit),
									$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='text' class='numeric form-control input-sm text-right' value='0' name=\"lagyqtyput\" id=\"lagyqtyput\">")
								).appendTo("#MyTableSerials tbody");

									$("input.numeric").numeric();
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
									$("input.numeric").on("keyup", function() {
									   if(parseFloat($(this).val()) > parseFloat(itemqty)){
												alert("Quantity must be less than available qty.");
												$(this).val(item.nqty);
										 }
									});
											   
					   	});
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});
		//MyTableSerials

		$("#SerialMod").modal("show");
					
		}
	
	});
	
	$("#selwhfrom").on("change", function() {
		var vardvalxs = $(this).val();
		$('#selwhto').find('option').not(':first').remove();
				$.ajax({
					url : "th_locations.php",
					async: false,
					dataType: "JSON",
					success: function(data)
					{	
					   	console.log(data);

						 $.each(data,function(index,item){
							 if(item.nid!=vardvalxs){
							 	$('#selwhto').append('<option value="'+item.nid+'">'+item.cdesc+'</option>');
							 }
						 });
			 
					}
				});
		
	});
});
function updateStat(){

			var cntr = 0;
			var nqty = 0;
				
			$("#MyTbl > tbody > tr").each(function(index) {
				
				varxqty = $(this).find('input[name="txtnqty"]').val(); 
				
				cntr = cntr + 1;
				
				$(this).find('div[id="rowItmNum"]').text(cntr);				
				
				
				nqty = parseFloat(nqty) + parseFloat(varxqty);
											
			});

			$("#divLblNo").html("<b>"+cntr+"</b>");
			$("#divLblQTy").html("<b>"+nqty+"</b>");

}

function chkform(){
		var ISOK = "YES";
		var trancode = "";
		var isDone = "True";
		var VARHDRSTAT = "";
		var VARHDRERR = "";
		
		var tbl1 = document.getElementById('MyTbl').getElementsByTagName('tr');
		var lastRow1 = tbl1.length-1;
				
		if(lastRow1!=0){
		
			var cmonth = $("#hdnmonth").val();
			var cyear = $("#hdnyear").val();
	
		//Saving Header
					$.ajax ({
					url: "InvCnt_SaveHdr.php",
					data: { mo: cmonth, yr: cyear },
					async: false,
					beforeSend: function(){
						$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW COUNT: </b> Please wait a moment...");
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

		// Saving Details
		if(trancode!=""){
			$("#MyTbl > tbody > tr").each(function(index) {

				var ucost = $(this).find('input[name="txtucost"]').val(); 
				var nqty = $(this).find('input[name="txtnqty"]').val(); 
				//var cscan = $(this).find('input[type="hidden"][name="txtcscancode"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtcpartno"]').val();
				var cunit = $(this).find('input[type="hidden"][name="txtcunit"]').val();    
				var bcode = $(this).find('input[type="text"][name="txtbcode"]').val();
				var cserial = $(this).find('input[type="text"][name="txtserial"]').val();
				var selloc = $(this).find('select[name="sellocs"]').val();
				var dexpdte = $(this).find('input[type="text"][name="txtexpdte"]').val();

				alert("InvCnt_SaveDet.php?trancode="+trancode+"&indx="+index+"&citmno="+citmno+"&cunit="+cunit+"&nqty="+nqty+"&ucost="+ucost+"&bcode="+bcode+"& cserial="+cserial+"&selloc="+selloc+"&dexpdte="+dexpdte);
				
				$.ajax ({
					url: "InvCnt_SaveDet.php",
					data: { trancode: trancode, indx: index, citmno: citmno, cunit: cunit, nqty:nqty, ucost:ucost, bcode:bcode, cserial:cserial, selloc:selloc, dexpdte:dexpdte },
					async: false,
					success: function( data ) {
						if(data.trim()!="False"){
							//$("#AlertMsg").html("<b>"+data.trim()+"</b>");
							//$("#alertbtnOK").hide();
						}else{
							isDone = "False";
						}
					}
				});

				
			});
			
			
			if(isDone=="True"){
				$("#AlertMsg").html("<b>SUCCESFULLY SAVED: </b> Please wait a moment...");
				$("#alertbtnOK").hide();

					setTimeout(function() {
						$("#AlertMsg").html("");
						$('#AlertModal').modal('hide');
			
							$("#txtctranno").val(trancode);
							$("#frmEdit").submit();
			
					}, 3000); // milliseconds = 3seconds

				
			}

		}
		else{
			$("#AlertMsg").html("Something went wrong<br>Status: "+VARHDRSTAT +"<br>Error: "+VARHDRERR);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		}
	
							
		}
		else{
			alert("Cannot be saved without details!");
		}

}

</script>
