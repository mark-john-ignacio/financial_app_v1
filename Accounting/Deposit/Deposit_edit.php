<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Deposit";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Deposit_edit'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	$printstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Deposit_print'");
	if(mysqli_num_rows($sql) == 0){
		$printstat = "False";
	}

	$corno = $_REQUEST['txtctranno'];


	@$arrfiles = array();
	@$arrname = array();

	if (file_exists('../../Components/assets/Deposit/'.$company.'_'.$corno.'/')) {
		$allfiles = scandir('../../Components/assets/Deposit/'.$company.'_'.$corno.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {

			$fileNameParts = explode('.', $file);
			$ext = end($fileNameParts);

			@$arrname[] = array("name" => $file, "ext" => $ext);
		}
	
	}else{
		//echo "NO FILES";
	}


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?=time()?>">
  	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
	<link href="../../global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
	
	<link rel="stylesheet" type="text/css" href="../../global/plugins/bootstrap-select/bootstrap-select.min.css"/>
	<link rel="stylesheet" type="text/css" href="../../global/plugins/select2/select2.css"/>
	<link rel="stylesheet" type="text/css" href="../../global/plugins/jquery-multi-select/css/multi-select.css"/>

	<link href="../../global/css/plugins.css" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	-->
	<script src="../../global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
	<script type="text/javascript" src="../../global/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="../../global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>

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

<body style="padding:5px; height:700px" onLoad="disabled();">

<input type="hidden" value='<?=json_encode(@$arrname)?>' id="hdnfileconfig"> 

<?php

    $sqlchk = mysqli_query($con,"Select a.cacctcode, a.namount, a.cortype, DATE_FORMAT(a.dcutdate,'%m/%d/%Y') as dcutdate, a.namount, a.lapproved, a.lcancelled, a.lprintposted, a.lvoid, a.cremarks, c.cacctdesc, c.nbalance, a.ccurrencycode, a.ccurrencydesc, a.nexchangerate, a.cbankcode, a.creference From deposit a left join accounts c on a.compcode=c.compcode and a.cacctcode=c.cacctid where a.compcode='$company' and a.ctranno='$corno'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cacctcode'];
			$nDebitDesc = $row['cacctdesc'];	
			$nBalance = $row['nbalance'];		 
			$cPayMeth = $row['cortype'];
			$dDate = $row['dcutdate'];
			$nAmount = $row['namount'];
			$cRemarks = $row['cremarks'];

			$cBankCode = $row['cbankcode'];
			$cReference = $row['creference'];

			$ccurrcode = $row['ccurrencycode'];  
			$ccurrdesc = $row['ccurrencydesc']; 
			$ccurrrate = $row['nexchangerate'];
			
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
			$lPrintPost = $row['lprintposted'];
			$lVoid = $row['lvoid'];
			
		}

?>

<form action="Deposit_editsave.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmOR" id="frmOR" method="post" enctype="multipart/form-data">
	<fieldset>
    	<legend>
				<div class="col-xs-6 nopadding"> Bank Deposit </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
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
					<li class="active"><a href="#items" data-toggle="tab">Details</a></li>
					<li><a href="#attc" data-toggle="tab">Attachments</a></li>
				</ul>

				<div class="tab-content">

					<div id="items" class="tab-pane fade in active" style="padding-left: 5px; padding-top: 10px;">

						<table width="100%" border="0">							
							<tr>
								<tH>Deposit No.:</tH>
								<td style="padding:2px;">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-4 nopadding"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $corno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmOR');">
										</div>
											
											<input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $corno;?>">
											
											<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
											<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
											<input type="hidden" name="hdnprintpost" id="hdnprintpost" value="<?php echo $lPrintPost;?>">
											<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?=$lVoid;?>">
											&nbsp;
											
									<button type="button" class="btn btn-entry btn-sm" id="btnentry">
										<i class="fa fa-bar-chart" aria-hidden="true"></i>
									</button>
								</td>
								<td colspan="2" style="padding:2px;">
									<div id="statmsgz" style="display:inline"></div>
											
								</td>
							</tr>
							<tr>
								<tH width="200">   	
									Bank:
								</tH>
								<td style="padding:2px;" width="500">
									<?php																				
										$sqlbaks = mysqli_query($con,"Select a.ccode, a.cname, a.cacctno, b.cacctdesc From bank a left join accounts b on a.compcode=b.compcode and a.cacctno=b.cacctid where a.compcode='$company' and a.cstatus='ACTIVE' Order By a.cname");											
									?>
									<div class="row nopadding">
										<div class="col-xs-10 nopadding">
											<select class="form-control select2 input-medium" name="selbanks" id="selbanks">
												<?php
													if (mysqli_num_rows($sqlbaks)!=0) {
														while($rows = mysqli_fetch_array($sqlbaks, MYSQLI_ASSOC)){
												?>
													<option value="<?=$rows['ccode']?>" data-cacctcode="<?=$rows['cacctno']?>" data-cacctdesc="<?=$rows['cacctdesc']?>" <?=($cBankCode ==$rows['ccode'] ) ? "selected" : ""?>><?=strtoupper($rows['cname'])?></option> 
												<?php
														}
													}
												?>
											</select>
										</div> 
										 
									</div>   
								</td>
								<tH width="150">Reference:</tH>
								<td style="padding:2px;">
									<div class="col-xs-10 nopadding">
										<input type="text" id="txtrefno" name="txtrefno" class="form-control input-sm required" required value="<?=$cReference?>">
									</div>
								</td>
							</tr>
							<tr>
								<tH width="200">   	
									Deposit To Account:
								</tH>
								<td style="padding:2px;" width="500">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-12 nopadding">
											<div class="col-xs-3 nopadding"> 
												<input type="text" id="txtcacctid" name="txtcacctid" class="form-control input-sm" readonly  value="<?php echo $nDebitDef;?>">
											</div> 
											<div class="col-xs-7 nopadwleft">
												<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>" autocomplete="off">
											</div>  
										</div> 										
									</div>     
								</td>
								<tH width="150">Balance:</tH>
								<td style="padding:2px;">
								<div class="col-xs-8 nopadding">
								<input type="text" id="txtacctbal" name="txtacctbal" class="form-control input-sm" readonly value="<?php echo $nBalance;?>">
								</div>
								</td>
							</tr>
							<tr>
							<tH width="150">Currency:</tH>
								<td style="padding:2px;">
									<div class="row nopadding">
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

													$sqlhead=mysqli_query($con,"Select symbol as id, CONCAT(symbol,\" - \",country,\" \",unit) as currencyName, rate from currency_rate");
													if (mysqli_num_rows($sqlhead)!=0) {
														while($rows = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
												?>
													<option value="<?=$rows['id']?>" <?php if ($ccurrcode==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>" data-desc="<?=$rows['currencyName']?>"><?=$rows['currencyName']?></option>
												<?php
														}
													}
												?>
											</select>
											<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?=$nvaluecurrbase; ?>"> 	
											<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?=$ccurrdesc; ?>"> 
										</div>
										<div class="col-xs-2 nopadwleft">
											<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="<?=$ccurrrate; ?>">	 
										</div>
										<div class="col-xs-2" id="statgetrate" style="padding: 4px !important"> 																	
										</div>
									</div>
								</td>	
								<tH>&nbsp;</tH>
								<td style="padding:2px;">&nbsp;</td>
							</tr>
							<tr>
								<!--
								<tH width="200" valign="top">Receipt By:</tH>
								<td valign="top" style="padding:2px">
									
									
									<div class="col-xs-12 nopadding">
										<div class="col-xs-6 nopadding">
											<select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
												<option value="Cash" <?//php if($cPayMeth=="Cash"){ echo "selected"; }?>>Cash</option>
												<option value="Cheque" <?//php if($cPayMeth=="Cheque"){ echo "selected"; }?>>Cheque</option>
												<option value="All" <?//php if($cPayMeth=="All"){ echo "selected"; }?>>All Methods</option>
											</select>
											</div>      
									</div> 
									</td>
								-->
								<tH width="200" rowspan="2" valign="top">Remarks:</tH>
								<td rowspan="2" valign="top" style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-10 nopadding">
											<textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"><?php echo $cRemarks; ?></textarea>
										</div>
									</div>
								</td>
								<tH style="padding:2px">Date:</tH>
								<td style="padding:2px">
									<div class="col-xs-8 nopadding">
										<div class="input-icon">
											<i class="fa fa-calendar"></i>
											<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $dDate; ?>"/>
										</div>
									</div>
								</td>
							</tr>
							<tr>

								<th valign="top" style="padding:2px">Total Deposited:</th>
								<td valign="top" style="padding:2px"><div class="col-xs-8 nopadding">
									<input type="text" id="txtnGross" name="txtnGross" class="form-control text-right" value="<?php echo number_format($nAmount,2);?>" readonly>
								</div></td>
								</tr>
							<tr>
								<th valign="top" style="padding:2px">&nbsp;</th>
								<td valign="top" style="padding:2px">&nbsp;</td>
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
				<div class="col-xs-12 nopadwdown"><b>Details</b></div>		

						<button type="button" class="btn btn-xs btn-info" onClick="getInvs();" style="margin-bottom:5px">
							<i class="fa fa-search"></i>&nbsp;Load OR
						</button>
						<br>
						<div id="tableContainer" class="alt2" dir="ltr" style="
								margin: 0px;
								padding: 3px;
								border: 1px solid #919b9c;
								width: 100%;
								height: 200px;
								text-align: left;
								overflow: auto">
														
								<table width="100%" border="0" cellpadding="3" id="MyTable" class="table table-striped">
									<thead>
										<tr>
											<th scope="col" width="15%">Trans No</th>
											<th scope="col">OR No.</th> 
											<th scope="col">Reference</th> 
											<th scope="col">Date</th>
											<th scope="col">Remarks</th>
											<th scope="col">Payment Method</th>
											<th scope="col" style="text-align: right">Amount</th>
											<th scope="col">&nbsp;</th>
										</tr>
									</thead>
									<tbody>
										<?php
						
										$sqlbody = mysqli_query($con,"select a.*, b.cornumber, b.dcutdate, b.cremarks, b.cpaymethod, b.namount from deposit_t a left join receipt b on a.compcode=b.compcode and a.corno=b.ctranno and a.compcode=b.compcode where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");

										if (mysqli_num_rows($sqlbody)!=0) {
											$cntr = 0;
											while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
												$cntr = $cntr + 1;
										?>
										<tr>
											<td><input type='hidden' name='txtcSalesNo<?php echo $cntr;?>' value='<?php echo $rowbody['corno'];?>' /><?php echo $rowbody['corno'];?></td>
											<td><?php echo $rowbody['cornumber'];?></td>
											<td><input type='hidden' name='txtcReference<?php echo $cntr;?>' value='<?php echo $rowbody['creference'];?>' /><?php echo $rowbody['creference'];?></td>
											<td><?php echo $rowbody['dcutdate'];?></td>
											<td><?php echo $rowbody['cremarks'];?></td>
											<td><?php echo ucwords($rowbody['cpaymethod']);?></td>
											<td align='right'><input type='hidden' name='txtnAmt<?php echo $cntr;?>' id='txtAmt' value='<?php echo $rowbody['namount'];?>' /><?php echo number_format($rowbody['namount'],2);?></td>
											<td align='center'><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr;?>_delete' value='delete' onClick='deleteRow(this);' /></td>
										</tr>
										<?php
											}
										}

										?>
				

									</tbody>
								</table>
								<input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
						</div>

				
				<br>
				<table width="100%" border="0" cellpadding="3">
					<tr>
						<td width="50%">
							<?php
								if($poststat=="True"){
							?>

							<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Deposit.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>';" id="btnMain" name="btnMain">
								Back to Main<br>(ESC)
							</button>
					
							<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='Deposit_new.php';" id="btnNew" name="btnNew">
								New<br>(F1)
							</button>

							<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmOR');" id="btnUndo" name="btnUndo">
								Undo Edit<br>(CTRL+Z)
							</button>

							<?php
								}

								if($printstat=="True"){
							?>

							<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $corno;?>');" id="btnPrint" name="btnPrint">
								Print<br>(CTRL+P)
							</button>
						
							<?php
								}

								if($poststat=="True"){
							?>

							<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
								Edit<br>(CTRL+E)    
							</button>
						
							<button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave" name="btnSave">
								Save<br>(CTRL+S)    
							</button>
							<?php
								}
							?>
						<td>
						<td align="right">&nbsp;</td>
					</tr>
				</table>
				

    </fieldset>

		</form>

<!-- Bootstrap modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">OR List</h3>
            </div>
            
            <div class="modal-body">
            
            	
                  <table name='MyORTbl' id='MyORTbl' class="table">
                   <thead>
                    <tr>
                      <th align="center">
                      <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                      <th>Trans No</th>
					  <th>Method</th>
                      <th>OR No</th>
                      <th>OR Date</th>
                      <th style='text-align: right'>Gross</th>
                      <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
				  </table>
                
            
			</div>
			
            <div class="modal-footer">
                
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Insert</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

			<!-- Alert Modal -->
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
			<!-- End Alert Modal -->


			<!--modal entry view-->
			<div class="modal fade" id="modGLEntry" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" id="btn-closemod" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 class="modal-title">GL Entry</h3>
						</div>
						<div class="modal-body pre-scrollable">
								
							<table width="100%" border="0" class="table table-condensed table-bordered atble-hover" id="TblGLEntry">
								<thead>
									<tr>
										<td>Account Code</td>
										<td>Account Title</td>
										<td>Account Debit</td>
										<td>Account Credit</td>  
									</tr>		
									<?php

										$getewtcd = mysqli_query($con,"SELECT * FROM glactivity where compcode='$company' and ctranno='$corno'"); 
										if (mysqli_num_rows($getewtcd)!=0) {
											while($row = mysqli_fetch_array($getewtcd, MYSQLI_ASSOC)){
									?>					
										<tr>
											<td><?=$row['acctno']?></td>
											<td><?=$row['ctitle']?></td>
											<td align="right"><?=(floatval($row['ndebit']) != 0) ? number_format($row['ndebit'],2) : ""?></td>
											<td align="right"><?=(floatval($row['ncredit']) != 0) ? number_format($row['ncredit'],2) : ""?></td>  
										</tr>	
									<?php
											}
										}
									?>
							</table>
								
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div>



<?php
}
else{
?>

<form action="Deposit_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Bank Deposit</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">Deposit No.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $corno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>Deposit No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>


<script type="text/javascript">

var fileslist = [];
	/*
	var xz = $("#hdnfiles").val();
	$.each(jQuery.parseJSON(xz), function() { 
		fileslist.push(xz);
	});
	*/

	console.log(fileslist);
	var filesconfigs = [];
	var xzconfig = JSON.parse($("#hdnfileconfig").val());

	//alert(xzconfig.length);

	var arroffice = new Array("xls","xlsx","doc","docx","ppt","pptx","csv");
	var arrimg = new Array("jpg","png","gif","jpeg");

	var xtc = "";
	for (var i = 0; i < xzconfig.length; i++) {
    var object = xzconfig[i];
		//alert(object.ext + " : " + object.name);
		fileslist.push("https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/Deposit/<?=$company."_".$corno?>/" + object.name)

		if(jQuery.inArray(object.ext, arroffice) !== -1){
			xtc = "office";
		}else if(jQuery.inArray(object.ext, arrimg) !== -1){
			xtc = "image";
		}else if(object.ext=="txt"){
			xtc = "text";
		}else{
			xtc = object.ext;
		}

		filesconfigs.push({
			type : xtc, 
			caption : object.name,
			width : "120px",
			url: "th_filedelete.php?id="+object.name+"&code=<?=$corno?>"+"&typ=Deposit", 
			key: i + 1
		});
	}

	$(document).keydown(function(e) {	 
	
	 if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='Deposit_new.php';
		}
	  }
	  else if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){ 
			e.preventDefault();
			$("#btnSave").click();
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
			printchk('<?php echo $corno;?>');
		}
	  }
	  else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
		if($("#btnUndo").is(":disabled")==false){
			e.preventDefault();
			chkSIEnter(13,'frmOR');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			$("#btnMain").click();
		}
	  }
	});



	$(document).ready(function(){            
           // Bootstrap DateTimePicker v4
		$('#date_delivery').datetimepicker({
			format: 'MM/DD/YYYY'
		});
			

		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});

		if(fileslist.length>0){
			$("#file-0").fileinput({
				theme: 'fa5',
				showUpload: false,
				showClose: false,
				browseOnZoneClick: true,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false, },
				initialPreview: fileslist,
				initialPreviewAsData: true,
				initialPreviewFileType: 'image',
				initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/Deposit/<?=$company."_".$corno?>/{filename}',
				initialPreviewConfig: filesconfigs
			});
		}else{
			$("#file-0").fileinput({
				theme: 'fa5',
				showUpload: false,
				showClose: false,
				browseOnZoneClick: true,
				allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
				overwriteInitial: false,
				maxFileSize:100000,
				maxFileCount: 5,
				browseOnZoneClick: true,
				fileActionSettings: { showUpload: false, showDrag: false, }
			});
		}

		$("#selbanks").select2({
            placeholder: "Select a Bank",
            allowClear: true
        }); 

		$("#selbanks").on("change", function(){
			$("#txtcacctid").val($(this).find(':selected').data('cacctcode'));  
			$("#txtcacct").val($(this).find(':selected').data('cacctdesc')); 
		});
			
	$('#txtcacct').typeahead({

		source: function (query, process) {
			return $.getJSON(
				'th_accounts.php',
				{ query: query },
				function (data) {
					newData = [];
					map = {};
					
					$.each(data, function(i, object) {
						map[object.name] = object;
						newData.push(object.name);
					});
					
					process(newData);
				});
		},
		updater: function (item) {	
				
			$('#txtcacctid').val(map[item].id);
			$('#txtacctbal').val(map[item].balance);
			return item;
		}

	});
	

 	$("#allbox").click(function () {
        if ($("#allbox").is(':checked')) {
            $("input[name='chkSales[]']").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("input[name='chkSales[]']").each(function () {
                $(this).prop("checked", false);
            });
        }
	});

	$("#btnentry").on("click", function(){		
		$("#modGLEntry").modal("show");
	});

});

function deleteRow(r) {
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	document.getElementById('MyTable').deleteRow(i);
	var lastRow = tbl.length;
	var z; //for loop counter changing textboxes ID;
	 
	for (z=i+1; z<=lastRow; z++){
		var tempsalesno =  $('input[name=txtcSalesNo'+z+']');
		var temprefno =  $('input[name=txtcReference'+z+']');
		var tempamt =  $('input[name=txtnAmt'+z+']');
				
		var x = z-1;
		tempsalesno.attr("name", "txtcSalesNo" + x);	
		temprefno.attr("name", "txtcReference" + x);	
		tempamt.attr("name", "txtnAmt" + x);		
		//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

	}

computeGross();

}

function computeGross(){
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var x = 0;
	var tot = 0;
	for (z=1; z<=lastRow-1; z++){
		x = $('input[name=txtnAmt'+z+']').val();
		
		x = x.replace(",","");
		if(x!=0 && x!=""){
		var tot = parseFloat(x) + parseFloat(tot);	
		}
	}
	
	//alert(tot);
	document.getElementById('txtnGross').value = tot.toFixed(2);

}

		function getInvs(){	
			
			//clear table body if may laman			
	
			$('#MyORTbl tbody').empty();
				
			//get or na selected na
			var y;
			var salesnos = "";
			var rc = $('#MyTable tr').length;
				
			if(rc>1){
				for(y=1;y<=rc-1;y++){ 
					if(y>1){
						salesnos = salesnos + ",";
					}
					salesnos = salesnos + $('input[name=txtcSalesNo'+y+']').val();
				}
			}
	
			//ajax lagay table details sa modal body			
			$('#invheader').html("OR List: " + $('#selpayment').val())
				
			$.ajax({
				url: 'th_depositlist.php',
				data: { y: salesnos },
				dataType: 'json',
				method: 'post',
				async: false,
				success: function (data) {
	
					console.log(data);
					$.each(data,function(index,item){
						$("<tr>").append(
							$("<td>").html("<input type='checkbox' value='"+item.ctranno+"' name='chkSales[]'>"),
							$("<td>").text(item.ctranno),
							$("<td>").text(item.cpaymethod),
							$("<td>").text(item.corno),
							$("<td>").text(item.dcutdate),
							$("<td style='text-align: right'>").text(item.namount + " " + item.ccurrencycode)
						).appendTo("#MyORTbl tbody");
					});
							 
					$('#myModal').modal('show');
				},
				error: function (err) {
	
					$("#AlertMsg").html("<b>ERROR: </b>Loading Error \n"+"or No receipt to be deposited!");
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');
				}
			});
	
		}

function save(){

	var i = 0;
	var rowCount = $('#MyTable tr').length;
	var totAmt = 0;
	
  $("input[name='chkSales[]']:checked").each( function () {
	   i += 1;
      // alert( $(this).val() );
	  			
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_getordetails.php?id=" + id,
					type: "GET",
					dataType: "JSON",
					async: false,
					success: function(data)
					{				
						console.log(data);
                       	$.each(data,function(index,item){
							$("<tr myAttr='"+item.corno+"'>").append(
								$("<td>").html("<input type='hidden' name='txtcSalesNo"+rowCount+"' value='"+item.ctranno+"' />"+item.ctranno),
								$("<td>").text(item.corno),
								$("<td>").html("<input type='hidden' name='txtcReference"+rowCount+"' value='"+item.creference+"' />"+item.creference),
								$("<td>").text(item.dcutdate),
								$("<td>").text(item.cremarks),
								$("<td>").text(item.cpaymethod),
								$("<td align='right'>").html("<input type='hidden' name='txtnAmt"+rowCount+"' id='txtAmt' value='"+item.namountorig+"' />"+item.namount),
								$("<td align='center'>").html("<input class='btn btn-danger btn-xs' type='button' id='row_"+rowCount+"_delete' value='delete' onClick='deleteRow(this);' />")
							).appendTo("#MyTable tbody");
														
						});
					rowCount = rowCount + 1;
					sortORTbl();

					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

	   
	   
	   
   });
   
   
  // alert(i + " Transactions Selected!");   
   
   
   if(i==0){
	   alert("No receipt is selected!")
   }

	   
		$('#MyTable > tbody  > tr').each(function() {
			//alert($(this).index());
			
			//alert($(this).find("input").val());
			
			var x = parseInt($(this).index()) + 1;
			//alert(x);
			$(this).find("input[id=txttranno]").attr('name', "txtcSalesNo" + x);
	
			$(this).find("input[id=txtAmt]").attr('name', "txtnAmt" + x);

		});


   $('#myModal').modal('hide');
   
   computeGross();
   
}

function sortORTbl(){
	var $table=$('#MyTable');
	
	var rows = $table.find('tbody>tr').get();
	rows.sort(function(a, b) {
	var keyA = $(a).attr('myAttr');
	var keyB = $(b).attr('myAttr');
	if (keyA < keyB) return -1;
	if (keyA > keyB) return 1;
	return 0;
	});
	$.each(rows, function(index, row) {
	$table.children('tbody').append(row);
	});
}



$('#frmOR').submit(function() {
	var subz = "YES";

  	if($('#txtnGross').val() == "" || $('#txtnGross').val() == 0){
		alert("Zero or Blank AMOUNT TO BE DEPOSITED is not allowed!");
		subz = "NO";
	}

	    			
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			
			if(lastRow==0){
				alert("Deposit Details Required!");
				subz = "NO";
			}
			else{
					
					$("#hdnrowcnt").val(lastRow);

			}

	
	if(subz=="NO"){
		return false;
	}
	else{
		
		$("#frmOR").submit();
	}

});


function disabled(){

	$("#frmOR :input").attr("disabled", true);
	
	
	$("#txtctranno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

	if(document.getElementById("hdnposted").value==1 && document.getElementById("hdnvoid").value==0){
		$("#btnentry").attr("disabled", false);
	}

	$("#btn-closemod").attr("disabled", false); 

	$(".kv-file-zoom").attr("disabled", false);

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
			var msgsx = "CANCELLED";
		}
		
		document.getElementById("statmsgz").innerHTML = "<font style=\"font-size: x-small\">TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!</font>";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{

		$("#frmOR :input").attr("disabled", false);

		$("#txtctranno").attr("readonly", true);
		$("#btnMain").attr("disabled", true);
		$("#btnNew").attr("disabled", true);
		$("#btnPrint").attr("disabled", true);
		$("#btnEdit").attr("disabled", true);

	}

}

function chkSIEnter(keyCode,frm){

	if(keyCode==13){			
		document.getElementById(frm).action = "Deposit_edit.php";
		document.getElementById(frm).submit();
	}
}


</script>


</body>
</html>
