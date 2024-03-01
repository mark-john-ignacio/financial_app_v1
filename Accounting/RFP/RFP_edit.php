<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "RFP.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$ccvno = $_REQUEST['txtctranno'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'RFP_edit.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//echo $_SERVER['SERVER_NAME'];

	$sqlchk = mysqli_query($con,"select a.*, b.cname from rfp a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.ctranno='$ccvno'");

	@$arrfiles = array();
	@$arrname = array();

	if (file_exists('../../Components/assets/RFP/'.$company.'_'.$ccvno.'/')) {
		$allfiles = scandir('../../Components/assets/RFP/'.$company.'_'.$ccvno.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {

			$fileNameParts = explode('.', $file);
			$ext = end($fileNameParts);

			@$arrname[] = array("name" => $file, "ext" => $ext);
		}
	
	}else{
		//echo "NO FILES";
	}

	//echo json_encode(@$arrname);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
  <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>
<script src="../../include/autoNumeric.js"></script>
<!--
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
-->
<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">

<input type="hidden" value='<?=json_encode(@$arrname)?>' id="hdnfileconfig"> 

<?php
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){

			$cCode = $row['ccode'];
			$cName = $row['cname'];
			
			$cpaymeth = $row['cpaymethod']; 

			$cBank = $row['cbankname'];
			$cBankAcct = $row['cbankacctno'];
			$cBankAcNm = $row['cbankacctname']; 

			$dTransdate = $row['dtransdate'];

			$cnAmount = $row['ngross']; 
			$cnBalamt = $row['nbalance'];

			$cdRemarks = $row['cremarks'];

			$ccurrcode = $row['ccurrencycode'];  
			$ccurrdesc = $row['ccurrencydesc']; 
			$ccurrrate = $row['nexchangerate'];
		
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
			$lVoid = $row['lvoid'];
		}
?>
	<form action="RFP_editsave.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" method="post" enctype="multipart/form-data" onsubmit="return chkform()">
		<fieldset>
				<legend>
					<div class="col-xs-6 nopadding"> Request For Payment Details </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
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
						<li class="active"><a href="#apv">APV List</a></li>
						<li><a href="#attc">Attachments</a></li>
					</ul>

					<div class="tab-content">  

						<div id="apv" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px;">

							<table width="100%" border="0" cellspacing="0" cellpadding="2">
								<tr>
									<tH>Tran No.:</tH>
									<td>
										<div class="col-xs-12"  style="padding-left:2px">
											<div class="col-xs-4 nopadding">
												<input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
												</div>
											</div>
										
										<input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $ccvno;?>">
										
										<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
										<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
										<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>"> 
										
									</td>
									<td colspan="2" align="right"><div id="statmsgz" class="small" style="display:inline"></div></td>
									
								</tr>

								<tr>
									<td><span style="padding:2px"><b>Paid To:</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px">
											<div class="col-xs-4 nopadding ">
													<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm required" required placeholder="Supplier Code..." readonly value="<?=$cCode?>">
											</div>
											<div class="col-xs-8 nopadwleft">
													<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4" value="<?=$cName?>">
											</div>
										</div>
									</td>
									<td><span style="padding:2px" id="chkdate"><b>Due Date:</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<div class='col-xs-8 nopadding'>
													<input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate" value="<?=date("m/d/Y",strtotime($dTransdate));?>" />
											</div>
										</div>
									</td>
								</tr>
							
								<tr>
									<td width="150"><span style="padding:2px" id="paymntdesc"><b>Payment Details</b></span></td>
								
									<td>
										<div class="col-xs-12" style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-4 nopadding">
												<select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
													<option value="cheque" <?=($cpaymeth=="cheque") ? "selected" : ""?>>Cheque</option>
													<option value="cash" <?=($cpaymeth=="cash") ? "selected" : ""?>>Cash</option>
													<option value="bank transfer" <?=($cpaymeth=="bank transfer") ? "selected" : ""?>>Bank Transfer</option>
													<option value="mobile payment" <?=($cpaymeth=="mobile payment") ? "selected" : ""?>>Mobile Payment</option>
													<option value="credit card" <?=($cpaymeth=="credit card") ? "selected" : ""?>>Credit Card</option>
													<option value="debit card" <?=($cpaymeth=="debit card") ? "selected" : ""?>>Debit Card</option>
												</select>
											</div>
											<div class="col-xs-8 nopadwleft" >
												<input type='text' class="form-control input-sm" name="txtBankName" id="txtBankName" value="<?=$cBank?>" placeholder="Enter Bank Name..."/>   
											</div>
									</td>
									<td valign="top" style="padding-top:8px;"><span style="padding:2px" id="chkdate"><b>Total Amount to Pay:</b></span></td>
									<td valign="top">
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<div class='col-xs-8 nopadding'>
													<input type='text' class="form-control input-sm text-right" name="txtnamount" id="txtnamount" value="<?=$cnAmount?>" readonly/>
													<input type='hidden' name="txtnamountbal" id="txtnamountbal" value="<?=$cnBalamt?>" />   
											</div>
										</div>
									</td>	
								</tr>
								<tr>
									<td width="150"><span style="padding:2px">&nbsp;</span></td>
									<td>
										<div class="col-xs-12" style="padding-left:2px; padding-bottom:2px">	
											<div class="col-xs-5 nopadding">
												<input type='text' class="form-control input-sm" name="txtAcctNo" id="txtAcctNo" value="<?=$cBankAcct?>" placeholder="Enter Account No..."/>
											</div>
											<div class="col-xs-7 nopadwleft" >
												<input type='text' class="form-control input-sm" name="txtAcctName" id="txtAcctName" value="<?=$cBankAcNm?>" placeholder="Enter Account Name..."/>
											</div>
										</div>
									</td>	
									<td valign="top" style="padding-top:8px;"><span style="padding:2px" id="chkdate">&nbsp;</span></td>
									<td valign="top">&nbsp;</td>								
								</tr>
								<tr>
									<td valign="top" style="padding-top:8px;" rowspan="2"><span style="padding:2px;"><b>Remarks</b></span></td>
									<td  rowspan="2">
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<textarea class="form-control input-sm" id="txtcremarks" name="txtcremarks" rows="3"><?=$cdRemarks?></textarea>
										</div>
									</td>
									<td valign="top" style="padding-top:8px;"><span style="padding:2px" id="chkdate">&nbsp;</span></td>
									<td valign="top">&nbsp;</td>				
								</tr>

								<tr>
									<td valign="top" style="padding-top:8px;"><span style="padding:2px" id="chkdate">&nbsp;</span></td>
									<td valign="top">&nbsp;</td>																					
								</tr>

								<tr>
									<td width="150"><span style="padding:2px" id="paymntdesc"><b>Currency</b></span></td>
									<td>
										<div class="row nopadding">
											<div class="col-xs-7 nopadwleft">
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
											<div class="col-xs-3" id="statgetrate" style="padding: 4px !important"> 																	
											</div>
										</div>
									</td>
									<td width="150">&nbsp;</td>
									<td>&nbsp;</td>		
														
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

								<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 1px solid #919b9c;width: 100%;height: 40vh;text-align: left;overflow: auto">
									<input type='hidden' name="hdndetails" id="hdndetails" value="0"/>
									<table id="MyTable" class="MyTable table table-condensed" width="100%">
										<thead>
											<tr>
												<th style="border-bottom:1px solid #999">APV No.</th>
												<th style="border-bottom:1px solid #999">Date</th>
												<th style="border-bottom:1px solid #999">Account Code</th>
												<th style="border-bottom:1px solid #999">Account Title</th>
												<th style="border-bottom:1px solid #999">Amount</th>
												<th style="border-bottom:1px solid #999">&nbsp;</th>
											</tr>	
											</thead>														
										<tbody class="tbody">
											<?php 
												$sqlbody = mysqli_query($con,"select a.* from rfp_t a where a.compcode = '$company' and a.ctranno = '$ccvno' order by a.nid");

												if (mysqli_num_rows($sqlbody)!=0) {
													$cntr = 0;
													while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
														$cntr = $cntr + 1;
											?>
												<tr>
													<td> <?=$rowbody['capvno']?> <input type='hidden' id='txtcapvno<?=$cntr?>' name='txtcapvno' value='<?=$rowbody['capvno']?>'> </td>
													<td> <?=$rowbody['dapvdate']?> <input type='hidden' id='txtcapvdate<?=$cntr?>' name='txtcapvdate' value='<?=$rowbody['dapvdate']?>'> </td>
													<td> <?=$rowbody['cacctno']?> <input type='hidden' id='txtapvacctid<?=$cntr?>' name='txtapvacctid' value='<?=$rowbody['cacctno']?>'> </td>
													<td> <?=$rowbody['cacctdesc']?> <input type='hidden' id='txtapvacctitle<?=$cntr?>' name='txtapvacctitle' value='<?=$rowbody['cacctdesc']?>'> </td>
													<td width="150px"> <input type='text' class='numeric form-control input-xs text-right' id='txtapvbal<?=$cntr?>' name='txtapvbal' value='<?=$rowbody['npayable']?>'> <input type='hidden' id='txtapvamt<?=$cntr?>' name='txtapvamt' value='<?=$rowbody['ngrossamt']?>'> </td>
													<td align='center'><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo<?=$cntr?>' value='delete' /></td>
												</tr>
												<script>
														$("#delinfo<?=$cntr?>").on('click', function() { 
															$(this).closest('tr').remove();
															recomdel();
															comtotamt();
														});
												</script>
											<?php
													}
												}
											?>

										</tbody>															
									</table>

								</div>
				
					<?php
						if($poststat=="True"){
					?>
					<br>
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<td width="60%" rowspan="2"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
													
											<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='RFP.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>';" id="btnMain" name="btnMain">
												Back to Main<br>(ESC)
											</button>
										
											<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='RFP_new.php';" id="btnNew" name="btnNew">
												New<br>(F1)
											</button>

											<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
												Undo Edit<br>(CTRL+Z)
											</button>
									
											<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?=$ccvno?>');" id="btnPrint" name="btnPrint">
												Print<br>(F4)
											</button>

											<button type="button" class="btn btn-info btn-sm" id="btnShowApv" name="btnShowApv">
												APV<br> (Insert)
											</button>	
								    
											<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
												Edit<br>(CTRL+E)
											</button>
											
											<button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave" name="btnSave">
												Save<br>(CTRL+S)
											</button>
											
							</td>
						</tr>
						
					</table>
					<?php
						}
					?>
			</fieldset>

	</form>

<?php
	}else{
?>
	<form action="RFP_edit.php" name="frmpos2" id="frmpos2" method="post">
		<fieldset>
			<legend>Request For Payment</legend>	
			<table width="100%" border="0">
				<tr>
					<tH width="100">Tran No.:</tH>
					<td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $ccvno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
					</tr>
				<tr>
					<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>Transaction No. DID NOT EXIST!</b></font></tH>
					</tr>
			</table>
		</fieldset>
	</form>
<?php
	}
?>

				<!-- DETAILS ONLY -->
				<div class="modal fade" id="myAPModal" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="APListHeader">AP List</h3>
							</div>
										
							<div class="modal-body pre-scrollable">
										
								<table name='MyAPVList' id='MyAPVList' class="table table-small table-hoverO" style="cursor:pointer">
									<thead>
										<tr>
											<th> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
											<th>AP No.</th>
											<th>Date</th>
											<th>Account</th>
											<th>Total Payable</th>
											<th>Payable Balance</th>
											<th>Currency</th>
										</tr>
									</thead>
									<tbody>
																			
									</tbody>
								</table>

							</div> 
										
							<div class="modal-footer">
								<button type="button" id="btnSave2" onClick="InsertSI()" class="btn btn-primary">Insert</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
							</div>        	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Bootstrap modal -->

				<!-- Banks List -->
				<div class="modal fade" id="myChkModal" role="dialog" data-keyboard="false" data-backdrop="static">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="BanksListHeader">Bank List</h3>
							</div>
							
							<div class="modal-body pre-scrollable">
							
								<table name='MyDRDetList' id='MyDRDetList' class="table table-small table-hoverO" style="cursor:pointer">
									<thead>
										<tr>
											<th>Bank Code</th>
											<th>Bank Name</th>
											<th>Bank Acct No</th>
										</tr>
									</thead>
									<tbody>																
									</tbody>
								</table>
							</div>         	
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Banks modal -->

				<!-- 1) Alert Modal -->
				<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
					<div class="vertical-alignment-helper">
						<div class="modal-dialog vertical-align-top">
							<div class="modal-content">
								<div class="alert-modal-danger">
									<p id="AlertMsg"></p>
									<p><center>
										<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
									</center></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- End Alert modal -->

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

</body>
</html>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

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
		fileslist.push("https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/RFP/<?=$company."_".$ccvno?>/" + object.name)

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
			url: "th_filedelete.php?id="+object.name+"&code=<?=$ccvno?>", 
			key: i + 1
		});
	}

	<?php
		if($poststat=="True"){
	?>
		$(document).keydown(function(e) {	 
			
			if(e.keyCode == 112) { //F1
				if($("#btnNew").is(":disabled")==false){
					e.preventDefault();
					window.location.href='RFP_new.php';
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
					printchk('<?=$ccvno?>');
				}
			}
			else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
				if($("#btnUndo").is(":disabled")==false){
					e.preventDefault();
					chkSIEnter(13,'frmpos');
				}
			}
			else if(e.keyCode == 45) { //Insert
				if($('#myAPModal').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
					var custid = $("#txtcustid").val();
					showapvmod(custid);
				}
			}
			else if(e.keyCode == 27){//ESC
				if($("#btnMain").is(":disabled")==false){
					e.preventDefault();
					$("#btnMain").click();
				}
			}
		});
	<?php
		}
	?>

	$(document).ready(function() {

		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});

		$("#txtnamount").autoNumeric('init',{mDec:2,wEmpty:'zero'});

		$("input.numeric").autoNumeric('destroy');
		$("input.numeric").autoNumeric('init',{mDec:2});

		$("input.numeric").on("focus", function () {
			$(this).select();
		});

		$("input.numeric").on("keyup", function () {
			comtotamt();
		});
		
		$('#txtChekDate').datetimepicker({
			format: 'MM/DD/YYYY',
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
				initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/RFP/<?=$company."_".$ccvno?>/{filename}',
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
				return '<div style="border-top:1px solid gray; width: 300px"><span><b>' + item.id + '</span><br><small>' + item.value + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 

				$('#txtcust').val(item.value).change(); 
				$("#txtcustid").val(item.id);

				$("#selbasecurr").val(item.cdefaultcurrency).change();
				$("#basecurrval").val($("#selbasecurr").find(':selected').data('val'));
				$("#hidcurrvaldesc").val($("#selbasecurr").find(':selected').data('desc'));
					
				showapvmod(item.id);

			}
		});

		$("#btnsearchbank").on("click", function() {

			$('#MyDRDetList tbody').empty();
			
				$.ajax({
					url: 'th_banklist.php',
					dataType: 'json',
					async:false,
					method: 'post',
					success: function (data) {
					// var classRoomsTable = $('#mytable tbody');
						console.log(data);
						$.each(data,function(index,item){

								$("<tr id=\"bank"+index+"\">").append(
									$("<td>").text(item.ccode),
									$("<td>").text(item.cname),
									$("<td>").text(item.cbankacctno)
								).appendTo("#MyDRDetList tbody");
									
							$("#bank"+index).on("click", function() {
								$("#txtBank").val(item.ccode);
								$("#txtBankName").val(item.cname);
								
								$("#myChkModal").modal("hide");
							});

						});

					},
					error: function (req, status, err) {

						$("#AlertMsg").html("<b>ERROR: </b>Something went wrong!<br>Status: "+ status + "<br>Error: "+err);
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

						console.log('Something went wrong', status, err);
					}
				});

			
			$("#myChkModal").modal("show");
		});
		
		$("#btnShowApv").on("click", function() {
			if($("#txtcustid").val()!==""){
				showapvmod($("#txtcustid").val());
			}else{
				$("#AlertMsg").html("<b>ERROR: </b>Pick a valid customer!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
			}
			
		});
		
		$("#allbox").click(function(e){
			var table= $(e.target).closest('table');
			$('td input:checkbox',table).not(this).prop('checked', this.checked);
		});

		$("#selbasecurr").on("change", function (){
	
			var dval = $(this).find(':selected').attr('data-val');
			var ddesc = $(this).find(':selected').attr('data-desc');

			$("#basecurrval").val(dval);
			$("#hidcurrvaldesc").val(ddesc);
			$("#statgetrate").html("");

			$('#MyTable tbody').empty();
				
		});

		disabled();

	});
			
	function showapvmod(custid){

		$('#APListHeader').html("AP List: "+$('#txtcust').val()+" ("+$('#selbasecurr').val()+")");

		$('#MyAPVList tbody').empty();
		
		if ( $.fn.DataTable.isDataTable('#MyAPVList') ) {
			$('#MyAPVList').DataTable().destroy();
		}

		$.ajax({
			url: 'th_APVlist.php',
			data: { code: custid, curr:$('#selbasecurr').val() },
			dataType: 'json',
			async:false,
			method: 'post',
			success: function (data) {

				console.log(data);
				$.each(data,function(index,item){
							
					if(item.ctranno=="NO"){
						alert("No Available Reference.");
										
						$('#txtcust').val("").change(); 
						$("#txtcustid").val("");

					}
					else{
				
						$("<tr id=\"APV"+index+"\">").append(
							$("<td>").html("<input type='checkbox' value='"+index+"' name='chkSales[]'>"), 
							$("<td>").html(item.ctranno+"<input type='hidden' id='APVtxtno"+index+"' name='APVtxtno' value='"+item.ctranno+"'>"),
							$("<td>").html(item.dapvdate+"<input type='hidden' id='APVdte"+index+"' name='APVdte' value='"+item.dapvdate+"'>"),
							$("<td>").html(item.cacctno+" - "+item.cacctdesc+"<input type='hidden' id='APVAcctPay"+index+"' name='APVAcctPay' value='"+item.cacctno+"'><input type='hidden' id='APVAcctPayDesc"+index+"' name='APVAcctPayDesc' value='"+item.cacctdesc+"'>"),
							$("<td align='right'>").html(item.namount+"<input type='hidden' id='APVamt"+index+"' name='APVamt' value='"+item.namount+"'>"),
							$("<td align='right'>").html(item.nbalance+"<input type='hidden' id='APVBal"+index+"' name='APVBal' value='"+item.nbalance+"'>"),
							$("<td align='center'>").html(item.ccurrencycode)
							
						).appendTo("#MyAPVList tbody");
										
						$("#myAPModal").modal("show");
									
					}

				});

				$('#MyAPVList').dataTable({
					"info":false, 
					"ordering":true, 
					"paging":false,
					"autoWidth": false,
					"columnDefs": [
						{ "width": "5%", "className": "text-center", "targets": 0 },
						{ "width": "10%", "targets": 1 },
						{ "width": "8%", "targets": 2 }
					],					
				});

			},
			error: function (req, status, err) {

				$("#AlertMsg").html("<b>ERROR: </b>Something went wrong!<br>Status: "+ status + "<br>Error: "+err);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				console.log('Something went wrong', status, err);
			}
		});

	}

	function InsertSI(){	

		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var tblrowcnt = tbl.length;

		$("input[name='chkSales[]']:checked").each( function () {

			xyz = $(this).val();
			
			var a = $("#APVtxtno"+xyz).val();
			var b = $("#APVamt"+xyz).val();
			var c = $("#APVBal"+xyz).val();
			var d = $("#APVAcctPay"+xyz).val();
			var e = $("#APVAcctPayDesc"+xyz).val(); 
			var f = $("#APVdte"+xyz).val();

			var tdapcm = "<td>"+a+"<input type='hidden' id='txtcapvno"+tblrowcnt+"' name='txtcapvno' value='"+a+"'></td>";
			var tddate = "<td>"+f+"<input type='hidden' id='txtcapvdate"+tblrowcnt+"' name='txtcapvdate' value='"+f+"'></td>";
			var tdaccid = "<td>"+d+"<input type='hidden' id='txtapvacctid"+tblrowcnt+"' name='txtapvacctid' value='"+d+"'></td>";
			var tdaccdsc = "<td>"+e+"<input type='hidden' id='txtapvacctitle"+tblrowcnt+"' name='txtapvacctitle' value='"+e+"'></td>";
			var tdamt = "<td width='150px'><input type='text' class='numeric form-control input-xs text-right' id='txtapvbal"+tblrowcnt+"' name='txtapvbal' value='"+c+"'> <input type='hidden' id='txtapvamt"+tblrowcnt+"' name='txtapvamt' value='"+b+"'></td>";
			var tddels = "<td align='center'><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo" + tblrowcnt + "' value='delete' /></td>";

			$('#MyTable > tbody:last-child').append('<tr>'+tdapcm + tddate + tdaccid + tdaccdsc + tdamt + tddels + '</tr>'); 

			$("#delinfo"+tblrowcnt).on('click', function() { 
				$(this).closest('tr').remove();
				recomdel();
				comtotamt();
			});

			$("input.numeric").autoNumeric('destroy');
			$("input.numeric").autoNumeric('init',{mDec:2});

			$("input.numeric").on("focus", function () {
				$(this).select();
			});

			$("input.numeric").on("keyup", function () {
				comtotamt();
			});

			tblrowcnt = tblrowcnt + 1;

		});

		$('#myAPModal').modal('hide');
		comtotamt();

	};

	function recomdel(){
		$("#MyTable > tbody > tr").each(function(index) {
			tx = index + 1;
			$(this).find('input[name="txtcapvno"]').attr("id","txtcapvno"+tx);
			$(this).find('input[name="txtcapvdate"]').attr("id","txtcapvdate"+tx);
			$(this).find('input[name="txtapvacctid"]').attr("id","txtapvacctid"+tx);
			$(this).find('input[name="txtapvacctitle"]').attr("id","txtapvacctitle"+tx);
			$(this).find('input[name="txtapvbal"]').attr("id","txtapvbal"+tx);
			$(this).find('input[name="txtapvamt"]').attr("id","txtapvamt"+tx);
			$(this).find('input[name="delinfo"]').attr("id","delinfo"+tx);
		});
	}

	function comtotamt(){
		var rowCount = $('#MyTable tr').length;			
		var gross = 0;

		if(rowCount>1){
			for (var i = 1; i <= rowCount-1; i++) {
				gross = gross + parseFloat($("#txtapvbal"+i).val().replace(/,/g,''));
			}
		}

		$("#txtnamount").val(gross);
		$("#txtnamount").autoNumeric('destroy');
		$("#txtnamount").autoNumeric('init',{mDec:2});
	}

	function chkform(){
		
		var emptyFields = $('input.required').filter(function() {
			return $(this).val() === "";
		}).length;

		if (emptyFields === 0) {

			var tx = 0;
			$("#MyTable > tbody > tr").each(function(index) {
				tx = index + 1;
				$(this).find('input[name="txtcapvno"]').attr("name","txtcapvno"+tx);
				$(this).find('input[name="txtcapvdate"]').attr("name","txtcapvdate"+tx);
				$(this).find('input[name="txtapvacctid"]').attr("name","txtapvacctid"+tx);
				$(this).find('input[name="txtapvacctitle"]').attr("name","txtapvacctitle"+tx);
				$(this).find('input[name="txtapvbal"]').attr("name","txtapvbal"+tx);
				$(this).find('input[name="txtapvamt"]').attr("name","txtapvamt"+tx);
				$(this).find('input[name="delinfo"]').attr("name","delinfo"+tx);
			});

			$("#hdndetails").val(tx);
			
			return true;
		} else {
			
			$("#AlertMsg").html("<b>ERROR: </b>Required Fields!<br>Supplier Code/Name, Bank Code/Name, and APV No.");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
		}

	}

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "RFP_edit.php";
			document.getElementById(frm).submit();
		}
	}

	function disabled(){

		$("#frmpos :input").attr("disabled", true);
		
		$("#txtctranno").attr("disabled", false);
		$("#btnMain").attr("disabled", false);
		$("#btnNew").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
		$("#btnEdit").attr("disabled", false);
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
				var msgsx = "CANCELLED"
			}
			
			document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
			
		}
		else{

			$("#frmpos :input").attr("disabled", false);
			
				
				$("#txtctranno").attr("readonly", true);
				$("#txtctranno").val($("#hdnorigNo").val());
				
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

				var url = "RFP_print.php?x="+x;
				
				$("#myprintframe").attr('src',url);


				$("#PrintModal").modal('show');

		}
	}
</script>
