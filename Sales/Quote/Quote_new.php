<?php
	if(!isset($_SESSION)){
	session_start();
	}
	$_SESSION['pageid'] = "Quote_new.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

	$ndcutdate = date("m/d/Y");
	$ndcutdate = date("m/d/Y", strtotime($ndcutdate . "+1 day"));

	//echo $_SESSION['chkitmbal']."<br>";
	//echo $_SESSION['chkcompvat'];

	////function listcurrencies(){ //API for currency list
		//$apikey = $_SESSION['currapikey'];
	
	//	$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
		//$obj = json_decode($json, true);
	
	//	return $json;
	//}

	$getfctrs = mysqli_query($con,"SELECT * FROM `items_factor` where compcode='$company' and cstatus='ACTIVE' order By nidentity"); 
	if (mysqli_num_rows($getfctrs)!=0) {
		while($row = mysqli_fetch_array($getfctrs, MYSQLI_ASSOC)){
			@$arruomslist[] = array('cpartno' => $row['cpartno'], 'nfactor' => $row['nfactor'], 'cunit' => $row['cunit']); 
		}
	}

	$postbilling = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'qotype_billing'");
	if(mysqli_num_rows($sql) == 0){
		$postbilling = "False";
	}


	$postquote = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'qotype_quote'");
	if(mysqli_num_rows($sql) == 0){
		$postquote = "False";
	}

	$lastremarksQ = "";
	$sqlno = mysqli_query($con,"select * from quote where compcode='$company' and quotetype='quote' Order By ddate DESC LIMIT 1");
	if (mysqli_num_rows($sqlno)!=0) {
		while($row = mysqli_fetch_array($sqlno, MYSQLI_ASSOC)){
			$lastremarksQ = $row['cremarks'];
		}
	}

	$lastremarksB = "";
	$sqlno = mysqli_query($con,"select * from quote where compcode='$company' and quotetype='billing' Order By ddate DESC LIMIT 1");
	if (mysqli_num_rows($sqlno)!=0) {
		while($row = mysqli_fetch_array($sqlno, MYSQLI_ASSOC)){
			$lastremarksB = $row['cremarks'];
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

	<link rel="stylesheet" href="../../include/summernote/summernote.css">
    
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
	-->

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<script src="../../include/summernote/summernote.js"></script>

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

<style>
	fieldset.scheduler-border {
	    border: 1px groove #ddd !important;
	    padding: 0 0.5em 0.5em 0.5em !important;
	    margin: 0 0 0 0 !important;
	    -webkit-box-shadow:  0px 0px 0px 0px #000;
	            box-shadow:  0px 0px 0px 0px #000;
	}

    legend.scheduler-border {
        font-size: 1.2em !important;
        font-weight: bold !important;
        text-align: left !important;
        width:auto;
        padding:0 10px;
        border-bottom:none;
    }

    input:required {
	  border: 1px solid #f18888 !important;
	  outline: none;
	}
</style>
</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
	<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">
	<input type="hidden" value='<?=$lastremarksQ?>' id="qrems">
	<input type="hidden" value='<?=$lastremarksB?>' id="brems">

<form action="Quote_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;" enctype="multipart/form-data">
	<fieldset>
    	<legend>New Quotation</legend>	

			<ul class="nav nav-tabs">
				<li class="active"><a href="#home">Quotation Details</a></li>
				<li><a href="#attc">Attachments</a></li>
			</ul>

				<div class="tab-content">

					<div id="home" class="tab-pane fade in active" style="padding-left: 5px; padding-top: 10px;">

						<div class="col-xs-12 nopadwtop">
							<div class="col-xs-2">
									<b>Quote Type</b>
								</div>
							<div class="col-xs-2 nopadding">
								<select id="selqotyp" name="selqotyp" class="form-control input-sm selectpicker"  tabindex="1">
									<?php						
										if($postquote=="True"){
									?>
									<option value="quote">Quote</option>
									<?php
										}
															
										if($postbilling=="True"){
									?>
									<option value="billing">Billing</option>
									<?php
										}

										$isdisblerecurr = "disabled='true'";
										if($postquote=="False" && $postbilling=="True"){
											$isdisblerecurr = "";
										}
									?>
								</select>
							</div>
							<div class="col-xs-2">
									<b>Reccur Every</b>
								</div>
							<div class="col-xs-2 nopadding">
								<select id="selrecurrtyp" name="selrecurrtyp" class="form-control input-sm selectpicker"  tabindex="1" <?=$isdisblerecurr?>>
									<option value="one">One Time Only</option>	
									<option value="weekly">Weekly</option>
									<option value="monthly">Monthly</option>
									<option value="quartertly">Quartertly</option>
									<option value="yearly">Yearly</option>
									<option value="semi_annual">Semi Annual</option>
								</select>
							</div>
							<div class="col-xs-2">
								<b>Sales Type</b>
							</div>
							<div class="col-xs-2 nopadding">
								<select id="selsityp" name="selsityp" class="form-control input-sm selectpicker"  tabindex="1">
									<option value="Goods">Goods</option>
									<option value="Services">Services</option>
								</select>
							</div>
						</div>

						<br>
						<fieldset class="scheduler-border">
							<legend class="scheduler-border">Customer Details</legend>

							<div class='col-xs-12 nopadding'>
								<div class="col-xs-2"><b>Customer</b></div>
								<div class="col-xs-1 nopadding">
									<input type="text" id="txtcustid" name="txtcustid" class="required form-control input-sm" placeholder="Code..." tabindex="1" required="true">
										<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
										<input type="hidden" id="hdnpricever" name="hdnpricever" value="">
								</div>
								<div class="col-xs-4 nopadwleft">
									<input type="text" class="required form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off" required="true">
								</div>
								<div class="col-xs-2"><b>Salutation</b></div>
								<div class="col-xs-3 nopadding"> 
									<input type="text" id="txtcontactsalut" name="txtcontactsalut" class="required form-control input-sm" placeholder="Salutation..." tabindex="1"  required="true" value="Dear Ma’am/Sir,">
								</div>
							</div>

							<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-2"><b>Contact Person</b></div>
								<div class="col-xs-1 nopadding"> 
									<button class="btn btn-sm btn-block btn-warning" name="btnSearchCont" id="btnSearchCont" type="button">Search</button>
								</div>
								<div class="col-xs-4 nopadwleft">
									<input type="text" id="txtcontactname" name="txtcontactname" class="required form-control input-sm" placeholder="Contact Person Name..." tabindex="1"  required="true">
								</div>
								<div class="col-xs-2"><b>Designation</b></div>
								<div class="col-xs-3 nopadding"> 
									<input type="text" id="txtcontactdesig" name="txtcontactdesig" class="form-control input-sm" placeholder="Designation..." tabindex="1">
								</div>
							</div>

							<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-2"><b>Department</b></div>
								<div class="col-xs-5 nopadding">
									<input type="text" id="txtcontactdept" name="txtcontactdept" class="form-control input-sm" placeholder="Department..." tabindex="1">
								</div>
								<div class="col-xs-2"><b>Email Address</b></div>
								<div class="col-xs-3 nopadding">
									<input type="text" id="txtcontactemail" name="txtcontactemail" class="required form-control input-sm" placeholder="Email Address..." tabindex="1" required="true">
								</div>
							</div>

						</fieldset>

						<br>
						<fieldset class="scheduler-border">
							<legend class="scheduler-border">Terms &amp; Conditions</legend>

							<div class='col-xs-12 nopadding'>
								<div class="col-xs-1"><b>Vat Type</b></div>
								<div class="col-xs-2 nopadwleft">
									<select class="form-control input-sm" name="selvattype" id="selvattype">
										<option value="VatEx">VAT Exclusive</option>
										<option value="VatIn">VAT Inclusive</option>
									</select>
								</div>
								<div class="col-xs-2" id="prcevallabel"><b>Price Validity</b></div>
								<div class="col-xs-2 nopadwleft">
									<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($ndcutdate),'m/d/Y'); ?>" />
								</div>
								<div class="col-xs-1"><b>Payment</b></div>
								<div class="col-xs-2 nopadwleft">
									<select class="form-control input-sm" name="selterms" id="selterms">
										<option value=''>N/A</option>
										<?php
											$sqlters = mysqli_query($con,"Select ccode, cdesc From groupings Where compcode='$company' and ctype='TERMS' and cstatus='ACTIVE'");
											while($row = mysqli_fetch_array($sqlters, MYSQLI_ASSOC)){
												echo "<option value='".$row['ccode']."'>".$row['cdesc']."</option>";
											}
										?>

									</select>
								</div>
							</div>

							<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-1"><b>Delivery</b></div>
								<div class="col-xs-9 nopadwleft">
									<input type='text' class="required form-control input-sm" id="txtdelinfo" name="txtdelinfo" value="5-15 days upon receipt of P.O for available items."  required="true"/>
								</div>
							</div>

							<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-1"><b>Service</b></div>
								<div class="col-xs-9 nopadwleft">
									<input type='text' class="required form-control input-sm" id="txtservinfo" name="txtservinfo" value="Free delivery/installation within Metro Manila / Cavite" required="true" />
								</div>
							</div>

							<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-1"><b>Remarks</b></div>
								<div class="col-xs-9 nopadwleft">

										<textarea rows="4" name="txtremarks" id="txtremarks" style="width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?=$lastremarksQ?></textarea>

								</div>
							</div>

							<div class='col-xs-12 nopadwtop'>
								<div class="col-xs-1"><b>Currency</b></div>
								<div class="col-xs-3 nopadwleft">
								<select class="form-control input-sm" name="selbasecurr" id="selbasecurr">							
								<?php
									$nvaluecurrbase = "";	
									$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_CURRENCY'"); 
									
										if (mysqli_num_rows($result)!=0) {
											$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
											
											$nvaluecurrbase = $all_course_data['cvalue']; 
												
										}
										else{
											$nvaluecurrbase = "";
										}
				
										//$objcurrs = listcurrencies();
									//	$objrows = json_decode($objcurrs, true);
												
									//foreach($objrows['results'] as $rows){

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
								<input type='hidden' id="currdesc" name="currdesc" value="">	
								
							</div>

							<div class="col-xs-1 nopadwleft">
								<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="1">	 
							</div>

							<div class="col-xs-5" id="statgetrate" style="padding: 4px !important"></div>
							
						</fieldset>
					
					</div>

					<div id="attc" class="tab-pane fade in" style="padding-left: 5px; padding-top: 10px;">
						<!-- Import Files Modal -->
							<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
							<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
							<input type="file" name="upload[]" id="file-0" multiple />
					</div>
					
				</div>


			<hr>
			<div class="col-xs-12 nopadwdown"><b>Details</b></div>

			<div class="col-xs-12 nopadwtop2x">

				<div class="col-xs-3 nopadwdown">
					<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." tabindex="4">

					<input type="hidden" name="hdnqty" id="hdnqty">
					<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
					<input type="hidden" name="hdnunit" id="hdnunit">

				</div>
				<div class="col-xs-5 nopadwleft">
					<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm" placeholder="Search Product Name..." size="80" tabindex="5">
				</div>
			</div>

				<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 1px solid #919b9c;width: 100%;height: 300px;text-align: left;overflow: auto">
		
							<table id="MyTable" class="MyTable table table-condensed" width="100%">
								<thead>
									<tr id="0">
										<th style="border-bottom:1px solid #999">Code</th>
										<th style="border-bottom:1px solid #999">Description</th>
										<th style="border-bottom:1px solid #999" id='tblAvailable'>Available</th>
										<th style="border-bottom:1px solid #999">UOM</th>
										<th style="border-bottom:1px solid #999">Factor</th>
										<th style="border-bottom:1px solid #999">Qty</th>									
										<th style="border-bottom:1px solid #999">Price</th>
										<th style="border-bottom:1px solid #999">Amount</th>
										<th style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
										<th style="border-bottom:1px solid #999">&nbsp;</th>
									</tr>
								</thead>       
								<tbody class="tbody">
								</tbody>                  
							</table>

			</div>

			<div class="col-xs-12 nopadwtop2x">
				<div class="col-xs-7">
						<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Quote.php';" id="btnMain" name="btnMain">
				Back to Main<br>(ESC)</button>

						
				<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">SAVE<br> (F2)</button>
				</div>	

				<div class="col-xs-2"  style="padding-top: 14px !important;">
						<b>TOTAL AMOUNT </b>
				</div>
				<div class="col-xs-3"  style="padding-top: 14px !important;">
					<input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
				</div>
			</div>  
			
			<div class="col-xs-12 nopadding">
				<div class="col-xs-7">
						
				</div>	

				<div class="col-xs-2">
						<b>TOTAL AMOUNT IN <?php echo $nvaluecurrbase; ?></b>
				</div>
				<div class="col-xs-3" >
				<input type="text" id="txtnGross" name="txtnGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
				</div>
			</div>

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
									<thead>
    				<tr>
						<th style="border-bottom:1px solid #999">Code</th>
						<th style="border-bottom:1px solid #999">Description</th>
                        <th style="border-bottom:1px solid #999">Field Name</th>
						<th style="border-bottom:1px solid #999">Value</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
						</thead>
					<tbody class="tbody">
                    </tbody>
                </table>
    
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



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


<form method="post" name="frmedit" id="frmedit" action="Quote_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>


</body>
</html>

<script type="text/javascript">
var xChkBal = "";

var xtoday = new Date();
var xdd = xtoday.getDate();
var xmm = xtoday.getMonth()+1; //January is 0!
var xyyyy = xtoday.getFullYear();

xtoday = xmm + '/' + xdd + '/' + xyyyy;


	$(document).keydown(function(e) {	
	
	  if(e.keyCode == 113) { //F2
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 27){ //ESC
		  e.preventDefault();
		  window.location.replace("Quote.php?f=");
	  }
	});

	
	$(document).ready(function(e) {

		$('#txtremarks').summernote({
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline', 'clear']],
				['fontname', ['fontname']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
			],
		});

		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});

	  //getRemarks();
		/*$("#file-0").fileinput({
				showUpload: false,
				showClose: false,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false,}
			});*/


		$("#txtnBaseGross").autoNumeric('init',{mDec:2});
		$("#txtnGross").autoNumeric('init',{mDec:2});
	
	
	   		$.ajax({
					url : "../../include/th_xtrasessions.php",
					type: "Post",
					async:false,
					dataType: "json",
					success: function(data)
					{	
					   console.log(data);
                       $.each(data,function(index,item){
						   xChkBal = item.chkinv; //0 = Check ; 1 = Dont Check
						   
					   });
					}
				});
	
		if(xChkBal==1){
			$("#tblAvailable").hide();
		}

	  $('#txtprodnme').attr("disabled", true);
	  $('#txtprodid').attr("disabled", true);


	  $('#date_delivery').datetimepicker({
      format: 'MM/DD/YYYY'
    });
	
		$("#txtcustid").keyup(function(event){
			if(event.keyCode == 13){
			
				var dInput = this.value;
				
					$.ajax({
						type:'post',
						url:'../get_customerid.php',
						data: 'c_id='+ dInput,                 
						success: function(value){
							//alert(value);
							if(value!=""){
								var data = value.split(":");
								$('#txtcust').val(data[0]);
								//$('#imgemp').attr("src",data[3]);
								$('#hdnpricever').val(data[2]);

								$("#selbasecurr").val(data[13]).change(); //val
								$("#basecurrvalmain").val($("#selbasecurr").data("val"));
												
								$('#hdnvalid').val("YES");

								getcontact(dInput);
								
							}
							else{
								$('#txtcustid').val("");
								$('#txtcust').val("");
								//$('#imgemp').attr("src","../../images/blueX.png");
								$('#hdnpricever').val("");
								
								$('#hdnvalid').val("NO");
							}
						},
						error: function(){
							$('#txtcustid').val("");
							$('#txtcust').val("");
							//$('#imgemp').attr("src","../../images/blueX.png");
							$('#hdnpricever').val("");
							
							$('#hdnvalid').val("NO");
						}
					});

			}
			
		});

	$('#txtcust, #txtcustid').on("blur", function(){
		if($('#hdnvalid').val()=="NO"){
		  $('#txtcust').attr("placeholder", "ENTER A VALID CUSTOMER FIRST...");
		  
		  $('#txtprodnme').attr("disabled", true);
		  $('#txtprodid').attr("disabled", true);
		}else{
			
		  $('#txtprodnme').attr("disabled", false);
		  $('#txtprodid').attr("disabled", false);
		  
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
			//$("#imgemp").attr("src",item.imgsrc);
			$("#hdnpricever").val(item.cver);

			$("#selbasecurr").val(item.cdefaultcurrency).change(); //val
			$("#basecurrvalmain").val($("#selbasecurr").data("val"));
			
			$('#hdnvalid').val("YES");
			
			$('#txtremarks').focus();

			getcontact(item.id);
			
			
		}
	
	});
	
	$('#txtprodnme').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_product.php",
				dataType: "json",
				data: { query: $("#txtprodnme").val(), itmbal: xChkBal, styp: $("#selsityp").val() },
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.desc+'</span</div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtprodnme').val(item.desc).change(); 
			$('#txtprodid').val(item.id); 
			$("#hdnunit").val(item.cunit); 
			$("#hdnqty").val(item.nqty);
			$("#hdnqtyunit").val(item.cqtyunit);
			
			addItemName();
			
			
		}
	
	});

	$("#txtprodid").keypress(function(event){
		if(event.keyCode == 13){

			$.ajax({
        url:'../get_productid.php',
        data: 'c_id='+ $(this).val()+"&itmbal="+xChkBal+"&styp="+ $("#selsityp").val(),                 
        success: function(value){
        	var data = value.split(",");
          $('#txtprodid').val(data[0]);
          $('#txtprodnme').val(data[1]);
					$('#hdnunit').val(data[2]);
					$("#hdnqty").val(data[3]);
					$("#hdnqtyunit").val(data[4]);


					if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
						//var isItem = "NO";
						//	var disID = "";
			
							/*
							$("#MyTable > tbody > tr").each(function() {	
								disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();

								if($("#txtprodid").val()==disID){
									
									isItem = "YES";

								}
							});	

						//if value is not blank
						}
						
						if(isItem=="NO"){		
							*/

							myFunctionadd();
							ComputeGross();	
		
							/*
							}
							else{
							
							addqty();
						}
						*/
		
						$("#txtprodid").val("");
						$("#txtprodnme").val("");
						$("#hdnunit").val("");
						$("#hdnqty").val("");
						$("#hdnqtyunit").val("");
 
	    
	    		} //closing for success: function(value){
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
		        url:'../get_contactinfonames.php',
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
			alert("Customer Required!");
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
		    if($(this).attr("class")=="disndesig"){
		    	$('#txtcontactdesig').val($(this).text());
		    }
		    if($(this).attr("class")=="disdept"){
		    	$('#txtcontactdept').val($(this).text());
		    }
		     if($(this).attr("class")=="disemls"){
		    	$("#txtcontactemail").val($(this).text());
		    }
		});

		$("#ContactModal").modal("hide");
  });

    $("#selsityp").on("change", function(){

    	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;

			if(lastRow > 0){
				var x = confirm("Changing this will erase all details!");
				if (x == true) {
					$("#MyTable").find("tr:gt(0)").remove();
				}
			}
			else{
				$("#MyTable").find("tr:gt(0)").remove();
			}
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

	$("#selqotyp").on("change", function (){
		var dval = $(this).find(':selected').val();

		

		 if(dval=="billing"){
		 	$("#selrecurrtyp").attr("disabled", false); 
		 	$("#selterms").attr("disabled", true); 
		 	$("#prcevallabel").html("<b>Due Date</b>");

			text = $("#brems").val();
			text = text.toString().replace(/"/g, "'");

			alert(text);
			$("#txtremarks").summernote("code",text);

		// 	xval = "<p>Kindly make all checks payable to <b>SERT TECHNOLOGY INC</b><br>For bank transfer please deposit to:</p><b><u>For MBTC acct:</u></b><br><b>Account Name: SERT TECHNOLOGY INC.</b><br><b>Account Number: 3547354509772</b><br><b>Bank Address: Mangghan,Gen. Trias, Cavite</b><br><br><i>Note: Please settle your account to prevent service interruptions.</i><br><i>Kindly disregards pass due notice if payments have been made.</i><div><br></div><br><br><br><br><br>";

		}else{
		 	$("#selrecurrtyp").attr("disabled", true); 
		 	$("#selterms").attr("disabled", false);
		 	$("#prcevallabel").html("<b>Price Validity</b>");

			text = $("#qrems").val();
			text = text.toString().replace(/"/g, "'")
			$("#txtremarks").summernote("code",text);

		// 	xval = "<b>WARRANTY </b>  : SERT Technology Inc. Guarantees the equipment and cable workmanship under this proposal to be free from defect for a period of Twelve (12) months from in service date. Provided that no party except  SERT Technology Inc. Trained technicians/enginners shall be entrusted with the service maintenance and repair of the equipment during the warranty period shall be at SERT Technology Inc. Defects arising from misuse, tampering, forced majored, such as fire earthquake, flood, lightning strike, and abnormal conditions are excluded from the above warranty.<br><br><br>If you have concern(s) please feel free to call at If you have concerns, please feel free to call at 02.8897.4830, 0917.503.1616, 0917.555.0849, 0917.551.3200.<br><br><br> Very truly yours,";
		}
		
		//getRemarks(dval)
	});
	

});

/*function getRemarks(type = $("selqotyp").find(":selected").val()) {
	let remarks = "";
		$.ajax({
			url: "../../System/th_loadQuotesPrint.php",
			dataType: 'json',
			async:false,
			success: function(res) {
				$.each(res,function(index,item){
					if(type == "billing")  {
						if(item.ccode == 'QUOTE_BILLING') {
							remarks = item.cdesc;
						}
					} else {
						if(item.ccode == 'QUOTE_RMKS') {
							remarks = item.cdesc;
						}
					}
					
					
				});     
			},
			error: function(msg) {
				console.log(msg)
			}
		})
		$("#txtremarks").summernote("code", remarks);
}*/

function getcontact(cid){

	$.ajax({
        url:'../get_contactinfo.php',
        data: 'c_id='+ cid,                 
        success: function(value){
        	if(value!=""){
        		if(value.trim()=="Multi"){
        			$("#btnSearchCont").click();
        		}else{
		            var data = value.split(":");
		            
		            $('#txtcontactname').val(data[0]);
		            $('#txtcontactdesig').val(data[1]);
					$('#txtcontactdept').val(data[2]);
					$("#txtcontactemail").val(data[3]);
        		}
        	}
		}
	});

}

function addItemName(){

	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

		/*
		var isItem = "NO";
		var disID = "";

			$("#MyTable > tbody > tr").each(function() {	
				disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();

				if($("#txtprodid").val()==disID){
					
					isItem = "YES";

				}
			});	

	 if(isItem=="NO"){	
		*/
	 	myFunctionadd();
		
		ComputeGross();	
	/*
	 }
	 else{

		addqty();	
			
	 }
	 */
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		
	 }

}

function myFunctionadd(){
	//alert("hello");
	var itmcode = $("#txtprodid").val();
	var itmdesc = $("#txtprodnme").val();
	var itmunit = $("#hdnunit").val();
	var itmqty = $("#hdnqty").val();
	var itmqtyunit = $("#hdnqtyunit").val();
	
	var itmccode = $("#hdnpricever").val();
	
	//alert(itmcode+","+itmunit+","+itmccode+","+xtoday);
	
	var price = chkprice(itmcode,itmunit,itmccode,xtoday);

	var baseprice = price * parseFloat($("#basecurrval").val());
	
		if(xChkBal==1){
			var avail = "";
		}
		else{
			var avail = "<td> " + itmqty + " " + itmqtyunit +" </td>";
		}
		
	
		var uomoptions = "";
		var xz = $("#hdnitmfactors").val();
		var uomoptions = "<option value='"+itmunit+"' selected data-factor='1'>"+itmunit+"</option>";

		$.each(jQuery.parseJSON(xz), function() { 
			if(itmcode==this['cpartno']){
				if(itmunit==this['cunit']){
					isselctd = "selected";
				}
				else{
					isselctd = ""; 
				}
				uomoptions = uomoptions + "<option value='"+this['cunit']+"' data-factor='"+this['nfactor']+"' "+isselctd+">"+this['cunit']+"</option>";

			}
		});			
										
	//var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	//var lastRow = tbl.length;

	tbl = $('#MyTable tr:last').attr('id');
	var lastRow = parseInt(tbl) + 1;

	 
	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+"</td>";
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	var tditmavail = avail;
	var tditmunit = "<td width=\"100\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\" data-main='"+itmunit+"'>"+uomoptions+"</select> </td>";
	var tditmfactor = "<td width=\"100\" nowrap> <input type='text' value='1' class='numeric form-control input-xs' style='text-align:right' name='hdnfactor' id='hdnfactor"+lastRow+"' readonly> </td>";
	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='1' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();'> <input type='hidden' value='"+itmunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'>  </td>";
		
	var tditmprice = "<td width=\"100\" nowrap> <input type='text' value='"+price.trim()+"' class='numeric2 form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' \"> </td>";
			
	var tditmbaseamount = "<td width=\"100\" nowrap> <input type='text' value='"+price.trim()+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> </td>";

	var tditmamount = "<td width=\"100\" nowrap> <input type='text' value='"+baseprice.toFixed(4)+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnamount\" id='txtnamount"+lastRow+"' readonly> </td>";
	
	var tditmdel = "<td width=\"90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + itmcode + "' value='delete' onClick=\"deleteRow(this);\"/> &nbsp; <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"','"+lastRow+"');\"/> </td>";


	$('#MyTable > tbody:last-child').append('<tr id="'+lastRow+'">'+tditmcode + tditmdesc + tditmavail + tditmunit + tditmfactor + tditmqty + tditmprice + tditmbaseamount + tditmamount+ tditmdel + '</tr>');

									$("#del"+itmcode).on('click', function() {
										$(this).closest('tr').remove();
									});

									$("input.numeric2").autoNumeric('init',{mDec:4});
									$("input.numeric").autoNumeric('init',{mDec:2});

									//$("input.numeric").numeric();
									$("input.numeric, input.numeric2").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric, input.numeric2").on("keyup", function () {
									   ComputeAmt($(this).attr('id'));
									   ComputeGross();
										});
									
									$("#seluom"+lastRow).on('change', function() {

										var xyz = chkprice(itmcode,$(this).val(),itmccode,xtoday);
										var mainuomdata = $(this).data("main");
										var fact = $(this).find(':selected').data('factor');
										
										if(fact!=0){
											$('#hdnfactor'+lastRow).val(fact);
										}

										if(mainuomdata!==$(this).val()){
											$('#hdnfactor'+lastRow).attr("readonly", false);
										}else{
											$('#hdnfactor'+lastRow).attr("readonly", true);
										}
										
										$('#txtnprice'+lastRow).val(xyz.trim());
										//alert($(this).attr('id'));
										ComputeAmt($(this).attr('id'));
										ComputeGross();
										
										//var fact = setfactor($(this).val(), itmcode);
										//alert(fact);
										
									});
									
									ComputeGross();
									
									
}

			
		function ComputeAmt(nme){
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val().replace(/,/g,'');
			nqty = parseFloat(nqty)
			nprc = $("#txtnprice"+r).val().replace(/,/g,'');
			nprc = parseFloat(nprc);
			
			namt = nqty * nprc;
			namt = namt.toFixed(4);

			namt2 = namt * parseFloat($("#basecurrval").val());
			namt2 = namt2.toFixed(4);

			
			$("#txtntranamount"+r).val(namt);		

			$("#txtnamount"+r).val(namt2);

			$("#txtntranamount"+r).autoNumeric('destroy');
			$("#txtnamount"+r).autoNumeric('destroy');

			$("#txtntranamount"+r).autoNumeric('init',{mDec:2});
			$("#txtnamount"+r).autoNumeric('init',{mDec:2});

		}

		function ComputeGross(){
			var rowCount = $('#MyTable tr').length;
			
			var gross = 0;
			var amt = 0;
			
			//	if(rowCount>1){
			//		for (var i = 1; i <= rowCount-1; i++) {
			//			amt = $("#txtntranamount"+i).val().replace(/,/g,'');
					
			//			gross = gross + parseFloat(amt);
			//		}
			//	}

			$("#MyTable > tbody > tr").each(function() {

				myid = this.id;

				amt = $("#txtntranamount"+myid).val().replace(/,/g,'');					
				gross = gross + parseFloat(amt);

			});

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

		/*
		function addqty(){

			var itmcode = document.getElementById("txtprodid").value;

			var TotQty = 0;
			var TotAmt = 0;
			
			$("#MyTable > tbody > tr").each(function() {	
			var disID = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
			
			//alert(disID);
				if(disID==itmcode){
					
					var itmqty = $(this).find("input[name='txtnqty']").val().replace(/,/g,'');
					var itmprice = $(this).find("input[name='txtnprice']").val().replace(/,/g,'');
					
					//alert(itmqty +" : "+ itmprice);
					
					TotQty = parseFloat(itmqty) + 1;
					$(this).find("input[name='txtnqty']").val(TotQty);
					
					TotAmt = TotQty * parseFloat(itmprice);
					$(this).find("input[name='txtnamount']").val(TotAmt);
				}

			});
			
			ComputeGross();

		}

		*/


		function viewhidden(itmcde,itmnme,ident){
			var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
			var lastRow2 = tbl.length-1;
			
			if(lastRow2>=1){
					$("#MyTable2 > tbody > tr").each(function() {	
					
						var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
						var citmnoident = $(this).find('input[type="hidden"][name="txtinforefident"]').val();

						//alert(citmno+"!="+itmcde);
						if(citmno!=itmcde && citmnoident!==ident){
							
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
					
			addinfo(itmcde,itmnme,ident);
			
			$('#MyDetModal').modal('show');
		}

		function addinfo(itmcde,itmnme,refident){
			//alert(itmcde+","+itmnme);
			var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
			var lastRow = tbl.length;

			
			var tdinfocode = "<td><input type='hidden' value='"+refident+"' name='txtinforefident' id='txtinforefident"+lastRow+"'><input type='hidden' value='"+itmcde+"' name='txtinfocode' id='txtinfocode"+lastRow+"'>"+itmcde+"</td>";
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
			
			$("#MyTable2 > tbody > tr").each(function(index) {	
					
				var citmfld = $(this).find('input[name="txtinfofld"]').val();
				var citmval = $(this).find('input[name="txtinfoval"]').val();
				
				if(citmfld=="" || citmval==""){
					isInfo = "FALSE";
				}
						
			});

			
			if(isInfo == "TRUE"){
				$('#MyDetModal').modal('hide');	}
			else{
				alert("Incomplete info values!");
			}
		}


function chkprice(itmcode,itmunit,ccode,datez){
	var result;
			
	$.ajax ({
		url: "../th_checkitmprice.php",
		data: { itm: itmcode, cust: ccode, cunit: itmunit, dte: datez },
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
		alert("Customer Required!");
		document.getElementById("txtcust").focus();	
		ISOK = "NO";

		return false;
	}

	$(".required").each( function() {
	    var check = $(this).val();

	    if(check == '') {
	    	alert("Please fill-up all fields in red textbox!");
	        ISOK = "NO";
	    }
	});

	// ACTIVATE MUNA LAHAT NG INFO
	
	$("#MyTable2 > tbody > tr").each(function() {				

		var itmcde = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
		
		$(this).find('input[name="txtinfofld"]').attr("disabled", false);
		$(this).find('input[name="txtinfoval"]').attr("disabled", false);
		$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");

	});


	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	if(lastRow == 0){
		alert("No details found!");
		ISOK = "NO";
		return false;
	}
	else{
		var msgz = "";
		var myqty = "";
		
		$("#MyTable > tbody > tr").each(function() {
			myqty = $(this).find('input[name="txtnqty"]').val();
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "\n Zero or blank qty is not allowed: row " + z;	
			}
			
		});
		
		if(msgz!=""){
			alert("Details Error: "+msgz);
			ISOK = "NO";
			return false;
		}
	}


	
	if(ISOK == "YES"){
	var trancode = "";
	var isDone = "True";
	
		//Saving the header
		$("#currdesc").val($("#selbasecurr option:selected").text());
		$("#txtremarks").summernote('destroy');

		var formdata = new FormData($("#frmpos")[0]);
		

		/**
		 * @property JQuery formulate every file to compose to formdata 
		 * @property formdata.delete('#upload') delete an upload key without values
		 */
		formdata.delete('upload[]');
		jQuery.each(jQuery('#file-0')[0].files, function(i, file) {
			formdata.append('file-'+i, file);
		});
		
		$.ajax ({
			url: "Quote_newsavehdr.php",
			data: formdata,
			cache: false,
			processData: false,
			contentType: false,
			method: 'post',
			type: 'post',
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW QUOTATION: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				if(data.trim()!="False"){
					trancode = data.trim();
				}
				else{
					$("#AlertMsg").html(data.trim());
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');
				}
			},
			error: function (request, error) {
			console.log(arguments);
			alert(" Can't do because: " + error);
			}
			});

	//	alert(trancode);
		

		
		if(trancode!=""){
			//Save Details
			$("#MyTable > tbody > tr").each(function(index) {	
			
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var cuom = $(this).find('select[name="seluom"]').val();
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nprice = $(this).find('input[name="txtnprice"]').val();
				var nbaseamt = $(this).find('input[name="txtntranamount"]').val();
				var namt = $(this).find('input[name="txtnamount"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[name="hdnfactor"]').val();
			

				if(nqty!==undefined){
					nqty = nqty.replace(/,/g,'');
					nprice = nprice.replace(/,/g,'');
					namt = namt.replace(/,/g,'');
					nbaseamt = nbaseamt.replace(/,/g,'');
					nfactor = nfactor.replace(/,/g,'');
				}

				//alert("Quote_newsavedet.php?trancode="+trancode+"&indx="+this.id+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+nprice+"&nbaseamt="+nbaseamt+"&namt="+namt+"&mainunit="+mainunit+"&nfactor="+nfactor);

				$.ajax ({
					url: "Quote_newsavedet.php",
					data: { trancode: trancode, indx:this.id, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, nbaseamt:nbaseamt, namt:namt, mainunit:mainunit, nfactor:nfactor },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});


			//Save Info
			$("#MyTable2 > tbody > tr").each(function(index) {	
			  
				var nrefidx = $(this).find('input[type="hidden"][name="txtinforefident"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				var citmfld = $(this).find('input[name="txtinfofld"]').val();
				var citmvlz = $(this).find('input[name="txtinfoval"]').val();
			
				$.ajax ({
					url: "Quote_newsaveinfo.php",
					data: { trancode: trancode, indx: index, nrefidx:nrefidx, citmno: citmno, citmfld: citmfld, citmvlz:citmvlz },
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
				$("#AlertMsg").html("<b>ERROR: </b> There's a problem saving your transaction...");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
		}
		


	}

}

/*
function convertCurrency(fromCurrency) {
  
 	toCurrency = $("#basecurrvalmain").val(); //statgetrate
  	$.ajax ({
		url: "../th_convertcurr.php",
		data: { fromcurr: fromCurrency, tocurr: toCurrency },
		async: false,
		beforeSend: function () {
			$("#statgetrate").html(" <i>Getting exchange rate please wait...</i>");
		},
		success: function( data ) {

			$("#basecurrval").val(data);

		},
		complete: function(){
			$("#statgetrate").html("");
			recomputeCurr();
		}
	});

}
*/

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



</script>