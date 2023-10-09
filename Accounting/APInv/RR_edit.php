<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SuppInv";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$employeeid = $_SESSION['employeeid'];
$company = $_SESSION['companyid'];

$poststat = "True";
$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'SuppInv_edit.php'");
if(mysqli_num_rows($sql) == 0){
	$poststat = "False";
}


if(isset($_REQUEST['txtctranno'])){
		$cpono = $_REQUEST['txtctranno'];
}
else{
		$cpono = $_REQUEST['txtcpono'];
	}

$sqlhead = mysqli_query($con,"select a.ctranno, a.ccode, a.cremarks, DATE_FORMAT(a.ddate,'%m/%d/%Y') as ddate, DATE_FORMAT(a.dreceived,'%m/%d/%Y') as dneeded, a.ngross, a.nbasegross, a.cpreparedby, a.lcancelled, a.lapproved, a.lprintposted, a.ccustacctcode, b.cname, a.crefsi, a.crefrr, a.ccurrencycode, a.ccurrencydesc, a.nexchangerate from suppinv a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.ctranno = '$cpono'");

	//function listcurrencies(){ //API for currency list
	//	$apikey = $_SESSION['currapikey'];
		
		//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");

		//if ( $json === false )
		//{
		  // return 1;
		//}else{

		//	$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
		//   return $json;
		//}
		
	//}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?= time();?>">
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

<body style="padding:5px">
<?php
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['ddate'];
		$DateNeeded = $row['dneeded'];
		$Gross = $row['ngross'];
		$nbasegross = $row['nbasegross'];
		$CustSI = $row['crefsi'];

		$RefRR = $row['crefrr'];

		$ccurrcode = $row['ccurrencycode'];
		$ccurrrate = $row['nexchangerate'];   
		$ccurrdesc = $row['ccurrencydesc'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
?>
<form action="RR_editsave.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>
        <div class="col-xs-6 nopadding"> Supplier's Invoice Details </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
    <?php
  if($lCancelled==1){
    echo "<font color='#FF0000'><b>CANCELLED</b></font>";
  }
  
  if($lPosted==1){
    echo "<font color='#FF0000'><b>POSTED</b></font>";
  }
  ?>
    </div>
        </legend>	
        <table width="100%" border="0">
  <tr>
    <tH>RR No.:</tH>
    <td colspan="2" style="padding:2px"><div class="col-xs-3 nopadding"><input type="text" class="form-control input-sm" id="txtcpono" name="txtcpono" width="20px" tabindex="1" value="<?= $cpono;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
      
      
      <input type="hidden" name="hdntranno" id="hdntranno" value="<?= $cpono;?>">
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?= $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?= $lCancelled;?>">
      
      <input type="hidden" name="hdnRRQtyAcc" id="hdnRRQtyAcc" value="<?= $AccRRQty;?>">
      <input type="hidden" name="hdnRRAmtAcc" id="hdnRRAmtAcc" value="<?= $AccRRAmt;?>">
      <input type="hidden" name="hdnwRefAPC" id="hdnwRefAPC" value="<?= $varwithref;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
    </td>
    <td style="padding:2px" align="center">
      
    </td>
    </tr>
  <tr>
    <tH width="100">Supplier:</tH>
    <td style="padding:2px">

			<div class="col-xs-12 nopadding">
				<div class="col-xs-3 nopadding">
					<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Supplier Code..." tabindex="1" value="<?= $CustCode;?>" readonly>
				</div>

				<div class="col-xs-8 nopadwleft">
					<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..."  size="60" autocomplete="off" value="<?= $CustName;?>">
				</div> 
			</div>

    </td>
    <tH width="150" style="padding:2px">Date Received:</tH>
    <td style="padding:2px">
    <div class="col-xs-11 nopadding">
		<input type='text' class="datepick form-control input-sm" id="date_received" name="date_received" value="<?= $DateNeeded; ?>" />

     </div>
    </td>
  </tr>
  <tr>
    <tH width="100">Remarks:</tH>
    <td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value="<?= $Remarks; ?>"></div></td>

		<tH width="150">Supplier SI:</tH>
    <td style="padding:2px;"><div class="col-xs-11 nopadding">
      <input type='text' class="form-control input-sm" id="txtSuppSI" name="txtSuppSI" value="<?= $CustSI; ?>" />
    </div></td>

    
  </tr>

	<tr>
    <tH width="100">&nbsp;</tH>
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
						
											//		$objcurrs = listcurrencies();
											//		$objrows = json_decode($objcurrs,true);
														
										//	foreach($objrows as $rows){
											//	if ($nvaluecurrbase==$rows['currencyCode']) {
											//		$nvaluecurrbasedesc = $rows['currencyName'];
											//	}
											$sqlhead=mysqli_query($con,"Select symbol as id, CONCAT(symbol,\" - \",country,\" \",unit) as currencyName, rate from currency_rate");
											if (mysqli_num_rows($sqlhead)!=0) {
												while($rows = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
							?>
										<option value="<?=$rows['id']?>" <?php if ($ccurrcode==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=$rows['currencyName']?></option>
									<?php
												}
											}
									?>
								<!--</select>-->
									<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?= $nvaluecurrbase; ?>"> 	
									<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?=$ccurrdesc?>">  
							</div>
							<div class="col-xs-2 nopadwleft"> 
								<!--  -->
								<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="<?=$ccurrrate?>">	 
							</div>

							<div class="col-xs-5" id="statgetrate" style="padding: 4px !important"> 
										
							</div>
		</div>
	</td>
	<tH width="100">Ref RR:</tH>
    <td style="padding:2px">
			<div class="col-xs-11 nopadding">
				<input type="text" class="form-control input-sm" id="txtrefrr" name="txtrefrr" width="20px" tabindex="2" value="<?=$RefRR?>">
			</div>
		</td>
  </tr>

	
  
    <tr>
    <td colspan="2">&nbsp;</td>
    <th style="padding:2px"><!--<span style="padding:2px">PURCHASE TYPE:</span>--></th>
    <td>&nbsp;</td>
    </tr>

  <tr>
    <td colspan="4">&nbsp;</td>
    </tr>
<tr>
    <td colspan="2">
			<input type="hidden" id="txtprodid" name="txtprodid">
   		<input type="hidden" id="txtprodnme" name="txtprodnme">
      <input type="hidden" name="hdnunit" id="hdnunit">
    </td>
    <td></td>
    <td>&nbsp;</td>

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
	
            <table id="MyTable" class="MyTable" cellpadding"3px" width="100%" border="0">
<thead>
								<tr>
									<!--<th style="border-bottom:1px solid #999">&nbsp;</th>-->
									<th style="border-bottom:1px solid #999">Code</th>
									<th style="border-bottom:1px solid #999">Description</th>
			            <th style="border-bottom:1px solid #999">UOM</th>
									<th style="border-bottom:1px solid #999">Qty</th>
									<th style="border-bottom:1px solid #999">Price</th>
									<th style="border-bottom:1px solid #999">Amount</th>
									<th style="border-bottom:1px solid #999">Total Amt in <?= $nvaluecurrbase; ?></th>
			                        <!--<th style="border-bottom:1px solid #999">Date Expired</th>
			                        <th style="border-bottom:1px solid #999">&nbsp;</th>-->  
								</tr>
	</thead>
								<tbody id="MyyTbltbody">
			                    </tbody>
			                    
						</table>
				</div>
			</div>

	</div>

<br>
<?php
	if($poststat=="True"){
?>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td rowspan="2">
    		<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
 
 
 				<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='RR.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>';" id="btnMain" name="btnMain">
					Back to Main<br>(ESC)
				</button>
   
    		<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='RR_new.php';" id="btnNew" name="btnNew">
					New<br>(F1)
				</button>

    		<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
					Undo Edit<br>(CTRL+Z)
   			</button>

				<?php
					$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'SuppInv_print'");

					if(mysqli_num_rows($sql) == 1){
					
				?>

   				<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?= $cpono;?>');" id="btnPrint" name="btnPrint">
						Print<br>(CTRL+P)
    			</button>

					<?php		
						}

					?>

					<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
						Edit<br>(CTRL+E)
					</button>
						
					<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
						Save<br>(CTRL+S)
					</button>
    
    </td>

		<td width="180px" align="right"><b>Gross Amount </b>&nbsp;&nbsp;</td>
    <td width="180px"> 
			<input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="<?=$nbasegross;?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="15">
		</td>
  </tr>
  <tr>
 	 <td width="180px" align="right"><b>Gross Amount in <?=$nvaluecurrbase; ?></b>&nbsp;&nbsp;</td>
      <td width="180px"> <input type="text" id="txtnGross" name="txtnGross" readonly value="<?=$Gross; ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="15">
		</td>
  </tr>
</table>
<?php
	}
?>
    </fieldset>
</form>
<?php
}
else{
?>
<form action="RR_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Receiving</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">RI NO.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-3"><input type="text" class="form-control input-sm" id="txtcpono" name="txtcpono" width="20px" tabindex="1" value="<?= $cpono;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>RI No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>

<!-- PRINT OUT MODAL-->
<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-contnorad">   
            <div class="modal-bodylong">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
        
               <iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:8.5in; display:block; margin:0px; padding:0px; border:0px"></iframe>
    
            	
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


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


<form method="post" name="frmedit" id="frmedit" action="RR_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>

</body>
</html>

<script type="text/javascript">
	$(document).keydown(function(e) {	 
	
	 if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='RR_new.php';
		}
	  }
	  else if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){ 
			e.preventDefault();
			return chkform();
		}
	  }
	  else if(e.keyCode == 69 && e.ctrlKey){//CTRL E
		if($("#btnEdit").is(":disabled")==false){
			e.preventDefault();
			enabled();
		}
	  }
	  else if(e.keyCode == 80 && e.ctrlKey){//CTRL+P
		if($("#btnPrint").is(":disabled")==false){
			e.preventDefault();
			printchk('<?= $cpono;?>');
		}
	  }
	  else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
		if($("#btnUndo").is(":disabled")==false){
			e.preventDefault();
			chkSIEnter(13,'frmpos');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			$("#btnMain").click();
		}
	  
	  }else if(e.keyCode == 88 && e.ctrlKey){ //CTRL X - Close Modal
			if($('#SerialMod').hasClass('in')==true){
		 		$("#btnClsSer").click();
			}

	  }

	});

$(document).ready(function() {
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY'
    });

			loaddetails();
		
			$("#txtcpono").focus();
		

			$("#txtnBaseGross").autoNumeric('init',{mDec:2});
			$("#txtnGross").autoNumeric('init',{mDec:2});

			disabled();


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
			return '<div style="border-top:1px solid gray; width: 300px"><span>'+ item.id + '</span><br><small>' + item.value + '</small></div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 
			$("#txtcust").val(item.value).change(); 
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
					   console.log(data);
					   $.each(data,function(index,item){

						$('#txtprodnme').val(item.desc); 
						$('#txtprodid').val(item.id); 
						$("#hdnunit").val(item.cunit); 
						//$("#hdnqty").val(item.nqty);
					//	$("#hdnqtyunit").val(item.cqtyunit);
						//alert(item.cqtyunit + ":" + item.cunit);
						//addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.xrefident);

						//nqty,nqtyorig,nprice,curramt,namount,nfactor,cmainunit,xref,nident
						myFunctionadd(item.totqty,item.totqty,item.nprice,item.namount,item.namount,item.nfactor,item.cqtyunit,item.xref,item.xrefident)

					 });

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

function myFunctionadd(nqty,nqtyorig,nprice,curramt,namount,nfactor,cmainunit,xref,nident){

	var itmcode = document.getElementById("txtprodid").value;
	var itmdesc = document.getElementById("txtprodnme").value;
	var itmunit = document.getElementById("hdnunit").value;
	//var dneeded= document.getElementById("date_received").value;
	
	var itmprice = nprice;
	var itmamnt = namount;
	var itmqty = nqty;
	var itmqtyorig = nqtyorig;
	var itmfactor = nfactor;
	var itmmainunit = cmainunit;
	var itmxref = xref;
	var itmident = nident;
	var itmbaseamt = curramt;


	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var uomoptions = "";
	
	uomoptions = "<input type='hidden' value='"+itmunit+"' name=\"seluom\" id=\"seluom"+lastRow+"\">"+itmunit;

	tditmbtn = "<td width=\"50\">  <input class='btn btn-info btn-xs' type='button' name='btninsitm' id='ins" + itmcode + "' value='insert' /> </td>";
	tditmcode = "<td width=\"120\">  <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+"<input type='hidden' value='"+itmxref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+itmident+"' name=\"txtnrefident\" id=\"txtnrefident\"> </td>";
	
	tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\"> " +  itmdesc + "</td>";
	
	tditmunit = "<td width=\"80\"> " + uomoptions + "</td>";
	
	tditmqty = "<td width=\"100\"> <input type='text' value='"+itmqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' readonly /> <input type='hidden' value='"+nqtyorig+"' name=\"txtnqtyORIG\" id=\"txtnqtyORIG"+lastRow+"\"> </td>";

	tditmprice = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+itmprice+"' class='numeric form-control input-xs' style='text-align:right'name=\"txtnprice\" id='txtnprice"+lastRow+"' autocomplete='off' onFocus='this.select();'> <input type='hidden' value='"+itmmainunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmfactor+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";
	
	tditmbaseamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+itmbaseamt+"' class='numeric form-control input-xs' style='text-align:right' name='txtntranamount' id='txtntranamount"+lastRow+"' readonly> </td>";

	tditmamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+itmamnt+"' class='numeric form-control input-xs' style='text-align:right' name='txtnamount' id='txtnamount"+lastRow+"' readonly> </td>";
	
	tditmdel = "<td width=\"80\" style=\"padding:1px\">  <input class='btn btn-danger btn-xs' type='button' name='btndelitm' id='del" + itmcode + lastRow +"' value='delete' /> </td>";

	$('#MyTable > tbody:last-child').append('<tr style=\"padding-top:1px\">'+tditmcode + tditmdesc + tditmunit + tditmqty + tditmprice + tditmbaseamount+ tditmamount  + '</tr>'); //tditmdel tditmbtn

									//$("#del"+itmcode+lastRow).on('click', function() {
									//	$(this).closest('tr').remove();
									//	ComputeGross();
									//});

									$("input.numeric").autoNumeric('init',{mDec:2});

									//$("input.numeric").numeric();
									//$("input.numeric").on("click", function () {
									//   $(this).select();
								//	});
									
									$("input.numeric").on("keyup", function () {										
										ComputeAmt($(this).attr('id'));
									  ComputeGross();
									});
									
									/*
									$("#seluom"+lastRow).on('change', function() {

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

		
		function ComputeAmt(nme){
			
			var disnme = nme.replace(/[0-9]/g, ''); // string only
			var r = nme.replace( /^\D+/g, ''); // numeric only
			var nnet = 0;
			var nqty = 0;
			
			nqty =  parseFloat($("#txtnqty"+r).val().replace(/,/g,''));
			nprc = parseFloat($("#txtnprice"+r).val().replace(/,/g,''));
			
			namt = nqty * nprc;
			namt = namt.toFixed(2);

			namt2 = namt * parseFloat($("#basecurrval").val());
			namt2 = namt2.toFixed(2);
		
			$("#txtnamount"+r).val(namt2);

			$("#txtntranamount"+r).val(namt);

			$("#txtntranamount"+r).autoNumeric('destroy');
			$("#txtnamount"+r).autoNumeric('destroy');

			$("#txtntranamount"+r).autoNumeric('init',{mDec:2});
			$("#txtnamount"+r).autoNumeric('init',{mDec:2});

		}

		function ComputeGross(){
			var rowCount = $('#MyTable > tbody tr').length;
			
			var gross = 0;
			var amt = 0;
			
			if(rowCount>=1){
				for (var i = 1; i <= rowCount; i++) {
					amt = $("#txtntranamount"+i).val().replace(/,/g,'');
					
					gross = gross + parseFloat(amt);
				}
			}

			gross = gross.toFixed(4);

			gross2 = gross * parseFloat($("#basecurrval").val());
			gross2 = gross2.toFixed(4);

			
			$("#txtnBaseGross").val(gross);

			$("#txtnGross").val(gross2);

			$("#txtnBaseGross").autoNumeric('destroy');
			$("#txtnGross").autoNumeric('destroy');

			$("#txtnBaseGross").autoNumeric('init',{mDec:2});
			$("#txtnGross").autoNumeric('init',{mDec:2});
			
		}

function chkprice(itmcode,itmunit){
	var result;
	var ccode = document.getElementById("txtcustid").value;
			
	$.ajax ({
		url: "th_checkitmprice.php",
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
		url: "th_checkitmfactor.php",
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
			url: "RR_editsave.php",
			data: myform,
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING RR: </b> Please wait a moment...");
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
				//alert(index);
				//if(index>0){
			
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
				
					
					//alert("trancode=" + trancode + "&indx=" + index + "&citmno=" + citmno + "&cuom=" + cuom + "&nqty=" + nqty + "&nprice=" + nprice + "&namt=" + namt + "&nbaseamt=" + nbaseamt + "&mainunit=" + mainunit + "&nfactor=" + nfactor + "&nqtyorig=" + nqtyOrig + "&xcref=" + xcref + "&crefidnt=" + crefidnt);
					
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

				//}
				
			});
			
			if(isDone=="True"){
				$("#AlertMsg").html("<b>SUCCESFULLY UPDATED: </b> Please wait a moment...");
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

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "RR_edit.php";
		document.getElementById(frm).submit();
	}
}

function disabled(){
	$("#frmpos :input").attr("disabled", true);
	
	
	$("#txtcpono").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);
}

function enabled(){
	var msgsx = "";
	
	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
			var msgsx = "POSTED"
			
				if(document.getElementById("hdnRRAmtAcc").value=="YES" && document.getElementById("hdnwRefAPC").value=="false"){
				
					var msgsx = "";
					
					$("#frmpos :input").attr("disabled", false);
				
					
					$("#txtctranno").attr("readonly", true);
					$("#txtctranno").val($("#hdnorigNo").val());
					
					$("#btnMain").attr("disabled", true);
					$("#btnNew").attr("disabled", true);
					$("#btnPrint").attr("disabled", true);
					$("#btnEdit").attr("disabled", true);	
					
					//readonly Amt if hdnRRAmt Acc No
					//if(document.getElementById("hdnRRAmtAcc").value=="NO"){
						//$("#MyTable > tbody > tr").each(function(index) {	
						
						//	var x = $(this).find('input[name="txtnamount"]');
							
						//	x.attr("readonly", true);
							
						//	var z = $(this).find('input[name="txtnprice"]');
							
						//	z.attr("readonly", true);
						
						//});
					//}

					//readonly Qty if hdnRRQty Acc No
				//	if(document.getElementById("hdnRRQtyAcc").value=="NO"){
						$("#MyTable > tbody > tr").each(function(index) {	
						
							var y = $(this).find('input[name="txtnqty"]');
							y.attr("readonly", true);

							var y2 = $(this).find('input[name="btninsitm"]');
							y2.attr("disabled", true);

							var y3 = $(this).find('input[name="btndelitm"]');
							y3.attr("disabled", true);													
						});
				//	}
					
				}

		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		
		if(msgsx != ""){
			document.getElementById("statmsgz").innerHTML = "<font style=\"font-size: x-small\">TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!</font>";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		
	}
	else{
		
		$("#frmpos :input").attr("disabled", false);
		
			$("#txtcpono").val($("#hdntranno").val());
			$("#txtcpono").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
	
	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{
		//   var url =  "RR_confirmprint.php?x="+x;
		var url = "RR_printv1.php?tranno="+x;
		  
		  $("#myprintframe").attr('src',url);


		$("#PrintModal").modal('show');

	}
}

function loaddetails(){
	//alert("th_loaddetails.php?id="+$("#txtcpono").val());
	$.ajax ({
		url: "th_loaddetails.php",
		data: { id: $("#txtcpono").val() },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			console.log(data);
			$.each(data,function(index,item){

				$('#txtprodnme').val(item.cdesc); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit); 
				//alert(item.nqty);

				//myFunctionadd(nqty,nprice,curramt,namount,nfactor,cmainunit,xref,nident)
				myFunctionadd(item.nqty,item.nqtyorig,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.cmainuom,item.xref,item.nident);
			});

		}
	});


		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");

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

