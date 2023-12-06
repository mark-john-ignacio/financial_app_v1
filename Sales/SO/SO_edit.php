<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "SO.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');
	require_once('../../Model/helper.php');

	$company = $_SESSION['companyid'];

	if(isset($_REQUEST['txtctranno'])){
		$txtctranno = $_REQUEST['txtctranno'];
	}
	else{
		$txtctranno = $_REQUEST['txtcsalesno'];
	}

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'SO_edit.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

//echo $ddeldate;

/*
function listcurrencies(){ //API for currency list
	$apikey = $_SESSION['currapikey'];
  
	//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
	//$obj = json_decode($json, true);

	$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
  
	return $json;
}

*/

	$gettaxcd = mysqli_query($con,"SELECT * FROM `taxcode` where compcode='$company' order By nidentity"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['ctaxcode'], 'ctaxdesc' => $row['ctaxdesc'], 'nrate' => $row['nrate']); 
		}
	}

	$getfctrs = mysqli_query($con,"SELECT * FROM `items_factor` where compcode='$company' and cstatus='ACTIVE' order By nidentity"); 
	if (mysqli_num_rows($getfctrs)!=0) {
		while($row = mysqli_fetch_array($getfctrs, MYSQLI_ASSOC)){
			@$arruomslist[] = array('cpartno' => $row['cpartno'], 'nfactor' => $row['nfactor'], 'cunit' => $row['cunit']); 
		}
	}

	@$arrname = array();
	$directory = "../../Components/assets/SO/{$company}_{$txtctranno}/";
	if(file_exists($directory)){
		@$arrname = file_checker($directory);
	}
	
	$xdetremlabel = "";
	$getremlabel = mysqli_query($con,"SELECT * FROM `parameters` where compcode='$company' and ccode='SO_DET_REM_LABEL'"); 
	if (mysqli_num_rows($getremlabel)!=0) {
		while($row = mysqli_fetch_array($getremlabel, MYSQLI_ASSOC)){
			$xdetremlabel = $row['cvalue']; 
		}
	}

	$setSman = "True";
	$getSmans = mysqli_query($con,"SELECT * FROM `salesman` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($getSmans)==0) {
		$setSman = "False";
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
<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../../include/autoNumeric.js"></script>
<!--
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>-->

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

<body style="padding:5px" onLoad="document.getElementById('txtcsalesno').focus(); ">
<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">  
<input type="hidden" value='<?=json_encode(@$arruomslist)?>' id="hdnitmfactors">

<?php
$sqlhead = mysqli_query($con,"select a.*,b.cname,b.cpricever, (TRIM(TRAILING '.' 
FROM(CAST(TRIM(TRAILING '0' FROM B.nlimit)AS char)))) as nlimit, c.cname as cdelname,
 d.cname as salesmaname from so a left join customers b on a.compcode=b.compcode and 
 a.ccode=b.cempid left join customers c on a.compcode=c.compcode and a.cdelcode=c.cempid
  left join salesman d on a.compcode=b.compcode and a.csalesman=d.ccode where a.ctranno 
  = '$txtctranno' and a.compcode='$company'");


if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		$DatePO = $row['dpodate'];
		$Gross = $row['ngross'];
		$cpricever = $row['cpricever'];
		$nlimit = $row['nlimit'];
    $cSITyp = $row['csalestype'];    
		$cCPONo = $row['cpono']; 

		$nbasegross = $row['nbasegross'];
		$ccurrcode = $row['ccurrencycode']; 
		$ccurrdesc = $row['ccurrencydesc']; 
		$ccurrrate = $row['nexchangerate']; 
		
		$salesmanid = $row['csalesman'];
		$salesmanme = $row['salesmaname'];
		$delcodes = $row['cdelcode'];
		$delname = $row['cdelname'];
		$delhousno = $row['cdeladdno'];
		$delcity = $row['cdeladdcity'];
		$delstate = $row['cdeladdstate'];
		$delcountry = $row['cdeladdcountry'];
		$delzip = $row['cdeladdzip'];
		$specins = $row['cspecins'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lVoid = $row['lvoid'];
	}
	
	
	if(!file_exists("../../imgcust/".$CustCode .".jpg")){
		$imgsrc = "../../images/blueX.png";
	}
	else{
		$imgsrc = "../../imgcust/".$CustCode .".jpg";
	}

?>
<form action="SO_edit.php" name="frmpos" id="frmpos" method="post">
	<fieldset>
    <legend><div class="col-xs-6 nopadding"> Sales Order Details </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
				<?php
			if($lCancelled==1){
				echo "<font color='#FF0000'><b>CANCELLED</b></font>";
			}
			
			if($lPosted==1){
				if($lVoid==1){
					echo "<font color='#FF0000'><b>VOIDED</b></font>";
				}else{
					echo "<font color='#FF0000'><b>POSTED</b></font>";
				}
			}
			?>
				</div>
		</legend>	
    
	<div class="col-xs-12 nopadwdown"><b>Sales Order Information</b></div>
	<ul class="nav nav-tabs">
			<li class="active"><a href="#home">Order Details</a></li>
			<li><a href="#menu1">Delivered To</a></li>
			<li><a href="#attc">Attachment</a></li>
	</ul>
 
	<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;text-align: left; overflow: inherit !important;">
			<div class="tab-content">
			
					<div id="home" class="tab-pane fade in active" style="padding-left:5px; padding-top: 10px;">
					
						<table width="100%" border="0">
							<tr>
								<tH>&nbsp;TRANS NO.:</tH>
								<td style="padding:2px">
									<div class="col-xs-3 nopadding">   
										<input type="text" class="form-control input-sm" id="txtcsalesno" name="txtcsalesno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
									
										<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
										<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
										<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>">
										&nbsp;&nbsp;
									<div id="statmsgz" style="display:inline"></div>
								</td>
								<tH>PO No.:</tH>
								<td style="padding:2px;">
									<div class="col-xs-11 nopadding">
										<input type='text' class="form-control input-sm" id="txtcPONo" name="txtcPONo" autocomplete="off" value="<?php echo $cCPONo; ?>" /> 
									</div>
								</td>
							</tr>

							<tr>
								<tH width="150">&nbsp;Customer:</tH>
								<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1" value="<?php echo $CustCode; ?>">
												<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
												<input type="hidden" id="hdnpricever" name="hdnpricever" value="<?php echo $cpricever;?>">
										</div>

										<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" value="<?php echo $CustName; ?>">
										</div> 
									</div>
								</td>
								<tH width="150">PO Date:</tH>
								<td style="padding:2px;">
								<div class="col-xs-11 nopadding">
										<input type='text' class="form-control input-sm" id="date_PO" name="date_PO" value="<?php echo date_format(date_create($DatePO),'m/d/Y'); ?>" />
								</div>
								</td>
							</tr>
							<tr>
								<tH width="150">&nbsp;Currency:</tH>
								<td style="padding:2px">
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
																		<option value="<?=$rows['id']?>" <?php if ($ccurrcode==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=$rows['currencyName']?></option>
																	<?php

																			}
																		}
																	?>
										</select>
										<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?php echo $nvaluecurrbase; ?>"> 	
										<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?php echo $ccurrdesc; ?>"> 
									</div>
									<div class="col-xs-2 nopadwleft">
										<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="<?php echo $ccurrrate; ?>">	 
									</div>
									<div class="col-xs-4" id="statgetrate" style="padding: 4px !important"> 
																
									</div>
								</td>
								<tH width="150">Delivery Date:</tH>
								<td style="padding:2px;">
								<div class="col-xs-11 nopadding">
										<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($Date),'m/d/Y'); ?>" />
								</div>
								</td>
							</tr>
							<tr>
								<tH>Remarks:</tH>
								<td style="padding:2px"><div class="col-xs-11 nopadding">
									<input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value="<?php echo $Remarks; ?>">
								</div></td>
								<tH width="150">Sales Type:</th>
								<td style="padding:2px">
									<div class="col-xs-11 nopadding">
										<select id="selsityp" name="selsityp" class="form-control input-sm selectpicker"  tabindex="1">
											<option value="Goods" <?php if($cSITyp=="Goods") { echo "selected"; } ?>>Goods</option>
											<option value="Services" <?php if($cSITyp=="Services") { echo "selected"; } ?>>Services</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<tH width="150">Special Instructions:</tH>
								<td rowspan="3" style="padding:2px"><div class="col-xs-11 nopadding">
									<textarea rows="3"  class="form-control input-sm" name="txtSpecIns"  id="txtSpecIns"><?php echo $specins; ?></textarea>
										</div>
								</td>
								<tH><div class="chklimit"><b>Credit Limit:</b></div></th>
								<td style="padding:2px"><div class="chklimit col-xs-10 nopadding" id="ncustlimit"><b><font size='+1'><?php echo $nlimit;?></font></b></div>
									<input type="hidden" id="hdncustlimit" name="hdncustlimit" value=""></td>
							</tr>
							<tr>	
								<td>&nbsp;</td>
								<td style="padding:2px"><div class="chklimit"><b>Balance:</b></div></td>
								<td style="padding:2px"><div class="chklimit col-xs-10 nopadding" id="ncustbalance"></div>
									<input type="hidden" id="hdncustbalance" name="hdncustbalance" value="">
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="vertical-align:top">&nbsp;</td>
							</tr>
							<tr>
								<tH width="150"><?=($setSman=="True") ? " Salesman:" : ""?></tH>
								<td style="padding:2px">
									<?php if($setSman=="True"){ ?>
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtsalesmanid" name="txtsalesmanid" class="form-control input-sm" placeholder="Salesman Code..." tabindex="1" value="<?php echo $salesmanid; ?>">
										</div>

										<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtsalesman" name="txtsalesman" width="20px" tabindex="1" placeholder="Search Salesman Name..."  size="60" autocomplete="off" value="<?php echo $salesmanme; ?>">
										</div> 
									</div>
								</td>
								<?php
									}
								?>
								<td>&nbsp;</td>
								<td style="padding:2px"><div class="chklimit col-xs-10 nopadding" id="ncustbalance2"></div>
									<div class="chklimit col-xs-10 nopadding" id="ncustbalance3"></div>
								</td>
							</tr>
						</table>

					</div>

					<div id="menu1" class="tab-pane fade in" style="padding-left:5px; padding-top: 10px;">
						<table width="100%" border="0">
							<tr>
								<td width="150"><b>Customer</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtdelcustid" name="txtdelcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1" value="<?php echo $delcodes; ?>">
										</div>
										<div class="col-xs-9 nopadwleft">
													<input type="text" class="form-control input-sm" id="txtdelcust" name="txtdelcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off" value="<?php echo $delname; ?>">
										</div> 
									</div>					
								</td>
							</tr>
							<tr>
								<td><button type="button" class="btn btn-primary btn-sm" tabindex="6" id="btnNewAdd" name="btnNewAdd">Select Address</button></td>
								<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  readonly="true"  value="<?php echo $delhousno; ?>" /></div></td>
							</tr>				
							<tr>
								<td>&nbsp;</td>
								<td colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-6 nopadding">
											<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off"  readonly="true"  value="<?php echo $delcity; ?>"/>
										</div>
																	
										<div class="col-xs-6 nopadwleft">
																		<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off"   readonly="true"  value="<?php echo $delstate; ?>"/>
										</div>
									</div>
								</td>
							</tr>				
							<tr>
								<td>&nbsp;</td>
								<td colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-9 nopadding">
											<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" readonly  value="<?php echo $delcountry; ?>"/>
										</div>
																
										<div class="col-xs-3 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off"  readonly="true"  value="<?php echo $delzip; ?>"/>
										</div>
									</div>
								</td>
							</tr>   
						</table>
					</div>

					<div id="attc" class="tab-pane fade in" style="padding-left: 5px; padding-top: 10px;">

						<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
						<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
						<input type="file" name="upload[]" id="file-0" multiple />

					</div>
			</div>
	</div>
<hr>
<div class="col-xs-12 nopadwdown"><b>Details</b></div>

<div class="col-xs-12 nopadwdown">
	    <input type="hidden" name="hdnqty" id="hdnqty">
      <input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
      <input type="hidden" name="hdnunit" id="hdnunit">
			<input type="hidden" name="hdnvat" id="hdnvat">
      
	<div class="col-xs-3 nopadding"><input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." tabindex="4"></div>
    <div class="col-xs-5 nopadwleft"><input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL + F) Search Product Name..." size="80" tabindex="5"></div>
</div>

						<div style="border: 1px solid #919b9c; height: 40vh; overflow: auto">
							<div id="tableContainer" class="alt2" dir="ltr" style="
								margin: 0px;
								padding: 3px;
								width: 1300px;
								height: 300px;
								text-align: left;">
		
								<table id="MyTable" class="MyTable table-sm table-bordered" border="1">
									<thead>
										<tr>
											<th width="100px" style="border-bottom:1px solid #999">Code</th>
											<th width="300px" style="border-bottom:1px solid #999">Description</th>
											<th width="100px" style="border-bottom:1px solid #999" id='tblAvailable'>Available</th>
											<th width="80px" style="border-bottom:1px solid #999" class="chkVATClass">VAT</th>
											<th width="80px" style="border-bottom:1px solid #999">UOM</th>
											<th width="80px" style="border-bottom:1px solid #999">Factor</th>
											<th width="80px" style="border-bottom:1px solid #999">Qty</th>
											<th width="100px" style="border-bottom:1px solid #999">Price</th>
											<th width="100px" style="border-bottom:1px solid #999">Amount</th>
											<th width="200px" style="border-bottom:1px solid #999"><?=$xdetremlabel?></th>
											<!--<th style="border-bottom:1px solid #999">Total Amt in <?//php echo $nvaluecurrbase; ?></th>-->
											<th style="border-bottom:1px solid #999">&nbsp;</th>
										</tr>	
										</thead>														
									<tbody class="tbody">
									</tbody>															
								</table>

							</div>
						</div>

		
		<table width="100%" border="0" cellpadding="3" style="margin-top: 5px">
			<tr>
				<td valign="top" width="70%">

					<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
					<?php
						if($poststat == "True"){
					?>
					<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='SO.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>';" id="btnMain" name="btnMain">Back to Main<br>(ESC)</button>

					<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='SO_new.php';" id="btnNew" name="btnNew">New<br>(F1)</button>

					<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">Quote<br>(Insert)</button>

					<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">Undo Edit<br>(CTRL+Z)
					</button>

					<?php
						$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'SO_print'");

						if(mysqli_num_rows($sql) == 1){
						
					?>
							<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $txtctranno;?>');" id="btnPrint" name="btnPrint">
					Print<br>(CTRL+P)
							</button>

					<?php		
						}

					?>
					
					<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">Edit<br>(CTRL+E)    </button>
					
					<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">Save<br>(CTRL+S)    </button>

					<?php
						}
					?>
				</td>	

				<td align="right" valign="top">
					
					<table width="90%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td nowrap align="right"><b>Net of VAT </b>&nbsp;&nbsp;</td>
							<td> <input type="text" id="txtnNetVAT" name="txtnNetVAT" readonly value="0" style="text-align:right; border:none;  background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="20"></td>
						</tr>
						<tr>
							<td nowrap align="right"><b>VAT </b>&nbsp;&nbsp;</td> 
							<td> <input type="text" id="txtnVAT" name="txtnVAT" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="20"></td>
						</tr>
						<tr>
							<td nowrap align="right"><b>Gross Amount </b>&nbsp;&nbsp;</td>
							<td> <input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="20" value="<?=$nbasegross; ?>"></td>
						</tr>
						<tr>
							<td nowrap align="right"><b>Gross Amount in <?php echo $nvaluecurrbase; ?></b>&nbsp;&nbsp;</td>
							<td> <input type="text" id="txtnGross" name="txtnGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="20" value="<?=$Gross; ?>"></td>
						</tr>
					</table>
				
				</td>
			</tr>
		</table>
		
</fieldset>
    
   
			<!-- Add Info -->
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


<!-- FULL PO LIST REFERENCES-->

<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="InvListHdr">PO List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
       <div class="col-xs-12 nopadding">

                <div class="form-group">
                    <div class="col-xs-4 nopadding pre-scrollable" style="height:37vh">
                          <table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
                           <thead>
                            <tr>
                              <th>Quote No</th>
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

								<input type="hidden" name="hdncurr" id="hdncurr">
								<input type="hidden" name="hdncurrate" id="hdncurrate">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End FULL INVOICE LIST -->

</form>

<?php
}
else{
?>
<form action="SO_edit.php" name="frmpos2" id="frmpos2">
  <fieldset>
   	<legend>Sales Order</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">TRANS NO.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>SO No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>

<!-- Address List -->
<div class="modal fade" id="MyAddModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader"> Address Lists </h3>           
			</div>
    
            <div class="modal-body">
                <table id="MyAddTble" class="table table-condensed" width="100%">
                	<thead>
    				<tr>
                    	<th style="border-bottom:1px solid #999">&nbsp;</th>
						<th style="border-bottom:1px solid #999">House No.</th>
						<th style="border-bottom:1px solid #999">City</th>
                        <th style="border-bottom:1px solid #999">State</th>
						<th style="border-bottom:1px solid #999">Country</th>
                        <th style="border-bottom:1px solid #999">Zip</th>
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

</body>
</html>

<script type="text/javascript">
var xChkBal = "";
var xChkLimit = "";
var xChkLimitWarn = "";

var xtoday = new Date();
var xdd = xtoday.getDate();
var xmm = xtoday.getMonth()+1; //January is 0!
var xyyyy = xtoday.getFullYear();

xtoday = xmm + '/' + xdd + '/' + xyyyy;

var file_name = <?= json_encode(@$arrname) ?>;
/**
 * Checking of list files
 */
file_name.map(({name, ext}) => {
		console.log("Name: " + name + " ext: " + ext)
})

var arroffice = new Array("xls","xlsx","doc","docx","ppt","pptx","csv");
var arrimg = new Array("jpg","png","gif","jpeg");

var list_file = [];
var file_config = [];
var extender;
/**
 * setting up an list of file and config of a file
 */
file_name.map(({name, ext}, i) => {
	list_file.push("https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/SO/<?=$company."_".$txtctranno?>/" + name)
	console.log(ext);

	if(jQuery.inArray(ext, arroffice) !== -1){
		extender = "office";
	} else if (jQuery.inArray(ext, arrimg) !== -1){
		extender = "image";
	} else if (ext == "txt"){
		extender = "text";
	} else {
		extender =  ext;
	}

	console.log(extender)
	file_config.push({
		type : extender, 
		caption : name,
		width : "120px",
		url: "th_filedelete.php?id="+name+"&code=<?=$txtctranno?>", 
		key: i + 1
	});
})

	<?php
		if($poststat == "True"){
	?>
	$(document).keydown(function(e) {	
			
	  if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='SO_new.php';
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
	  else if(e.keyCode == 80 && e.ctrlKey){//CTRL P
		if($("#btnPrint").is(":disabled")==false){
			e.preventDefault();
			printchk('<?php echo $txtctranno;?>');
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
			window.location.href='SO.php';
		}
	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false && $("#btnIns").is(":disabled")==false){
			openinv();
		}
	  }
	  else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
		   if($('#hdnvalid').val()!="NO"){
			e.preventDefault();
		  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
				$('#txtprodnme').focus();
				}
	   	}
		}
	});
	<?php
		}
	?>
	
	$(document).ready(function(e) {
			$(".nav-tabs a").click(function(){
    			$(this).tab('show');
			});

			if(file_name.length > 0){
				$('#file-0').fileinput({
					showUpload: false,
					showClose: false,
					allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
					overwriteInitial: false,
					maxFileSize:100000,
					maxFileCount: 5,
					browseOnZoneClick: true,
					fileActionSettings: { showUpload: false, showDrag: false, },
					initialPreview: list_file,
					initialPreviewAsData: true,
					initialPreviewFileType: 'image',
					initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/SO/<?=$company."_".$txtctranno?>/{filename}',
					initialPreviewConfig: file_config
				});
			} else {
				$("#file-0").fileinput({
					showUpload: false,
					showClose: false,
					allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
					overwriteInitial: false,
					maxFileSize:100000,
					maxFileCount: 5,
					browseOnZoneClick: true,
					fileActionSettings: { showUpload: false, showDrag: false, }
				});
			}
			

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
						   xChkLimit = item.chkcustlmt; //0 = Disable ; 1 = Enable
						   xChkLimitWarn = item.chklmtwarn; //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
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

		if(xChkBal==1){
			$("#tblAvailable").hide();
		}
		else{
			$("#tblAvailable").show();
		}


		if(xChkLimit==0){
			$(".chklimit").hide();
		}
		else{
			$(".chklimit").show();
		}

		loaddetails();
		loaddetinfo();
	
	  $('#txtprodnme').attr("disabled", true);
	  $('#txtprodid').attr("disabled", true);
	  
	disabled();
		
	$('#date_delivery, #date_PO').datetimepicker({
    format: 'MM/DD/YYYY'
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
				//$('#imgemp').attr("src",data[3]);
				$('#hdnpricever').val(data[2]);
				//deliveredto   
				$('#txtdelcustid').val(dInput);
				$('#txtdelcust').val(data[0]); 
				 
				$('#txtsalesmanid').val(data[10]);
				$('#txtsalesman').val(data[11]);
				
				$('#txtchouseno').val(data[5]);
				$('#txtcCity').val(data[6]);
				$('#txtcState').val(data[7]);
				$('#txtcCountry').val(data[8]);
				$('#txtcZip').val(data[9]);							
							
								
				$('#hdnvalid').val("YES");

				if(xChkLimit==1){

					var limit = data[1];
					if(limit % 1 == 0){
						limit = parseInt(limit);
					}
					
					limit = Number(limit).toLocaleString('en');
					
					$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
					$('#hdncustlimit').val(data[1]);
					
					checkcustlimit($(this).val(), data[1]);
				}
				
			}
			else{
				$('#txtcustid').val("");
				$('#txtcust').val("");
				//$('#imgemp').attr("src","../../images/blueX.png");
				$('#hdnpricever').val("");
				$('#txtdelcustid').val("");
				$('#txtdelcust').val(""); 
				 
				$('#txtsalesmanid').val("");
				$('#txtsalesman').val("");
				
				$('#txtchouseno').val("");
				$('#txtcCity').val("");
				$('#txtcState').val("");
				$('#txtcCountry').val("");
				$('#txtcZip').val("");				
				$('#hdnvalid').val("NO");
			}
		},
		error: function(){
			$('#txtcustid').val("");
			$('#txtcust').val("");
			//$('#imgemp').attr("src","../../images/blueX.png");
			$('#hdnpricever').val("");
				$('#txtdelcustid').val("");
				$('#txtdelcust').val(""); 
				 
				$('#txtsalesmanid').val("");
				$('#txtsalesman').val("");
				
				$('#txtchouseno').val("");
				$('#txtcCity').val("");
				$('#txtcState').val("");
				$('#txtcCountry').val("");
				$('#txtcZip').val("");			
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
				$('#txtdelcustid').val(item.id);
				$('#txtdelcust').val(item.value); 
				 
				$('#txtsalesmanid').val(item.csman);
				$('#txtsalesman').val(item.smaname);
				
				$('#txtchouseno').val(item.chouseno);
				$('#txtcCity').val(item.ccity);
				$('#txtcState').val(item.cstate);
				$('#txtcCountry').val(item.ccountry);
				$('#txtcZip').val(item.czip);			
			$('#hdnvalid').val("YES");
			
			$('#txtremarks').focus();

				if(xChkLimit==1){
					
					var limit = item.nlimit;
					if(limit % 1 == 0){
						limit = parseInt(limit);
					}

					limit = Number(limit).toLocaleString('en');					
					$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
					$('#hdncustlimit').val(item.nlimit);
					
					checkcustlimit(item.id, item.nlimit);

				}
			
			
		}
	
	});
	
	$("#txtsalesmanid").keydown(function(event){
		if(event.keyCode == 13){
		
			var dInput = this.value;
			
			$.ajax({
				type:'post',
				url:'../get_salesmanid.php',
				data: 'c_id='+ $(this).val(),                 
				success: function(value){
					if(value!=""){				 
						$('#txtsalesman').val(value);
					}
				}
			});
		}
	});
	
	$('#txtsalesman').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_salesman.php",
				dataType: "json",
				data: {
					query: $("#txtsalesman").val()
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
						
			$('#txtsalesman').val(item.value).change(); 
			$("#txtsalesmanid").val(item.id);
			
			
		}
	
	});
	
	$("#txtdelcustid").keydown(function(event){
		if(event.keyCode == 13){
		
			var dInput = this.value;
			
			$.ajax({
				type:'post',
				url:'../get_customerid.php',
				data: 'c_id='+ $(this).val(),                 
				success: function(value){
					if(value!=""){				 
						var data = value.split(":");

						$('#txtdelcust').val(data[0]); 
						
						$('#txtchouseno').val(data[5]);
						$('#txtcCity').val(data[6]);
						$('#txtcState').val(data[7]);
						$('#txtcCountry').val(data[8]);
						$('#txtcZip').val(data[9]);
					}
				}
			});
		}
	});
	
	$('#txtdelcust').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_customer.php",
				dataType: "json",
				data: {
					query: $("#txtdelcust").val()
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
						
			$('#txtdelcust').val(item.value).change(); 
			$("#txtdelcustid").val(item.id);			

			$('#txtchouseno').val(item.chouseno);
			$('#txtcCity').val(item.ccity);
			$('#txtcState').val(item.cstate);
			$('#txtcCountry').val(item.ccountry);
			$('#txtcZip').val(item.czip);
							
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
			$("#hdnvat").val(item.ctaxcode);
			
			myFunctionadd("","","","","","","","");
			ComputeGross();	

			$("#txtprodid").val("");
			$("#txtprodnme").val("");
			$("#hdnunit").val("");
			$("#hdnqty").val("");
			$("#hdnqtyunit").val("");
			
			
		}
	
	});


	$("#txtprodid").keyup(function(event){
		if(event.keyCode == 13){

		$.ajax({
      url:'../get_productid.php',
      data: 'c_id='+ $(this).val() + "&itmbal=" + xChkBal+"&styp="+ $("#selsityp").val(),                 
      success: function(value){
        var data = value.split(",");
        $('#txtprodid').val(data[0]);
        $('#txtprodnme').val(data[1]);
				$('#hdnunit').val(data[2]);
				$("#hdnqty").val(data[3]);
				$("#hdnqtyunit").val(data[4]);
				$("#hdnvat").val(data[6]);


				if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
					var isItem = "NO";
					var disID = "";
					
					$("#MyTable > tbody > tr").each(function() {	
						disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();

						if($("#txtprodid").val()==disID){
							
							isItem = "YES";

						}
					});	

				//if value is not blank
				}
				
				//if(isItem=="NO"){		

					myFunctionadd("","","","","","","","");
					ComputeGross();	
					
			//   }
			//   else{
					
			//		addqty();
			//	}
		
				$("#txtprodid").val("");
				$("#txtprodnme").val("");
				$("#hdnunit").val("");
				$("#hdnqty").val("");
				$("#hdnqtyunit").val("");
 
	    //closing for success: function(value){
	    }
        }); 

	
		 
		//if ebter is clicked
		}
		
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
  
  $("#btnNewAdd").on("click", function(){
		if($("#txtdelcustid").val()=="" || $("#txtdelcust").val()==""){
			alert("Select Delivery To Customer!");
		}else{
			$('#MyAddTble tbody').empty();
			//get addressses...
			$.ajax({
				url : "th_addresslist.php?id=" + $("#txtdelcustid").val() ,
				type: "GET",
				dataType: "JSON",
				success: function(data)
				{	
					console.log(data);
                    $.each(data,function(index,item){
						
						$("<tr>").append(
						$("<td>").html("<a onclick=\"trclickable('"+item.chouseno+"','"+item.ccity+"','"+item.cstate+"','"+item.ccountry+"','"+item.czip+"')\" style=\"cursor: pointer;\">Select</a>"),
						$("<td>").html(item.chouseno),
						$("<td>").html(item.ccity),
						$("<td>").html(item.cstate),
						$("<td>").html(item.ccountry),
						$("<td>").html(item.czip)
						).appendTo("#MyAddTble tbody");
											   
					});
						
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert(jqXHR.responseText);
				}					
			});
			
		
			 $("#MyAddModal").modal("show");// 
		}
	});

	$("#selbasecurr").on("change", function (){
			
		//	convertCurrency($(this).val());

		var dval = $(this).find(':selected').attr('data-val');

		$("#basecurrval").val(dval);
		$("#statgetrate").html("");
		recomputeCurr();
		
		});
		
		$("#basecurrval").on("keyup", function () {
			recomputeCurr();
		});
	

});

function checkcustlimit(id,xcred){
	//Check Credit Limit BALNCE here
	var xBalance = 0;
	var xinvs = 0;
	var xors = 0;
	
		$.ajax ({
			url: "../th_creditlimit.php",
			data: { id: id },
			async: false,
			dataType: "json",
			success: function( data ) {
											
				console.log(data);
				$.each(data,function(index,item){
					if(item.invs!=null){
						xinvs = item.invs;
					}
					
					if(item.ors!=null){
						xors = item.ors;
					}
					
				});
			}
		});
	
	//alert("("+parseFloat(xcred) +"-"+ parseFloat(xinvs)+") + "+parseFloat(xors));
		
	xBalance = (parseFloat(xcred) - parseFloat(xinvs)) + parseFloat(xors);
	$("#hdncustbalance").val(xBalance);
	
	
	
	if(xBalance > 0){
		xBalance = Number(xBalance).toLocaleString('en');
		$("#ncustbalance").html("<b><font size='+1'>"+xBalance+"</font></b>");
	}
	else{
		
		if(parseFloat(xcred) > 0){
			if(xChkLimitWarn==0) { //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
				$("#ncustbalance").html("<b><i><font color='red'>Max Limit Reached</font></i></b>");
			}
			else if(xChkLimitWarn==1) {
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
				$("#ncustbalance2").html("<b><i><font color='red' size='-1'>Delivery is blocked</font></i></b>");
			}
			else if(xChkLimitWarn==2) {
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
				$("#ncustbalance").html("<b><i><font color='red' size='-1'>ORDERS BLOCKED</font></i></b>");
				$("#btnSave").attr("disabled", true);
				$("#btnIns").attr("disabled", true);
				$('#txtprodnme').attr("disabled", true);
					$('#txtprodid').attr("disabled", true);

			}
		}
	}

}

function addItemName(qty,price,curramt,amt,factr,cref,nrefident,crmx){

	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

		var isItem = "NO";
		var disID = "";

			$("#MyTable > tbody > tr").each(function() {	
				disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();

				if($("#txtprodid").val()==disID){
					
					isItem = "YES";

				}
			});	

	 //if(isItem=="NO"){	
	 	myFunctionadd(qty,price,curramt,amt,factr,cref,nrefident,crmx);
		
		ComputeGross();	

	// }
	// else{

	//	addqty();	
			
	// }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		$("#hdnvat").val("");
		
	 }

}

function myFunctionadd(qty,pricex,curramt,amtx,factr,cref,nrefident,crmx){
	//alert("hello");
	var itmcode = $("#txtprodid").val();
	var itmdesc = $("#txtprodnme").val();
	var itmqtyunit = $("#hdnqtyunit").val();
	var itmqty = $("#hdnqty").val();
	var itmunit = $("#hdnunit").val();
	var itmccode = $("#hdnpricever").val();

	if(qty=="" && pricex=="" && amtx=="" && factr==""){
		var itmtotqty = 1;
		var price = chkprice(itmcode,itmunit,itmccode,xtoday);
		var curramtz = price;
		//var amtz = price;
		var factz = 1;
		var baseprice = curramtz * parseFloat($("#basecurrval").val());
		baseprice = baseprice.toFixed(4);
	}
	else{
		var itmtotqty = qty
		var price = pricex;
		var curramtz = curramt;
	//	var amtz = amtx;	
		var factz = factr;	
		var baseprice = amtx;
	}

	
	
	//alert(itmcode+","+itmunit+","+itmccode+","+xtoday);
		
		if(xChkBal==1){
			var avail = "";
		}
		else{

      if($("#selsityp").val()=="Goods"){
        if(parseFloat(itmqty)>0){
          var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='"+itmqty+"'> " + itmqty + " " + itmqtyunit +" </td>";
          var qtystat = "";
        }
        else{
          var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='0'> Unavailable </td>";
          var qtystat = "readonly";
         // itmtotqty = 0;
        }
      }else{
          var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='0'> NA </td>";
          var qtystat = "";
         // itmtotqty = 0;
      }
		}
		
	
		var xz = $("#hdnitmfactors").val();
		if(itmqtyunit==itmunit){
			isselctd = "selected";
		}else{
			isselctd = "";
		}
		var uomoptions = "<option value='"+itmqtyunit+"' data-factor='1' "+isselctd+">"+itmqtyunit+"</option>";

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

	if(cref==null){
		cref = ""
	}

	
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+nrefident+"' name=\"hdnrefident\" id=\"hdnrefident\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference\"></td>";
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	var tditmavail = avail;

	var tditmvats = "";
		if(xChkVatableStatus==1){ 
			
				var xz = $("#hdntaxcodes").val();
				taxoptions = "";
				$.each(jQuery.parseJSON(xz), function() { 
					if($("#hdnvat").val()==this['ctaxcode']){
						isselctd = "selected";
					}else{
						isselctd = "";
					}
					taxoptions = taxoptions + "<option value='"+this['ctaxcode']+"' data-id='"+this['nrate']+"' "+isselctd+">"+this['ctaxdesc']+"</option>";
				});

			tditmvats = "<td width=\"100\" nowrap> <select class='form-control input-xs' name=\"selitmvatyp\" id=\"selitmvatyp"+lastRow+"\">" + taxoptions + "</select> </td>";

		}

	var tditmunit = "<td width=\"100\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\" data-main='"+itmqtyunit+"'>"+uomoptions+"</select> </td>";

	isfactoread = "";
	if(itmqtyunit==itmunit){
		isfactoread = "readonly";
	}

	var tditmfactor = "<td width=\"100\" nowrap> <input type='text' value='"+factz+"' class='numeric form-control input-xs' style='text-align:right' name='hdnfactor' id='hdnfactor"+lastRow+"' "+isfactoread+"> </td>";

	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' data-v-min=\"1\" class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' "+qtystat+"> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> </td>";
		
	var tditmprice = "<td width=\"100\" nowrap> <input type='text' value='"+price+"' class='numeric2 form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' "+qtystat+" \"> </td>";

	var tditmbaseamount = "<td width=\"100\" nowrap> <input type='text' value='"+curramtz+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> <input type='hidden' value='"+baseprice+"' name=\"txtnamount\" id='txtnamount"+lastRow+"' readonly> </td>";

	var tditmremx = "<td><input type='text' value='"+crmx+"' class='form-control input-xs' name=\"txtcitmremx\" id='txtcitmremx"+lastRow+"'></td>";

	var tditmdel = "<td width=\90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + lastRow + "' value='delete'/></td>"; // &nbsp; <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/> 

	$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + tditmavail + tditmvats + tditmunit + tditmfactor + tditmqty + tditmprice + tditmbaseamount + tditmremx + tditmdel + '</tr>');

									$("#del"+lastRow).on('click', function() {
										$(this).closest('tr').remove();

										Reindex();
										ComputeGross();
									});

									$("input.numeric2").autoNumeric('init',{mDec:4});

									$("input.numeric").autoNumeric('init',{mDec:2});

									$("#selitmvatyp"+lastRow).on("change", function() {
										ComputeGross();
									});

									/*
                  $("input.numeric").numeric(
                    {negative: false}
                  );

                  $("input.numericdec").numeric(
                    {
                      negative: false,
                      decimalPlaces: 4
                    }
                  );
									*/

									$("input.numeric, input.numericdec").on("click", function () {
									   $(this).select();
									});
									
                  $("input.numeric, input.numericdec").on("keyup", function () {
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

									});
									
									ComputeGross();
									
									
}

function Reindex(){
			$("#MyTable > tbody > tr").each(function(index) {	
				tx = index + 1;

				$(this).find('select[name="seluom"]').attr("id","seluom"+tx);
				$(this).find('input[name="txtnqty"]').attr("id","txtnqty"+tx);
				$(this).find('input[name="txtnprice"]').attr("id","txtnprice"+tx);
				$(this).find('input[type="hidden"][name="txtnamount"]').attr("id","txtnamount"+tx);
				$(this).find('input[name="txtntranamount"]').attr("id","txtntranamount"+tx);
				$(this).find('input[type="hidden"][name="hdnmainuom"]').attr("id","hdnmainuom"+tx);
				$(this).find('input[name="hdnfactor"]').attr("id","hdnfactor"+tx); 
				$(this).find('input[name="del"]').attr("id","del"+tx);

				if(xChkVatableStatus==1){ 
					$(this).find('select[name="selitmvatyp"]').attr("id","selitmvatyp"+tx); 
				}

				$(this).find('input[name="txtcitmremx"]').attr("id","txtcitmremx"+tx);

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
			
			namt = nqty * nprc;
			namt = namt.toFixed(4);

			namt2 = namt * parseFloat($("#basecurrval").val());
			namt2 = namt2.toFixed(4);

			
			$("#txtntranamount"+r).val(namt);		

			$("#txtnamount"+r).val(namt2);

			$("#txtntranamount"+r).autoNumeric('destroy');
			//$("#txtnamount"+r).autoNumeric('destroy');

			$("#txtntranamount"+r).autoNumeric('init',{mDec:2});
			//$("#txtnamount"+r).autoNumeric('init',{mDec:2});


		}

		function ComputeGross(){
			var rowCount = $('#MyTable tr').length;
			
			var gross = 0;
			var nnet = 0;
			var vatz = 0;

			var nnetTot = 0;
			var vatzTot = 0;

			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
			
					if(xChkVatableStatus==1){  
						var slctdval = $("#selitmvatyp"+i+" option:selected").data('id');

						if(slctdval!=0){
							if(parseFloat($("#txtntranamount"+i).val().replace(/,/g,'')) > 0 ){

								nnet = parseFloat($("#txtntranamount"+i).val().replace(/,/g,'')) / parseFloat(1 + (parseInt(slctdval)/100));
								vatz = nnet * (parseInt(slctdval)/100);

								nnetTot = nnetTot + nnet;
								vatzTot = vatzTot + vatz;
							}
						}else{
							nnetTot = nnetTot + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
						}
					}else{

						nnetTot = nnetTot + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));

					}

					gross = gross + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
				}
			}

			gross2 = gross * parseFloat($("#basecurrval").val().replace(/,/g,''));

			$("#txtnNetVAT").val(nnetTot);
			$("#txtnVAT").val(vatzTot);
			$("#txtnGross").val(gross2);
			$("#txtnBaseGross").val(gross);

			$("#txtnNetVAT").autoNumeric('destroy');
			$("#txtnVAT").autoNumeric('destroy');			
			$("#txtnGross").autoNumeric('destroy');
			$("#txtnBaseGross").autoNumeric('destroy');

			$("#txtnNetVAT").autoNumeric('init',{mDec:2});
			$("#txtnVAT").autoNumeric('init',{mDec:2});
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
			var itmprice = $(this).find("input[name='txtnprice']").val().replace(/,/g,'');
			
			//alert(itmqty +" : "+ itmprice);
			
			TotQty = parseFloat(itmqty) + 1;
			$(this).find("input[name='txtnqty']").val(TotQty);
			
			TotAmt = TotQty * parseFloat(itmprice);
			$(this).find("input[name='txtntranamount']").val(TotAmt.toFixed(4)); 

			$("#txtntranamount"+r).autoNumeric('destroy');
			$("#txtntranamount"+r).autoNumeric('init',{mDec:2});


			namt2 = TotAmt * parseFloat($("#basecurrval").val());
			$(this).find("input[type='hidden'][name='txtnamount']").val(namt2.toFixed(4)); 

			//$("#txtnamount"+r).autoNumeric('destroy');
			//$("#txtnamount"+r).autoNumeric('init',{mDec:2});
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
				alert(citmno+"!="+itmcde);
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
			
	addinfo(itmcde,itmnme,"","");
	
	$('#MyDetModal').modal('show');
}

function addinfo(itmcde,itmnme,fldnme,cvlaz){
	//alert(itmcde+","+itmnme);
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;

	
	var tdinfocode = "<td><input type='hidden' value='"+itmcde+"' name='txtinfocode' id='txtinfocode"+lastRow+"'>"+itmcde+"</td>";
	var tdinfodesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmnme+"</td>"
	var tdinfofld = "<td><input type='text' name='txtinfofld' id='txtinfofld"+lastRow+"' class='form-control input-xs' value=\""+fldnme+"\"></td>";
	var tdinfoval = "<td><input type='text' name='txtinfoval' id='txtinfoval"+lastRow+"' class='form-control input-xs' value=\""+cvlaz+"\"></td>";
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

function openinv(){
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid customer!");
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
			$('#InvListHdr').html("Quote List: " + $('#txtcust').val())

			var xstat = "YES";
			
			//disable escape insert and save button muna
			
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
						$("#AlertMsg").html("No Quotations Available");
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

							xstat = "NO";
							
										$("#txtcustid").attr("readonly", false);
										$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
							$("<td id='td"+item.cpono+"' data-curr='"+item.ccurrencycode+"' data-rate='"+item.nexchangerate+"'>").text(item.cpono),
							$("<td>").text(item.ngross)
							).appendTo("#MyInvTbl tbody");
							
							
							$("#td"+item.cpono).on("click", function(){
								checkcurrency($(this).text(),$(this).data("curr"),$(this).data("rate"));
								//opengetdet($(this).text());
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

function checkcurrency(tranno,currcode,currrate){
	var cnttbl = $('#MyTable tr').length - 1;

	if(cnttbl>0){
		//check if same ng currency
		if(currcode!=$("#selbasecurr").val()){
			var xyz = confirm("Currency of the selected reference is different from the previous reference selected.\nIf you continue, total amount in "+$("#basecurrvalmain").val()+" will be computed base from the current currency selected.");

			if(xyz==true){
				//$("#selbasecurr").val(currcode).change();
				//$("#basecurrval").val(currrate);
				opengetdet(tranno);
			}
		}else{
			opengetdet(tranno);
		}
	}else{
		$("#hdncurr").val(currcode);
		$("#hdncurrate").val(currrate);
		opengetdet(tranno);
	}
}

function opengetdet(valz){
	var drno = valz;

	$("#txtrefSI").val(drno);

	$('#InvListHdr').html("Quote List: " + $('#txtcust').val() + " | Quote Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
	
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
							  alert("NO more items to add!")
						  }
						  else{
						  
							if (item.nqty>=1){
								if(item.navail>=1){
									var xxmsg = "<input type='checkbox' value='"+item.id+"' name='chkSales[]' data-id=\""+drno+"\">";
								}
								else{
									var xxmsg = "<font color='red'><b>X</b></font>";
								}
								
								$("<tr>").append(
								$("<td>").html(xxmsg),
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

	if($("#hdncurr").val()!=""){
		$("#selbasecurr").val($("#hdncurr").val()).change();
		$("#basecurrval").val($("#hdncurrate").val());
	}
	
   $("input[name='chkSales[]']:checked").each( function () {
	   
	
				var tranno = $(this).data("id");
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_qolistput.php?id=" + tranno + "&itm=" + id + "&itmbal=" + xChkBal,
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
							//alert(item.cqtyunit);
							addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref)
											   
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


function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "SO_edit.php";
		document.getElementById(frm).submit();
	}
}

function disabled(){

	$("#frmpos :input").attr("disabled", true);
	
	$("#txtcsalesno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){
	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
			if(document.getElementById("hdnvoid").value==1){
					var msgsx = "VOIDED";
				}else{
					var msgsx = "POSTED";
				}
		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		document.getElementById("statmsgz").innerHTML = "<font style=\"font-size: x-small\">TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!</font>";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{
		
		$("#frmpos :input").attr("disabled", false);
		
			
			$("#txtcsalesno").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
					
		ComputeGross();
		
		checkcustlimit($("#txtcustid").val(), $("#ncustlimit").text());
		

	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{

		  var url = "SO_confirmprint.php?x="+x;
		  
		  $("#myprintframe").attr('src',url);


		  $("#PrintModal").modal('show');

	}
}


function loaddetails(){
	//alert(xChkBal);
	
	$.ajax ({
		url: "th_loaddetails.php",
		data: { id: $("#txtcsalesno").val(), itmbal: xChkBal },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			console.log(data);
			$.each(data,function(index,item){

				$('#txtprodnme').val(item.desc); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit); 
				$("#hdnqty").val(item.nqty);
				$("#hdnqtyunit").val(item.cqtyunit);
				$("#hdnvat").val(item.ctaxcode);

				addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref,item.nident,item.cremarks)
			});

		}
	});

}

function loaddetinfo(){
	$.ajax ({
		url: "th_loaddetinfo.php",
		data: { id: $("#txtcsalesno").val() },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			console.log(data);
			$.each(data,function(index,item){

				addinfo(item.id,item.desc,item.fldnme,item.cvalue);

			});

		}
	});

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
	
	/*$("#MyTable2 > tbody > tr").each(function() {				

		var itmcde = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
		
		$(this).find('input[name="txtinfofld"]').attr("disabled", false);
		$(this).find('input[name="txtinfoval"]').attr("disabled", false);
		$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");

	});*/
	
	//alert(ISOK);


	// Check pag meron wla Qty na Order vs available inventory
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
			myav = $(this).find('input[type="hidden"][name="hdnavailqty"]').val();
			myfacx = $(this).find('input[name="hdnfactor"]').val();
			
			myprice = $(this).find('input[type="hidden"][name="txtnamount"]').val();
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
			}else{
				var myqtytots = parseFloat(myqty) * parseFloat(myfacx);
				
        if($("#selsityp").val()=="Goods"){
  				if(parseFloat(myav) < parseFloat(myqtytots)){
  					msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Not enough inventory: row " + index;
  				}
        }
			}
			
			//if(myprice == 0 || myprice == ""){
			//	msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
		//	}

			
		});
		
		if(msgz!=""){
			$("#AlertMsg").html("&nbsp;&nbsp;Details Error: "+msgz);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
			ISOK = "NO";
		}
	}


	// Check if Credit Limit activated (kung sobra)
	if(xChkLimit==1 && parseFloat($('#hdncustlimit').val()) > 0){
		if(parseFloat($("#txtnGross").val())>parseFloat($("#hdncustbalance").val())){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>ERROR: </b> Available Credit Limit is not enough!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
				
				return false;
				ISOK = "NO";


		}
	}
	
	
	if(ISOK == "YES"){
	var isDone = "True";
	
		//Saving the header
		var trancode = $("#txtcsalesno").val();
		var ccode = $("#txtcustid").val();
		var crem = $("#txtremarks").val();
		var ddate = $("#date_delivery").val();
		var ngross = $("#txtnGross").val().replace(/,/g,'');
		var csitype = $("#selsityp").val(); 
    var ccpono = $("#txtcPONo").val(); 

		var ncurrcode = $("#selbasecurr").val();
		var ncurrdesc = $("#selbasecurr option:selected").text();
		var ncurrrate = $("#basecurrval").val();
		var nbasegross = $("#txtnBaseGross").val().replace(/,/g,'');

		$("#hidcurrvaldesc").val($("#selbasecurr option:selected").text());

		var specins = $("#txtSpecIns").val();
		var salesman = $("#txtsalesmanid").val();
		var delcodes = $("#txtdelcustid").val();
		var delhousno = $("#txtchouseno").val();
		var delcity = $("#txtcCity").val();
		var delstate = $("#txtcState").val();
		var delcountry = $("#txtcCountry").val();
		var delzip = $("#txtcZip").val();
		
		//alert("SO_updatehdr.php?ccode=" + ccode + "&crem="+ crem + "&ddate="+ ddate + "&ngross="+ngross + "&selsityp="+csitype+"&ccpono="+ ccpono+"&salesman="+ salesman+"&delcodes="+ delcodes+"&delhousno="+ delhousno+"&delcity="+ delcity+"&delstate="+ delstate+"&delcountry="+ delcountry+"&delzip="+ delzip+"&specins="+ specins);
		//data: { id:trancode, ccode: ccode, crem: crem, ddate: ddate, ngross: ngross, selsityp: csitype, ccpono:ccpono, salesman:salesman, delcodes:delcodes, delhousno:delhousno, delcity:delcity, delstate:delstate, delcountry:delcountry, delzip:delzip, specins:specins },
		var myform = $("#frmpos").serialize();

		var formdata = new FormData($('#frmpos')[0]);
		formdata.delete('upload[]');
		jQuery.each($('#file-0')[0].files, function(i, file){
			formdata.append('file-'+i, file);
		})

		$.ajax ({
			url: "SO_updatehdr.php",
			data: formdata,
			cache: false,
			processData: false,
			contentType: false,
			type: 'post',
			method: 'post',
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING SALES ORDER: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				if(data.trim()!="False"){
					trancode = data.trim();
				}
				else{
					$("#AlertMsg").html(trancode);
				}
			}
		});
		
		if(trancode!=""){
			//Save Details
			$("#MyTable > tbody > tr").each(function(index) {	
			//alert(index);
				var nrefident = $(this).find('input[type="hidden"][name="hdnrefident"]').val();
				var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var cuom = $(this).find('select[name="seluom"]').val();
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nprice = $(this).find('input[name="txtnprice"]').val();
				var namt = $(this).find('input[type="hidden"][name="txtnamount"]').val();
				var nbaseamt = $(this).find('input[name="txtntranamount"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[name="hdnfactor"]').val(); 

				if(xChkVatableStatus==1){ 
					var vatcode = $(this).find('select[name="selitmvatyp"]').val(); 
					var nrate = $(this).find('select[name="selitmvatyp"] option:selected').data('id');
				}else{
					var vatcode = "";
					var nrate = 0;
				}

				var citmremx = $(this).find('input[name="txtcitmremx"]').val();

				if(nqty!==undefined){
					nqty = nqty.replace(/,/g,'');
					nprice = nprice.replace(/,/g,'');
					namt = namt.replace(/,/g,'');
					nbaseamt = nbaseamt.replace(/,/g,'');
				}
			
				//alert("SO_newsavedet.php?nrefident="+nrefident+"&trancode="+trancode+"&crefno="+crefno+"&indx="+index+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+nprice+"&namt="+namt+"&nbaseamt="+nbaseamt+"&mainunit="+mainunit+"&nfactor="+nfactor+"&vatcode="+vatcode+"&nrate="+nrate+"&citmremx="+citmremx);

				$.ajax ({
					url: "SO_newsavedet.php",
					data: { nrefident:nrefident, trancode: trancode, crefno: crefno, indx:index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, nbaseamt:nbaseamt, mainunit:mainunit, nfactor:nfactor, vatcode:vatcode, nrate:nrate, citmremx:citmremx },
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
			  
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				var citmfld = $(this).find('input[name="txtinfofld"]').val();
				var citmvlz = $(this).find('input[name="txtinfoval"]').val();
			
				$.ajax ({
					url: "SO_newsaveinfo.php",
					data: { trancode: trancode, indx: index, citmno: citmno, citmfld: citmfld, citmvlz:citmvlz },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});
			
			if(isDone=="True"){
				$("#AlertMsg").html("<b>SUCCESFULLY UPDATED: </b> Please wait a moment...");
				$("#alertbtnOK").hide();

					setTimeout(function() {
						$("#AlertMsg").html("");
						$('#AlertModal').modal('hide');
			
							//$("#txtcsalesno").val(trancode);
							$("#frmpos").submit();
			
					}, 3000); // milliseconds = 3seconds

				
			}
			
		}
		else{
				$("#AlertMsg").html("<b>ERROR: </b> There's a problem updating your transaction...");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
		}


	}

}

function trclickable(hsno,ccty,stt,ctry,zip){
	$('#txtchouseno').val(hsno);
	$('#txtcCity').val(ccty);
	$('#txtcState').val(stt);
	$('#txtcCountry').val(ctry);
	$('#txtcZip').val(zip);
	
	$("#MyAddModal").modal("hide");
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