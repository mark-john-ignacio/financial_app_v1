<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SuppInv_new.php";


//echo $_SESSION['pageid'];

include('../../Connection/connection_string.php');
//include('../../include/denied.php');
//include('../../include/access2.php');


	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='ALLOW_REF_RR'"); 
					
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
						 
		$nCHKREFvalue = $all_course_data['cvalue']; 							
	}

	// 0 = Allow No Reference
	// 1 = W/ Reference Check Qty .. Qty must be less than or equal to reference
	// 2 = W/ Reference Open Qty .. allow qty even if more tha reference

	//function listcurrencies(){ //API for currency list
//		$apikey = $_SESSION['currapikey'];
		
		//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");

		//if ( $json === false )
		//{
		  // return 1;
		//}else{

	//		$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
	//	   return $json;
		//}
		
//	}

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
<script src="../../Bootstrap/js/jquery.numeric.js"></script>-->

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">


<form action="RR_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Supplier's Invoice</legend>	
        <input type="hidden" value="<?php echo $nCHKREFvalue;?>" name="hdnCHECKREFval" id="hdnCHECKREFval">
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
		<tH width="150" style="padding:2px">Date Received:</tH>
    <td style="padding:2px"><div class="col-xs-11 nopadding">
      <input type='text' class="datepick form-control input-sm" id="date_received" name="date_received" />
    </div>
   
  </tr>
  <tr>
    <tH width="100">Remarks:</tH>
    <td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div></td>
		<tH width="150">Supplier SI</tH>
    <td style="padding:2px;"><div class="col-xs-11 nopadding">
      <input type='text' class="form-control input-sm" id="txtSuppSI" name="txtSuppSI" required/>
    </div></td>
	
	</td>
	
  <tr>
    <tH width="100"><b>Currency:</b></tH>
    <td style="padding:2px">
			<div class="col-xs-12 nopadding">
							<div class="col-xs-8 nopadding">
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
						
												//	$objcurrs = listcurrencies();
												//	$objrows = json_decode($objcurrs,true);
														
											//foreach($objrows as $rows){
											//	if ($nvaluecurrbase==$rows['currencyCode']) {
											//		$nvaluecurrbasedesc = $rows['currencyName'];
											//	}
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

							<div class="col-xs-5" id="statgetrate" style="padding: 4px !important"> 
										
							</div>
		</div>
		<tH width="100">Ref RR:</tH>
    <td style="padding:2px">
			<div class="col-xs-11 nopadding">
				<input type="text" class="form-control input-sm" id="txtrefrr" name="txtrefrr" width="20px" tabindex="2">
			</div>
		</td>
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
   --></td>
    </tr>
<tr>
    <td colspan="2">

      <input type="hidden" id="txtprodid" name="txtprodid">
      <input type="hidden" id="txtprodnme" name="txtprodnme">

        <input type="hidden" name="hdnunit" id="hdnunit">
    </td>
    <td></td>
    <td></td>

</tr>
</table>


        <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 300px;
					text-align: left;
					overflow: auto">
	
            <table id="MyTable" class="MyTable" width="100%" cellpadding="1px">
								<thead>
									<tr>
										<!--<th style="border-bottom:1px solid #999">&nbsp;</th>-->
										<th style="border-bottom:1px solid #999">Code</th>
										<th style="border-bottom:1px solid #999">Description</th>
				            <th style="border-bottom:1px solid #999">UOM</th>
										<th style="border-bottom:1px solid #999">Qty</th>
										<th style="border-bottom:1px solid #999">Price</th>
										<th style="border-bottom:1px solid #999">Amount</th>
										<th style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
				            <!--<th style="border-bottom:1px solid #999">Date Expired</th>-->
				            <!--<th style="border-bottom:1px solid #999">&nbsp;</th>-->
									</tr>
								</thead>
									<tbody id="MyyTbltbody">
                  </tbody>
                    
						</table>
				</div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td rowspan="2"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 

    <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='RR.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>

     <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>
    </td>

  <td width="180px" align="right"><b>Gross Amount </b>&nbsp;&nbsp;</td>
    <td width="180px"> 
			<input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="0.00" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="15">
		</td>
  </tr>

  <tr>
 	 <td width="180px" align="right"><b>Gross Amount in <?=$nvaluecurrbase; ?></b>&nbsp;&nbsp;</td>
      <td width="180px"> <input type="text" id="txtnGross" name="txtnGross" readonly value="0.00" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="15">
		</td>
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

<div class="modal fade" id="mySIModal" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="DRListHeader">RR List</h3>
      </div>
            
      <div class="modal-body pre-scrollable">           
        <table name='MyDRDetList' id='MyDRDetList' class="table table-small">
          <thead>
            <tr>
              <th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
              <th>RR No</th>
              <th>Received Date</th>
              <th>Gross</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
                            	
          </tbody>
        </table>
      </div>         	
          			
      <div class="modal-footer">
        <button type="button" id="btnSave" onClick="InsertSI()" class="btn btn-primary">Insert</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<form method="post" name="frmedit" id="frmedit" action="RR_edit.php">
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
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("RR.php");
	  } 
	});


$(document).ready(function() {
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY',
		useCurrent: false,
		minDate: moment(),
		defaultDate: moment(),
    });	

	$("#selbasecurr").on("change", function (){
			
		//convertCurrency($(this).val());
				
		var dval = $(this).find(':selected').attr('data-val');

		$("#basecurrval").val(dval);
		$("#statgetrate").html("");
		recomputeCurr();


	});
				
	$("#basecurrval").on("keyup", function () {
		recomputeCurr();
	});
});
	
$(function(){	
	$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
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
		}
	});

	$("#txtrefrr").keydown(function(event){
		
		var issokso = "YES";
		var msgs = "";
		
		if(event.keyCode == 13){

			//SO Header
			$.ajax({
				url : "th_getrr.php?id=" + $(this).val() ,
				type: "GET",
				dataType: "JSON",
				async: false,
				success: function(data)
				{	
					console.log(data);
                    $.each(data,function(index,item){

						if(item.lapproved==0 && item.lcancelled==0){
						   msgs = "Transaction is still pending";
						   issokso = "NO";
						}
						
						if(item.lapproved==0 && item.lcancelled==1){
						   msgs = "Transaction is already cancelled";
						   issokso = "NO";
						}
					
					if(issokso=="YES"){
						$('#txtcust').val(item.cname); 
						$("#txtcustid").val(item.ccode);

						$('#date_received').val(item.dcutdate);

						$("#basecurrval").val(item.currate);
						$("#hidcurrvaldesc").val(item.currdesc); 
						$("#selbasecurr").val(item.currcode).change();   
						   
					}
						
					});
						
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert(jqXHR.responseText);
				}					
			});
			
			if(issokso=="YES"){

				
				$("#MyyTbltbody").empty();
			//add details
			//alert("th_qolistputall.php?id=" + $(this).val() + "&itmbal=" + xChkBal);
				$.ajax({
					url : "th_qolistputall.php?id=" + $(this).val(),
					type: "GET",
					dataType: "JSON",
					async: false,
					success: function(data)
					{	

						if(data.length==0){
							$("#AlertMsg").html("");
			
							$("#AlertMsg").html("&nbsp;&nbsp;No details to add!");
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');
						}else{
							console.log(data);
							$.each(data,function(index,item){

								$('#txtprodnme').val(item.desc); 
								$('#txtprodid').val(item.id); 
								$("#hdnunit").val(item.cunit); 
								//$("#hdnqty").val(item.nqty);
							//	$("#hdnqtyunit").val(item.cqtyunit);
								//alert(item.cqtyunit + ":" + item.cunit);
								//addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.xrefident);

								//nqty,nprice,curramt,namount,nfactor,cmainunit,xref,nident
								myFunctionadd(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.cmainunit,item.xref,item.xrefident);

								$('#txtprodnme').val("").change(); 
								$('#txtprodid').val(""); 
								$("#hdnunit").val(""); 

							});
						}

					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}

				});
			}
			
			if(issokso=="NO"){
				alert(msgs);
			}
		}
	});

});


function myFunctionadd(nqty,nprice,curramt,namount,nfactor,cmainunit,xref,nident){

	var itmcode = document.getElementById("txtprodid").value;
	var itmdesc = document.getElementById("txtprodnme").value;
	var itmunit = document.getElementById("hdnunit").value;
	//var dneeded = document.getElementById("date_received").value;

		var itmprice = nprice;
		var itmamnt = namount;
		var itmqty = nqty;
		var itmqtyorig = nqty;
		var itmfactor = nfactor;
		var itmmainunit = cmainunit;
		var itmxref = xref;
		var itmident = nident;

		var curramtz = curramt;


	var baseprice = curramtz * parseFloat($("#basecurrval").val());


	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;


	var uomoptions = "";
	

	uomoptions = "<input type='hidden' value='"+itmunit+"' name=\"seluom\" id=\"seluom\">"+itmunit;
		

	tditmbtn = "<td width=\"50\">  <input class='btn btn-info btn-xs' type='button' id='ins" + itmcode + "' value='insert' /> </td>";
	
	tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+"<input type='hidden' value='"+itmxref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+itmident+"' name=\"txtnrefident\" id=\"txtnrefident\"> </td>";
	
	tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\"> " + itmdesc + "</td>";
	
	tditmunit = "<td width=\"80\"> " + uomoptions + "</td>";
	
	tditmqty = "<td width=\"100\"> <input type='text' value='"+itmqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' readonly/> <input type='hidden' value='"+itmqtyorig+"' name=\"txtnqtyORIG\" id=\"txtnqtyORIG"+lastRow+"\"> </td>";
	
	tditmprice = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+itmprice+"' class='numeric form-control input-xs' style='text-align:right'name=\"txtnprice\" id='txtnprice"+lastRow+"' autocomplete='off' onFocus='this.select();'> <input type='hidden' value='"+itmmainunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmfactor+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";

	tditmbaseamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+curramtz+"' class='numeric form-control input-xs' style='text-align:right' name='txtntranamount' id='txtntranamount"+lastRow+"' readonly> </td>";
	
	tditmamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+baseprice.toFixed(4)+"' class='numeric form-control input-xs' style='text-align:right' name='txtnamount' id='txtnamount"+lastRow+"' readonly> </td>";
	
	tditmdel = "<td width=\"80\" style=\"padding:1px\"> <input class='btn btn-danger btn-xs' type='button' id='del" + itmcode + "' value='delete' /> </td>";

	$('#MyTable > tbody:last-child').append('<tr style=\"padding-top:1px\">'+tditmcode + tditmdesc + tditmunit + tditmqty + tditmprice + tditmbaseamount+ tditmamount  + '</tr>'); //tditmdel tditmbtn


								//	$("#del"+itmcode).on('click', function() {
									//	$(this).closest('tr').remove();
									//	 ComputeGross();
								//	});

								
								//	$("input.numeric").numeric();
								//	$("input.numeric").on("click", function () {
								//	   $(this).select();
								//	});

								$("input.numeric").autoNumeric('init',{mDec:2});

									$("input.numeric").on("keyup", function (e) {
									   ComputeAmt($(this).attr('id'));
									   ComputeGross();
									  // tblnav(e.keyCode,$(this).attr('id'));
									});
						
									/*
									$(".xseluom"+lastRow).on('change', function() {
										alert($(this).val());
										var xyz = chkprice(itmcode,$(this).val());
										
										$('#txtnprice'+lastRow).val(xyz.trim());
										
										ComputeAmt($(this).attr('id'));
										ComputeGross();
										
										var fact = setfactor($(this).val(), itmcode);
										
										$('#hdnfactor'+lastRow).val(fact.trim());
										
									});

									*/

									ComputeGross();
}

/*
function tblnav(xcode,txtinput){
	//alert(xcode);
				var inputCNT = txtinput.replace(/\D/g,'');
				var inputNME = txtinput.replace(/\d+/g, '');
				 
				switch(xcode){
					case 39: // <Right>
						if(inputNME=="txtnqty"){
							$("#txtnprice"+inputCNT).focus();
						}
						else if(inputNME=="txtnprice"){
							$("#txtnfactor"+inputCNT).focus();
						}
						 
						break;
					case 38: // <Up>  
					 	var idx =  parseInt(inputCNT) - 1;
               			$("#"+inputNME+idx).focus();
						break;
					case 37: // <Left>
						if(inputNME=="txtnfactor"){
							$("#txtnprice"+inputCNT).focus();
						}
						else if(inputNME=="txtnprice"){
							$("#txtnqty"+inputCNT).focus();
						}

						break;
					case 13:
					case 40: // <Down>
					 	var idx =  parseInt(inputCNT) + 1;
               			$("#"+inputNME+idx).focus();
						break;
				}       

}
*/

		function ComputeAmt(nme){
			
			var disnme = nme.replace(/[0-9]/g, ''); // string only
			var r = nme.replace( /^\D+/g, ''); // numeric only
			var nnet = 0;
			var nqty = 0;
			var chkValref = $("#hdnCHECKREFval").val();
			
			nqty =  parseFloat($("#txtnqty"+r).val().replace(/,/g,''));
			nprc = parseFloat($("#txtnprice"+r).val().replace(/,/g,''));
			
			namt = nqty * nprc;
			namt2 = namt * parseFloat($("#basecurrval").val());
		
			$("#txtnamount"+r).val(namt2);

			$("#txtntranamount"+r).val(namt);

			$("#txtntranamount"+r).autoNumeric('destroy');
			$("#txtnamount"+r).autoNumeric('destroy');

			$("#txtntranamount"+r).autoNumeric('init',{mDec:2});
			$("#txtnamount"+r).autoNumeric('init',{mDec:2});

		}

		function ComputeGross(){
			var rowCount = $('#MyTable >tbody tr').length;
			
			var gross = 0;
			var amt = 0;
			
			if(rowCount>=1){
				for (var i = 1; i <= rowCount; i++) {
					amt = $("#txtntranamount"+i).val().replace(/,/g,'');

					gross = gross + parseFloat(amt);
				}
			}

			gross2 = gross * parseFloat($("#basecurrval").val());
			
			$("#txtnBaseGross").val(gross);

			$("#txtnGross").val(gross2);

			$("#txtnBaseGross").autoNumeric('destroy');
			$("#txtnGross").autoNumeric('destroy');

			$("#txtnBaseGross").autoNumeric('init',{mDec:2});
			$("#txtnGross").autoNumeric('init',{mDec:2});
			
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
	
	if(document.getElementById("txtSuppSI").value==""){

			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Supplier SI is required!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		document.getElementById("txtSuppSI").focus();
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
		$("#hidcurrvaldesc").val($("#selbasecurr option:selected").text());

		var myform = $("#frmpos").serialize();

		$.ajax ({
			url: "RR_newsave.php",
			//data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross, ccustsi:ccustsi },
			data: myform,
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW RR: </b> Please wait a moment...");
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
				var nprice = $(this).find('input[name="txtnprice"]').val();
				var namt = $(this).find('input[name="txtnamount"]').val();
				var nbaseamt = $(this).find('input[name="txtntranamount"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
				//var dneed = $(this).find('input[name="dexpired"]').val();
			
				
				//$("#txtremarks").val("trancode="+ trancode+ "&indx=" + index+ "&citmno=" + citmno+ "&cuom=" + cuom+ "&nqty=" + nqty+ "&nprice=" + nprice+ "&namt=" + namt+ "&mainunit=" + mainunit+ "&nfactor=" + nfactor+ "&nqtyorig=" + nqtyOrig+ "&xcref=" + xcref+ "&crefidnt=" + crefidnt);
				
				if(nqty!==undefined){
					nqty = nqty.replace(/,/g,'');
					nprice = nprice.replace(/,/g,'');
					namt = namt.replace(/,/g,'');
					nbaseamt = nbaseamt.replace(/,/g,'');
				}

				$.ajax ({
					url: "RR_newsavedet.php",
					data: { trancode: trancode, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, nbaseamt:nbaseamt, mainunit:mainunit, nfactor:nfactor, nqtyorig:nqtyOrig, xcref:xcref, crefidnt:crefidnt},
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
			amt = $("#txtntranamount"+i).val().replace(/,/g,'');			
			recurr = parseFloat(newcurate) * parseFloat(amt);

			$("#txtnamount"+i).val(recurr);

			$("#txtnamount"+i).autoNumeric('destroy');
			$("#txtnamount"+i).autoNumeric('init',{mDec:2}); 
		}
	}

	ComputeGross();
}
</script>
