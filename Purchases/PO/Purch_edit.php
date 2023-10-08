<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Purch.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');
	require_once('../../Model/helper.php');

	$company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Purch_edit.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	if(isset($_REQUEST['txtctranno'])){
		$cpono = $_REQUEST['txtctranno'];
	}
	else{
		$cpono = $_REQUEST['txtcpono'];
	}

		$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='ALLOW_REF_PR'"); 
												
		if (mysqli_num_rows($result)!=0) {
			$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);											
			$xAllowPR = $all_course_data['cvalue']; 												
		}
		else{
			$xAllowPR = "";
		}


	@$arrewtlist = array();
	$getewt = mysqli_query($con,"SELECT * FROM `wtaxcodes` WHERE compcode='$company'"); 
	if (mysqli_num_rows($getewt)!=0) {
		while($rows = mysqli_fetch_array($getewt, MYSQLI_ASSOC)){
			@$arrewtlist[] = array('ctaxcode' => $rows['ctaxcode'], 'nrate' => $rows['nrate']); 
		}
	}

	$gettaxcd = mysqli_query($con,"SELECT * FROM `taxcode` where compcode='$company' order By nidentity"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['ctaxcode'], 'ctaxdesc' => $row['ctaxdesc'], 'nrate' => $row['nrate']); 
		}
	}

	$sqlhead = mysqli_query($con,"select a.cpono, a.ccode, a.cremarks, DATE_FORMAT(a.ddate,'%m/%d/%Y') as ddate, DATE_FORMAT(a.dneeded,'%m/%d/%Y') as dneeded, a.ngross, a.cpreparedby, a.nbasegross, a.ccurrencycode, a.ccurrencydesc, a.nexchangerate, a.lcancelled, a.lapproved, a.lprintposted, a.lvoid, a.ccustacctcode, b.cname, a.ccontact, a.ccontactemail, a.ladvancepay, a.cterms, a.cdelto, a.ddeladd, a.ddelinfo, a.cbillto, a.cewtcode from purchase a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.cpono = '$cpono'");


	@$arrname = array();
	$directory = "../../Components/assets/PO/{$company}_{$cpono}/";
	if(file_exists($directory)){
		@$arrname = file_checker($directory);
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
<script src="../../Bootstrap/js/bootstrap-datetimepicker.js"></script>

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

	<input type="hidden" value='<?=json_encode(@$arrewtlist)?>' id="hdnewtlist"> 
	<input type="hidden" value='<?=json_encode(@$arrtaxlist)?>' id="hdntaxcodes">


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
		$ccurrcode = $row['ccurrencycode']; 
		$ccurrdesc = $row['ccurrencydesc']; 
		$ccurrrate = $row['nexchangerate']; 
		$cpaytype = $row['ladvancepay']; 
		$cpayterms = $row['cterms']; 

		$cewtcode = $row['cewtcode']; 

		$ccontact = $row['ccontact']; 
		$ccontactemail = $row['ccontactemail']; 

		$cterms = $row['cterms']; 
		$delto = $row['cdelto']; 
		$deladd = $row['ddeladd']; 
		$delinfo = $row['ddelinfo']; 
		$billto = $row['cbillto']; 
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lVoid = $row['lvoid'];
	}
?>
	<form action="Purch_editsave.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
		<fieldset>
    	<legend>
				<div class="col-xs-6 nopadding"> Purchase Order Details </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
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

			<ul class="nav nav-tabs">
				<li class="active"><a href="#home">PO Details</a></li>
				<li><a href="#menu1">Delivery/Billing</a></li>
				<li><a href="#attc">Attachments</a></li>
			</ul>

 				<div class="tab-content">  

      		<div id="home" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px">

						<table width="100%" border="0">
							<tr>
								<tH>PO No.:</tH>
								<td style="padding:2px">
									<div class="col-xs-3 nopadding">
										<input type="text" class="form-control input-sm" id="txtcpono" name="txtcpono" width="20px" tabindex="1" value="<?php echo $cpono;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
									</div>     
									<input type="hidden" name="hdntranno" id="hdntranno" value="<?php echo $cpono;?>">
									<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
									<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
									<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>">
									&nbsp;&nbsp;
									
								</td>
								<td colspan="2" style="padding:2px" align="right">
									<div id="statmsgz" class="small" style="display:inline"></div>
								</td>
							</tr>

							<tr>
								<tH width="100">Supplier:</tH>
								<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Supplier Code..." tabindex="1" value="<?php echo $CustCode;?>" readonly>
										</div>

										<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Supplier Name..."  size="60" autocomplete="off" value="<?php echo $CustName;?>">
										</div> 
									</div>
								</td>
								<tH width="150">PO Date:</tH>
								<td width="250" style="padding:2px;">
								<div class="col-xs-5 nopadding">
								<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $Date; ?>" readonly/>

								</div>
								</td>
							</tr>

							<tr>
								<tH width="100">Remarks:</tH>
								<td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value='<?php echo str_replace("'","\'",$Remarks); ?>'></div></td>
								<tH width="100" style="padding:2px">Date Needed:</tH>
								<td style="padding:2px">
								<div class="col-xs-5 nopadding">
								<input type='text' class="datepick form-control input-sm" id="date_needed" name="date_needed" value="<?php echo $DateNeeded; ?>" />

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
										<input type="text" id="txtcontactname" name="txtcontactname" class="required form-control input-sm" placeholder="Contact Person Name..." tabindex="1"  required="true" value="<?php echo $ccontact; ?>">
									</div>
								</td>
								<tH width="150" style="padding:2px">Terms: </tH> 
									<td style="padding:2px">				
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
													<option value="<?php echo $row['ccode'];?>" <?=($cpayterms==$row['ccode']) ? "selected" : ""?>><?php echo $row['cdesc']?></option>
												<?php
													}
												?>
											</select>
									</td>
							</tr>

							<tr>
								<tH width="100">Email:</tH>
								<td style="padding:2px">
								<div class="col-xs-11 nopadding">
									<input type='text' class="form-control input-sm" id="contact_email" name="contact_email" value="<?php echo $ccontactemail; ?>" />

								</div>
								</td>
								<tH width="150" style="padding:2px">Payment Type: </tH>
								<td style="padding:2px">
									<select class="form-control input-sm" name="selpaytype" id="selpaytype">
										<option value="0" <?=($cpaytype==0) ? "selected" : ""?>>Credit (Paid After Delivery)</option>
										<option value="1" <?=($cpaytype==1) ? "selected" : ""?>>Advance (Payment Before Delivery)</option>
									</select>
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
																		$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='DEF_CURRENCY'"); 
																		
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
										</div>
									</td>
									<tH width="150" style="padding:2px"><div id="setewt" style="<?=($cpaytype==0) ? "display:none" : ""?>">EWT Code: </div> </tH>
									<td style="padding:2px">
										<div id="setewtval" style="<?=($cpaytype==0) ? "display:none" : ""?>"> 
											<select id="selewt" name="selewt" class="form-control input-sm selectpicker"  tabindex="3">
													<option value="none">None</option>
													<option value="multi">Multiple</option>
													<?php
														foreach(@$arrewtlist as $rows){ //$cewtcode
															if($cewtcode==$rows['ctaxcode']){
																$isselc = "selected";
															}else{
																$isselc = "";
															}
															echo "<option value=\"".$rows['ctaxcode']."\" ".$isselc.">".$rows['ctaxcode'].": ".$rows['nrate']."%</option>";
														}
													?>
													
											</select>
										</div>
									</td>
								</tr>


						</table>
					
					</div>

					<div id="menu1" class="tab-pane fade" style="padding-left:5px; padding-top:10px">
						<table width="100%" border="0">
							<tr>
								<td width="150"><b>Deliver To</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-12 nopadding">
											<input type="text" class="form-control input-sm" id="txtdelcust" name="txtdelcust" width="20px" tabindex="1" placeholder="Enter Deliver To..."  size="60" autocomplete="off" value="<?=$delto?>">
										</div> 
									</div>						
								</td>
							</tr>
							<tr>
								<td><b>Delivery Address</b></td>
								<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><textarea class="form-control input-sm" id="txtdeladd" name="txtdeladd" placeholder="Enter Delivery Address..." autocomplete="off"> <?=$deladd?> </textarea></div></td>
							</tr>					

							<tr>
								<td width="150"><b>Delivery Notes</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-12 nopadding">
											<input type="text" class="form-control input-sm" id="textdelnotes" name="textdelnotes" width="20px" tabindex="1" placeholder="Enter Delivery Notes..."  size="60" autocomplete="off" value="<?=$delinfo?>">
										</div> 
									</div>						
								</td>
							</tr>

							<tr>
								<td width="150"><b>Bill To</b></td>
								<td width="310" colspan="2" style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="col-xs-12 nopadding">
											<input type="text" class="form-control input-sm" id="txtbillto" name="txtbillto" width="20px" tabindex="1" placeholder="Enter Bill To..."  size="60" autocomplete="off" value="<?=$billto?>">
										</div> 
									</div>						
								</td>
							</tr>

						</table>
					</div>

					<div id="attc" class="tab-pane fade in" style="padding-left:5px; padding-top:10px">
						<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
						<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
						<input type="file" name="upload[]" id="file-0" multiple />
					</div>

				</div>

		<hr>
		<div class="col-xs-12 nopadwdown"><b>Details</b></div>
		<div class="col-xs-12 nopadwdown"> 

			<input type="hidden" name="hdnxrefrpr" id="hdnxrefrpr">
			<input type="hidden" name="hdnxrefrprident" id="hdnxrefrprident">

			<input type="hidden" name="hdnunit" id="hdnunit">
			<input type="hidden" name="hdnqty" id="hdnqty">
			<input type="hidden" name="hdnfact" id="hdnfact">
			<input type="hidden" name="hdnmainunit" id="hdnmainunit">

			<?php
				if($xAllowPR==0){
			?>
			<div class="col-xs-3 nopadding"><input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Item/SKU Code..." tabindex="4"></div>
			<div class="col-xs-5 nopadwleft"><input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL + F) Search Product Name..." size="80" tabindex="5"></div>
			<?php
				}else{
			?>
				<input type="hidden" name="txtprodid" id="txtprodid">
				<input type="hidden" name="txtprodnme" id="txtprodnme">
			<?php
				}
			?>
		</div>  


        <div class="alt2" dir="ltr" style="
						margin: 0px;
						padding: 3px;
						border: 1px solid #919b9c;
						width: 100%;
						height: 250px;
						text-align: left;
						overflow: auto">
	
            <table id="MyTable" class="MyTable" cellpadding"3px" width="130%" border="0">
							<thead> 
								<tr>
									<th style="border-bottom:1px solid #999">Code</th>
									<th style="border-bottom:1px solid #999">Description</th>
									<th style="border-bottom:1px solid #999 <?=($cpaytype==0) ? "; display:none" : ""?>" class="codeshdn">EWT Code</th>
									<th style="border-bottom:1px solid #999 <?=($cpaytype==0) ? "; display:none" : ""?>" class="codeshdn">VAT</th>
									<th style="border-bottom:1px solid #999">UOM</th>
									<th style="border-bottom:1px solid #999">Qty</th>
									<th style="border-bottom:1px solid #999">Price</th>
									<th style="border-bottom:1px solid #999">Amount</th>
									<th style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
									<th style="border-bottom:1px solid #999">Date Needed</th>
									<th style="border-bottom:1px solid #999">Remarks</th>
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
						<td rowspan="2" width="70%">
							<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
					
							<?php
								if($poststat=="True"){
							?>

							<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Purch.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>';" id="btnMain" name="btnMain">
								Back to Main<br>(ESC)
							</button>
						
							<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='Purch_new.php';" id="btnNew" name="btnNew">
								New<br>(F1)
							</button>

							<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
								PR<br>(Insert)
							</button>

							<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
								Undo Edit<br>(CTRL+Z)
							</button>

							<?php
								$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'Purch_print'");

								if(mysqli_num_rows($sql) == 1){
								
							?>

								<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $cpono;?>','Print');" id="btnPrint" name="btnPrint">
									Print<br>(CTRL+P)
								</button>

								<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $cpono;?>','PDF');" id="btnPDF" name="btnPDF">
									View PDF<br>&nbsp;
								</button>

								<?php
									if($lPosted==1){
								?>
								<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $cpono;?>','Email');" id="btnEmail" name="btnEmail">
									Send Email<br>&nbsp;
								</button>

							<?php		
									}
								}
							?>

							<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
								Edit<br>(CTRL+E)    
							</button>
							
							<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
								Save<br>(CTRL+S)
							</button>
						
							<?php
								}
							?>

						</td>
						<td width="110px" align="right"><b>Gross Amount </b>&nbsp;&nbsp;</td>
							<td width="150px"> <input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="<?php echo number_format($nbasegross,4); ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
						</td>
					</tr>
					<tr>
						<td width="110px" align="right"><b>Gross Amount in <?php echo $nvaluecurrbase; ?></b>&nbsp;&nbsp;</td>
						<td width="150px"> <input type="text" id="txtnGross" name="txtnGross" readonly value="<?php echo number_format($Gross,4); ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></td>
					</tr>
				</table>
				

    </fieldset>
	</form>

<?php
}
else{
?>
	<form action="Purch_edit.php" name="frmpos2" id="frmpos2" method="post">
		<fieldset>
			<legend>Purchase Order</legend>	

			<table width="100%" border="0">
				<tr>
					<tH width="100">PO NO.:</tH>
					<td colspan="3" style="padding:2px" align="left"><div class="col-xs-3"><input type="text" class="form-control input-sm" id="txtcpono" name="txtcpono" width="20px" tabindex="1" value="<?php echo $cpono;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
				</tr>
				<tr>
					<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>PO No. DID NOT EXIST!</b></font></tH>
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

<!-- FULL PO LIST REFERENCES-->
	<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-lg">			
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="InvListHdr">PR List</h3>
				</div>
							
				<div class="modal-body" style="height:40vh">
							
					<div class="col-xs-12 nopadding">

						<div class="form-group">
							<div class="col-xs-3 nopadding pre-scrollable" style="height:37vh">
								<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight small">
									<thead>
										<tr>
											<th>PR No</th>
											<th>Section</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>

							<div class="col-xs-9 nopadwleft pre-scrollable" style="height:37vh">
								<table name='MyInvDetList' id='MyInvDetList' class="table table-small small">
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
					<button type="button" id="btnInsDet" onClick="InsertPRDets()" class="btn btn-primary">Insert</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<!-- End FULL INVOICE LIST -->


<form method="post" name="frmedit" id="frmedit" action="Purch_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>

<form action="PrintPO.php" method="post" name="frmQPrint" id="frmQprint" target="_blank">
	<input type="hidden" name="hdntransid" id="hdntransid" value="<?php echo $cpono; ?>">
</form>

</body>
</html>

<script type="text/javascript">
	
	var file_name = <?= json_encode(@$arrname) ?>;
	/**
	 * Checking of list files
	 */
	if(file_name.length != 0){
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

			list_file.push("https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/PO/<?=$company."_".$cpono?>/" + name)
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
				url: "th_filedelete.php?id="+name+"&code=<?=$cpono?>", 
				key: i + 1
			});
		})
	}

	<?php
		if($poststat=="True"){
	?>
	$(document).keydown(function(e) {	 
	
	 if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='Purch_new.php';
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
			printchk('<?php echo $cpono;?>', 'Print');
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
	  }
	  else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
		e.preventDefault();
		$('#txtprodnme').focus();
      }

	});
	<?php
		}
	?>

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

	$(document).ready(function() {
    $('.datepick').datetimepicker({
        format: 'MM/DD/YYYY'
    });

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
					initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/PO/<?=$company."_".$cpono?>/{filename}',
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
		
		$('#txtprodnme').attr("disabled", true);
		$('#txtprodid').attr("disabled", true);
		
		$("#txtcpono").focus();

		$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});
		
		loaddetails();
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
				$("#basecurrvalmain").val($("#selbasecurr").data("val"));

				$("#selterms").val(item.cterms).change();
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

									
					$('#txtprodnme').val(item.cname).change(); 
					$('#txtprodid').val(item.id); 
					$("#hdnunit").val(item.cunit);
					$("#hdnqty").val(1);
					$("#hdnfact").val(1); 
					$("#hdnmainunit").val(item.cunit);
					$("#hdnxrefrpr").val("");
					$("#hdnxrefrprident").val("");
					
					addItemName();	
								
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
						$('#hdnqty').val(1);
						$("#hdnfact").val(1); 
						$("#hdnmainunit").val(data[2]);
						$("#hdnxrefrpr").val("");
						$("#hdnxrefrprident").val("");
			

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
					
								myFunctionadd("","","","","","","","","","","","");
								ComputeGross();	
													
							}
							else{
							//alert("ITEM NOT IN THE MASTERLIST!");
							addqty();
						}
						
						$("#txtprodid").val("");
						$("#txtprodnme").val("");
						$("#hdnunit").val("");
						$("#hdnqty").val("");
						$("#hdnfact").val("");
						$("#hdnmainunit").val("");
						$("#hdnxrefrpr").val("");
						$("#hdnxrefrprident").val("");
				
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

		$("#selpaytype").on("change", function(){
			if($(this).val()==1){
				$("#selterms").attr("disabled", true);

				$("#setewtval").show();  
				$("#setewt").show(); 
				$(".codeshdn").show();

			}else{
				$("#selterms").attr("disabled", false);

				$("#setewtval").hide();
				$("#setewt").hide();
				$(".codeshdn").hide();

			}
		});

		$("#selewt").on("change", function(){ 
			var rowCount = $('#MyTable tr').length;

			if(rowCount>1){
				if($(this).val()!=="multi"){			
						for (var i = 1; i <= rowCount-1; i++) {

							$("#selitmewtyp"+i).attr("disabled", false);

							var slctdvalid = $("#selitmewtyp"+i).val($(this).val());

							$("#selitmewtyp"+i).attr("disabled", true);
						}
				}else{
					for (var i = 1; i <= rowCount-1; i++) {
						$("#selitmewtyp"+i).attr("disabled", false);
					}
				}

			}
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

					myFunctionadd("","","","","","","","","","","","");		
					ComputeGross();	
			}
			else{
				
				addqty();	
					
			}
				
			$("#txtprodid").val("");
			$("#txtprodnme").val("");
			$("#hdnunit").val("");
			$("#hdnqty").val("");
			$("#hdnfact").val("");
			$("#hdnmainunit").val("");
			$("#hdnxrefrpr").val("");
			$("#hdnxrefrprident").val("");
			
		}

	}

	function myFunctionadd(nqty, nprice, nbaseamt, namount, nfactor, cmainunit, dneed, crem="", ewtcode, vatcode, dxref, dcrefident){

		var crefPR = document.getElementById("hdnxrefrpr").value;
		var crefPRIdent = document.getElementById("hdnxrefrprident").value;

		var itmcode = document.getElementById("txtprodid").value;
		var itmdesc = document.getElementById("txtprodnme").value;
		var itmunit = document.getElementById("hdnunit").value;
		var itmnqty = document.getElementById("hdnqty").value; 
		var itmfactor = document.getElementById("hdnfact").value;
		var mainuom = document.getElementById("hdnmainunit").value;

		if(crefPR==""){
			crefPR = dxref;
			crefPRIdent = dcrefident;

			if(nqty=="" && nprice=="" && namount=="" && nfactor=="" && cmainunit==""){
				var itmprice = chkprice(itmcode,itmunit);
				var itmamnt = itmprice;
				var itmbaseamnt = itmprice;
				var itmfactor = 1;
				var mainuom = itmunit;
				var itmnqty = 1;
				var dneeded= document.getElementById("date_needed").value;
			}
			else{
				var itmprice = nprice;
				var itmamnt = namount;
				var itmbaseamnt = nbaseamt;
				var itmfactor = nfactor;
				var mainuom = cmainunit;
				var itmnqty = nqty;
				var dneeded = moment(dneed).format('MM/DD/YYYY');;
				
					if(itmprice == null){
						var itmnqty = 1;
						var itmprice = 0;
						var itmamnt = 0;
						var itmfactor = 1;
						var mainuom = itmunit.toUpperCase();
					}
			}
		}else{
			var itmprice = chkprice(itmcode,itmunit);
			var itmamnt = parseFloat(itmnqty)*parseFloat(itmprice);
			var itmbaseamnt = parseFloat($("#basecurrval").val())*parseFloat(itmamnt);  
		}

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

		var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+" <input type='hidden' value='"+crefPR+"' name=\"hdncreference\" id=\"hdncreference\"> <input type='hidden' value='"+crefPRIdent+"' name=\"hdnrefident\" id=\"hdnrefident\"> </td>";
		var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\"><input type='hidden' value='"+itmdesc.toUpperCase()+"' name=\"txtitemdesc\" id=\"txtitemdesc\">"+itmdesc.toUpperCase()+"</td>";


		if($("#selpaytype").val()=="1"){
			var ewtstyle="";
		}else{
			var ewtstyle="display: none";
		}

				var gvnewt = $("#selewt").val();
				var xz = $("#hdnewtlist").val();
				ewtoptions = "";
				$.each(jQuery.parseJSON(xz), function() { 
					if(gvnewt=="multi"){
						if(this['ctaxcode']==ewtcode){
							isselctd = "selected";
						}else{
							isselctd = "";
						}
					}else{
						if(gvnewt==this['ctaxcode']){
							isselctd = "selected";
						}else{
							isselctd = "";
						}
					}
					ewtoptions = ewtoptions + "<option value='"+this['ctaxcode']+"' data-rate='"+this['nrate']+"' "+isselctd+">"+this['ctaxcode']+": "+this['nrate']+"%</option>";
				});

				if(gvnewt!=="none" || gvnewt!=="multi"){
					isdisabled = "disabled";
				}else{
					isdisabled = "";
				}

				var ewttd = "<td width=\"100\" nowrap style=\""+ewtstyle+"\" class=\"codeshdn\"> <select class='form-control input-xs' name=\"selitmewtyp\" id=\"selitmewtyp"+lastRow+"\" "+isdisabled+"> <option value=\"none\">None</option>" + ewtoptions + "</select> </td>";



				var xz = $("#hdntaxcodes").val();
				taxoptions = "";
				$.each(jQuery.parseJSON(xz), function() { 
					if(vatcode==this['ctaxcode']){
						isselctd = "selected";
					}else{
						isselctd = "";
					}
					taxoptions = taxoptions + "<option value='"+this['ctaxcode']+"' data-id='"+this['nrate']+"' "+isselctd+">"+this['ctaxdesc']+"</option>";
				});

				var vattd = "<td width=\"120\" nowrap style=\""+ewtstyle+"\" class=\"codeshdn\"> <select class='form-control input-xs' name=\"selitmvatyp\" id=\"selitmvatyp"+lastRow+"\">" + taxoptions + "</select> </td>";



		var tditmunit = "<td width=\"80\" style=\"padding: 1px\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">"+uomoptions+"</select> </td>";

		var tditmqty = "<td width=\"100\" style=\"padding: 1px\" nowrap> <input type='text' value='"+itmnqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' /> <input type='hidden' value='"+mainuom+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+itmfactor+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";
			
		var tditmprice = "<td width=\"100\" style=\"padding: 1px\" nowrap> <input type='text' value='"+itmprice+"' class='numeric2 form-control input-xs' style='text-align:right'name=\"txtnprice\" id='txtnprice"+lastRow+"' autocomplete='off' onFocus='this.select();'> </td>";

		var tditmbaseamount = "<td width=\"100\" style=\"padding: 1px\" nowrap> <input type='text' value='"+itmbaseamnt+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> </td>";

		var tditmamount = "<td width=\"100\" style=\"padding: 1px\" nowrap> <input type='text' value='"+itmamnt+"' class='numeric form-control input-xs' style='text-align:right' name='txtnamount' id='txtnamount"+lastRow+"' readonly> </td>";

		var tdneeded = "<td width=\"100\" style=\"padding: 1px; position:relative;\" nowrap><input type='text' class='datepick form-control input-xs' id='dneed"+lastRow+"' name='dneed' value='"+dneeded+"' /></td>"
		
		var tditmdel = "<td width=\"80\" style=\"padding: 1px\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + lastRow + "' value='delete' /> </td>";

		var tditmremarks = "<td width=\"150\"> <input type='text' class='form-control input-xs' value='"+crem+"' name=\"txtitemrem\" id=\"txtitemrem" + lastRow + "\" maxlength=\"255\"></td>";

		$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + ewttd + vattd + tditmunit + tditmqty + tditmprice + tditmbaseamount + tditmamount+ tdneeded + tditmremarks + tditmdel + '</tr>');


										$("#del"+lastRow).on('click', function() {
											$(this).closest('tr').remove();
										});

										//$("input.numeric").numeric();
										$("input.numeric2").autoNumeric('init',{mDec:4});
										$("input.numeric").autoNumeric('init',{mDec:2});

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
										
										$('#dneed'+lastRow).datetimepicker({
											format: 'MM/DD/YYYY',
											useCurrent: false,
											minDate: moment().format('L'),
											defaultDate: moment().format('L'),
											widgetPositioning: {
													horizontal: 'right',
													vertical: 'bottom'
											}
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

			//ndsc = $("#txtndisc"+r).val();
			//ndsc = parseFloat(ndsc);
			
			//if (parseFloat(ndsc) != 0) {
			//	nprcdisc = parseFloat(nprc) * (parseFloat(ndsc) / 100);
			//	nprc = parseFloat(nprc) - nprcdisc;

			//}
			
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
			var amt = 0;
			
			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
					amt = $("#txtntranamount"+i).val().replace(/,/g,'');
					gross = gross + parseFloat(amt);
					
				}
				
				
			}
			gross2 = gross * parseFloat($("#basecurrval").val());

			//	$("#txtnGross").val(Number(gross2).toLocaleString('en', { minimumFractionDigits: 4 }));
			//	$("#txtnBaseGross").val(Number(gross).toLocaleString('en', { minimumFractionDigits: 4 }));

			$("#txtnBaseGross").val(gross);

			$("#txtnGross").val(gross2);

			$("#txtnBaseGross").autoNumeric('destroy');
			$("#txtnGross").autoNumeric('destroy');

			$("#txtnBaseGross").autoNumeric('init',{mDec:2});
			$("#txtnGross").autoNumeric('init',{mDec:2});
			
		}

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
					$(this).find("input[name='txtntranamount']").val(TotAmt); 

					$("#txtntranamount"+r).autoNumeric('destroy');
					$("#txtntranamount"+r).autoNumeric('init',{mDec:2});


					namt2 = TotAmt * parseFloat($("#basecurrval").val());
					$(this).find("input[name='txtnamount']").val(namt2); 

					$("#txtnamount"+r).autoNumeric('destroy');
					$("#txtnamount"+r).autoNumeric('init',{mDec:2});
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
			var pono = $("#txtcpono").val();
			var ccode = $("#txtcustid").val();
			var crem = $("#txtremarks").val();
			var ddate = $("#date_needed").val();
			var ngross = $("#txtnGross").val();
					
			var myform = $("#frmpos").serialize();	
			//alert(myform);	
			var formdata = new FormData($('#frmpos')[0]);
			formdata.delete('upload[]');
			jQuery.each($('#file-0')[0].files, function(i, file){
				formdata.append('file-'+i, file);
			});

			$.ajax ({
				url: "Purch_editsave.php",
				//data: { pono:pono, ccode: ccode, crem: crem, ddate: ddate, ngross: ngross },
				data: formdata,
				cache: false,
				processData: false,
				contentType: false,
				method: 'post',
				type: 'post',
				async: false,
				beforeSend: function(){
					$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING PO: </b> Please wait a moment...");
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
					//if(index>0){

						var crefpr = $(this).find('input[type="hidden"][name="hdncreference"]').val(); 
						var crefprident = $(this).find('input[type="hidden"][name="hdnrefident"]').val();

						//alert("a");
						var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
						//alert("b");
						var citmdesc = $(this).find('input[type="hidden"][name="txtitemdesc"]').val();
						//alert("c");
						var cuom = $(this).find('select[name="seluom"]').val();
						//alert("d");
						var nqty = $(this).find('input[name="txtnqty"]').val();
						//alert("e");
						var nprice = $(this).find('input[name="txtnprice"]').val();
						//alert("f");
						var ntranamt = $(this).find('input[name="txtntranamount"]').val();
						var namt = $(this).find('input[name="txtnamount"]').val();
						//alert("g");
						var dneed = $(this).find('input[name="dneed"]').val();
						//alert("h");
						var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
						//alert("i");
						var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
						//alert("j");
						var citmremarks = $(this).find('input[name="txtitemrem"]').val();


						var ewtcode = $(this).find('select[name="selitmewtyp"]').val();
						var ewtrate = $(this).find('select[name="selitmewtyp"] option:selected').data('rate'); 
						var vatcode = $(this).find('select[name="selitmvatyp"]').val(); 
						var nrate = $(this).find('select[name="selitmvatyp"] option:selected').data('id'); 
				

						if(nqty!==undefined){
							nqty = nqty.replace(/,/g,'');
							nprice = nprice.replace(/,/g,'');
							namt = namt.replace(/,/g,'');
							ntranamt = ntranamt.replace(/,/g,'');
						}

						//alert("Purch_updatesavedet.php?trancode="+ trancode + "&dneed="+ dneed + "&indx="+ index + "&citmno="+ citmno+ "&cuom="+ cuom+ "&nqty="+ nqty + "&nprice="+ nprice+ "&namt=" + namt + "&mainunit="+ mainunit + "&nfactor=" + nfactor + "&citmdesc=" + citmdesc + "&ntranamt="+ntranamt+"&citmremarks="+citmremarks);
					
						$.ajax ({
							url: "Purch_newsavedet.php",
							data: { trancode: trancode, crefpr:crefpr, crefprident:crefprident, dneed: dneed, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, mainunit:mainunit, nfactor:nfactor, citmdesc:citmdesc, ntranamt:ntranamt, citmremarks:citmremarks, vatcode:vatcode, nrate:nrate, ewtcode:ewtcode, ewtrate:ewtrate },
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

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "Purch_edit.php";
			document.getElementById(frm).submit();
		}
	}

	function disabled(){
		$("#frmpos :input").attr("disabled", true);
		
		
		$("#txtcpono").attr("disabled", false);
		$("#btnMain").attr("disabled", false);
		$("#btnNew").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
		$("#btnPDF").attr("disabled", false);   
		$("#btnEmail").attr("disabled", false);
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
			
			document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
			
		}
		else{
			
			$("#frmpos :input").attr("disabled", false);
			
				$("#txtcpono").val($("#hdntranno").val());
				$("#txtcpono").attr("readonly", true);
				$("#btnMain").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
				$("#btnPDF").attr("disabled", true);   
				$("#btnEmail").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);

				if($("#selpaytype").val()==1){
					
					$("#selewt").change();
				}
				
		
		}
	}

	function printchk(x,typx){
		if(document.getElementById("hdncancel").value==1){	
			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		else{
			// var url =  "Purch_confirmprint.php?x="+x;
				
			// $("#myprintframe").attr('src',url);


				if(typx=="Print"){
					//alert("PrintPO.php?hdntransid="+x);
					$("#myprintframe").attr("src","PrintPO.php?hdntransid="+x);

					$("#PrintModal").modal('show');
				}else if(typx=="PDF"){
					$("#frmQprint").attr("action","PrintPO_PDF.php");
					$("#frmQprint").submit();
				}else if(typx=="Email"){
					if($("#contact_email").val()==""){
						$("#AlertMsg").html("<b>ERROR: </b> Can't send email without the contact person email address!");
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');
					}else{
						$("#frmQprint").attr("action","PrintPO_Email.php");
						$("#frmQprint").submit();
					}
				}
				//$("#frmQprint").submit();



			

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

					$('#txtprodnme').val(item.desc); 
					$('#txtprodid').val(item.id); 
					$("#hdnunit").val(item.cunit); 
					//alert(item.nqty);
					myFunctionadd(item.nqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.cmainunit,item.dneed,item.cremarks,item.cewtcode,item.ctaxcode,item.creference,item.nrefident);
				});

			}
		});
		
		
			$("#txtprodid").val("");
			$("#txtprodnme").val("");
			$("#hdnunit").val("");


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
									//$('#txtcontactdesig').val(data[1]);
						//$('#txtcontactdept').val(data[2]);
						$("#contact_email").val(data[3]);
							}
						}
			}
		});

	}

	function openinv(){

		//clear table body if may laman
		$('#MyInvTbl tbody').empty(); 
		$('#MyInvDetList tbody').empty();
				
		//get salesno na selected na
		var y;
		var salesnos = "";
		var xstat =  "YES";

		$.ajax({ //		data: 'x='+x,
			url: 'th_prlist.php',
			dataType: 'json',
			method: 'post',
			success: function (data) {

				$("#allbox").prop('checked', false);
							
				console.log(data);
				$.each(data,function(index,item){
								
					if(item.cpono=="NONE"){
						$("#AlertMsg").html("No Purchase Request Available");
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

						xstat = "NO";
									
						$("#txtcustid").attr("readonly", false);
						$("#txtcust").attr("readonly", false);

					}
					else{
						$("<tr>").append(
							$("<td id='td"+item.cprno+"'>").text(item.cprno),
							$("<td>").text(item.cdesc)
						).appendTo("#MyInvTbl tbody");
									
									
						$("#td"+item.cprno).on("click", function(){
							opengetdet($(this).text());
						});
									
						$("#td"+item.cprno).on("mouseover", function(){
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

	function opengetdet(valz){
		var drno = valz;

		$("#txtrefSI").val(drno);

		$('#InvListHdr').html("PR Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");

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

		//alert('th_prlistdet.php?x='+drno+"&y="+salesnos);
		$.ajax({
			url: 'th_prlistdet.php',
			data: 'x='+drno+"&y="+salesnos,
			dataType: 'json',
			method: 'post',
			success: function (data) {

				$("#allbox").prop('checked', false); 					   
				console.log(data);
				$.each(data,function(index,item){
					if(item.citemno==""){
						alert("NO more items to add!")
					}
					else{
							
						$("<tr>").append(
							$("<td>").html("<input type='checkbox' value='"+item.nident+"' name='chkSales[]' data-id=\""+drno+"\" data-ident=\""+item.nident+"\" data-itm='"+item.citemno+"' data-itmdesc='"+item.cdesc+"' data-itmunit='"+item.cunit+"' data-qty='"+item.nqty+"' data-factor='"+item.nqty+"'>"),
							$("<td>").text(item.citemno),
							$("<td>").text(item.cdesc),
							$("<td>").text(item.cunit),
							$("<td>").text(item.nqty),
						).appendTo("#MyInvDetList tbody");
					}
				});
			},
			complete: function(){
				$('#loadimg').hide();
			},
			error: function (req, status, err) {
				console.log('Something went wrong', status, err);
				$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
			}
		});

	}

	function InsertPRDets(){
		var i = 0;
		var rcnt = 0;

		$("input[name='chkSales[]']:checked").each( function () {

			$("#hdnxrefrpr").val($(this).data("id"));
			$("#hdnxrefrprident").val($(this).data("ident"));

			$("#txtprodid").val($(this).data("itm"));
			$("#txtprodnme").val($(this).data("itmdesc"));
			$("#hdnunit").val($(this).data("itmunit"));
			$("#hdnqty").val($(this).data("qty"));
			$("#hdnfact").val($(this).data("factor"));

			myFunctionadd("","","","","","","","","","","","");

			$('#mySIRef').modal('hide');

		});
	}

</script>

