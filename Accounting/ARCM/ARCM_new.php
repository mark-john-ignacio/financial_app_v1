<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesRet_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

//echo $_SESSION['chkitmbal']."<br>";
//echo $_SESSION['chkcompvat'];

$ddeldate = date("m/d/Y");
$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));

//echo $ddeldate;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
   <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
    
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="SR_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Credit Memo</legend>	
        <table width="100%" border="0">
  <tr>
    <tH width="100" rowspan="3">
    <span style="padding:2px"><img src="../../images/blueX.png" width="100" height="100" style="border:solid 1px  #06F;" name="imgemp" id="imgemp"></span>
    </tH>
    <tH width="100">&nbsp;Supplier:</tH>
    <td style="padding:2px">
    <div class="col-xs-12 nopadding">
        <div class="col-xs-3 nopadding">
        	<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1">
            <input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
            <input type="hidden" id="hdnpricever" name="hdnpricever" value="">
        </div>

    	<div class="col-xs-8 nopadwleft">
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off">
        </div> 
      </div>
    </td>
    <tH width="150">CM Date:</tH>
    <td style="padding:2px;">
     <div class="col-xs-10 nopadding">
		<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $ddeldate; ?>" />
     </div>
    </td>
  </tr>
  <tr>
    <tH width="100">&nbsp;Remarks:</tH>
    <td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div></td>
    <tH width="150" style="padding:2px">CM Type:</tH>
    <td style="padding:2px">&nbsp;</td>
  </tr>
  <tr>
  	<td>&nbsp;</td>
  	<td>&nbsp;</td>
  	<tH width="150" style="padding:2px">Total Amount:</tH>
    <td style="padding:2px">
		<div class="col-xs-10 nopadding">
			<input type="text" id="txtnGross" name="txtnGross" readonly value="0" size="10" class="form-control input-sm text-right font-weight-bold" >
		</div>	
		</td>
  </tr>

<tr>
    <td colspan="5">&nbsp;
      <input type="hidden" id="txtprodid" name="txtprodid">
      <input type="hidden" id="txtprodnme" name="txtprodnme">
      <input type="hidden" name="hdnqty" id="hdnqty">
      <input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
      <input type="hidden" name="hdnunit" id="hdnunit">

    </td>
    </tr>
</table>
         <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 250px;
					text-align: left;
					overflow: auto">
	
            <table id="MyTable" class="MyTable table table-condensed" width="100%">

					<tr>
						<th style="border-bottom:1px solid #999">Code</th>
						<th style="border-bottom:1px solid #999">Description</th>
                        <th style="border-bottom:1px solid #999">UOM</th>
                        <th style="border-bottom:1px solid #999">Qty (Deducted)</th>
						<th style="border-bottom:1px solid #999">Price</th>
                        <th style="border-bottom:1px solid #999">Amount</th>
                        <th style="border-bottom:1px solid #999">Reason</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
                    
					<tbody class="tbody">
                    </tbody>
                    
			</table>

</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
    <button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='SR.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>

    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
SI<br>(Insert)</button>

    
    <input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (F2)</button></td>
    <td align="right" valign="top">&nbsp;
    
    
      </td>
    </tr>
</table>

    </fieldset>
    
   
    <div class="modal fade" id="MyDetModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  aria-label="Close"  onclick="chkCloseInfo();"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader"> Additional Details Info</h3>           
			</div>
    
            <div class="modal-body">
                <input type="hidden" name="hdnrowcnt2" id="hdnrowcnt2">
                <table id="MyTable2" class="MyTable table table-condensed" width="100%">
    				<tr>
						<th style="border-bottom:1px solid #999">Code</th>
						<th style="border-bottom:1px solid #999">Description</th>
                        <th style="border-bottom:1px solid #999">Field Name</th>
						<th style="border-bottom:1px solid #999">Value</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
					<tbody class="tbody">
                    </tbody>
                </table>
    
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- FULL PO LIST REFERENCES-->

<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="InvListHdr">SI List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
       <div class="col-xs-12 nopadding">

                <div class="form-group">
                    <div class="col-xs-4 nopadding pre-scrollable" style="height:37vh">
                          <table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
                           <thead>
                            <tr>
                              <th>SI No</th>
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



<form method="post" name="frmedit" id="frmedit" action="SR_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>


</body>
</html>

<script type="text/javascript">
var xtoday = new Date();
var xdd = xtoday.getDate();
var xmm = xtoday.getMonth()+1; //January is 0!
var xyyyy = xtoday.getFullYear();

xtoday = xmm + '/' + xdd + '/' + xyyyy;


	$(document).keydown(function(e) {	
	
	  if(e.keyCode == 83 && e.ctrlKey) { //F2
	  	  
		 if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		  e.preventDefault();
		  return chkform();
		 }
	  }
	  else if(e.keyCode == 27){ //ESC
		  e.preventDefault();
		if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		 window.location.replace("SR.php");
	    }

	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			openinv();
		}
	  }

	
	});

$(function(){
	    $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY',
				 //minDate: new Date(),
        });

		$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});

		$("#txtcustid").keyup(function(event){
		if(event.keyCode == 13){
		
		var dInput = this.value;
		
		$.ajax({
        type:'post',
        url:'../get_customerid.php',
        data: 'c_id='+ $(this).val(),                 
        success: function(value){
			//alert(value);
			if(value!=""){
				var data = value.split(":");
				$('#txtcust').val(data[0]);
				$('#imgemp').attr("src",data[3]);
				$('#hdnpricever').val(data[2]);
								
				$('#hdnvalid').val("YES");
				
				$('#txtremarks').focus();
								
			}
			else{
				$('#txtcustid').val("");
				$('#txtcust').val("");
				$('#imgemp').attr("src","../../images/blueX.png");
				$('#hdnpricever').val("");
				
				$('#hdnvalid').val("NO");
			}
		},
		error: function(){
			$('#txtcustid').val("");
			$('#txtcust').val("");
			$('#imgemp').attr("src","../../images/blueX.png");
			$('#hdnpricever').val("");
			
			$('#hdnvalid').val("NO");
		}
		});

		}
		
	});

	$('#txtcust, #txtcustid').on("blur", function(){
		if($('#hdnvalid').val()=="NO"){
		  $('#txtcust').attr("placeholder", "ENTER A VALID CUSTOMER FIRST...");
		  
		 // $('#txtprodnme').attr("disabled", true);
		 // $('#txtprodid').attr("disabled", true);
		}else{
			
		 // $('#txtprodnme').attr("disabled", false);
		 // $('#txtprodid').attr("disabled", false);
		  
		  $('#txtremarks').focus();
	
		}
	});
	//Search Cust name
	$('#txtcust').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_customer.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
			$("#imgemp").attr("src",item.imgsrc);
			$("#hdnpricever").val(item.cver);
			
			$('#hdnvalid').val("YES");
			
			$('#txtremarks').focus();			
			
		}
	
	});
		

});


function addItemName(qty,price,amt,factr,cref,ident){

	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

		var isItem = "NO";
		var disID = "";

			$("#MyTable > tbody > tr").each(function() {	
				disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				disref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				disiDent = $(this).find('input[type="hidden"][name="txtnident"]').val();
				
				if($("#txtprodid").val()==disID && cref==disref && ident==disiDent){
					
					isItem = "YES";

				}
			});	

	 if(isItem=="NO"){	
	 	myFunctionadd(qty,price,amt,factr,cref,ident);
		
		ComputeGross();	

	 }
	 else{

		addqty();	
			
	 }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		
	 }

}

function myFunctionadd(qty,pricex,amtx,factr,cref,ident){
	//alert("hello");
	var itmcode = $("#txtprodid").val();
	var itmdesc = $("#txtprodnme").val();
	var itmqtyunit = $("#hdnqtyunit").val();
	var itmqty = $("#hdnqty").val();
	var itmunit = $("#hdnunit").val();
	var itmccode = $("#hdnpricever").val();
	//alert(itmqtyunit);
	if(qty=="" && pricex=="" && amtx=="" && factr==""){
		var itmtotqty = 1;
		var price = pricex;
		var amtz = pricex;
		var factz = 1;
	}
	else{
		var itmtotqty = qty
		var price = pricex;
		var amtz = amtx;	
		var factz = factr;	
	}
		
		
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	if(cref==null){
		cref = ""
	}
	
	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference\"><input type='hidden' value='"+ident+"' name=\"txtnident\" id=\"txtnident\"></td>";
	
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	
	var tditmunit = "<td width=\"100\" nowrap> <input type='hidden' value='"+itmunit+"' name=\"seluom\" id=\"seluom"+lastRow+"\">"+itmunit+"</td>";
	
	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();'> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+factz+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";
		
	var tditmprice = "<td width=\"100\" nowrap> <input type='text' value='"+price+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' \"> </td>";
			
	var tditmamount = "<td width=\"100\" nowrap> <input type='text' value='"+amtz+"' class='form-control input-xs' style='text-align:right' name=\"txtnamount\" id='txtnamount"+lastRow+"' readonly> <input type='hidden' value='"+itmtotqty+"' name=\"txtnqty0\" id='txtnqty0"+lastRow+"'> <input type='hidden' value='"+price+"' name=\"txtnprice0\" id='txtnprice0"+lastRow+"'> </td>";
	
	var tditmreason = "<td width=\"120\" nowrap> <input type='text' value='' class='form-control input-xs' name=\"txtcreason\" id=\"txtcreason\" placeholder=\"Reason...\"> </td>";
	
	var tditmdel = "<td width=\90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + ident + "' value='delete' onClick=\"deleteRow(this);\"/> &nbsp; <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/> </td>";


	$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + tditmunit + tditmqty + tditmprice + tditmamount + tditmreason + tditmdel + '</tr>');

									$("#del"+ident).on('click', function() {
										$(this).closest('tr').remove();
									});


									$("input.numeric").numeric();
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function () {
									   ChkValOrig($(this).attr('id'));
									   ComputeAmt($(this).attr('id'));
									   ComputeGross();
									});
																		
									ComputeGross();
									
									
}

		function ChkValOrig(nme){
			var nmez = nme.replace(/\d+/g, '');
			var r = nme.replace( /^\D+/g, '');
			
			//if(nmez=="txtnqty"){
				var newqty = $("#"+nme).val();
				var oldqty = $("#"+nmez+"0"+r).val();
				
				if(parseFloat(newqty) > parseFloat(oldqty)){
					$("#"+nme).val(oldqty);
				}
			//}

			
		}
		
		function ComputeAmt(nme){
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val();
			nqty = parseFloat(nqty)
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

			$("#txtnGross").val(gross);
			
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


function viewhidden(itmcde,itmnme){
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow2 = tbl.length-1;
	
	if(lastRow2>=1){
			$("#MyTable2 > tbody > tr").each(function() {	
			
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				//alert(citmno+"!="+itmcde);
				if(citmno!=itmcde){
					
					$(this).find('input[name="txtinfofld"]').attr("disabled", true);
					$(this).find('input[name="txtinfoval"]').attr("disabled", true);
					$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs disabled");
					
				}
				else{
					$(this).find('input[name="txtinfofld"]').attr("disabled", false);
					$(this).find('input[name="txtinfoval"]').attr("disabled", false);
					$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");
				}
				
			});
	}			
			
	addinfo(itmcde,itmnme);
	
	$('#MyDetModal').modal('show');
}

function addinfo(itmcde,itmnme){
	//alert(itmcde+","+itmnme);
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;

	
	var tdinfocode = "<td><input type='hidden' value='"+itmcde+"' name='txtinfocode' id='txtinfocode"+lastRow+"'>"+itmcde+"</td>";
	var tdinfodesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmnme+"</td>"
	var tdinfofld = "<td><input type='text' name='txtinfofld' id='txtinfofld"+lastRow+"' class='form-control input-xs'></td>";
	var tdinfoval = "<td><input type='text' name='txtinfoval' id='txtinfoval"+lastRow+"' class='form-control input-xs'></td>";
	var tdinfodel = "<td><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo" + lastRow + itmcde + "' value='delete' /></td>";

	//alert(tdinfocode + "\n" + tdinfodesc + "\n" + tdinfofld + "\n" + tdinfoval + "\n" + tdinfodel);
	
	$('#MyTable2 > tbody:last-child').append('<tr>'+tdinfocode + tdinfodesc + tdinfofld + tdinfoval + tdinfodel + '</tr>');

									$("#delinfo"+lastRow+itmcde).on('click', function() {
										$(this).closest('tr').remove();
									});

}

function chkCloseInfo(){
	var isInfo = "TRUE";
	
	$("#MyTable > tbody > tr").each(function(index) {	
			
		var citmfld = $(this).find('input[name="txtinfofld"]');
		var citmval = $(this).find('input[name="txtinfoval"]');
		
		if(citmfld=="" || citmval==""){
			isInfo = "FALSE";
		}
				
	});

	
	if(isInfo == "TRUE"){
		$('#MyDetModal').modal('hide');	}
	else{
		//alert("");
		
		$("#AlertMsg").html("");
			
		$("#AlertMsg").html("&nbsp;&nbsp;Incomplete info values!");
		$("#alertbtnOK").show();
		$("#AlertModal").modal('show');
	}
}

function openinv(){
		if($('#txtcustid').val() == ""){
			//alert("");
			
			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Please pick a valid customer!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');
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
			$('#InvListHdr').html("SI List: " + $('#txtcust').val())

			var xstat = "YES";
			
			//disable escape insert and save button muna
			//alert("th_qolist.php?x="+x);
			$.ajax({
                    url: 'th_qolist.php',
					data: 'x='+x,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
					   $("#allbox").prop('checked', false);
					   
                       console.log(data);
                       $.each(data,function(index,item){

								
						if(item.cpono=="NONE"){
						$("#AlertMsg").html("No Deliver Receipt Available");
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

							xstat = "NO";
							
										$("#txtcustid").attr("readonly", false);
										$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
							$("<td id='td"+item.cpono+"'>").text(item.cpono),
							$("<td>").text(item.ngross)
							).appendTo("#MyInvTbl tbody");
							
							
							$("#td"+item.cpono).on("click", function(){
								opengetdet($(this).text());
							});
							
							$("#td"+item.cpono).on("mouseover", function(){
								$(this).css('cursor','pointer');
							});
					   	}

                       });
					   

					   if(xstat=="YES"){
						   $('#mySIRef').modal('show');
					   }
                    },
                    error: function (req, status, err) {
						//alert();
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

	$('#InvListHdr').html("SI List: " + $('#txtcust').val() + " | SI Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
	
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
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
					  $("#allbox").prop('checked', false); 
					   
                      console.log(data);
					  $.each(data,function(index,item){
						  if(item.citemno==""){
							//  alert("")
							    $("#AlertMsg").html("");
			
								$("#AlertMsg").html("&nbsp;&nbsp;NO more items to add!");
								$("#alertbtnOK").show();
								$("#AlertModal").modal('show');
						  }
						  else{
						  
							if (item.nqty>=1){
								$("<tr>").append(
								$("<td>").html("<input type='checkbox' value='"+item.ident+"' name='chkSales[]' data-id=\""+drno+"\">"),
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
						
							$('#txtprodnme').val(item.desc); 
							$('#txtprodid').val(item.id); 
							$("#hdnunit").val(item.cunit); 
							$("#hdnqty").val(item.nqty);
							$("#hdnqtyunit").val(item.cqtyunit);
							//alert(item.cqtyunit + ":" + item.cunit);
							addItemName(item.totqty,item.nprice,item.namount,item.nfactor,item.xref,item.ident)
											   
					   });
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						//alert();
						$("#AlertMsg").html("");
			
						$("#AlertMsg").html(jqXHR.responseText);
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');
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
			
			$("#AlertMsg").html("&nbsp;&nbsp;Customer Required!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		document.getElementById("txtcust").focus();
		return false;
		
		ISOK = "NO";
	}
	// ACTIVATE MUNA LAHAT NG INFO
	
	$("#MyTable2 > tbody > tr").each(function() {				

		var itmcde = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
		
		$(this).find('input[name="txtinfofld"]').attr("disabled", false);
		$(this).find('input[name="txtinfoval"]').attr("disabled", false);
		$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");

	});

	// Check pag meron wla Qty na Order
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
			myfacx = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
			
			myprice = $(this).find('input[name="txtnamount"]').val();
			
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
		var ddate = $("#date_delivery").val();
		var ngross = $("#txtnGross").val();
		
		//alert("SR_newsavehdr.php?ccode=" + ccode + "&crem="+ crem + "&ddate="+ ddate + "&ngross="+ngross);
		
		$.ajax ({
			url: "SR_newsavehdr.php",
			data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross },
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW CREDIT MEMO: </b> Please wait a moment...");
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
			
				var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				var nident = $(this).find('input[type="hidden"][name="txtnident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var cuom = $(this).find('input[type="hidden"][name="seluom"]').val();
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nprice = $(this).find('input[name="txtnprice"]').val();
				var namt = $(this).find('input[name="txtnamount"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
				var creason = $(this).find('input[name="txtcreason"]').val();
				var nqty0 = $(this).find('input[type="hidden"][name="txtnqty0"]').val();
				var nprice0 = $(this).find('input[type="hidden"][name="txtnprice0"]').val();
			
				
				if(index>=1){
					//alert("SR_newsavedet.php?trancode="+trancode+"&crefno="+crefno + "&indx=" + index + "&citmno=" + citmno + "&cuom=" + cuom + "&nqty=" + nqty + "&nprice=" + nprice + "&namt=" + namt + "&mainunit=" + mainunit + "&nfactor=" + nfactor);
					
					$.ajax ({
						url: "SR_newsavedet.php",
						data: { trancode: trancode, crefno: crefno, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, mainunit:mainunit, nfactor:nfactor, creason:creason, ident:nident, nqty0:nqty0, nprice0: nprice0, ccode:ccode  },
						async: false,
						success: function( data ) {
							//alert(data.trim());
							
							if(data.trim()!="True"){
								isDone = "False";
								//alert(data.trim());
								
							}
						}
					});
					
				}
				
			});


			//Save Info
			$("#MyTable2 > tbody > tr").each(function(index) {	
			  
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				var citmfld = $(this).find('input[name="txtinfofld"]').val();
				var citmvlz = $(this).find('input[name="txtinfoval"]').val();
			
				if(index>=1){
					$.ajax ({
						url: "SR_newsaveinfo.php",
						data: { trancode: trancode, indx: index, citmno: citmno, citmfld: citmfld, citmvlz:citmvlz },
						async: false,
						success: function( data ) {
							if(data.trim()!="True"){
								isDone = "False";
								//alert(data.trim());
							}
						}
					});
				}
				
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