<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvTrans";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];
	$EmpID = $_SESSION['employeeid'];

	$_SESSION['myxtoken'] = gen_token();


	$poststat = "True"; 
	$sql = mysqli_query($con,"select * from users_access where userid = '$EmpID' and pageid = 'InvTrans_edit'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	$printstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$EmpID' and pageid = 'InvTrans_print'");
	if(mysqli_num_rows($sql) == 0){
		$printstat = "False";
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

	$arrtempsname[] = array();
	$sqltempname = mysqli_query($con,"select * from invtransfer_template_names where compcode='$company'");
	$rowdettempname= $sqltempname->fetch_all(MYSQLI_ASSOC);
	foreach($rowdettempname as $row0){
		$arrtempsname[] = $row0;
	}

	$arrtemplates= array();
	$sqltemplate = mysqli_query($con,"select A.section_nid as nid, A.citemno, B.citemdesc, B.cunit, A.sortnum, A.template_id from invtransfer_template A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.section_nid in (".implode(",",$arrseclist).") Order By A.section_nid, A.sortnum");
	$rowTemplate = $sqltemplate->fetch_all(MYSQLI_ASSOC);
	foreach($rowTemplate as $row0){
		$arrtemplates[] = array('itemid' => $row0['citemno'], 'itemdesc' => $row0['citemdesc'], 'itemunit' => $row0['cunit'], 'itemsort' => $row0['sortnum'], 'secid' => $row0['nid'], 'template_id' => $row0['template_id']);
	}

	$prntnme = array();
	$sqltempname = mysqli_query($con,"select * from nav_menu_prints where compcode='$company'");
	$rowdettempname= $sqltempname->fetch_all(MYSQLI_ASSOC);
	foreach($rowdettempname as $row0){
		$prntnme[$row0['code']] = $row0['filename'];
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
	<input type="hidden" id="hdntemplist" value='<?=json_encode($arrtemplates)?>'>
	<input type="hidden" id="hdntempsname" value='<?=json_encode($arrtempsname)?>'>

	<?php
		$sqlhead = mysqli_query($con,"Select * from invtransfer where compcode='$company' and ctranno='".$_REQUEST['id']."'");
		if (mysqli_num_rows($sqlhead)!=0) {

		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){

			$selwhfrom = $row['csection1'];
			$selwhto = $row['csection2'];
			$seltype = $row['ctrantype'];
			$hdremarks = $row['cremarks'];
			$hddatecnt = $row['dcutdate'];

			$seltemid = $row['template_id'];

			$lCancelled = $row['lcancelled1'];
			$lPosted = $row['lapproved1'];

		}
	?>

		<form id="frmCount" name="frmCount" method="post" action="<?="http://".$_SERVER['SERVER_NAME']?>/Inventory_BPTI/Transfers/InvTrans_EditSave.php">

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
						
						<select class="form-control input-sm" name="selwhfrom" id="selwhfrom">
						<?php
								foreach($arrsecrow as $localocs){
							?>
								<option value="<?php echo $localocs['nid'];?>" <?=($selwhfrom==$localocs['nid']) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
							<?php	
								}						
							?>
						</select>
					</div>
					
					<div class="col-xs-1 nopadding"> 
							&nbsp;
					</div>
						
					<div class="col-xs-2 nopadding">
						<b>Inventory Template: </b>
					</div>
					
					<div class="col-xs-2 nopadding">
						<select class="form-control input-sm" name="seltempname" id="seltempname">			
							<option value="">None</option>
							<?php
								foreach($arrtempsname as $xcfv){
									if($xcfv['section_nid']==$selwhfrom){
							?>	
								<option value="<?=$xcfv['nid']?>" <?=($seltemid==$xcfv['nid']) ? "selected" : ""?>><?=$xcfv['tempname']?></option>	
							<?php
									}
								}
							?>					
						</select>
					</div>

				</div>
		
				<div class="col-xs-12 nopadwtop">

					<div class="col-xs-2 nopadding" id="secto">
						<b><b><?=($seltype=="request") ? "Issuing Section" : "Receiving Section"?>: </b></b>
					</div>
					<div class="col-xs-3 nopadding">
						
						<select class="form-control input-sm" name="selwhto" id="selwhto">
						<?php
							$issel = 0;
								foreach($arrallsec as $localocs){
									$issel++;
							?>
								<option value="<?php echo $localocs['nid'];?>" <?=($selwhto==$localocs['nid']) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
							<?php	
								}						
							?>	
						</select>
					</div>
					<div class="col-xs-1 nopadding">
							&nbsp;
					</div>
						
					<div class="col-xs-2 nopadding">
						<b>Inventory Date: </b>
					</div>
					
					<div class="col-xs-2 nopadding">
						<input type="text" class="datepick form-control input-sm" name="txtdtrandate" id="txtdtrandate" value="<?php echo date_format(date_create($hddatecnt),'m/d/Y'); ?>">
					</div>
					
				</div>

				<div class="col-xs-12 nopadwtop">
					<div class="col-xs-2 nopadding">
						<b>Remarks: </b>
					</div>
					<div class="col-xs-3 nopadding">
						<textarea class="form-control input-sm" name="txtccrems" id="txtccrems" value="" placeholder="Enter Remarks..."> <?=$hdremarks?> </textarea>
					</div>

					<div class="col-xs-1 nopadding">
							&nbsp;
					</div>

					<div class="col-xs-2 nopadding">
						<b>Transfer Type: </b>
					</div>
					<div class="col-xs-2 nopadding">
						<select class="form-control input-sm" name="selcntyp" id="selcntyp">			
							<option value="request" <?=($seltype=="request") ? "selected" : ""?> data-prt="<?=$prntnme['INVTRANS_REQUEST']?>">MRS - Material Requisition Slip</option>		
							<option value="fg_transfer" <?=($seltype=="fg_transfer") ? "selected" : ""?> data-prt="<?=$prntnme['INVTRANS_FGISS']?>">SIS - Stock In Slip</option>
							<option value="transfer" <?=($seltype=="transfer") ? "selected" : ""?> data-prt="<?=$prntnme['INVTRANS_ISSUANCE']?>">IRS - Item Return Slip</option>													
						</select>
					</div>

				</div>
		
			</fieldset>	

			<div class="col-xs-12 nopadwtop2x">			
				<input type="text" class="form-control input-md" name="txtscan" id="txtscan" value="" placeholder="Search Item Name...">

				<input type="hidden" name="rowcnt" id="rowcnt" value="">
			</div>

								
			<div class="alt2" dir="ltr" style="
				margin: 0px;
				padding: 3px;
				border: 1px solid #919b9c;
				width: 100%;
				height: 250px;
				text-align: left;
				overflow: auto; margin-top: 2px !important">

				<table name='MyTbl' id='MyTbl' class="table table-scroll table-striped table-condensed">
					<thead>
						<tr>
							<th width="50">&nbsp;</th>
							<th width="150">Item Code</th>
							<th>Item Description</th>
							<th width="70">Unit</th>
							<th width="100" class="text-center">Qty</th>
							<th width="250" class="text-center">Remarks</th>
							<th width="50">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sqlhead = mysqli_query($con,"Select A.*, B.citemdesc from invtransfer_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno='".$_REQUEST['id']."' Order By A.nidentity");
							if (mysqli_num_rows($sqlhead)!=0) {
					
								$cnt = 0;
								while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
									$cnt++;
						?>
							<tr>				
								<td><?=$cnt?></td>
								<td><input type='hidden' value='<?=$row['citemno']?>' name="txtitmcode" id="txtitmcode<?=$cnt?>"><?=$row['citemno']?></td>
								<td><input type='hidden' value='<?=$row['citemdesc']?>' name="txtitmdesc" id="txtitmdesc<?=$cnt?>"><?=$row['citemdesc']?></td>
								<td><input type='hidden' value='<?=$row['cunit']?>' name="txtcunit" id="txtcunit<?=$cnt?>"><?=$row['cunit']?></td>
								<td>
									<input type='text' class="numeric form-control input-xs text-center" name="txtnqty" id="txtnqty<?=$cnt?>" value="<?=number_format($row['nqty1'],2)?>">
								</td>
								<td>
									<input type='text' class="form-control input-xs text-center" name="txtcrems" id="txtcrems<?=$cnt?>" value="<?=$row['cremarks']?>">
								</td>
								<td align="center"><button type="button" class="btn btn-danger btn-xs" id="btnDel<?=$cnt?>"><i class="fa fa-times"></i></button></td>
							</tr>

								<script type="text/javascript">
									$(document).ready(function(e) {

										$("#btnDel<?=$cnt?>").on('click', function() {
											$(this).closest('tr').remove();
											Reinitialize();
										});

									});
								</script>
						<?php
								}
							}
						?>
					</tbody>
				</table>

			</div>

			<br>

			<?php
				if($poststat == "True" || $printstat == "True"){
			?>

			<table width="100%" border="0" cellpadding="3">
				<tr>
					<td>
					<?php
						if($poststat == "True"){
					?>
					<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Inv.php';" id="btnMain" name="btnMain">
						Back to Main<br>(ESC)
					</button>

					<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location='https://<?=$_SERVER['SERVER_NAME']?>/Inventory/Transfers/InvTrans_New.php'" id="btnNew" name="btnNew">
						New<br>(F1)
					</button>

					<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="window.location='https://<?=$_SERVER['SERVER_NAME']?>/Inventory/Transfers/InvTrans_Edit.php?id=<?=$_REQUEST['id']?>'" id="btnUndo" name="btnUndo">
						Undo Edit<br>(CTRL+Z)
					</button>
					<?php
						}
						
						if($printstat == "True"){
					?>
					<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?=$_REQUEST['id']?>');" id="btnPrint" name="btnPrint">
						Print<br>(CTRL+P)
					</button>
					<?php
						}

						if($poststat == "True"){
					?>
					<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
						Edit<br>(CTRL+E)
					</button>

					<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (CTRL+S)</button>
					<?php
						}
					?>
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

	<!-- PRINT OUT MODAL-->
		<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
			<div class="modal-dialog modal-lg">
				<div class="modal-contnorad">   
					<div class="modal-body" style="height: 12in !important">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
				
							<iframe id="myprintframe" name="myprintframe" scrolling="yes" style="width:100%; height: 11.5in; display:block; margin:0px; padding:0px; border:0px; overflow: scroll;"></iframe>    
						
						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	<!-- End Bootstrap modal -->
</body>

<form action="boamax_mrs.php" method="post" name="frmMrs" id="frmMrs" target="_blank">
	<input type="hidden" name="txtx" id="txtx" value="<?=$_REQUEST['id']?>">
	<input type="hidden" name="n" id="n" value="1">
</form>

</html>

<script type="text/javascript">

	$("#txtscan").focus();

	$(document).keydown(function(e) {	 
		if(e.keyCode == 83 && e.ctrlKey){//CTRL S
				if($("#btnSave").is(":disabled")==false){
					e.preventDefault();
					return chkform();
				}
		}
		else if(e.keyCode == 80 && e.ctrlKey){//CTRL+P
			if($("#btnPrint").is(":disabled")==false){
				e.preventDefault();
				printchk('<?=$_REQUEST['id']?>');
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

		$('body').on('keydown', 'input.numeric', function(e) {
			if (e.which == 13) {
				var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
				focusable = form.find('input').filter(':visible');
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
		$("input.numeric").on("focus", function () {
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
	
		$('#txtscan').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product_whse.php",
					dataType: "json",
					data: { query: $("#txtscan").val(), styp: "Goods", cwhse: $("#selwhfrom").val() },
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 900px"><span >'+item.id+": "+item.desc+'</span</div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 	

				var rowCount = $('#MyTbl tr').length;

				InsTotable(item.id,item.desc,item.cunit,rowCount);
					
				$('#txtscan').val("").change();
																		
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
				$("<td id=\"tdx"+sornum+"\">").html(sornum), 
				$("<td>").html("<input type='hidden' value='"+itmid+"' name=\"txtitmcode\" id=\"txtitmcode"+sornum+"\">"+itmid),  
				$("<td>").html("<input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc\" id=\"txtitmdesc"+sornum+"\">"+itmdesc),
				$("<td>").html("<input type='hidden' value='"+itmunit+"' name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+itmunit),
				$("<td>").html("<input type='text' class=\"numeric form-control input-xs text-center\" name=\"txtnqty\" id=\"txtnqty"+sornum+"\" value=\"0\">"),
				$("<td>").html("<input type='text' class=\"form-control input-xs text-center\" name=\"txtcrems\" id=\"txtcrems"+sornum+"\" value=\"\">"),
				$("<td align=\"center\">").html("<button type=\"button\" class=\"btn btn-danger btn-xs\" id=\"btnDel"+sornum+"\"><i class=\"fa fa-times\"></i></button>")
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

	function Reinitialize(){
		$("#MyTbl > tbody > tr").each(function(index) {
				$newval = index+1;

				$(this).find('td').attr('id','tdx'+$newval);

				$(this).find('input:hidden[name="txtitmcode"]').attr('id','txtitmcode'+$newval);

				$(this).find('input:hidden[name="txtitmdesc"]').attr('id','txtitmdesc'+$newval);
				$(this).find('input:hidden[name="txtcunit"]').attr('id','txtcunit'+$newval); 
				$(this).find('input[name="txtnqty"]').attr('id','txtnqty'+$newval); 
				$(this).find('input[name="txtcrems"]').attr('id','txtcrems'+$newval); 

				$('#tdx'+$newval).html($newval);
			});
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

					$(this).find('input:hidden[name="txtitmcode"]').attr('name','txtitmcode'+$newval);
					$(this).find('input:hidden[name="txtitmdesc"]').attr('name','txtitmdesc'+$newval);
					$(this).find('input:hidden[name="txtcunit"]').attr('name','txtcunit'+$newval);
					$(this).find('input[name="txtnqty"]').attr('name','txtnqty'+$newval); 
					$(this).find('input[name="txtcrems"]').attr('name','txtcrems'+$newval); 
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

	function printchk(x){
		if(document.getElementById("hdncancel").value==1){	
			document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
			document.getElementById("statmsgz").style.color = "#FF0000";
		}
		else{
			var $prtname = $("#selcntyp option:selected").data("prt");
			$("#frmMrs").submit();
			//$("#myprintframe").attr("src",$prtname+"?id="+x+"&n=1");
			//$("#PrintModal").modal('show');
		}
	}

	function disabled(){

		$("#frmCount :input").attr("disabled", true);

		$("#btnMain").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
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
			
			$("#selwhfrom").attr("disabled", true);
			$("#seltempname").attr("disabled", true);

				$("#btnMain").attr("disabled", true);
				$("#btnPrint").attr("disabled", true);
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
