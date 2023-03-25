<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "PurchRet_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
	<form action="PurchRet_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
		<fieldset>
    	<legend>Purchase Return</legend>	

        <table width="100%" border="0">
					<tr>
						<tH width="100">Supplier:</tH>
						<td style="padding:2px">
											<div class="col-xs-12 nopadding">
												<div class="col-xs-3 nopadding">
													<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Supplier Code..." tabindex="1" value="" readonly>
												</div>

												<div class="col-xs-8 nopadwleft">
													<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..."  size="60" autocomplete="off" value="">
												</div> 
											</div>
						</td>
						<tH width="150" style="padding:2px">Date Returned:</tH>
						<td style="padding:2px">
							<div class="col-xs-8 nopadding">
								<input type='text' class="datepick form-control input-sm" id="date_returned" name="date_returned" />
							</div>
						</td>
					</tr>
					<tr>
						<tH width="100">Remarks:</tH>
						<td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div></td>
						<tH width="150">&nbsp;</tH>
						<td style="padding:2px;">
						&nbsp;
						</td>
					</tr>

					<tr>
							<td colspan="2">
								<input type="hidden" id="txtprodid" name="txtprodid">
								<input type="hidden" id="txtprodnme" name="txtprodnme">
									<input type="hidden" name="hdnunit" id="hdnunit">
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>

					</tr>
				</table>

				<ul class="nav nav-tabs">
					<li class="active" id="lidet"><a href="#1Det" data-toggle="tab">Items List</a></li>
					<li id="liacct"><a href="#2Acct" data-toggle="tab">Items Inventory</a></li>
				</ul>

				<div class="tab-content nopadwtop2x">
					<div class="tab-pane active" id="1Det">

							<div class="alt2" dir="ltr" style="
								margin: 0px;
								padding: 3px;
								border: 1px solid #919b9c;
								width: 100%;
								height: 300px;
								text-align: left;
								overflow: auto">
				
									<table id="MyTable" class="MyTable" width="100%">
										<thead>	
											<tr>
												<th style="border-bottom:1px solid #999">&nbsp;</th>
												<th style="border-bottom:1px solid #999">Code</th>
												<th style="border-bottom:1px solid #999">Description</th>
												<th style="border-bottom:1px solid #999">UOM</th>
												<th style="border-bottom:1px solid #999">Qty</th>
												<th style="border-bottom:1px solid #999">Remarks</th>
												<th style="border-bottom:1px solid #999">&nbsp;</th>
											</tr>
										</thead>
										<tbody class="tbody">
										</tbody>			                    
									</table>

								</div>
						</div>

						<div class="tab-pane" id="2Acct">

									<div class="alt2" dir="ltr" style="
															margin: 0px;
															padding: 3px;
															border: 1px solid #919b9c;
															width: 100%;
															height: 250px;
															text-align: left;
															overflow: auto">
							
											<table id="MyTable2" cellpadding="3px" width="100%" border="0">
												<thead>
															<tr>
																
																	<th style="border-bottom:1px solid #999">Item Code</th>
																	<th style="border-bottom:1px solid #999">Serial No.</th>
																	<th style="border-bottom:1px solid #999">UOM</th>
																	<th style="border-bottom:1px solid #999">Qty</th>
																	<th style="border-bottom:1px solid #999">Location</th>
																	<th style="border-bottom:1px solid #999">Expiration Date</th>
																	<th style="border-bottom:1px solid #999">&nbsp;</th>
															</tr>
												</thead>
												<tbody>
												</tbody>
															
											</table>
												<input type="hidden" name="hdnserialscnt" id="hdnserialscnt">
										</div>
						</div>
				</div>

				<br>
				<table width="100%" border="0" cellpadding="3">
					<tr>
						<td>
							<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 

							<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='PurchRet.php';" id="btnMain" name="btnMain">
								Back to Main<br>(ESC)
							</button>

							<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
								RR<br>(Insert)
							</button>
							
							<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>

    </fieldset>

</form>

<!-- FULL PO LIST REFERENCES-->
<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="InvListHdr">Receiving List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
       <div class="col-xs-12 nopadding">

                <div class="form-group">
                    <div class="col-xs-4 nopadding pre-scrollable" style="height:37vh">
                          <table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
                           <thead>
                            <tr>
                              <th>RR No</th>
                              <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                          </table>
                    </div>

                    <div class="col-xs-8 nopadwleft pre-scrollable" style="height:37vh">
                          <table name='MyInvDetList' id='MyInvDetList' class="table table-small">
                           <thead>
                            <tr>
                              <th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
															<th>Item No</th>
                              <th>Description</th>
                              <th>UOM</th>
                              <th>Qty</th>
                            </tr>
                            </thead>
                            <tbody>
                            	
                            </tbody>
                          </table>
                    </div>
               </div>

        </div>
         	            
			</div>
			
            <div class="modal-footer">
                <button type="button" id="btnInsDet" onClick="InsertSI()" class="btn btn-primary">Insert</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End FULL INVOICE LIST -->

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



<form method="post" name="frmedit" id="frmedit" action="PurchRet_edit.php">
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
		$('#txtprodnme').focus();
      }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("RR.php");

	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			openinv();
			}
	  }
		else if(e.keyCode == 88 && e.ctrlKey){ //CTRL X - Close Modal
			if($('#SerialMod').hasClass('in')==true){
		 		$("#btnClsSer").click();
			}
	  } 
	});

	$(document).keypress(function(e) {
	  if ($("#SerialMod").hasClass('in') && (e.keycode == 13 || e.which == 13)) {
	    $("#btnInsSer").click();
	  }
	});

$(document).ready(function() {
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY',
		useCurrent: false,
		minDate: moment(),
		defaultDate: moment(),
    });
	
});
	
$(function(){	
	$('#txtcust').typeahead({
	
		items: 10,
		source: function(request, response) {
			$.ajax({
				url: "../th_supplier.php",
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

	$("#btnInsSer").on("click", function(){
	
			var tbl = document.getElementById('MyTableSerials').getElementsByTagName('tr');
			var lastRow = tbl.length;
	
			if(lastRow>1){
					$("#MyTableSerials > tbody > tr").each(function(index) {
						var zxitmcode = $(this).find('input[type="hidden"][name="lagyitmcode"]').val();
						var zxserial = $(this).find('input[type="hidden"][name="lagyserial"]').val();
						var zxuom = $(this).find('input[type="hidden"][name="lagycuom"]').val();	
						var zxqty = $(this).find('input[name="lagyqtyput"]').val();		
						var zxloca = $(this).find('input[type="hidden"][name="lagylocas"]').val();	
						var zxlocadesc = $(this).find('input[type="hidden"][name="lagylocadesc"]').val();
						var zxexpd = $(this).find('input[type="hidden"][name="lagyexpd"]').val();
						var zxnident = $(this).find('input[type="hidden"][name="lagyrefident"]').val();
						var zxreference = $(this).find('input[type="hidden"][name="lagyrefno"]').val();
						var zxmainident = $("#serdisrefident").val();

						if(parseFloat(zxqty) > 0){
							InsertToSerials(zxitmcode,zxserial,zxuom,zxqty,zxloca,zxlocadesc,zxexpd,zxnident,zxreference,zxmainident);			
						}

					});
			}
		
			//close modal
			$("#SerialMod").modal("hide");
	});

});

function addItemName(){
	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
		/*
		var rowCount = $('#MyTable tr').length;
		var isItem = "NO";
		var itemindex = 1;
		
		if(rowCount > 1){
			 var cntr = rowCount-1;
			 
			 for (var counter = 1; counter <= cntr; counter++) {
				// alert(counter);
				if($("#txtprodid").val()==$("#txtitemcode"+counter).val()){
					isItem = "YES";
					itemindex = counter;
				}
			 }
		 }
		 */
		//if(isItem=="NO"){	

				myFunctionadd("","","","","","","");		
				ComputeGross();	
		//}
		//else{
			
			//addqty();	
				
		//}
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		
	 }

}

function myFunctionadd(nqty,nprice,namount,nfactor,cmainunit,xref,nident){

	var itmcode = document.getElementById("txtprodid").value;
	var itmdesc = document.getElementById("txtprodnme").value;
	var itmunit = document.getElementById("hdnunit").value;
	
	if(nqty=="" && nprice=="" && namount=="" && nfactor=="" && cmainunit=="" && xref=="" && nident==""){	
		var itmprice = chkprice(itmcode,itmunit);
		var itmamnt = itmprice;
		var itmqty = 1;
		var itmqtyorig = 0;
		var itmfactor = 1;
		var itmmainunit = itmunit;
		var itmxref = "";
		var itmident = "";

	}
	else{
		var itmprice = nprice;
		var itmamnt = namount;
		var itmqty = nqty;
		var itmqtyorig = nqty;
		var itmfactor = nfactor;
		var itmmainunit = cmainunit;
		var itmxref = xref;
		var itmident = nident;
	}


	var uomoptions = "";
	
	if(xref == ""){							
		 $.ajax ({
			url: "../th_loaduomperitm.php",
			data: { id: itmcode },
			async: false,
			dataType: "json",
			success: function( data ) {
											
				console.log(data);
				$.each(data,function(index,item){
					if(item.id==itmunit){
						isselctd = "selected";
					}
					else{
						isselctd = "";
					}
					
					uomoptions = uomoptions + '<option value='+item.id+' '+isselctd+'>'+item.name+'</option>';
				});
						
			}
		});
		
		uomoptions = "<select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">"+uomoptions+"</select>";
		
	}else{
		uomoptions = "<input type='hidden' value='"+itmunit+"' name=\"seluom\" id=\"seluom\">"+itmunit;
	}
		
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;


		$('#MyTable > tbody:last-child').append(
			"<tr>"
			+"<td width='50px'><input class='btn btn-info btn-xs' type='button' id='ins" + itmcode + "' value='insert' /></td>"
			+"<td width='120px'><input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+"<input type='hidden' value='"+itmxref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+itmident+"' name=\"txtnrefident\" id=\"txtnrefident\"></td>"
			+"<td width='120px' nowrap style='padding:1px;overflow: hidden;text-overflow: ellipsis;'>"+itmdesc+"</td>"
			+"<td width='80px' style='padding:1px'>"+uomoptions+"</td>"
			+"<td width='100px' style='padding:1px'><input type='text' value='"+itmqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' /> <input type='hidden' value='"+itmqtyorig+"' name=\"txtnqtyORIG\" id=\"txtnqtyORIG"+lastRow+"\"> <input type='hidden' value='"+itmprice+"' name=\"txtnprice\" id='txtnprice"+lastRow+"'> <input type='hidden' value='"+itmmainunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmfactor+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> <input type='hidden' value='"+itmamnt+"' name='txtnamount' id='txtnamount"+lastRow+"'></td>"
			+"<td width='100px' style='padding:1px'><input type='text' class='form-control input-xs' id='dremarks"+lastRow+"' name='dremarks' placeholder='Enter remarks...' /></td>"
			+"<td width='80px' style='padding:1px'><input class='btn btn-danger btn-xs' type='button' id='del" + itmcode + "' value='delete' /></td>"
		);								

									$("#del"+itmcode).on('click', function() {
										$(this).closest('tr').remove();
									});

									$("#ins"+itmcode).on('click', function() {
										 var xcsd = $(this).closest("tr").find("input[name=txtnqty]").val();
										 InsertDetSerial(itmcode, itmdesc, itmunit, itmident, xcsd, itmfactor, itmmainunit, itmxref)
									});

									$("input.numeric").numeric();
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function () {
									   ComputeAmt($(this).attr('id'));
									   ComputeGross();
									});
									
									$(".xseluom").on('change', function() {

										var xyz = chkprice(itmcode,$(this).val());
										
										$('#txtnprice'+lastRow).val(xyz.trim());
										
										ComputeAmt($(this).attr('id'));
										ComputeGross();
										
										var fact = setfactor($(this).val(), itmcode);
										
										$('#hdnfactor'+lastRow).val(fact.trim());
										
									});

}

function InsertDetSerial(itmcode, itmname, itmunit, itemrrident, itemqty, itmfctr, itemcunit, itmxref){
	$("#InvSerDetHdr").text("Inventory Details ("+itmname+")");
	$("#hdnserqtyneed").val(itemqty); 
	$("#htmlserqtyneed").text(itemqty); 
	$("#hdnserqtyuom").val(itemcunit); 
	$("#htmlserqtyuom").text(itemcunit);
	//alert("th_serialslist-manual.php?itm="+itmcode+"&cuom="+itmunit+"&qty="+itemqty+"&factr="+itmfctr+"&mainuom="+itemcunit);

	$('#MyTableSerials tbody').empty();

			$.ajax({
					url : "th_serialslist-manual.php",
					data: { itm: itmcode, cuom: itmunit, qty: itemqty, factr: itmfctr, mainuom: itemcunit, itmxref: itmxref },
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

function InsertToSerials(itmcode,serials,uoms,qtys,locas,locasdesc,expz,nident,refe,mainident){ 

	$("<tr>").append(
		$("<td width=\"120px\" style=\"padding:1px\">").html("<input type='hidden' value='"+itmcode+"' name=\"sertabitmcode\" id=\"sertabitmcode\"><input type='hidden' value='"+mainident+"' name=\"sertabident\" id=\"sertabident\"><input type='hidden' value='"+nident+"' name=\"sertabreferid\" id=\"sertabreferid\"><input type='hidden' value='"+refe+"' name=\"sertabrefer\" id=\"sertabrefer\">"+itmcode),
		$("<td>").html("<input type='hidden' value='"+serials+"' name=\"sertabserial\" id=\"sertabserial\">"+serials), 
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+uoms+"' name=\"sertabuom\" id=\"sertabuom\">"+uoms),
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input type='hidden' value='"+qtys+"' name=\"sertabqty\" id=\"sertabqty\">"+qtys),
		$("<td width=\"150x\" style=\"padding:1px\">").html("<input type='hidden' value='"+locas+"' name=\"sertablocas\" id=\"sertablocas\">"+locasdesc),
		$("<td width=\"100px\" style=\"padding:1px\">").html("<input type='hidden' value='"+expz+"' name=\"sertabesp\" id=\"sertabesp\">"+expz),
		$("<td width=\"80px\" style=\"padding:1px\">").html("<input class='btn btn-danger btn-xs' type='button' id='delsrx" + itmcode + "' value='delete' />")
	).appendTo("#MyTable2 tbody");

									$("#delsrx"+itmcode).on('click', function() {
										$(this).closest('tr').remove();
									});		
}

		function ComputeAmt(nme){
			
			var disnme = nme.replace(/[0-9]/g, ''); // string only
			var r = nme.replace( /^\D+/g, ''); // numeric only
			var nnet = 0;
			var nqty = 0;
			//alert(disnme + ":" + $("#hdnCHECKREFval").val());
				if(disnme=="txtnqty"){ // If qty textbox check muna ung qty vs orig pag 1 or 2 ung CHEKREFval
					
						nqty = $("#txtnqty"+r).val();
						nqty = parseFloat(nqty);

						nqtyorig = $("#txtnqtyORIG"+r).val();
						nqtyorig = parseFloat(nqtyorig);
						
						if(nqty > nqtyorig){
							
							$("#AlertMsg").html("");
							
							$("#AlertMsg").html("<b>ERROR: </b>Bigger qty is not allowed!<br><b>Original Qty: </b>" + nqtyorig);
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');
							
							$("#txtnqty"+r).val(nqtyorig);
						}
						
				}
			
			nqty = $("#txtnqty"+r).val();
			nqty = parseFloat(nqty);
			nprc = $("#txtnprice"+r).val();
			nprc = parseFloat(nprc);
			
			namt = nqty * nprc;
			namt = namt.toFixed(4);
						
			$("#txtnamount"+r).val(namt);

		}

		function ComputeGross(){
			var rowCount = $('#MyTable tr').length;
			
			var gross = 0;
			var amt = 0;
			
			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
					amt = $("#txtnamount"+i).val();
					gross = gross + parseFloat(amt);
				}
			}

			$("#txtnGross").val(gross.toFixed(4));
			
		}

function setfactor(itmunit, itmcode){
	var result;
			
	$.ajax ({
		url: "../th_checkitmfactor.php",
		data: { itm: itmcode, cunit: itmunit },
		async: false,
		success: function( data ) {
			 result = data;
		}
	});
			
	return result;
	
}

	function openinv(){
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid Supplier!");
		}
		else{
			
			$("#txtcustid").attr("readonly", true);
			$("#txtcust").attr("readonly", true);

			//clear table body if may laman
			$('#MyInvTbl tbody').empty(); 
			$('#MyInvDetList tbody').empty();
			
			//get salesno na selected na
			var y;
			var salesnos = "";

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#InvListHdr').html("RR List: " + $('#txtcust').val());

			var xstat = "YES";
			
			//disable escape insert and save button muna
			
			$.ajax({
        url: 'th_qolist.php',
				data: 'x='+x,
        dataType: 'json',
        method: 'post',
        success: function (data) {

					$("#allbox").prop('checked', false);					   
          console.log(data);
          $.each(data,function(index,item){
								
						if(item.ctranno=="NONE"){
							$("#AlertMsg").html("No Receiving List Available");
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');

							xstat = "NO";
							
							$("#txtcustid").attr("readonly", false);
							$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
								$("<td id='td"+item.ctranno+"'>").text(item.ctranno),
								$("<td>").text(item.dcutdate)
							).appendTo("#MyInvTbl tbody");
													
							$("#td"+item.ctranno).on("click", function(){
								opengetdet($(this).text());
							});
							
							$("#td"+item.ctranno).on("mouseover", function(){
								$(this).css('cursor','pointer');
							});
					  }

          });
					   

					if(xstat=="YES"){
						$('#mySIRef').modal('show');
					}
        },
       	error: function (req, status, err) {
					console.log('Something went wrong', status, err);
					$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');
				}
    	});
						
		}
	}

function opengetdet(valz){
	var drno = valz;

	$("#txtrefSI").val(drno);

	$('#InvListHdr').html("RR List: " + $('#txtcust').val() + " | RR Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
	
	$('#MyInvDetList tbody').empty();
	$('#MyDRDetList tbody').empty();
		
	$('#loadimg').show();
	
			var salesnos = "";
			var cnt = 0;
			
			$("#MyTable > tbody > tr").each(function() {
				myxref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				
				if(myxref == drno){
					cnt = cnt + 1;
					
				  if(cnt>1){
					  salesnos = salesnos + ",";
				  }
							  
					salesnos = salesnos +  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				}
				
			});

					//alert('th_sinumdet.php?x='+drno+"&y="+salesnos);
					$.ajax({
                    url: 'th_qolistdet.php',
										data: 'x='+drno+"&y="+salesnos,
                    dataType: 'json',
										async: false,
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
					  $("#allbox").prop('checked', false); 
					   
                      console.log(data);
					  $.each(data,function(index,item){
						  if(item.citemno==""){
							  alert("NO more items to add!")
						  }
						  else{
							if (item.nqty>=1){
								$("<tr>").append(
								$("<td>").html("<input type='checkbox' value='"+item.nident+"' name='chkSales[]' data-id=\""+drno+"\">"),
								$("<td>").text(item.citemno),
								$("<td>").text(item.cdesc),
								$("<td>").text(item.cunit),
								$("<td>").text(item.nqty)
								).appendTo("#MyInvDetList tbody");
							}
					 	 }
					 });
                    },
					complete: function(){
						$('#loadimg').hide();
					},
                    error: function (req, status, err) {
						//alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
 						$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');
                   }
                });

}

function InsertSI(){	
	
   $("input[name='chkSales[]']:checked").each( function () {
	   
	
				var tranno = $(this).data("id");
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_qolistput.php?id=" + tranno + "&itm=" + id,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{	
					   console.log(data);
                       $.each(data,function(index,item){
						
							$('#txtprodnme').val(item.cdesc); 
							$('#txtprodid').val(item.id); 
							$("#hdnunit").val(item.cunit); 

							//alert(item.cqtyunit + ":" + item.cunit);
							myFunctionadd(item.nqty,item.nprice,item.namount,item.nfactor,item.cmainuom,item.xref,item.nident)
											   
					   });
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

   });
   //alert($("#hdnQuoteNo").val());
   
   $('#mySIModal').modal('hide');
   $('#mySIRef').modal('hide');

}



function chkform(){
	var ISOK = "YES";
	
	if(document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value==""){

			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Supplier Required!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		document.getElementById("txtcust").focus();
		return false;

		
		ISOK = "NO";
	}
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	
	if(lastRow == 0){
			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;NO details found!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		return false;
		ISOK = "NO";
	}
	else{
		var msgz = "";
		var myqty = "";
		var myav = "";
		var myfacx = "";
		var myprice = "";

		$("#MyTable > tbody > tr").each(function(index) {
			
			myqty = $(this).find('input[name="txtnqty"]').val();
			//myprice = $(this).find('input[name="txtnprice"]').val();
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
			}
			
			//if(myprice == 0 || myprice == ""){
			//	msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
		//	}

		});
		
		if(msgz!=""){
			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Details Error: "+msgz);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
			ISOK = "NO";
		}
	}
	
	if(ISOK == "YES"){
	var trancode = "";
	var isDone = "True";


		//Saving the header
		var ccode = $("#txtcustid").val();
		var crem = $("#txtremarks").val();
		var ddate = $("#date_returned").val();
		var ngross = 0;

		$.ajax ({
			url: "PurchRet_newsave.php",
			data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross },
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW PO: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				if(data.trim()!="False"){
					trancode = data.trim();
				}
			}
		});
		

		if(trancode!=""){
			//Save Details
			$("#MyTable > tbody > tr").each(function(index) {	
			
				var xcref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				var crefidnt = $(this).find('input[type="hidden"][name="txtnrefident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var cuom = $(this).find('select[name="seluom"]').val();
						if(cuom=="" || cuom==null){
							var cuom = $(this).find('input[type="hidden"][name="seluom"]').val();
						}
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nqtyOrig = $(this).find('input[type="hidden"][name="txtnqtyORIG"]').val();
				var nprice = $(this).find('input[type="hidden"][name="txtnprice"]').val();
				var namt = $(this).find('input[type="hidden"][name="txtnamount"]').val();
				var drems = $(this).find('input[name="dremarks"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
			
				//alert("trancode="+trancode+"&drems="+drems+"&indx="+index+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+nprice+"&namt="+namt+"&mainunit="+mainunit+"&nfactor="+nfactor+"&nqtyorig="+nqtyOrig+"&xcref="+xcref+"&crefidnt="+crefidnt);
				
				$.ajax ({
					url: "PurchRet_newsavedet.php",
					data: { trancode: trancode, drems: drems, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, mainunit:mainunit, nfactor:nfactor, nqtyorig:nqtyOrig, xcref:xcref, crefidnt:crefidnt },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});

			$("#MyTable2 > tbody > tr").each(function(index) {	


				var xcref = $(this).find('input[type="hidden"][name="sertabrefer"]').val(); 
				var crefidnt = $(this).find('input[type="hidden"][name="sertabident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="sertabitmcode"]').val();
				var cuom = $(this).find('input[type="hidden"][name="sertabuom"]').val();
				var nqty = $(this).find('input[type="hidden"][name="sertabqty"]').val();
				var dneed = $(this).find('input[type="hidden"][name="sertabesp"]').val();
				var clocas = $(this).find('input[type="hidden"][name="sertablocas"]').val();
				var seiraln = $(this).find('input[type="hidden"][name="sertabserial"]').val();

				$.ajax ({
					url: "PurchRet_newsavedetserials.php",
					data: { trancode: trancode, dneed: dneed, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, clocas:clocas, xcref:xcref, crefidnt:crefidnt, seiraln:seiraln },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
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
							$("#frmedit").submit();
			
					}, 3000); // milliseconds = 3seconds

				
			}

		}
		else{
				$("#AlertMsg").html("<b>ERROR: </b> There's a problem saving your transaction...<br><br>" + trancode);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
		}



	}

}
</script>
