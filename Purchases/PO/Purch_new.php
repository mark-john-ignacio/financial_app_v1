<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Purch_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');


$company = $_SESSION['companyid'];
/*
function listcurrencies(){ //API for currency list
	$apikey = $_SESSION['currapikey'];
  
	//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
	//$obj = json_decode($json, true);

	$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
  
	return $json;
}
*/

	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$compname = $rowcomp['compname'];
			$compadd = $rowcomp['compadd'];
		}

	}

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
<script src="../../include/autoNumeric.js"></script>
<!--
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
-->

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="Purch_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Purchase Order</legend>	

			<ul class="nav nav-tabs">
				<li class="active"><a href="#home">PO Details</a></li>
				<li><a href="#menu1">Delivery/Billing</a></li>
			</ul>

			<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;text-align: left;overflow: auto">
 				<div class="tab-content">  

      		<div id="home" class="tab-pane fade in active" style="padding-left:5px;">

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
								<tH width="150">PO Date:</tH>
								<td width="250" style="padding:2px;">
									<div class="col-xs-5 nopadding">
										<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date("m/d/Y"); ?>" readonly/>
									</div>
								</td>
							</tr>
							<tr>
								<tH width="100">Remarks:</tH>
								<td style="padding:2px">
									<div class="col-xs-11 nopadding">
										<input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2">
									</div>
								</td>
								<tH width="150" style="padding:2px">Date Needed:</tH>
								<td style="padding:2px">
								<div class="col-xs-5 nopadding">

								<input type='text' class="datepick form-control input-sm" id="date_needed" name="date_needed" />

								</div>
								</td>
							</tr>

							<tr>
								<tH width="100">Contact:</tH>
								<td style="padding:2px">
									<div class="col-xs-3 nopadding"> 
										<button class="btn btn-sm btn-block btn-warning" name="btnSearchCont" id="btnSearchCont" type="button">Search</button>
									</div>
									<div class="col-xs-8 nopadwleft">
										<input type="text" id="txtcontactname" name="txtcontactname" class="required form-control input-sm" placeholder="Contact Person Name..." tabindex="1"  required="true">
									</div>
								</td>
								<tH width="100" style="padding:2px">Email:</tH>
								<td style="padding:2px">
								<div class="col-xs-11 nopadding">
									<input type='text' class="form-control input-sm" id="contact_email" name="contact_email" />

								</div>
								</td>
							</tr>


							<tr>
								<tH width="100">Currency:</tH>
								<td style="padding:2px">
												<div class="col-xs-12 nopadding">
													<div class="col-xs-6 nopadding">
														<select class="form-control input-sm" name="selbasecurr" id="selbasecurr"> 						
															<?php
																	$nvaluecurrbase = "";	
																	$nvaluecurrbasedesc = "";	
																	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_CURRENCY'"); 
																	
																		if (mysqli_num_rows($result)!=0) {
																			$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
																			
																			$nvaluecurrbase = $all_course_data['cvalue']; 
																				
																		}
																		else{
																			$nvaluecurrbase = "";
																		}

																		/*
												
																			$objcurrs = listcurrencies();
																			$objrows = json_decode($objcurrs, true);
																				
																	foreach($objrows as $rows){
																		if ($nvaluecurrbase==$rows['currencyCode']) {
																			$nvaluecurrbasedesc = $rows['currencyName'];
																		}

																		if($rows['countryCode']!=="Crypto" && $rows['currencyName']!==null){

																			*/

																			$sqlhead=mysqli_query($con,"Select symbol as id, CONCAT(symbol,\" - \",country,\" \",unit) as currencyName, rate from currency_rate");
																			if (mysqli_num_rows($sqlhead)!=0) {
																				while($rows = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
															?>
																		<option value="<?=$rows['id']?>" <?php if ($nvaluecurrbase==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=$rows['currencyName']?></option>
															<?php
																		}
																	}
															?>
														</select>
															<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?php echo $nvaluecurrbase; ?>"> 	
															<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?php echo $nvaluecurrbasedesc; ?>"> 
													</div>
													<div class="col-xs-2 nopadwleft">
														<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="1">	 
													</div>

													<div class="col-xs-4" id="statgetrate" style="padding: 4px !important"> 
																
													</div>
												</div>
									</td>
									<tH width="150" style="padding:2px">Terms:</tH>
									<td style="padding:2px">
										<div class="col-xs-8 nopadding">							
											<select id="selterms" name="selterms" class="form-control input-sm selectpicker">
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
									</td>
							</tr>

							<tr>
								<td colspan="2">&nbsp;</td>
								<th style="padding:2px"><!--<span style="padding:2px">PURCHASE TYPE:</span>-->&nbsp;</th>
								<td>&nbsp;
								<!--
								<div class="col-xs-5">
										<select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="3">
											<option value="Grocery">Grocery</option>
											<option value="Cripples">Cripples</option>
										</select>
							</div>
							-->
							</td>
							</tr>

							<tr>
								<td colspan="2">
									<div class="col-xs-12 nopadwdown">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." width="25" tabindex="4"  autocomplete="off">
										</div>
										<div class="col-xs-6 nopadwleft">
											<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL+F) Search Product Name..." size="80" tabindex="5" autocomplete="off">
										</div>
									</div>

										<input type="hidden" name="hdnunit" id="hdnunit">
								</td>
								<td></td>
								<td></td>

							</tr>
						</table>

					</div>

					<div id="menu1" class="tab-pane fade" style="padding-left:5px">
						<table width="100%" border="0">
							<tr>
								<td width="150"><b>Deliver To</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-12 nopadding">
											<input type="text" class="form-control input-sm" id="txtdelcust" name="txtdelcust" width="20px" tabindex="1" placeholder="Enter Deliver To..."  size="60" autocomplete="off" value="<?=$compname?>">
										</div> 
									</div>						
								</td>
							</tr>
							<tr>
								<td><b>Delivery Address</b></td>
								<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><textarea class="form-control input-sm" id="txtdeladd" name="txtdeladd" placeholder="Enter Delivery Address..." autocomplete="off"> <?=$compadd?> </textarea></div></td>
							</tr>					

							<tr>
								<td width="150"><b>Delivery Notes</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-12 nopadding">
											<input type="text" class="form-control input-sm" id="textdelnotes" name="textdelnotes" width="20px" tabindex="1" placeholder="Enter Delivery Notes..."  size="60" autocomplete="off">
										</div> 
									</div>						
								</td>
							</tr>

							<tr>
								<td width="150"><b>Bill To</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-12 nopadding">
											<input type="text" class="form-control input-sm" id="txtbillto" name="txtbillto" width="20px" tabindex="1" placeholder="Enter Bill To..."  size="60" autocomplete="off" value="<?=$compname?>">
										</div> 
									</div>						
								</td>
							</tr>

							<tr>
								<td width="150" colspan="2"><br><br></td>

							</tr>

						</table>
					</div>
				</div>
			</div>

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
									<th style="border-bottom:1px solid #999">Code</th>
									<th style="border-bottom:1px solid #999">Description</th>
									<th style="border-bottom:1px solid #999">UOM</th>
									<th style="border-bottom:1px solid #999">Qty</th>
									<th style="border-bottom:1px solid #999">Price</th>
									<th style="border-bottom:1px solid #999">Amount</th>
									<th style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
									<th style="border-bottom:1px solid #999">Date Needed</th>
									<th style="border-bottom:1px solid #999">&nbsp;</th>
								</tr>
							</thead>
							<tbody class="tbody">
							</tbody>                    
						</table>

				</div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td rowspan="2" width="70%"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 

    <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Purch.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
    
     <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>
    </td>
    <td width="110px" align="right"><b>Gross Amount </b>&nbsp;&nbsp;</td>
    <td width="150px"> <input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
	</td>
      
  </tr>
  <tr>
 	 <td width="110px" align="right"><b>Gross Amount in <?php echo $nvaluecurrbase; ?></b>&nbsp;&nbsp;</td>
        <td width="150px"> <input type="text" id="txtnGross" name="txtnGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></td>

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

<!-- MODAL FOR CONTACT NAME -->
<div class="modal fade" id="ContactModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog vertical-align-top">
        <div class="modal-content">
        	<div class="modal-header">
        		Select Contact Person
            </div>
            <div class="modal-body">
            	<table id="ContactTbls" class="table table-condensed" width="100%">
            		
	            	<thead>
	            		<tr>
	            			<th>Name</th>
	            			<th>Designation</th>
	            			<th>Department</th>
	            			<th>Email</th>
	            		</tr>
	            	</thead>
	            	<tbody>

	            	</tbody>
            	</table>
            </div>
            <div class="modal-footer">
            	<button type="button" class="btn btn-warning btn-sm" data-dismiss="modal" id="btnmodclose">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<form method="post" name="frmedit" id="frmedit" action="Purch_edit.php">
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
		window.location.replace("Purch.php");

	  }

	});


$(document).ready(function() {

	$(".nav-tabs a").click(function(){
    			$(this).tab('show');
			});


    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY',
		useCurrent: false,
		minDate: moment(),
		defaultDate: moment(),
    });

	$("#selbasecurr").on("change", function (){
			
		convertCurrency($(this).val());
			
	});
			
	$("#basecurrval").on("keyup", function () {
		recomputeCurr();
	});
		
	
});
	
$(function(){	
	$('#BlankItmModal').on('shown.bs.modal', function () {
		$('#txtblankitm').focus();
	}) 

	$("#txtblankitm").on("keydown", function(e) {
		if(e.keyCode == 13){
			
			var x = $(this).val();
			
			var x1 = x.split("*",2);
			
				document.getElementById("txtprodid").value = "NEW_ITEM";
				document.getElementById("txtprodnme").value = x1[1];
				document.getElementById("hdnunit").value = x1[0];
	
				myFunctionadd();
				
				$("#BlankItmModal").modal('hide');
				
				$("#txtprodid").val("");
				$("#txtprodnme").val("");
				$("#hdnunit").val("");


		}
	});
	
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
			$('#selterms').val(item.cterms).change(); 

			getcontact(item.id);
		}
	});
	
	$('#txtprodnme').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_product.php",
				dataType: "json",
				data: {
					query: $("#txtprodnme").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.cname+'</span><br><small><span class="dropdown-item-extra">' + item.cunit + '</span></small></div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 					

			//$('.datepick').each(function(){
			//	$(this).data('DateTimePicker').destroy();
			//});
		
				$('#txtprodnme').val(item.cname).change(); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit);
				
				addItemName("");	
				
			$('.datepick').each(function(){
				$(this).datetimepicker({format: 'MM/DD/YYYY'});	
			});
			
		}
	
	});


	$("#txtprodid").keydown(function(e){
		if(e.keyCode == 13){

		$.ajax({
        url:'../get_productid.php',
        data: 'c_id='+ $(this).val(),                 
        success: function(value){
			
            var data = value.split(",");
            $('#txtprodid').val(data[0]);
            $('#txtprodnme').val(data[1]);
			$('#hdnunit').val(data[2]);
		

		if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
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
					//alert($("#txtitemcode"+counter).val());
					//alert(isItem);
				//if prd id exist
				}
			//for loop
			 }
		   //if rowcount >1
		   }
		//if value is not blank
		 }
		 
		if(isItem=="NO"){		

			$('.datepick').each(function(){
				$(this).data('DateTimePicker').destroy();
			});
	
				myFunctionadd();
				ComputeGross();	
				
			$('.datepick').each(function(){
				$(this).datetimepicker({format: 'MM/DD/YYYY'});	
			});
					
	    }
	    else{
			//alert("ITEM NOT IN THE MASTERLIST!");
			addqty();
		}
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
 
	    //closing for success: function(value){
	    }
        }); 

	
		 
		//if ebter is clicked
		}
		
	});


	$("#btnSearchCont").on("click", function(){

		//get contact names
		if($('#txtcustid').val()!="" && $('#txtcust').val()!=""){
			$('#ContactTbls tbody').empty(); 

			$.ajax({
						url:'get_contactinfonames.php',
						data: 'c_id='+ $('#txtcustid').val(),  
						dataType: "json",               
						success: function(data){
							
							$.each(data,function(index,item){

								//put to table
								$("<tr class='bdydeigid' style='cursor:pointer'>").append(
									$("<td class='disnme'>").text(item.cname),
									$("<td class='disndesig'>").text(item.cdesig),
									$("<td class='disdept'>").text(item.cdept),
									$("<td class='disemls'>").text(item.cemail)
								).appendTo("#ContactTbls tbody");

							});
				}
			});

			$("#ContactModal").modal("show");
		}else{
			alert("Supplier Required!");
			document.getElementById("txtcust").focus();
			return false;
		}


	});

	
	$(document).on("click", "tr.bdydeigid" , function() {
    var $row = $(this).closest("tr"),       // Finds the closest row <tr> 
	  $tds = $row.find("td");             // Finds all children <td> elements

		$.each($tds, function() {               // Visits every single <td> element
		   // alert($(this).attr("class"));        // Prints out the text within the <td>

		    if($(this).attr("class")=="disnme"){
		    	$('#txtcontactname').val($(this).text());
		    }
		     if($(this).attr("class")=="disemls"){
		    	$("#contact_email").val($(this).text());
		    }
		});

		$("#ContactModal").modal("hide");
  });
	

});

function addItemName(tranno){
	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
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
		 
	 if(isItem=="NO"){	

			myFunctionadd();		
			ComputeGross();	
	 }
	 else{
		
		addqty();	
			
	 }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		
	 }

}

function myFunctionadd(){
	var itmcode = document.getElementById("txtprodid").value;
	var itmdesc = document.getElementById("txtprodnme").value;
	var itmunit = document.getElementById("hdnunit").value;
	var dneeded= document.getElementById("date_needed").value;
	
	var itmprice = chkprice(itmcode,itmunit);


		var uomoptions = "";
		
		if(itmcode == "NEW_ITEM"){	
			uomoptions = "<option value='"+itmunit.toUpperCase()+"'>"+itmunit.toUpperCase()+"</option>";
		}else{
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
		}
		
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+"</td>";
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\"><input type='hidden' value='"+itmdesc.toUpperCase()+"' name=\"txtitemdesc\" id=\"txtitemdesc\">"+itmdesc.toUpperCase()+"</td>";
	var tditmunit = "<td width=\"80\" style=\"padding: 1px\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">"+uomoptions+"</select> </td>";
	var tditmqty = "<td width=\"100\" style=\"padding: 1px\" nowrap> <input type='text' value='1' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' /> <input type='hidden' value='"+itmunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='1' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";
		
	var tditmprice = "<td width=\"100\" style=\"padding: 1px\" nowrap> <input type='text' value='"+itmprice+"' class='numeric form-control input-xs' style='text-align:right'name=\"txtnprice\" id='txtnprice"+lastRow+"' autocomplete='off' onFocus='this.select();'> </td>";
			
	var tditmbaseamount = "<td width=\"100\" style=\"padding: 1px\" nowrap> <input type='text' value='"+itmprice+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> </td>";

	var tditmamount = "<td width=\"100\" style=\"padding: 1px\" nowrap> <input type='text' value='"+itmprice+"' class='numeric form-control input-xs' style='text-align:right' name='txtnamount' id='txtnamount"+lastRow+"' readonly> </td>";

	var tdneeded = "<td width=\"100\" style=\"padding: 1px\" nowrap><input type='text' class='datepick form-control input-xs' id='dneed"+lastRow+"' name='dneed' value='"+dneeded+"' /></td>"
	
	var tditmdel = "<td width=\"80\" style=\"padding: 1px\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + lastRow + "' value='delete' /> </td>";


	$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + tditmunit + tditmqty + tditmprice + tditmbaseamount + tditmamount+ tdneeded + tditmdel + '</tr>');


									$("#del"+lastRow).on('click', function() {
										$(this).closest('tr').remove();
									});

									$("input.numeric").autoNumeric('init',{mDec:2});

								//	$("input.numeric").numeric();
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function () {
									   ComputeAmt($(this).attr('id'));
									   ComputeGross();
									});
									
									$("#seluom"+lastRow).on('change', function() {

										var xyz = chkprice(itmcode,$(this).val());
										
										$('#txtnprice'+lastRow).val(xyz.trim());
										
										ComputeAmt($(this).attr('id'));
										ComputeGross();
										
										var fact = setfactor($(this).val(), itmcode);
										
										$('#hdnfactor'+lastRow).val(fact.trim());
										
									});
									


}


		function ComputeAmt(nme){
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val().replace(/,/g,'');
			nqty = parseFloat(nqty)

			nprc = $("#txtnprice"+r).val().replace(/,/g,'');
			nprc = parseFloat(nprc);

			//ndsc = $("#txtndisc"+r).val();
			//ndsc = parseFloat(ndsc);
			
			//if (parseFloat(ndsc) != 0) {
			//	nprcdisc = parseFloat(nprc) * (parseFloat(ndsc) / 100);
			//	nprc = parseFloat(nprc) - nprcdisc;

			//}
			
			namt = nqty * nprc;
			namt = namt.toFixed(4);

			namt2 = namt * parseFloat($("#basecurrval").val());
			namt2 = namt2.toFixed(4);
		
			$("#txtnamount"+r).val(namt2);

			$("#txtntranamount"+r).val(namt);

			$("#txtntranamount"+r).autoNumeric('destroy');
			$("#txtnamount"+r).autoNumeric('destroy');

			$("#txtntranamount"+r).autoNumeric('init',{mDec:2});
			$("#txtnamount"+r).autoNumeric('init',{mDec:2});

		}

		function ComputeGross(){
			var rowCount = $('#MyTable tr').length;

			var gross = 0;
			var amt = 0;
			
			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
					amt = $("#txtntranamount"+i).val().replace(/,/g,'');
					gross = gross + parseFloat(amt);
					
				}
				
				
			}
			gross2 = gross * parseFloat($("#basecurrval").val());

			$("#txtnGross").val(gross2);
			$("#txtnBaseGross").val(gross);

			$("#txtnGross").autoNumeric('destroy');
			$("#txtnBaseGross").autoNumeric('destroy');

			$("#txtnGross").autoNumeric('init',{mDec:2});
			$("#txtnBaseGross").autoNumeric('init',{mDec:2});	
			
		}

function addqty(){

	var itmcode = document.getElementById("txtprodid").value;

	var TotQty = 0;
	var TotAmt = 0;
	
	$("#MyTable > tbody > tr").each(function() {	
	var disID = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
	
	//alert(disID);
		if(disID==itmcode){
			
			var itmqty = $(this).find("input[name='txtnqty']").val();
			var itmprice = $(this).find("input[name='txtnprice']").val();
			
			//alert(itmqty +" : "+ itmprice);
			
			TotQty = parseFloat(itmqty) + 1;
			$(this).find("input[name='txtnqty']").val(TotQty);
			
			TotAmt = TotQty * parseFloat(itmprice);
			$(this).find("input[name='txtnamount']").val(TotAmt);
		}

	});
	
	ComputeGross();

}

function chkprice(itmcode,itmunit){
	var result;
	var ccode = document.getElementById("txtcustid").value;
			
	$.ajax ({
		url: "../th_checkitmprice.php",
		data: { itm: itmcode, cust: ccode, cunit: itmunit},
		async: false,
		success: function( data ) {
			 result = data;
		}
	});
			
	return result;
	
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
			myprice = $(this).find('input[name="txtnprice"]').val();
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
			}
			
			if(myprice == 0 || myprice == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
			}

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
		var ddate = $("#date_needed").val();
		var ngross = $("#txtnGross").val();

		var myform = $("#frmpos").serialize();		
		$.ajax ({
			url: "Purch_newsave.php",
			//data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross },
			data: myform,
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
			
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var citmdesc = $(this).find('input[type="hidden"][name="txtitemdesc"]').val();
				var cuom = $(this).find('select[name="seluom"]').val();
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nprice = $(this).find('input[name="txtnprice"]').val();
				var ntranamt = $(this).find('input[name="txtntranamount"]').val();
				var namt = $(this).find('input[name="txtnamount"]').val();
				var dneed = $(this).find('input[name="dneed"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
				
					
				//alert("Purch_newsavedet.php?trancode="+ trancode + "&dneed="+ dneed + "&indx="+ index + "&citmno="+ citmno+ "&cuom="+ cuom+ "&nqty="+ nqty + "&nprice="+ nprice+ "&namt=" + namt + "&mainunit="+ mainunit + "&nfactor=" + nfactor + "&citmdesc=" + citmdesc);

				if(nqty!==undefined){
					nqty = nqty.replace(/,/g,'');
					nprice = nprice.replace(/,/g,'');
					namt = namt.replace(/,/g,'');
					ntranamt = ntranamt.replace(/,/g,'');
				}
				
				$.ajax ({
					url: "Purch_newsavedet.php",
					data: { trancode: trancode, dneed: dneed, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, mainunit:mainunit, nfactor:nfactor, citmdesc:citmdesc, ntranamt:ntranamt },
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

function convertCurrency(fromCurrency) {
  
  toCurrency = $("#basecurrvalmain").val(); //statgetrate
   $.ajax ({
	 url: "../../Sales/th_convertcurr.php",
	 data: { fromcurr: fromCurrency, tocurr: toCurrency },
	 async: false,
	 beforeSend: function () {
		 $("#statgetrate").html(" <i>Getting exchange rate please wait...</i>");
	 },
	 success: function( data ) {

		 $("#basecurrval").val(data);
		 $("#hidcurrvaldesc").val($( "#selbasecurr option:selected" ).text()); 

	 },
	 complete: function(){
		 $("#statgetrate").html("");
		 recomputeCurr();
	 }
 });

}

function recomputeCurr(){

	var newcurate = $("#basecurrval").val();
	var rowCount = $('#MyTable tr').length;
			
	var gross = 0;
	var amt = 0;

	if(rowCount>1){
		for (var i = 1; i <= rowCount-1; i++) {
			amt = $("#txtntranamount"+i).val();			
			recurr = parseFloat(newcurate) * parseFloat(amt);

			$("#txtnamount"+i).val(recurr.toFixed(4));
		}
	}

	ComputeGross();
}

function getcontact(cid){

	$.ajax({
				url:'get_contactinfo.php',
				data: 'c_id='+ cid,                 
				success: function(value){
					if(value!=""){
						if(value.trim()=="Multi"){
							$("#btnSearchCont").click();
						}else{
								var data = value.split(":");
								
								$('#txtcontactname').val(data[0]);
								//$('#txtcontactdesig').val(data[1]);
					//$('#txtcontactdept').val(data[2]);
					$("#contact_email").val(data[3]);
						}
					}
		}
	});

}

</script>
