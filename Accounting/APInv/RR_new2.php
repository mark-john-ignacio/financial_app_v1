<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SuppInv2";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

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

	//access for items encoding
	$varitmenc = "";
	$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'SuppInv2'");
	if(mysqli_num_rows($sql) == 0){
				
		$varitmenc = "NO";
	}

	@$arrtaxlist = array();
	$gettaxcd = mysqli_query($con,"SELECT * FROM `vatcode` where compcode='$company' and ctype = 'Purchase' and cstatus='ACTIVE' order By cvatdesc"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['cvatcode'], 'ctaxdesc' => $row['cvatdesc'], 'nrate' => $row['nrate']); 
		}
	}

	@$arruomslist = array();
	$getfctrs = mysqli_query($con,"SELECT * FROM `items_factor` where compcode='$company' and cstatus='ACTIVE' order By nidentity"); 
	if (mysqli_num_rows($getfctrs)!=0) {
		while($row = mysqli_fetch_array($getfctrs, MYSQLI_ASSOC)){
			@$arruomslist[] = array('cpartno' => $row['cpartno'], 'nfactor' => $row['nfactor'], 'cunit' => $row['cunit']); 
		}
	}

	@$arrewtlist = array();
	$getewt = mysqli_query($con,"SELECT * FROM `wtaxcodes` WHERE compcode='$company'"); 
	if (mysqli_num_rows($getewt)!=0) {
		while($rows = mysqli_fetch_array($getewt, MYSQLI_ASSOC)){
			@$arrewtlist[] = array('ctaxcode' => $rows['ctaxcode'], 'nrate' => $rows['nrate']); 
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
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<link href="../../global/css/components.css?t=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<script src="../../include/FormatNumber.js"></script>
	<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>-->
	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<!--
--
-- FileType Bootstrap Scripts and Link
--
-->
<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px">
<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">  
<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">
<input type="hidden" value='<?=json_encode(@$arrewtlist)?>' id="hdnewtlist">

<form action="RR_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<input type="hidden" value="<?php echo $nCHKREFvalue;?>" name="hdnCHECKREFval" id="hdnCHECKREFval">
	<fieldset>
    <legend>Supplier's Invoice</legend>	       

	<ul class="nav nav-tabs">
		<li class="active"><a href="#jed">Supplier's Invoice Details</a></li>
		<li><a href="#attc">Attachments</a></li>
	</ul>

	<div class="tab-content">  

		<div id="jed" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px;">

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
					<tH width="100" style="padding:2px">SI Date:</tH>
					<td width="250" style="padding:2px"><div class="col-xs-11 nopadding">
						<input type='text' class="datepick form-control input-sm" id="date_received" name="date_received" />
					</div>
				
				</tr>
				
				<tr>
					<tH width="100">Remarks:</tH>
					<td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div></td>
					<tH>Supplier SI</tH>
					<td style="padding:2px;">
						<div class="col-xs-11 nopadding">
							<input type='text' class="form-control input-sm" id="txtSuppSI" name="txtSuppSI" required/>
						</div>
					</td>				
				</tr>
				
				<tr>
					<tH width="100"><b>Currency:</b></tH>
					<td style="padding:2px">
						<div class="col-xs-12 nopadding">
							<div class="col-xs-7 nopadding">
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
								<input type='text' class="required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="1">	 
							</div>

							<div class="col-xs-3" id="statgetrate" style="padding: 4px !important"> 
										
							</div>
						</div>
					</td>
					<tH>Ref RR:</tH>
					<td style="padding:2px">
						<div class="col-xs-11 nopadding">
							<input type="text" class="form-control input-sm" id="txtrefrr" name="txtrefrr" width="20px" tabindex="2" placeholder="Enter Receving Transaction No.">
						</div>
					</td>
				</tr>					

				<tr>
					<tH width="100">&nbsp;</tH>
					<td style="padding:2px">&nbsp;</td>
					<tH width="150" style="padding:2px">EWT Code:</tH>
					<td style="padding:2px">
						<div class="col-xs-11 nopadding">
							<select id="selewt" name="selewt[]" class="form-control input-sm selectpicker" multiple required tabindex="3">
								<?php
									foreach(@$arrewtlist as $rows){
										echo "<option value=\"".$rows['ctaxcode']."\" data-rate=\"".$rows['nrate']."\">".$rows['ctaxcode'].": ".$rows['nrate']."%</option>";
									}
								?>											
							</select>
						</div>
					</td>				
				</tr>
				
			</table>
		</div>	

		<div id="attc" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">

			<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
			<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
			<input type="file" name="upload[]" id="file-0" multiple />

		</div>
	</div>

	<hr>

			<div class="row nopadwdown">
				<div class="col-xs-4 nopadwdown">
					<b>Details</b>
				</div>
				<div class="col-xs-8 nopadwdown"> 

					<div class="col-xs-3 nopadding">
						<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." width="25" tabindex="4"  autocomplete="off">
					</div>
					<div class="col-xs-9 nopadwleft">
						<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL+F) Search Product Name..." size="80" tabindex="5" autocomplete="off">
					</div>
					<input type="hidden" name="hdnunit" id="hdnunit">
					<input type="hidden" name="hdncvat" id="hdncvat">
					<input type="hidden" name="hdnacctno" id="hdnacctno">  
					<input type="hidden" name="hdnacctid" id="hdnacctid"> 
					<input type="hidden" name="hdnacctdesc" id="hdnacctdesc">
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

				<table id="MyTable" class="MyTable" width="140%" cellpadding="1px">
					<thead>
						<tr>
							<!--<th style="border-bottom:1px solid #999">&nbsp;</th>-->
							<th style="border-bottom:1px solid #999">Code</th>
							<th width="250px" style="border-bottom:1px solid #999">Description</th>
							<th width="250px" style="border-bottom:1px solid #999" class="chkVATClass">VAT</th>
							<th style="border-bottom:1px solid #999">UOM</th>
							<th style="border-bottom:1px solid #999">Qty</th>
							<th style="border-bottom:1px solid #999">Price</th>
							<th style="border-bottom:1px solid #999">Amount</th>
							<th style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
							<th width="80px" style="border-bottom:1px solid #999" class="chkinctype">Acct Code</th>
							<th width="200px" style="border-bottom:1px solid #999" class="chkinctype">Acct Title</th>
							<!--<th style="border-bottom:1px solid #999">Date Expired</th>-->
							<!--<th style="border-bottom:1px solid #999">&nbsp;</th>-->
						</tr>
					</thead>
					<tbody id="MyyTbltbody">
					</tbody>
							
				</table>
			</div>

			<div class="row nopadwtop2x">
				<div class="col-xs-7">
					<div class="portlet">
						<div class="portlet-body">
							<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
							<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='RR.php';" id="btnMain" name="btnMain">
								Back to Main<br>(ESC)
							</button>

							<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">Save<br> (CTRL+S)</button>
						</div>
					</div>
				</div>
				<div class="col-xs-5">
					<div class="well">	
						<div class="row static-info align-reverse">
							<div class="col-xs-7 name">
								Vatable Purchase:
								<input type="hidden" id="txtnNetVAT" name="txtnNetVAT" value="0">
							</div>
							<div class="col-xs-4 value" id="divtxtnNetVAT">
								0.00
							</div>
						</div>
						<div class="row static-info align-reverse">
							<div class="col-xs-7 name">
								Non-Vatable Purchase:
								<input type="hidden" id="txtnExemptVAT" name="txtnExemptVAT" value="0">
							</div>
							<div class="col-xs-4 value" id="divtxtnExemptVAT"> 
								0.00
							</div>
						</div>
						<div class="row static-info align-reverse">
							<div class="col-xs-7 name">
								add VAT:
								<input type="hidden" id="txtnVAT" name="txtnVAT" value="0">
							</div>
							<div class="col-xs-4 value" id="divtxtnVAT">
								0.00
							</div>
						</div>
						<div class="row static-info align-reverse">
							<div class="col-xs-7 name">
								Total Purchase:
								<input type="hidden" id="txtnGrossBef" name="txtnGrossBef" value="0">
							</div>
							<div class="col-xs-4 value" id="divtxtnGrossBef"> 
								0.00
							</div>
						</div>
						<div class="row static-info align-reverse">
							<div class="col-xs-7 name">
								less EWT:
								<input type="hidden" id="txtnEWT" name="txtnEWT" value="0">
							</div>
							<div class="col-xs-4 value" id="divtxtnEWT"> 
								0.00
							</div>
						</div>
						<div class="row static-info align-reverse">
							<div class="col-xs-7 name">
								<b>Total Amount Payable: </b>
								<input type="hidden" id="txtnGross" name="txtnGross" value="0">
								<input type="hidden" id="txtnBaseGross" name="txtnBaseGross" value="0">								
							</div>
							<div class="col-xs-4 value" id="divtxtnGross" style="border-top: 1px solid #ccc">
								0.00
							</div>
						</div>
					</div>
				</div>
			</div>

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

<form method="post" name="frmedit" id="frmedit" action="<?=($varitmenc=="NO") ? "RR_edit.php": "RR_edit2.php" ?>">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>

</body>
</html>

<script type="text/javascript">

var xChkVatableStatus = "";


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

		$(".nav-tabs a").click(function(){
    		$(this).tab('show');
		});

		$("#selewt").select2({
			placeholder: "Select EWT Code..."
		});

		$("#file-0").fileinput({
			theme: 'fa5',
			showUpload: false,
			showClose: false,
			allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
			overwriteInitial: false,
			maxFileSize:100000,
			maxFileCount: 5,
			browseOnZoneClick: true,
			fileActionSettings: { showUpload: false, showDrag: false,}
		});

		$('body').on('focus',".cacctdesc", function(){
			var $input = $(".cacctdesc");

			var id = $(document.activeElement).attr('id');	
			var numid = id.replace("txtacctname","");

			$("#"+id).typeahead({
				items: 10,
				source: function(request, response) {
					$.ajax({
						url: "../../Sales/th_accounts.php",
						dataType: "json",
						data: {
							query: $("#"+id).val()
						},
						success: function (data) {
							console.log(data);
							response(data);
						}
					});
				},
				autoSelect: true,
				displayText: function (item) {
					return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.acct + '</span><br><small>' + item.name + '</small></div>';
				},
				highlighter: Object,
				afterSelect: function(item) { 

					$('#'+id).val(item.name).change(); 
					$("#txtacctno"+numid).val(item.id); 
					$("#txtacctcode"+numid).val(item.acct);

				}
			});

		});


		$.ajax({
			url : "../../include/th_xtrasessions.php",
			type: "Post",
			async:false,
			dataType: "json",
			success: function(data)
			{	
				console.log(data);
					$.each(data,function(index,item){
					xChkVatableStatus = item.chkcompvat;						   
				});
			}
		});

		if(xChkVatableStatus==1){
			$(".chkVATClass").show();	
		}
		else{
			$(".chkVATClass").hide();
		}

		$('.datepick').datetimepicker({
			format: 'MM/DD/YYYY',
			useCurrent: false,
			//minDate: moment(),
			defaultDate: moment(),
		});	

		$("#basecurrval").autoNumeric('init',{mDec:4});
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

				$("#selbasecurr").val(item.cdefaultcurrency).change(); //val

				$("#selterms").val(item.cterms).change();
			}
		});

		document.getElementById('txtcust').focus();

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

							var values = item.ewtcode;
							$('#selewt').val(values.split(',')).change();
								
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

									$("#hdnacctno").val(item.cacctno); 
									$("#hdnacctid").val(item.cacctid); 
									$("#hdnacctdesc").val(item.cacctdesc); 
									
									//$("#hdnqty").val(item.nqty);
								//	$("#hdnqtyunit").val(item.cqtyunit);
									//alert(item.cqtyunit + ":" + item.cunit);
									//addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.xrefident);

									//nqty,nprice,curramt,namount,nfactor,cmainunit,xref,nident
									myFunctionadd(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.cmainunit,item.xref,item.xrefident,item.ctaxcode,item.cewtcode,item.xrefPO,item.xrefidentPO,item.ladvancepay);

									$('#txtprodnme').val("").change(); 
									$('#txtprodid').val(""); 
									$("#hdnunit").val(""); 
									$("#hdnacctno").val("");
									$("#hdnacctid").val("");
									$("#hdnacctdesc").val("");

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

		$('#txtprodnme').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
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
					$("#hdncvat").val(item.ctaxcode);

					$("#hdnacctno").val(item.cacctno); 
					$("#hdnacctid").val(item.cacctid); 
					$("#hdnacctdesc").val(item.cacctdesc); 

					myFunctionadd(1,0,0,0,1,item.cunit,"","","","","","");

					$('#txtprodnme').val("").change(); 
					$('#txtprodid').val(""); 
					$("#hdnunit").val("");
					$("#hdncvat").val("");
					$("#hdnacctno").val(""); 
					$("#hdnacctid").val(""); 
					$("#hdnacctdesc").val(""); 
			}
		
		});


		$("#txtprodid").keydown(function(e){
			if(e.keyCode == 13){

				$.ajax({
					url:'get_productid.php',
					data: 'c_id='+ $(this).val(),                 
					success: function(value){
				
						var data = value.split(",");
						$('#txtprodid').val(data[0]);
						$('#txtprodnme').val(data[1]);
						$('#hdnunit').val(data[2]);
						$('#hdncvat').val(data[3]);
			
						$("#hdnacctno").val(data[4]); 
						$("#hdnacctid").val(data[5]); 
						$("#hdnacctdesc").val(data[6]); 

						myFunctionadd(1,0,0,0,1,data[2],"","","","","","");
						
						$("#txtprodid").val("");
						$("#txtprodnme").val("");
						$("#hdnunit").val("");
						$("#hdncvat").val("")
						$("#hdnacctno").val(""); 
						$("#hdnacctid").val(""); 
						$("#hdnacctdesc").val("");

						//closing for success: function(value){
					}
				}); 

		
			
			//if ebter is clicked
			}
			
		});

		$("#selewt").on("change", function(){ 
			ComputeGross();
		});

	});


function myFunctionadd(nqty,nprice,curramt,namount,nfactor,cmainunit,xref,nident,ctaxcode,cewtcode,itmxrefPO,itmidentPO,itemladvpay){

	var itmcode = document.getElementById("txtprodid").value;
	var itmdesc = document.getElementById("txtprodnme").value;
	var itmunit = document.getElementById("hdnunit").value;
	//var dneeded = document.getElementById("date_received").value;

	var itmacctno = $("#hdnacctno").val(); 
	var itmacctid = $("#hdnacctid").val(); 
	var itmacctnm = $("#hdnacctdesc").val();

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
	
	tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+"<input type='hidden' value='"+itmxref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+itmident+"' name=\"txtnrefident\" id=\"txtnrefident\"> <input type='hidden' value='"+itmxrefPO+"' name=\"txtcrefPO\" id=\"txtcrefPO\"> <input type='hidden' value='"+itmidentPO+"' name=\"txtnrefidentPO\" id=\"txtnrefidentPO\"> </td>";
	
	tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\"> " + itmdesc + "</td>";


	var tditmvats = "";
		if(xChkVatableStatus==1){ 

			var xz = $("#hdntaxcodes").val();
			taxoptions = "";
			$.each(jQuery.parseJSON(xz), function() { 

				if(ctaxcode==""){

					if($("#hdncvat").val()==this['ctaxcode']){
						isselctd = "selected";
					}else{
						isselctd = "";
					}

				}else{

					if(ctaxcode==this['ctaxcode']){
						isselctd = "selected";
					}else{
						isselctd = "";
					}

				}
				taxoptions = taxoptions + "<option value='"+this['ctaxcode']+"' data-id='"+this['nrate']+"' "+isselctd+">"+this['ctaxdesc']+"</option>";
			});

			tditmvats = "<td width=\"100\" nowrap style=\"padding:1px\"> <select class='form-control input-xs' name=\"selitmvatyp\" id=\"selitmvatyp"+lastRow+"\">" + taxoptions + "</select> </td>";

		}

	tditmunit = "<td width=\"50\"> &nbsp; " + uomoptions + "</td>";
	
	if(itmxref!==""){
		$qtystatx = "readonly";
	}else{

		$qtystatx = "";
	}
	tditmqty = "<td width=\"100\"> <input type='text' value='"+itmqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' "+$qtystatx+"/> <input type='hidden' value='"+itmqtyorig+"' name=\"txtnqtyORIG\" id=\"txtnqtyORIG"+lastRow+"\"> </td>";
	
	tditmprice = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+itmprice+"' class='numeric2 form-control input-xs' style='text-align:right'name=\"txtnprice\" id='txtnprice"+lastRow+"' autocomplete='off' onFocus='this.select();'> <input type='hidden' value='"+itmmainunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmfactor+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";

	tditmbaseamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+curramtz+"' class='numeric form-control input-xs' style='text-align:right' name='txtntranamount' id='txtntranamount"+lastRow+"' readonly> </td>";
	
	tditmamount = "<td width=\"100\" style=\"padding:1px\"> <input type='text' value='"+baseprice.toFixed(4)+"' class='numeric form-control input-xs' style='text-align:right' name='txtnamount' id='txtnamount"+lastRow+"' readonly> </td>";

	var tdglaccount = "<td nowrap><input type='text' value='"+itmacctid+"' class='form-control input-xs' name=\"txtacctcode\" id='txtacctcode"+lastRow+"' readonly> <input type='hidden' value='"+itmacctno+"' name=\"txtacctno\" id='txtacctno"+lastRow+"'> </td>";

	var tdgltitle = "<td nowrap style=\"padding-left:2px\"><input type='text' value='"+itmacctnm+"' class='cacctdesc form-control input-xs' name=\"txtacctname\" id='txtacctname"+lastRow+"'></td>";

	
	tditmdel = "<td width=\"80\" style=\"padding:1px\"> <input class='btn btn-danger btn-xs' type='button' id='del" + itmcode + "' value='delete' /> </td>";

	$('#MyTable > tbody:last-child').append('<tr style=\"padding-top:1px\">'+tditmcode + tditmdesc + tditmvats + tditmunit + tditmqty + tditmprice + tditmbaseamount+ tditmamount  + tdglaccount + tdgltitle +'</tr>'); //tditmdel tditmbtn


								//	$("#del"+itmcode).on('click', function() {
									//	$(this).closest('tr').remove();
									//	 ComputeGross();
								//	});

								
								//	$("input.numeric").numeric();
								//	$("input.numeric").on("click", function () {
								//	   $(this).select();
								//	});

								$("input.numeric2").autoNumeric('init',{mDec:4});
								$("input.numeric").autoNumeric('init',{mDec:2});

								$("input.numeric, input.numeric2").on("click", function () {
									$(this).select();
								});

								$("input.numeric, input.numeric2").on("keyup", function (e) {
									ComputeAmt($(this).attr('id'));
									ComputeGross();
									// tblnav(e.keyCode,$(this).attr('id'));
								});

								$("#selitmvatyp"+lastRow).on("change", function() {
									ComputeGross();
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
			var rowCount = $('#MyTable tr').length;

			var gross = 0;
			var nwvat = 0;
			var nvat = 0;
			var nwovat = 0;
			var totewt = 0;
			var xcrate = 0;
			var TotAmtDue = 0;

			var nvatble = 0;
			var vatzTot = 0;

			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {

					var slctdval = $("#selitmvatyp"+i+" option:selected").data('id'); //data-id is the rate

					if(parseFloat(slctdval)>0){
						nvatble = parseFloat($("#txtntranamount"+i).val().replace(/,/g,'')) / parseFloat(1 + (parseInt(slctdval)/100));
						nvat = nvatble * (parseInt(slctdval)/100);

						nwvat = nwvat + nvatble;
						vatzTot = vatzTot + nvat;
						
					}else{
						nwovat = nwovat + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
					}

					gross = gross + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
					
				}
							
			}

			//VATABLE
			$("#txtnNetVAT").val(nwvat);
			$("#divtxtnNetVAT").text(nwvat.toFixed(2));
			$("#divtxtnNetVAT").formatNumber();

			//NO VAT
			$("#txtnExemptVAT").val(nwovat);
			$("#divtxtnExemptVAT").text(nwovat.toFixed(2));
			$("#divtxtnExemptVAT").formatNumber();

			// ADD VAT
			$("#txtnVAT").val(vatzTot);
			$("#divtxtnVAT").text(vatzTot.toFixed(2));
			$("#divtxtnVAT").formatNumber();

			//TOTAL GROSS
			$("#txtnGrossBef").val(gross);
			$("#divtxtnGrossBef").text(gross.toFixed(2));
			$("#divtxtnGrossBef").formatNumber();

			// LESS EWT
			$xtotewrate = 0;
			ewtTotz = 0;
			$('#selewt > option:selected').each(function() {
				$xtotewrate = $xtotewrate + parseFloat($(this).data("rate"));
			});
			if(parseFloat($xtotewrate)>0){
				ewtTotz = (parseFloat(nwvat) + parseFloat(nwovat)) * ($xtotewrate/100);
			}
			$("#txtnEWT").val(ewtTotz);
			$("#divtxtnEWT").text(ewtTotz.toFixed(2));  
			$("#divtxtnEWT").formatNumber();


			//Total Amount
			$gettmtt = gross - parseFloat(ewtTotz);
			gross2 = $gettmtt * parseFloat($("#basecurrval").val().replace(/,/g,''));
			
			$("#txtnGross").val(gross2);
			$("#txtnBaseGross").val($gettmtt);
			$("#divtxtnGross").text($gettmtt.toFixed(2));		
			$("#divtxtnGross").formatNumber();

			
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

			myacctno = $(this).find('input[type="hidden"][name="txtacctno"]').val();

			inxrow = parseFloat(index) + 1;
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + inxrow;	
			}
			
			if(myprice == 0 || myprice == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + inxrow;	
			}

			if(myacctno == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Account Code required: row " + inxrow;	
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
		var formdata = new FormData($('#frmpos')[0])
		formdata.delete('upload[]')
		jQuery.each($('#file-0')[0].files, function(i, file){
			formdata.append('file-'+i, file);
		})

		//alert("RR_newsave.php?"+myform);
		$.ajax ({
			url: "RR_newsave.php",
			//data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross, ccustsi:ccustsi },
			data: formdata,
			cache: false,
			processData: false,
			contentType: false,
			method: 'post',
			type: 'post',
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

				var xcrefPO = $(this).find('input[type="hidden"][name="txtcrefPO"]').val();
				var crefidntPO = $(this).find('input[type="hidden"][name="txtnrefidentPO"]').val();

				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();

				var vatcode = $(this).find('select[name="selitmvatyp"]').val(); 
				var nrate = $(this).find('select[name="selitmvatyp"] option:selected').data('id'); 
				var ewtcode = $(this).find('select[name="selitmewtyp"]').val();
				var ewtrate = $(this).find('select[name="selitmewtyp"] option:selected').data('rate'); 


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

				var nacctno = $(this).find('input[type="hidden"][name="txtacctno"]').val();
				
				
				//$("#txtremarks").val("trancode="+ trancode+ "&indx=" + index+ "&citmno=" + citmno+ "&cuom=" + cuom+ "&nqty=" + nqty+ "&nprice=" + nprice+ "&namt=" + namt+ "&mainunit=" + mainunit+ "&nfactor=" + nfactor+ "&nqtyorig=" + nqtyOrig+ "&xcref=" + xcref+ "&crefidnt=" + crefidnt);
				
				if(nqty!==undefined){
					nqty = nqty.replace(/,/g,'');
					nprice = nprice.replace(/,/g,'');
					namt = namt.replace(/,/g,'');
					nbaseamt = nbaseamt.replace(/,/g,'');
				}

				//alert("trancode="+trancode+"&indx="+index+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+nprice+"&namt="+namt+"&nbaseamt="+nbaseamt+"&mainunit="+mainunit+"&nfactor="+nfactor+"&nqtyorig="+nqtyOrig+"&xcref="+xcref+"&crefidnt="+crefidnt+"&vatcode="+vatcode+"&nrate="+nrate+"&ewtcode="+ewtcode+"&ewtrate="+ewtrate+"&xcrefPO="+xcrefPO+"&crefidntPO="+crefidntPO);

				$.ajax ({
					url: "RR_newsavedet.php",  
					data: { trancode: trancode, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, nbaseamt:nbaseamt, mainunit:mainunit, nfactor:nfactor, nqtyorig:nqtyOrig, xcref:xcref, crefidnt:crefidnt, vatcode:vatcode, nrate:nrate, xcrefPO:xcrefPO, crefidntPO:crefidntPO, nacctno:nacctno },
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
