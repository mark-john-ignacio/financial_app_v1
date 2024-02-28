<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvTrans.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];
	$EmpID = $_SESSION['employeeid'];

	$_SESSION['myxtoken'] = gen_token();


	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$EmpID' and pageid = 'InvTrans_edit.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}


	$arrseclist = array();
	$arrallsec = array();
	$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc, ifnull(B.UserID,'') as UserID From locations A left join users_sections B on A.nid = B.section_nid and B.UserID = '$EmpID' Where A.compcode='$company' Order By A.cdesc");
	$arrseclist[] = 0;
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		if($row0['UserID']==$EmpID){
			$arrsecrow[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);
			$arrseclist[] = $row0['nid'];
		}
		
		$arrallsec[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);
				
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css"/>
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

</head>

<body>
	<?php
		$sqlhead = mysqli_query($con,"Select A.* from invtransfer A where A.compcode='$company' and A.ctranno='".$_REQUEST['id']."'");
		if (mysqli_num_rows($sqlhead)!=0) {

		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){

			$selwhfrom = $row['csection1'];
			$selwhto = $row['csection2'];
			$seltype = $row['ctrantype'];
			$hdremarks = $row['cremarks'];
			$hddatecnt = $row['dcutdate'];

			$lCancelled = $row['lcancelled2'];
			$lPosted = $row['lapproved2'];

		}
	?>

		<form id="frmCount" name="frmCount" method="post" action="<?="https://".$_SERVER['SERVER_NAME']?>/Inventory/Transfers/InvTrans_EditToSave.php">

			<input type="hidden" name="hdnmyxfin" value="<?= $_SESSION['myxtoken'] ?? '' ?>">
			<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
    	<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">

			<fieldset>
				<legend><div class="col-xs-6 nopadding">Inventory Transfer Details</div>

				<div class= "col-xs-6 text-right nopadding" id="invcntstat">
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

				<div class="col-xs-12 nopadding">
					<div class="col-xs-2 nopadding">
						<b>Trans No: </b>
					</div>
					<div class="col-xs-3 nopadding">
						<input type="text" class="form-control input-sm" name="id" id="id" value="<?=$_REQUEST['id']?>" readonly>
					</div>

					<div class="col-xs-1 nopadding">
							&nbsp;
					</div>

					<div class="col-xs-4 nopadding" id="statmsgz">

					</div>
				</div>

				<div class="col-xs-12 nopadwtop">
					<div class="col-xs-2 nopadding" id="secfrom">
						<b><?=($seltype=="request") ? "Requesting Section" : "Issuing Section"?>: </b>
					</div>
					<div class="col-xs-3 nopadding">

							<?php
								foreach($arrsecrow as $localocs){
									if($localocs['nid']==$selwhfrom){
										$selwhfromdesc = $localocs['cdesc'];
									}
									
								}
							?>
							<input type="hidden" id="selwhfrom" value="<?=$selwhfrom ?>">
							<input type="text" class="form-control input-sm" value="<?=$selwhfromdesc?>" readonly>									
							
						</select>
					</div>
					
					<div class="col-xs-1 nopadding">
							&nbsp;
					</div>
						
					<div class="col-xs-2 nopadding">
						<b>Inventory Date: </b>
					</div>
					
					<div class="col-xs-2 nopadding">
						<input type="text" class="datepick form-control input-sm" id="txtdtrandate" value="<?php echo date_format(date_create($hddatecnt),'m/d/Y'); ?>" readonly>
					</div>

				</div>
		
				<div class="col-xs-12 nopadwtop">

					<div class="col-xs-2 nopadding" id="secto">
						<b><?=($seltype=="request") ? "Issuing Section" : "Receiving Section"?>: </b>
					</div>
					<div class="col-xs-3 nopadding">
						
							<?php
								foreach($arrsecrow as $localocs){
									if($localocs['nid']==$selwhto){
										$selwhtodesc = $localocs['cdesc'];
									}
									
								}
							?>
							<input type="hidden" id="selwhto" value="<?=$selwhto ?>">
							<input type="text" class="form-control input-sm" value="<?=$selwhtodesc?>" readonly>	

					</div>

					<div class="col-xs-1 nopadding">
							&nbsp;
					</div>

					<div class="col-xs-2 nopadding">
						<b>Transfer Type: </b>
					</div>
					<div class="col-xs-2 nopadding">
						<?php
							$seltypx = "";
							if($seltype=="request"){
								$seltypx = "Request";
							}elseif($seltype=="transfer"){
								$seltypx = "Transfer";
							}elseif($seltype=="fg_transfer"){
								$seltypx = "FG Transfer";
							}
						?>	
							<input type="hidden" id="selcntyp" value="<?=$selwhto ?>">
							<input type="text" class="form-control input-sm" value="<?=$seltypx?>" readonly>	
					</div>
				</div>

				<div class="col-xs-12 nopadwtop">
					<div class="col-xs-2 nopadding">
						<b>Remarks: </b>
					</div>
					<div class="col-xs-8 nopadding">
						<input type="text" class="form-control input-sm" id="txtccrems" value="<?=$hdremarks?>" placeholder="Enter Remarks..." readonly>
					</div>
				</div>
		
			</fieldset>	
<br><br>												
									<table name='MyTbl' id='MyTbl' class="table table-scroll table-striped table-condensed">
										<thead>
											<tr>
												<th width="50">&nbsp;<input type="hidden" name="rowcnt" id="rowcnt" value=""></th>
												<th width="150">Item Code</th>
												<th>Item Description</th>
												<th width="70">Unit</th>
												<th width="100" class="text-center" id="qty1desc"><?=($seltype=="request") ? "Requested Qty" : "Issued Qty"?></th>
												<th width="100" class="text-center" id="qty2desc"><?=($seltype=="request") ? "Issued Qty" : "Received Qty"?></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sqlhead = mysqli_query($con,"Select A.*, B.citemdesc from invtransfer_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno='".$_REQUEST['id']."'");
												if (mysqli_num_rows($sqlhead)!=0) {
										
													$cnt = 0;
													while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
														$cnt++;
											?>
												<tr>				
													<td><?=$cnt?></td>
													<td><input type='hidden' value='<?=$row['cidentity']?>' name="txtcidentity" id="txtcidentity<?=$cnt?>"><?=$row['citemno']?></td>
													<td><?=$row['citemdesc']?></td>
													<td><?=$row['cunit']?></td>
													<td>
														<input type='text' class="numeric form-control input-xs text-center" name="txtnqty1" id="txtnqty1<?=$cnt?>" value="<?=number_format($row['nqty1'],2)?>" readonly>
													</td>
													<td>
														<input type='text' class="numeric2 form-control input-xs text-center" name="txtnqty2" id="txtnqty2<?=$cnt?>" value="<?=number_format($row['nqty2'],2)?>">
													</td>
												</tr>
											<?php
													}
												}
											?>
										</tbody>
									</table>


			<br>

			<?php
				if($poststat == "True"){
			?>

			<table width="100%" border="0" cellpadding="3">
				<tr>
					<td>
						<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Inv.php';" id="btnMain" name="btnMain">
							Back to Main<br>(ESC)
						</button>

						<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location='https://<?=$_SERVER['SERVER_NAME']?>/Inventory/Transfers/InvTrans_New.php'" id="btnNew" name="btnNew">
							New<br>(F1)
						</button>

						<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="window.location='https://<?=$_SERVER['SERVER_NAME']?>/Inventory/Transfers/InvTrans_Edit.php?id=<?=$_REQUEST['id']?>'" id="btnUndo" name="btnUndo">
							Undo Edit<br>(CTRL+Z)
						</button>

						<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
							Edit<br>(CTRL+E)
						</button>

						<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (CTRL+S)</button>
					</td>
				</tr>
			</table>

			<?php
				}
			?>

		</form>

	<?php
		}
		else{
		?>
		<form action="InvCnt_Edit.php" name="frmpos2" id="frmpos2" method="get">
			<fieldset>
				<legend>Inventory Count Details</legend>	
					<table width="100%" border="0">
						<tr>
							<tH width="100">Trans No.:</tH>
							<td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="id" width="20px" tabindex="1" value="<?=$_REQUEST['id'];?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
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

</body>

</html>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey){//CTRL S
			if($("#btnSave").is(":disabled")==false){
				e.preventDefault();
				return chkform();
			}
	  }
	  else if(e.keyCode == 27){//ESC
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Inv.php';
			}
	  }

	});


	$(document).ready(function(e) {	

		$('body').on('keydown', 'input.numeric2', function(e) {
			if (e.which == 13) {
				var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
				focusable = form.find('input.numeric2').filter(':visible');
				next = focusable.eq(focusable.index(this)+1);

				if (next.length) {
					next.focus();
				}

				e.preventDefault();
				return false;
			}
		});

		disabled();
		
		$("input.numeric").autoNumeric('init',{mDec:2});
		$("input.numeric2").autoNumeric('init',{mDec:2});
		$("input.numeric2").on("focus", function () {
			$(this).select();
		});

		$(".datepick").datetimepicker({
      format: 'MM/DD/YYYY',
			useCurrent: false,
			//minDate: moment(),
			defaultDate: moment(),
		});

		$("#selwhfrom").on("change", function(){
			$("#MyTbl tbody").empty();
			loadItms();
		});

		$("#selcntyp").on("change", function(){
			if($(this).val()=="request"){
				$("#secfrom").html("<b>Requesting Section</b>");
				$("#secto").html("<b>Issuing Section</b>");
			}else{
				$("#secfrom").html("<b>Issuing Section</b>");
				$("#secto").html("<b>Receiving Section</b>");
			}
		});
	
	});


	function InsTotable(itmid,itmdesc,itmunit,sornum){

		//loop check if item exist
		var isExist = "False";
		$("#MyTbl > tbody > tr").each(function(index) {	
			citmno = $(this).find('input[type="hidden"][name="txtitmcode"]').val();

			if(citmno==itmid){
				isExist = "True";
			}
		});


		if(isExist=="False"){

			
			$("<tr>").append( 
				$("<td>").html(sornum), 
				$("<td>").html("<input type='hidden' value='"+itmid+"' name=\"txtitmcode\" id=\"txtitmcode"+sornum+"\">"+itmid),  
				$("<td>").html("<input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc\" id=\"txtitmdesc"+sornum+"\">"+itmdesc),
				$("<td>").html("<input type='hidden' value='"+itmunit+"' name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+itmunit),
				$("<td>").html("<input type='text' class=\"numeric form-control input-xs text-center\" name=\"txtnqty\" id=\"txtnqty"+sornum+"\" value=\"0\">"),
				$("<td align=\"center\">").html("<button class=\"btn btn-danger btn-xs\" id=\"btnDel\" id=\"btnDel"+sornum+"\"><i class=\"fa fa-times\"></i></button>")
			).appendTo("#MyTbl tbody");

			$("#btnDel"+sornum).on('click', function() {
				$(this).closest('tr').remove();
				Reinitialize();
			});

			$("input.numeric").autoNumeric('init',{mDec:2});
			$("input.numeric").on("focus", function () {
				$(this).select();
			});
			

		}
	}

	function chkform(){
		var qty = "True";

		var tbl1 = document.getElementById('MyTbl').getElementsByTagName('tr');
		var lastRow1 = tbl1.length-1;

		if(lastRow1!=0){
			$("#MyTbl > tbody > tr").each(function(index) {
				var nqty = $(this).find('input[name="txtnqty"]').val();
				if(nqty==""){
					qty = "False";
				}
			});

			if(qty=="False"){
				$("#AlertMsg").html("Blank quantity is not allowed!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
			}else{

				$("#MyTbl > tbody > tr").each(function(index) {
					$newval = index+1;

					$(this).find('input:hidden[name="txtcidentity"]').attr('name','txtcidentity'+$newval);
					$(this).find('input[name="txtnqty2"]').attr('name','txtnqty2'+$newval); 
				});

				$("#rowcnt").val(lastRow1);
				$("#frmCount").submit();

			}
		}else{
			$("#AlertMsg").html("No details to save!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');
		}
	}

	function disabled(){

		$("#frmCount :input").attr("disabled", true);

		$("#btnMain").attr("disabled", false);
		//$("#btnPrint").attr("disabled", false);
		$("#btnNew").attr("disabled", false);
		$("#btnEdit").attr("disabled", false); 

	}

	function enabled(){
		if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
			if(document.getElementById("hdnposted").value==1){
				var msgsx = "POSTED"
			}
			
			if(document.getElementById("hdncancel").value==1){
				var msgsx = "CANCELLED"
			}
			
			document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+"!";
			document.getElementById("statmsgz").style.color = "#FF0000";
			
		}
		else{
			
			$("#frmCount :input").attr("disabled", false);
			
				$("#btnMain").attr("disabled", true);
				//$("#btnPrint").attr("disabled", true);
				$("#btnNew").attr("disabled", true);
				$("#btnEdit").attr("disabled", true);
						

		}
	}

	function chkSIEnter(keyCode,frm){
		if(keyCode==13){
			document.getElementById(frm).action = "InvCnt_Edit.php";
			document.getElementById(frm).submit();
		}
	}


</script>
