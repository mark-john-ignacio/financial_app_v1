<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "SalesRet";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');
	require('../../Model/helper.php');

	$company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'SalesRet_edit'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	if(isset($_REQUEST['txtctranno'])){
		$txtctranno = $_REQUEST['txtctranno'];
	}
	else{
		$txtctranno = $_REQUEST['txtcsalesno'];
	}

	$sqlhead = mysqli_query($con,"select a.*,b.cname from salesreturn a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.ctranno = '$txtctranno' and a.compcode='$company'");

	/*
	function listcurrencies(){ //API for currency list
		$apikey = $_SESSION['currapikey'];
		
		//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
		//$obj = json_decode($json, true);

		$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
		
		return $json;
	}
	*/
	@$arrname = array();
	$directory = "../../Components/assets/SR/{$company}_{$txtctranno}/";
	if(file_exists($directory)){
		@$arrname = file_checker($directory);
	}

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
	<script src="../../include/autoNumeric.js"></script>
<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
-->

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
	<?php

		if (mysqli_num_rows($sqlhead)!=0) {
			while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
				$CustCode = $row['ccode'];
				$CustName = $row['cname'];
				$Remarks = $row['cremarks'];
				$Date = $row['dreceived'];
				$Gross = $row['ngross'];
				/*
				//$cpricever = $row['cpricever'];
				//$nlimit = $row['nlimit'];

				$nbasegross = $row['nbasegross'];
				$ccurrcode = $row['ccurrencycode']; 
				$ccurrdesc = $row['ccurrencydesc']; 
				$ccurrrate = $row['nexchangerate']; 
				*/
				
				$lCancelled = $row['lcancelled'];
				$lPosted = $row['lapproved'];
				$lVoid = $row['lvoid'];
			}

	?>

	<form action="SR_edit.php?hdnsrchval=<?=(isset($_REQUEST['hdnsrchval'])) ? $_REQUEST['hdnsrchval'] : ""?>" name="frmpos" id="frmpos" onSubmit="return false;" method="post">
		<fieldset>
			<legend>
				<div class="col-xs-6 nopadding"> Sales Return Details </div>  <div class= "col-xs-6 text-right nopadding" id="salesstat">
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
					<li class="active"><a href="#home" data-toggle="tab">Details</a></li>
					<li><a href="#attc" data-toggle="tab">Attachments</a></li>
				</ul>

					<div class="tab-content">
						<div id="home" class="tab-pane fade in active" style="padding-left: 5px; padding-top:10px">
							<table width="100%" border="0">
								<tr>
									<tH>&nbsp;Trans No.:</tH>
									<td style="padding:2px">
										<div class="col-xs-3 nopadding">   
											<input type="text" class="form-control input-sm" id="txtcsalesno" name="txtcsalesno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');">
										</div>     
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
									<tH width="100">&nbsp;Customer:</tH>
									<td style="padding:2px">
										<div class="col-xs-12 nopadding">
											<div class="col-xs-3 nopadding">
												<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1" value="<?php echo $CustCode; ?>">
												<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
												<input type="hidden" id="hdnpricever" name="hdnpricever" value="">
											</div>

											<div class="col-xs-8 nopadwleft">
												<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" value="<?php echo $CustName; ?>">
											</div> 
										</div>
									</td>
									<tH width="150">Delivery Date:</tH>
									<td style="padding:2px;">
										<div class="col-xs-10 nopadding">
											<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($Date),'m/d/Y'); ?>" />
										</div>
									</td>
								</tr>

								<tr>
									<tH width="100">&nbsp;Remarks:</tH>
									<td style="padding:2px">
										<div class="col-xs-11 nopadding">
											<input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value="<?php echo $Remarks;?>">
										</div>
									</td>
									<tH width="150" style="padding:2px">&nbsp;</tH>
									<td style="padding:2px" align="right">&nbsp;</td>
								</tr>

								<tr>
									<td colspan="4">&nbsp;</td>
								</tr>

								<tr>
									<td colspan="5">
										<input type="hidden" id="txtprodid" name="txtprodid">
										<input type="hidden" id="txtprodnme" name="txtprodnme">
										<input type="hidden" name="hdnqty" id="hdnqty">
										<input type="hidden" name="hdnqtyorig" id="hdnqtyorig">
										<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
										<input type="hidden" name="hdnunit" id="hdnunit">
									</td>
								</tr>
							</table>
						</div>
						<div id="attc" class="tab-pane fade in" style="padding-left: 5px; padding-top:10px">

							<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
							<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
							<input type="file" name="upload[]" id="file-0" multiple />
						
						</div>
					</div>

				
				
				<hr>
<div class="col-xs-12 nopadwdown"><b>Details</b></div>

        <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 250px;
					text-align: left;
					overflow: auto">
	
          <table id="MyTable" class="MyTable table table-condensed" width="100%">
						<thead>
							<tr>
								<th style="border-bottom:1px solid #999">Code</th>
								<th style="border-bottom:1px solid #999">Description</th>
              	<th style="border-bottom:1px solid #999">UOM</th>
                <th style="border-bottom:1px solid #999">Qty</th>
                <th style="border-bottom:1px solid #999">Reason</th>
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
						<td rowspan="2">
							<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
				
							<?php
								if($poststat == "True"){
							?>
							
							<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='SR.php?ix=<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>&st=<?=isset($_REQUEST['hdnsrchsta']) ? $_REQUEST['hdnsrchsta'] : ""?>';" id="btnMain" name="btnMain">
								Back to Main<br>(ESC)
							</button>
					
							<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='SR_new.php';" id="btnNew" name="btnNew">
								New<br>(F1)
							</button>

							<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
								SI<br>(Insert)
							</button>

							<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
								Undo Edit<br>(F3)
							</button>

							<?php
								}

								$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'SalesRet_print'");

								if(mysqli_num_rows($sql) == 1){
								
							?>
									<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $txtctranno;?>');" id="btnPrint" name="btnPrint">
							Print<br>(F4)
									</button>

							<?php		
								}

								if($poststat == "True"){
							?>
						
							<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
								Edit<br>(F8)    
							</button>
							
							<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
								Save<br>(F2)    
							</button>

							<?php
								}
							?>
						</td>
					</tr>
						<!--
						<td align="right"><b>Gross Amount : 
							<input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="<?//php echo number_format($nbasegross,4); ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
						</b></td>

					</tr>
					<tr>
							<td align="right" valign="top">
							<b>Gross Amount in <//?php echo $nvaluecurrbase; ?></b>&nbsp;&nbsp;
							<input type="text" id="txtnGross" name="txtnGross" readonly value="<?//php echo number_format($Gross,4); ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
								</td>
					</tr>
					-->
				</table>
				
				
    </fieldset>
	</form>

	<?php
		}
		else{
	?>
		<form action="SR_edit.php" name="frmpos2" id="frmpos2">
			<fieldset>
				<legend>Sales Return</legend>	
				<table width="100%" border="0">
					<tr>
						<tH width="100">TRANS NO.:</tH>
						<td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
						</tr>
					<tr>
						<tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>SR No. DID NOT EXIST!</b></font></tH>
						</tr>
				</table>
			</fieldset>
		</form>
	<?php
		}
	?>

		<!-- Reference List Modal-->
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
                  <table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight small">
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
              <button type="button" id="btnInsDet" onClick="InsertSI()" class="btn btn-primary">Insert</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
       	 </div>
    	</div>
		</div>

		<!-- Alert Modal -->
		<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    	<div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
          <div class="modal-content">
            <div class="alert-modal-danger">
              <p id="AlertMsg"></p>
							<p><center><button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button></center></p>
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
        </div>
   		</div>
		</div>

</body>
</html>

<script type="text/javascript">
	var xtoday = new Date();
	var xdd = xtoday.getDate();
	var xmm = xtoday.getMonth()+1; //January is 0!
	var xyyyy = xtoday.getFullYear();

	xtoday = xmm + '/' + xdd + '/' + xyyyy;
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

		$vrx = encodeURIComponent(name);
		list_file.push("<?=$AttachUrlBase?>SR/<?=$company."_".$txtctranno?>/" + $vrx)
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
}

	<?php
		if($poststat == "True"){
	?>
		$(document).keydown(function(e) {	 
			if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='SR_new.php';
			}
			}
			else if(e.keyCode == 113){//F2
			if($("#btnSave").is(":disabled")==false){
				return chkform();
			}
			}
			else if(e.keyCode == 119){//F8
			if($("#btnEdit").is(":disabled")==false){
				enabled();
			}
			}
			else if(e.keyCode == 115){//F4
			if($("#btnPrint").is(":disabled")==false){
				e.preventDefault();
				printchk('<?php echo $txtctranno;?>');
			}
			}
			else if(e.keyCode == 114){//F3
			if($("#btnUndo").is(":disabled")==false){
				e.preventDefault();
				chkSIEnter(13,'frmpos');
			}
			}
			else if(e.keyCode == 27){//ESC
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				window.location.href='SR.php';
			}
			}
			else if(e.keyCode == 45) { //Insert
				if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false && $("#btnIns").is(":disabled")==false){
				openinv();
			}
			}

		});
	<?php
		}
	?>
	
	$(document).ready(function(e) {
		
		loaddetails();	
		disabled();
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
				initialPreviewDownloadUrl: 'https://<?=$_SERVER['HTTP_HOST']?>/RFP_Files/<?=$company."_".$txtctranno?>/{filename}',
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

	  $('#date_delivery').datetimepicker({
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
							$('#imgemp').attr("src",data[3]);
							$('#hdnpricever').val(data[2]);
											
							$('#hdnvalid').val("YES");
							
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
				
			//  $('#txtprodnme').attr("disabled", true);
			// $('#txtprodid').attr("disabled", true);
			}else{
				
			//  $('#txtprodnme').attr("disabled", false);
			//  $('#txtprodid').attr("disabled", false);
				
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


function addItemName(qty,ndisc,price,curramt,amt,factr,cref,xreason,ident){

	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
/*
		var isItem = "NO";
		var disID = "";

			$("#MyTable > tbody > tr").each(function() {	
				disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				disref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				disiDent = $(this).find('input[type="hidden"][name="txtnident"]').val();
				//alert(disiDent);
				if($("#txtprodid").val()==disID && cref==disref && ident==disiDent){
					
					isItem = "YES";

				}
			});	

		 if(isItem=="NO"){	
			*/
			myFunctionadd(qty,ndisc,price,curramt,amt,factr,cref,xreason,ident);
			
			//ComputeGross();	
	
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
		$("#hdnqtyorig").val("");
		
	 }

}

function myFunctionadd(qty,ndisc,pricex,currcode,currate,factr,cref,creason,ident){
	//alert("hello");
	var itmcode = $("#txtprodid").val();
	var itmdesc = $("#txtprodnme").val();
	var itmqtyunit = $("#hdnqtyunit").val();
	var itmqty = $("#hdnqty").val();
	var itmqtyorig = $("#hdnqtyorig").val();
	var itmunit = $("#hdnunit").val();
	var itmccode = $("#hdnpricever").val();
	
	var itmreason = creason;
	
	//alert(itmqtyunit);
	if(qty=="" && pricex=="" && amtx=="" && factr==""){
		var itmtotqty = 1;
		var itmtotqtyorig = 1;
		var price = pricex;
		//var amtz = pricex;
		var factz = 1;
	}
	else{
		var itmtotqty = qty
		var itmtotqtyorig = itmqtyorig;
		var price = pricex;
		var price = pricex;
	//	var amtz = amtx;	
		var factz = factr;	
	}
			
	if(cref==null){
		cref = "";
	}
	if(creason==null){
		creason = "";
	}
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference\"><input type='hidden' value='"+ident+"' name=\"txtnident\" id=\"txtnident\"></td>";
	
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	
	var tditmunit = "<td width=\"100\" nowrap> <input type='hidden' value='"+itmunit+"' name=\"seluom\" id=\"seluom"+lastRow+"\">"+itmunit+"</select> </td>";
	
	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' data-main='"+itmtotqtyorig+"'> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+factz+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> <input type='hidden' value='"+price+"' name=\"txtnprice\" id='txtnprice"+lastRow+"'> <input type='hidden' value='"+currcode+"' name=\"txtcurrcode\" id='txtcurrcode"+lastRow+"'> <input type='hidden' value='"+currate+"' name=\"txtcurrate\" id='txtcurrate"+lastRow+"'> </td>";

	var tditmreason = "<td width=\"120\" nowrap> <input type='text' value='"+creason+"' class='form-control input-xs' name=\"txtcreason\" id=\"txtcreason\" placeholder=\"Reason...\"> </td>";
	
	var tditmdel = "<td width=\90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + ident + "' value='delete' onClick=\"deleteRow(this);\"/> </td>";


	$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + tditmunit + tditmqty + tditmreason + tditmdel + '</tr>');

									$("#del"+ident).on('click', function() {
										$(this).closest('tr').remove();
									});


									//$("input.numeric").numeric();
									$("input.numeric").autoNumeric('init',{mDec:2});
									$("input.numeric").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function () {

									  if(parseFloat($(this).val()) > parseFloat($(this).data("main"))){
										  alert("Quantity is greater than the remaining qty!");
										  $(this).val($(this).data("main")).change();
									  }

									  if($(this).val() == 0){
											alert("Quantity cannot be zero!");
										  $(this).val($(this).data("main")).change(); 
									  }

									  // ComputeAmt($(this).attr('id'));
									 //  ComputeGross();
									});
																		
								//	ComputeGross();
									
									
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
			$('#InvListHdr').html("SI List: " + $('#txtcust').val())

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
						$("#AlertMsg").html("No Sales Invoice Available");
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
							  
					salesnos = salesnos +  $(this).find('input[type="hidden"][name="txtnident"]').val();
				}
				
			});

					//alert('th_qolistdet.php?x='+drno+"&y="+salesnos);
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
								var xxmsg = "<input type='checkbox' value='"+item.ident+"' name='chkSales[]' data-id=\""+drno+"\">";
								
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

	//check muna if pareparehas ng currency
	
	//get defsult curr if may laman na ang details
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var tblrowcnt = tbl.length;

	var trannocurr = "";
	var trannocurrcnt = 0;

		if(tblrowcnt>1){
			trannocurr = $("#selbasecurr").val();
			trannocurrcnt = 1;
		}

		$("input[name='chkSales[]']:checked").each( function () {

			if(trannocurr != $(this).data("curr")){
				trannocurr = $(this).data("curr");
				trannocurrcnt++;
			}
		});

	if(trannocurrcnt>1){
		alert("Multi currency in one invoice is not allowed!");
	}
	else{

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
							$("#hdnqtyorig").val(item.nqty);
							$("#hdnqtyunit").val(item.cqtyunit);
							//alert(item.cqtyunit);

							addItemName(item.totqty,item.ndiscount,item.nprice,item.ccurrencycode,item.nexchangerate,item.nfactor,item.xref,"",item.ident)
											   
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

}


function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "SR_edit.php";
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
		
		document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{
		
		$("#frmpos :input").attr("disabled", false);
		
			
			$("#txtcsalesno").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
					
		//ComputeGross();
				

	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{

		//   var url = "SR_confirmprint.php?x="+x;
		var url = "SR_printv1.php?tranno="+x;
		  
		  $("#myprintframe").attr('src',url);


		  $("#PrintModal").modal('show');

	}
}


function loaddetails(){
	
	$.ajax ({
		url: "th_loaddetails.php",
		data: { id: $("#txtcsalesno").val() },
		async: false,
		dataType: "json",
		success: function( data ) {
							
			console.log(data);
			$.each(data,function(index,item){
			
				$('#txtprodnme').val(item.desc); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit); 
				$("#hdnqty").val(item.nqty);
				$("#hdnqtyorig").val(item.norigqty);
				$("#hdnqtyunit").val(item.cqtyunit); 

				//qty,ndisc,price,curramt,amt,factr,cref,xreason,ident
				addItemName(item.totqty,item.ndiscount,item.nprice,item.ccurrencycode,item.nexchangerate,item.nfactor,item.xref,item.xreason,item.ident)
				
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
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
			}
			
		});
		
		if(msgz!=""){
			$("#AlertMsg").html("&nbsp;&nbsp;Details Error: "+msgz);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
			ISOK = "NO";
		}
	}

	
	if(ISOK == "YES"){
		var isDone = "True";
		
		var myform = $("#frmpos").serialize();

		//alert("SR_updatehdr.php?" +myform);
		var formdata = new FormData($('#frmpos')[0]);
		formdata.delete('upload[]');
		jQuery.each($('#file-0')[0].files, function(i, file){
			formdata.append('file-'+i, file);
		})

		$.ajax ({
			url: "SR_updatehdr.php",
			data: formdata,
			cache: false,
			processData: false,
			contentType: false,
			type: "post",
			method: "post",
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING SALES RETURN: </b> Please wait a moment...");
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
		
		//alert(trancode);
		
		if(trancode!=""){
			//Save Details
				$("#MyTable > tbody > tr").each(function(index) {	
				//alert(index);
				//if($(this).find('input[type="hidden"][name="txtitemcode"]').val() != ""){
					
					var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val();
					var nident = $(this).find('input[type="hidden"][name="txtnident"]').val();
					var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
					var cuom = $(this).find('input[type="hidden"][name="seluom"]').val();
					var nqty = $(this).find('input[name="txtnqty"]').val();
					var nqtyorig = $(this).find('input[name="txtnqty"]').data("main");
					var nprice = $(this).find('input[type="hidden"][name="txtnprice"]').val();
					//var ndiscount = $(this).find('input[name="txtndisc"]').val(); 
					var currcode = $(this).find('input[type="hidden"][name="txtcurrcode"]').val();
					var currate = $(this).find('input[type="hidden"][name="txtcurrate"]').val();
					var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
					var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
					var creason = $(this).find('input[name="txtcreason"]').val();
				
					alert("trancode=" + trancode +"&crefno=" + crefno+"&indx=" + index+"&citmno=" + citmno+"&cuom=" + cuom+"&nqty=" + nqty +"&mainunit=" + mainunit+"&nfactor=" + nfactor + "&creason=" + creason + "&ident=" + nident + "&nqtyorig=" + nqtyorig + "&nprice=" + nprice + "&currcode=" + currcode + "&currate=" + currate);
					
						$.ajax ({
							url: "SR_newsavedet.php",
							data: { trancode: trancode, crefno: crefno, indx:index, citmno: citmno, cuom: cuom, nqty:nqty, mainunit:mainunit, nfactor:nfactor, creason:creason, ident:nident, nqtyorig:nqtyorig, nprice:nprice, currcode:currcode, currate:currate },
							async: false,
							beforeSend: function(){
								$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING SALES RETURN DETAILS: </b> Please wait a moment...");
								$("#alertbtnOK").hide();
								$("#AlertModal").modal('show');
							},
							success: function( data ) {
								if(data.trim()=="False"){
									isDone = "False";
								}
								else{
									//alert(data.trim())
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
			
							$("#txtcsalesno").val(trancode);
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



</script>