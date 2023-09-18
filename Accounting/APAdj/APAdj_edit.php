<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "APAdj_edit.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$ddeldate = date("m/d/Y");
	$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));
	
	$cjeno = $_REQUEST['txtctranno'];

	$sqlhead = mysqli_query($con,"select a.*, b.cname from apadjustment a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.ctranno = '$cjeno'");

	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$ccode = $row['ccode'];
			$cname = $row['cname'];
			$ddate = $row['dcutdate'];
			$cremarks = $row['cremarks'];
			$ngross = $row['ngross'];
			$ctype = $row['ctype'];
			$crefsr = $row['crefsr'];
			$crefsi = $row['crefsi'];
			$lsreturn = $row['isreturn'];
			
			$lCancelled = $row['lcancelled'];
			$lPosted = $row['lapproved'];

			$lVoid = $row['lvoid'];
		}
	}

	@$arrfiles = array();
	@$arrname = array();

	if (file_exists('../../Components/assets/AP-ADJ/'.$company.'_'.$cjeno.'/')) {
		$allfiles = scandir('../../Components/assets/AP-ADJ/'.$company.'_'.$cjeno.'/');
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
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
  <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
    
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

	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<input type="hidden" value='<?=json_encode(@$arrname)?>' id="hdnfileconfig"> 
	<form action="APAdj_editsave.php" name="frmpos" id="frmpos" method="post" enctype="multipart/form-data">
		<fieldset>
    	<legend>
				<div class="col-xs-6 nopadding"> AP Adjustment Details </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
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
					<li class="active"><a href="#items" data-toggle="tab">AP Adjustment Details</a></li>
					<li><a href="#attc" data-toggle="tab">Attachments</a></li>
				</ul>
				
				<div class="tab-content">
					<div id="items" class="tab-pane fade in active" style="padding-left: 5px; padding-top: 10px;">

						<table width="100%" border="0">
							<tr>
								<tH width="150">&nbsp;Transaction No.:</tH>
								<td style="padding:2px;">
						
									<div class="col-xs-3 nopadding">
										<input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" placeholder="Enter Journal No..." required autocomplete="off" value="<?php echo $cjeno;?>"  onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
									</div>
									
									<input type="hidden" name="hdntranno" id="hdntranno" value="<?php echo $cjeno;?>">

									<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
									<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
									<input type="hidden" name="hdnvoid" id="hdnvoid" value="<?php echo $lVoid;?>">
									 
								</td>    
								<td colspan="2" style="padding:2px;" align="right">
					
									<div id="statmsgz" class="small" style="color:#F00"></div>
						
								</td>
							</tr>
							<tr>
								<tH width="150">&nbsp;Supplier:</tH>
								<td style="padding:2px">
									<div class="col-xs-12 nopadding">
										<div class="col-xs-3 nopadding">
											<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1" value="<?=$ccode?>">
												<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
										</div>

										<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  autocomplete="off" value="<?=$cname?>">
										</div> 
									</div>
								</td>
								<tH width="150">Date:</tH>
								<td style="padding:2px;">
								<div class="col-xs-11 nopadding">
								<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?=date_format(date_create($ddate), "m/d/Y"); ?>" />
								</div>
								</td>
							</tr>
							<tr>
								<tH width="100">&nbsp;Remarks:</tH>
								<td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value="<?=$cremarks?>"></div></td>
								<tH width="150">Type:</tH>
								<td style="padding:2px" align="right">
									<div class="col-xs-11 nopadding">
									<select name="seltype" id="seltype" class="form-control input-sm">
												<option value="Credit" <?=($ctype=="Credit") ? "selected" : "";?>>Credit</option>
												<option value="Debit" <?=($ctype=="Debit") ? "selected" : "";?>>Debit</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<tH width="100">&nbsp;<!--RETURN NO.:-->Reference:</tH>
								<td>       
									<div class="col-xs-12 nopadding">
											<div class="col-xs-3 nopadding">
												<input type="text" class="form-control input-sm" id="txtSIRef" name="txtSIRef" width="20px" tabindex="2" readonly placeholder="Search Sales Return No..."  value="<?=$crefsr ?>">
											</div>
							
											<div class="col-xs-1 nopadwleft">
												<button class="btncgroup btn btn-block btn-sm btn-danger" type="button" id="btnSISearch" onClick="InsertDet('REF');"><i class="fa fa-search"></i></button>
											</div>

											<div class="col-xs-3 nopadwleft">
												<input type="text" class="form-control input-sm" id="txtInvoiceRef" name="txtInvoiceRef" width="20px" tabindex="2" placeholder="Search Sales Invoice No..." readonly  value="<?=$crefsi ?>">      
												<input type="hidden" id="invtyp" name="invtyp" value="">      
											</div>
									</div>
								</td>
								<tH width="150">&nbsp;</tH>
								<td style="padding:2px"><div class="col-xs-11 nopadding">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" value="1" id="isReturn" name="isReturn" <?=($lsreturn==1) ? "checked": ""?>/>
										<label class="form-check-label" for="flexCheckChecked">Purchase Return</label>
									</div>
								</div></td>
								<td style="padding:2px"  align="right">&nbsp;</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td style="padding:2px">&nbsp;</td>
								<td style="padding:2px"  align="right">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="5">
								&nbsp;
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
					<div class="col-xs-12 nopadwdown"><b>Details</b></div>

						<div class="col-xs-12 nopadding">
							<div class="col-xs-4 nopadding"><small><i>*Press <b>ENTER</b> on remarks field (last row) to add new line..</i></small></div>
							<div class="col-xs-8 nopadding text-danger" style='text-align: right !important' id="unbaltext"></div>
						</div>

						<div class="alt2" dir="ltr" style="
							margin: 0px;
							padding: 3px;
							border: 1px solid #919b9c;
							width: 100%;
							height: 350px;
							text-align: left;
							overflow: auto">
			
							<table id="MyTable" class="MyTable table table-condensed" width="100%">
								<thead>
									<tr>
										<th style="border-bottom:1px solid #999">Account No.</th>
										<th style="border-bottom:1px solid #999">Account Title</th>
										<th style="border-bottom:1px solid #999">Debit</th>
										<th style="border-bottom:1px solid #999">Credit</th>
										<th style="border-bottom:1px solid #999">Remarks</th>
										<th style="border-bottom:1px solid #999">&nbsp;</th>
									</tr>
								</thead>   
								<tbody class="tbody">
									<?php
										$cnt = 0;
										$sqldtls = mysqli_query($con,"select * From apadjustment_t a where a.compcode='$company' and a.ctranno = '$cjeno'");
										while($row = mysqli_fetch_array($sqldtls, MYSQLI_ASSOC)){
											$cnt++;
									?>
									<tr>
										<td width="100px" style="padding:1px"><input type="text" class="typeno form-control input-xs" name="txtcAcctNo<?=$cnt?>" id="txtcAcctNo<?=$cnt?>"  placeholder="Enter Acct No..." autocomplete="off" onFocus="this.select();" data-id="txtcAcctDesc<?=$cnt?>" data-debit="txtnDebit<?=$cnt?>" value="<?=$row['cacctno']?>"></td>

										<td style="padding:1px"><input type="text" class="typedesc form-control input-xs" name="txtcAcctDesc<?=$cnt?>" id="txtcAcctDesc<?=$cnt?>"  placeholder="Enter Acct Description..." autocomplete="off" onFocus="this.select();" data-id="txtcAcctNo<?=$cnt?>" data-debit="txtnDebit<?=$cnt?>"  value="<?=$row['ctitle']?>"></td>
										<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnDebit<?=$cnt?>" id="txtnDebit<?=$cnt?>"  value="<?=$row['ndebit']?>" autocomplete="off"></td>
										<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnCredit<?=$cnt?>" id="txtnCredit<?=$cnt?>"  value="<?=$row['ncredit']?>" autocomplete="off"></td>
										<td width="200px" style="padding:1px"><input type="text" class="cRem form-control input-xs" name="txtcRem<?=$cnt?>" id="txtcRem<?=$cnt?>" placeholder="Remarks..." autocomplete="off" onFocus="this.select();"  value="<?=$row['cremarks']?>"></td>
										<td width="40px" align="right">&nbsp;</td>
									</tr>
									<?php
										}
									?>
								</tbody>                  
							</table>

						</div>


				<br>
				<table width="100%" border="0" cellpadding="3">
					<tr>
						<td>
							<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='APAdj.php';" id="btnMain" name="btnMain">
								Back to Main<br>(ESC)
							</button>

							<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='APAdj_new.php';" id="btnNew" name="btnNew">
								New<br>(F1)
							</button>

							<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
								Undo Edit<br>(CTRL+Z)
							</button>
										
							<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
								Edit<br>(CTRL+E)
							</button>
														
							<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
							<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
								SAVE<br> (F2)
							</button>
						</td>
						<td align="right" valign="top">
							<b>TOTAL AMOUNT </b>
							&nbsp;&nbsp;
							<input type="text" id="txtnGross" name="txtnGross" readonly value="<?=number_format($ngross,2)?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="25">
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
                        <button type="button" class="btnmodz btn btn-primary btn-sm" id="OK">Ok</button>
                        <button type="button" class="btnmodz btn btn-danger btn-sm" id="Cancel">Cancel</button>
                        
                        
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                        
                        <input type="hidden" id="typ" name="typ" value = "">
                        <input type="hidden" id="modzx" name="modzx" value = "">
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>

<!-- FULL PO LIST REFERENCES-->

<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="InvListHdr">Sales Return List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
                    <div class="col-xs-12 nopadding pre-scrollable" style="height:37vh">
                          <table name='MyInvTbl' id='MyInvTbl' class="table table-condensed">
                           <thead>
                            <tr>
                              <th>SR No.</th>
															<th>Receiving.</th>
															<th>Supp. Inv.</th>
                              <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                          </table>
                    </div>
         	            
			</div>
			
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End FULL INVOICE LIST -->
</body>
</html>

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
		fileslist.push("https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/AP-ADJ/<?=$company."_".$cjeno?>/" + object.name)

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
			url: "th_filedelete.php?id="+object.name+"&code=<?=$cjeno?>", 
			key: i + 1
		});
	}


	$(document).keydown(function(e) {	
	
	  if(e.keyCode == 113) { //F2
	  	  e.preventDefault();
		 if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		  return chkform();
		 }
	  }
	  else if(e.keyCode == 27){ //ESC
		  e.preventDefault();
		if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		 window.location.replace("ARAdj.php");
	    }

	  }
	
	
	});

	$(document).ready(function(){
		disabled();

		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});

	  $('#date_delivery').datetimepicker({
      format: 'MM/DD/YYYY',
			//minDate: new Date(),
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
				initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/Components/assets/AP-ADJ/<?=$company."_".$cjeno?>/{filename}',
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

		$("input.numeric").autoNumeric('init',{mDec:2});
		$("input.numeric").on("focus click", function () {
			$(this).select();
		});

		$('#MyTable :input').keydown(function(e) {
			//TABLE NAVIGATION
			var inFocus = $(this).attr('id');
			tblnavigate(e.keyCode,inFocus);	   

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
											
							$('#hdnvalid').val("YES");
							
							$('#txtremarks').focus();
											
						}
						else{
							$('#txtcustid').val("");
							$('#txtcust').val("");
							$('#imgemp').attr("src","../../images/blueX.png");
							
							$('#hdnvalid').val("NO");
						}
					},
					error: function(){
						$('#txtcustid').val("");
						$('#txtcust').val("");
						$('#imgemp').attr("src","../../images/blueX.png");
						
						$('#hdnvalid').val("NO");
					}
				});

			}
		
		});

		$('#txtcust, #txtcustid').on("blur", function(){
			if($('#hdnvalid').val()=="NO"){
				$('#txtcust').attr("placeholder", "ENTER A VALID CUSTOMER FIRST...");
			}else{				
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
				
				$('#hdnvalid').val("YES");
				
				$('#txtremarks').focus();			
				
			}
		
		});
	
		var sltypprev_val;
			
		$('#seltype').on('focus', function () {
				sltypprev_val = $(this).val();
			}).change(function() {

			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			
			if(lastRow != 0){
		
				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("<b>Note: </b>Changing selection will remove all your details!");
				$("#alertbtnOK").hide();
				$("#OK").show();
				$("#Cancel").show();
				$("#AlertModal").modal('show');
				
					$('.btnmodz').on('click', function(){
						var x = $(this).attr('id');
						
						if(x=="OK"){
							$('#MyTable > tbody > tr').not(':first').empty();
						}
						else{
							
							$('#seltype option[value="'+sltypprev_val+'"]').prop('selected', true);
						}
						
						$("#AlertModal").modal('hide');
					});

			}
			else{
				$('#MyTable > tbody > tr').not(':first').empty();
			}
					
		});
	
		$('body').on('keyup', '.typedesc', function() {

			var varid = $(this).attr("id"); 
			var varidno = $(this).attr("data-id"); 
			var variddr = $(this).attr("data-debit");

			$("input.typedesc").typeahead({
				autoSelect: true,
				source: function(request, response) {
					$.ajax({
						url: "../th_accounts.php",
						dataType: "json",
						data: {
							query: request, id: "cacctdesc"
						},
						success: function (data) {
							response(data);
						}
					});
				},
				displayText: function (item) {
					return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
				},
				highlighter: Object,
				afterSelect: function(item) { 	
					$('#'+varid).val(item.name).change(); 
					$('#'+varidno).val(item.id); 
					$('#'+variddr).focus();													
				}
			});
		});	

		$('body').on('keyup', '.typeno', function() {

			var varid = $(this).attr("id"); 
			var varidno = $(this).attr("data-id"); 
			var variddr = $(this).attr("data-debit"); 

			$("input.typeno").typeahead({
				autoSelect: true,
				source: function(request, response) {
					$.ajax({
						url: "th_accounts.php",
						dataType: "json",
						data: {
							query: request, id: "cacctid"
						},
						success: function (data) {
							response(data);
						}
					});
				},
				displayText: function (item) {
					return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.name+'</span><br><small>' + item.id + '</small>';
				},
				highlighter: Object,
				afterSelect: function(item) { 	
					$('#'+varid).val(item.id).change(); 
					$('#'+varidno).val(item.name); 
					$('#'+variddr).focus();													
				}
			});
		});	

		$('body').on('keyup', '#txtInvoiceRef', function() {
			$("#txtInvoiceRef").typeahead({
				autoSelect: true,
				source: function(request, response) {
					$.ajax({
						url: "th_invoices.php",
						dataType: "json",
						data: {
							query: request, ccode: $("#txtcustid").val()
						},
						success: function (data) {
							response(data);
						}
					});
				},
				displayText: function (item) {
					return '<div style="border-top:1px solid gray; width: 300px"><span clas="dropdown-item-extra">'+item.no+'</span><br><small>' + item.cname + '</small><br><small>' + item.cutdate;
				},
				highlighter: Object,
				afterSelect: function(item) { 		
					$('#txtInvoiceRef').val(item.no).change(); 	
					$('#invtyp').val(item.typx).change();							
				}
			});
		});

		$('body').on('keypress', '.cRem', function(e) {
			if(e.keyCode==13){
				var cnt = $('#MyTable tr').length;
				var inFocus = $(this).attr('id');
				var thisName = inFocus.replace(/\d+/g, '');
				var thisindex = inFocus.replace(/\D/g,'');

				var lstrow = parseInt(cnt)-1;

				if(parseInt(thisindex)==lstrow){
					InsertRows(thisName,cnt);
				}
			}
		});

		$('body').on('keyup', '.numeric', function(e) {
			computegross();
		});

		$('#isReturn').change(function() {
			$("#txtSIRef").val("");
			$("#txtInvoiceRef").val("");

			$('#MyTable > tbody > tr').not(':first').empty();		

      if(this.checked) {				
				$("#btnSISearch").attr("disabled", false);
				$("#txtInvoiceRef").attr("readonly", true);
			}else{
				$("#btnSISearch").attr("disabled", true);
				$("#txtInvoiceRef").attr("readonly", false);
			}
		});

	});

	function computegross(){
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var cnt = tbl.length;
		
			cnt = cnt - 1;

			var xdeb = 0;
			var xcrd = 0;
			
			for (i = 1; i <= cnt; i++) {
				xdeb = xdeb + parseFloat($('#txtnDebit'+i).val().replace(/,/g,''));
				xcrd = xcrd + parseFloat($('#txtnCredit'+i).val().replace(/,/g,''));
			}

			var totdebit = xdeb.toFixed(2);
			var totcredit = xcrd.toFixed(2);
			
			if(totdebit==totcredit){
				$("#txtnGross").val(totdebit);
				$("#txtnGross").autoNumeric('destroy');
				$("#txtnGross").autoNumeric('init',{mDec:2});
				//document.getElementById("grosmsg").innerHTML = "";

				$("#unbaltext").html("");
			}
			else{ 
				$("#unbaltext").html('<b>Unbalanced: (DR: '+totdebit+', CR: '+totcredit+')</b>');
				$("#txtnGross").val(0);
			}
	}

	function InsertDet(){
		if($("#txtcust").val()!="" || $("#txtcustid").val()!=""){

			$('#MyInvTbl tbody').empty();

			$('#InvListHdr').html("Invoice List: " + $('#txtcust').val());
		
				var ccode = $("#txtcustid").val();						
				var xstat = "YES";
						
				$.ajax({
					url: 'th_qolist.php',
					data: 'x='+ccode,
					dataType: 'json',
					method: 'post',
					success: function (data) {

						console.log(data);
						$.each(data,function(index,item){
									
							if(item.cpono=="NONE"){
								$("#AlertMsg").html("No Sales Return Available");
								$("#alertbtnOK").show();
								$("#OK").hide();
								$("#Cancel").hide();
								$("#AlertModal").modal('show');

								xstat = "NO";
									
								$("#txtcustid").attr("readonly", false);
								$("#txtcust").attr("readonly", false);
							}
							else{

								if(item.crefinv!==""){
									
									$("<tr>").append(
										$("<td id='td"+item.cpono+"'>").html("<a href=\"javascript:;\" data-dismiss=\"modal\" onclick=\"setinvref('"+item.cpono+"', '"+item.crefinv+"')\">"+item.cpono+"</a>"),
										$("<td>").text(item.cref),
										$("<td>").text(item.crefinv),
										$("<td>").text(item.dcutdate)
									).appendTo("#MyInvTbl tbody");
																
									$("#td"+item.cpono).on("mouseover", function(){
										$(this).css('cursor','pointer');
									});

								}else{
									$("<tr>").append(
										$("<td id='td"+item.cpono+"'>").html(item.cpono),
										$("<td>").text(item.cref),
										$("<td>").text("No Inv."),
										$("<td>").text(item.dcutdate)
									).appendTo("#MyInvTbl tbody");
								}
								
							}
						});
							
						if(xstat=="YES"){
							$('#mySIRef').modal("show");
						}
					},
					error: function (req, status, err) {
						console.log('Something went wrong', status, err);
						$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
						$("#alertbtnOK").show();
						$("#OK").hide();
						$("#Cancel").hide();
						$("#AlertModal").modal('show');
					}
				});
		}else{
			$("#AlertMsg").html("&nbsp;&nbsp;Enter a valid customer first...");
			$("#alertbtnOK").show();
			$("#OK").hide();
			$("#Cancel").hide();
			$("#AlertModal").modal('show');
			
			$('#txtcust').focus();
		}
	}

	function setinvref(srno,invno){
		$('#txtSIRef').val(srno);
		$('#txtInvoiceRef').val(invno);
		$('#invtyp').val(typx);		

		//default entry from invoice... reverese
		$('#MyTable > tbody').empty();	

		$.ajax({
			url: 'th_getsientry.php',
			data: 'srno='+srno+'&invno='+invno,
			dataType: 'json',
			method: 'post',
			success: function (data) {

				console.log(data);
				$.each(data,function(index,item){

					rowCount = index + 1;

					$('#MyTable > tbody:last-child').append(
						'<tr>'// need to change closing tag to an opening `<tr>` tag.
						+'<td width="100px" style="padding:1px"><input type="text" class="typeno form-control input-xs" name="txtcAcctNo'+rowCount+'" id="txtcAcctNo'+rowCount+'"  placeholder="Enter Acct No..." autocomplete="off" onFocus="this.select();" data-id="txtcAcctDesc'+rowCount+'" data-debit="txtnDebit'+rowCount+'" value="'+item.cacctid+'"></td>'
						+'<td style="padding:1px"><input type="text" class="typedesc form-control input-xs" name="txtcAcctDesc'+rowCount+'" id="txtcAcctDesc'+rowCount+'"  placeholder="Enter Acct Description..." autocomplete="off" onFocus="this.select();" data-id="txtcAcctNo'+rowCount+'" data-debit="txtnDebit'+rowCount+'" value="'+item.cacctdesc+'"> </td>'
						+'<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnDebit'+rowCount+'" id="txtnDebit'+rowCount+'" autocomplete="off" value="'+item.ndebit+'"</td>'
						+'<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnCredit'+rowCount+'" id="txtnCredit'+rowCount+'" autocomplete="off" value="'+item.ncredit+'"></td>'
						+'<td width="200px" style="padding:1px"><input type="text" class="cRem form-control input-xs" name="txtcRem'+rowCount+'" id="txtcRem'+rowCount+'" placeholder="Remarks..." autocomplete="off" onFocus="this.select();"></td>'
						+'<td width="40px" align="right"><input class="btn btn-danger btn-xs" type="button" id="row_'+rowCount+'_delete" value="delete" onClick="deleteRow(this);"/></td>'+'</tr>'
					);

				});

				$("input.numeric").autoNumeric('init',{mDec:2});
				$("input.numeric").on("focus click", function () {
					$(this).select();
				});

				computegross()

			}
		});
	}

	function InsertRows(thisNme,rowCount){

		$('#MyTable > tbody:last-child').append(
			'<tr>'// need to change closing tag to an opening `<tr>` tag.
			+'<td width="100px" style="padding:1px"><input type="text" class="typeno form-control input-xs" name="txtcAcctNo'+rowCount+'" id="txtcAcctNo'+rowCount+'"  placeholder="Enter Acct No..." autocomplete="off" onFocus="this.select();" data-id="txtcAcctDesc'+rowCount+'" data-debit="txtnDebit'+rowCount+'"></td>'
			+'<td style="padding:1px"><input type="text" class="typedesc form-control input-xs" name="txtcAcctDesc'+rowCount+'" id="txtcAcctDesc'+rowCount+'"  placeholder="Enter Acct Description..." autocomplete="off" onFocus="this.select();" data-id="txtcAcctNo'+rowCount+'" data-debit="txtnDebit'+rowCount+'"> </td>'
			+'<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnDebit'+rowCount+'" id="txtnDebit'+rowCount+'" value="0.00" autocomplete="off"></td>'
			+'<td width="100px" style="padding:1px"><input type="text" class="numeric form-control input-xs" style="text-align:right" name="txtnCredit'+rowCount+'" id="txtnCredit'+rowCount+'" value="0.00" autocomplete="off"></td>'
			+'<td width="200px" style="padding:1px"><input type="text" class="cRem form-control input-xs" name="txtcRem'+rowCount+'" id="txtcRem'+rowCount+'" placeholder="Remarks..." autocomplete="off" onFocus="this.select();"></td>'
			+'<td width="40px" align="right"><input class="btn btn-danger btn-xs" type="button" id="row_'+rowCount+'_delete" value="delete" onClick="deleteRow(this);"/></td>'+'</tr>'
		);

		$("input.numeric").autoNumeric('init',{mDec:2});
		$("input.numeric").on("focus click", function () {
			$(this).select();
		});
		$("#txtcAcctDesc"+rowCount).focus();

	}

	function tblnavigate(x,txtinput){
		
		var inputCNT = txtinput.replace(/\D/g,'');
		var inputNME = txtinput.replace(/\d+/g, '');
		
		switch(x){
			case 39: // <Left>
				if(inputNME=="txtcAcctNo"){
					$("#txtcAcctDesc"+inputCNT).focus();
				}
				else if(inputNME=="txtcAcctDesc"){
					$("#txtnDebit"+inputCNT).focus();
				}
				else if(inputNME=="txtnDebit"){
					$("#txtnCredit"+inputCNT).focus();
				}
				else if(inputNME=="txtnCredit"){
					$("#txtcRem"+inputCNT).focus();
				}
				else if(inputNME=="txtcRem"){
					var idx =  parseInt(inputCNT) + 1;
					$("#txtcAcctNo"+idx).focus();
				}
				
				break;
			case 38: // <Up>  
				var idx =  parseInt(inputCNT) - 1;
				$("#"+inputNME+idx).focus();
				break;
			case 37: // <Right>
				if(inputNME=="txtcAcctNo"){
					var idx =  parseInt(inputCNT) - 1;
					$("#txtcRem"+idx).focus();
				}
				else if(inputNME=="txtcAcctDesc"){
					$("#txtcAcctNo"+inputCNT).focus();
				}
				else if(inputNME=="txtnDebit"){
					$("#txtcAcctDesc"+inputCNT).focus();
				}
				else if(inputNME=="txtnCredit"){
					$("#txtnDebit"+inputCNT).focus();
				}
				else if(inputNME=="txtcRem"){
					$("#txtnCredit"+inputCNT).focus();
				}

				break;
			case 40: // <Down>
				var idx =  parseInt(inputCNT) + 1;
								$("#"+inputNME+idx).focus();
				break;
		}       

	}

	function deleteRow(r) {
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length;
		var i=r.parentNode.parentNode.rowIndex;
		document.getElementById('MyTable').deleteRow(i);

		var lastRow = tbl.length;
		var z; //for loop counter changing textboxes ID;

		for (z=i+1; z<=lastRow; z++){  
			var tempcAcctNo = document.getElementById('txtcAcctNo' + z);
			var tempcAcctDesc = document.getElementById('txtcAcctDesc' + z);
			var tempnDebit = document.getElementById('txtnDebit' + z);
			var tempnCredit= document.getElementById('txtnCredit' + z);
			var tempcRem= document.getElementById('txtcRem' + z);
			var tempdel = document.getElementById('row_'+z+'_delete');
			
			var x = z-1;
			tempcAcctNo.id = "txtcAcctNo" + x;
			tempcAcctNo.name = "txtcAcctNo" + x;
			tempcAcctDesc.id = "txtcAcctDesc" + x;
			tempcAcctDesc.name = "txtcAcctDesc" + x;
			tempnDebit.id = "txtnDebit" + x;
			tempnDebit.name = "txtnDebit" + x;
			tempnCredit.id = "txtnCredit" + x;
			tempnCredit.name = "txtnCredit" + x;
			tempcRem.id = "txtcRem" + x;
			tempcRem.name = "txtcRem" + x;
			tempdel.id = "row_"+x+"_delete";
			tempdel .name = "row_"+x+"_delete"; 

			$("#txtcAcctDesc"+x).data("id","txtcAcctNo" + x);
			$("#txtcAcctDesc"+x).data("debit","txtnDebit" + x);

			$("#txtcAcctNo"+x).data("id","txtcAcctDesc" + x);
			$("#txtcAcctNo"+x).data("debit","txtnDebit" + x);

		}
		GoToComp("txtnDebit" + x);
		
		GoToComp("txtnCredit" + x);
	}

	function chkform(){
		
		var tbl1 = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRowRR = tbl1.length-1;

		var isOK = "YES";
		if(lastRowRR==0){  
			alert("Transaction has No Details!");
			return false;
		}
		else{
			
			if($("#txtnGross").val()==0 || $("#txtnGross").val()==""){

				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Please check your details!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');

				isOK=="NO";
				return false;
			}
			
			if(isOK=="YES"){
				
				$("#hdnrowcnt").val(lastRowRR);
				$("#frmpos").submit();
			
			}

		}

	}

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "APAdj_edit.php";
			document.getElementById(frm).submit();
		}
	}

function disabled(){

	$("#frmpos :input").attr("disabled", true);
	
	
	$("#txtctranno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
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
		
		$("#statmsgz").html("&nbsp;&nbsp;TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!");
		//$("#statmsgz").show();
		
	}
	else{

		$("#frmpos :input").attr("disabled", false);
		
			$("#txtctranno").val($("#hdntranno").val());
			$("#txtctranno").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
				
	}

}

</script>