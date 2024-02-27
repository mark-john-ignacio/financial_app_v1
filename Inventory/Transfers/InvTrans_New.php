<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvCnt_new.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];
	$EmpID = $_SESSION['employeeid'];

	$_SESSION['myxtoken'] = gen_token();

	$arrseclist = array();
	$arrallsec = array();
	$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc, ifnull(B.UserID,'') as UserID From locations A left join users_sections B on A.nid = B.section_nid and B.UserID = '$EmpID' Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");
	$arrseclist[] = 0;
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		if($row0['UserID']==$EmpID){
			$arrsecrow[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);
			$arrseclist[] = $row0['nid'];
		}
		
		$arrallsec[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);
				
	}

	$arrtemplates= array();
	$sqltemplate = mysqli_query($con,"select A.section_nid as nid, A.citemno, B.citemdesc, B.cunit, A.sortnum, A.template_id from invtransfer_template A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.section_nid in (".implode(",",$arrseclist).") Order By A.section_nid, A.sortnum");
	$rowTemplate = $sqltemplate->fetch_all(MYSQLI_ASSOC);
	foreach($rowTemplate as $row0){
		$arrtemplates[] = array('itemid' => $row0['citemno'], 'itemdesc' => $row0['citemdesc'], 'itemunit' => $row0['cunit'], 'itemsort' => $row0['sortnum'], 'secid' => $row0['nid'], 'template_id' => $row0['template_id']);
	}

	$arrtempsname[] = array();
	$sqltempname = mysqli_query($con,"select * from invtransfer_template_names where compcode='$company'");
	$rowdettempname= $sqltempname->fetch_all(MYSQLI_ASSOC);
	foreach($rowdettempname as $row0){
		$arrtempsname[] = $row0;
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

	<form id="frmCount" name="frmCount" method="post" action="InvTrans_NewSave.php">

		<input type="hidden" name="hdnmyxfin" value="<?= $_SESSION['myxtoken'] ?? '' ?>">

		<fieldset>
			<legend>New Inventory Transfer</legend>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-2 nopadding" id="secfrom">
					<b>Requesting Section: </b>
				</div>
				<div class="col-xs-3 nopadding">
					
					<select class="form-control input-sm" name="selwhfrom" id="selwhfrom">
					<?php
						$issel = 0;
							foreach($arrsecrow as $localocs){
								$issel++;
						?>
							<option value="<?php echo $localocs['nid'];?>" <?=($issel==1) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
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
					</select>
				</div>

			</div>
	
			<div class="col-xs-12 nopadwtop">
				<div class="col-xs-2 nopadding" id="secto">
					<b>Issuing Section: </b>
				</div>
				<div class="col-xs-3 nopadding">
					
					<select class="form-control input-sm" name="selwhto" id="selwhto">
					<?php
						$issel = 0;
							foreach($arrallsec as $localocs){
								$issel++;
						?>
							<option value="<?php echo $localocs['nid'];?>" <?=($issel==1) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
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
					<input type="text" class="datepick form-control input-sm" name="txtdtrandate" id="txtdtrandate">
				</div>
			</div>

			<div class="col-xs-12 nopadwtop">
				<div class="col-xs-2 nopadding">
					<b>Remarks: </b>
				</div>
				<div class="col-xs-3 nopadding">					
					<textarea class="form-control input-sm" name="txtccrems" id="txtccrems" value="" placeholder="Enter Remarks..."> </textarea>
				</div>

				<div class="col-xs-1 nopadding">
						&nbsp;
				</div>

				<div class="col-xs-2 nopadding">
					<b>Transfer Type: </b>
				</div>
				<div class="col-xs-2 nopadding">
					<select class="form-control input-sm" name="selcntyp" id="selcntyp">			
						<option value="request">Request</option>		
						<option value="transfer">Transfer</option>		
						<option value="fg_transfer">FG Transfer</option>				
					</select>
				</div>
			</div>

	
		</fieldset>	

		<div class="row nopadding">
			<div class="col-xs-12 nopadwtop2x">			
				<input type="text" class="form-control input-md" name="txtscan" id="txtscan" value="" placeholder="Search Item Name...">

				<input type="hidden" name="rowcnt" id="rowcnt" value="">
			</div>
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
                  </tbody>
				 				</table>
			</div>

		<br>
		<table width="100%" border="0" cellpadding="3">
			<tr>
				<td>
				<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Inv.php';" id="btnMain" name="btnMain">
					Back to Main<br>(ESC)
				</button>

				<!--
				<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="loadItms();" id="btnMain" name="btnMain">
					Load Template<br>(Insert)
				</button>
				-->

				<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (CTRL+S)</button></td>

				</tr>
		</table>

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

</body>

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
	  else if(e.keyCode == 27){//ESC
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Inv.php';
			}
	  }

	});


	$(function(){	

			loadtepnames();

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

			$(".datepick").datetimepicker({
				format: 'MM/DD/YYYY',
				useCurrent: false,
				//minDate: moment(),
				defaultDate: moment(),
			});

			$("#selwhfrom").on("change", function(){
				//$("#MyTbl tbody").empty();
				//loadItms();

				loadtepnames();
			});
			
			$("#seltempname").on("change", function(){
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

	function loadtepnames(){
		$("#MyTbl tbody").empty();

		var selctdoption = $("#selwhfrom").val(); 
		var xz = $("#hdntempsname").val();

		$("#seltempname").find('option').not(':first').remove();

		$.each(jQuery.parseJSON(xz), function() {  

			if(this['section_nid']==selctdoption){

				$('#seltempname').append($('<option>', { 
						value: this['nid'],
						text : this['tempname'] 
				}));

			}
		});

		//loadItms();
	
	}

	function loadItms(){
		var selctdoption = $("#selwhfrom").val(); 
		var selctdtempid = $("#seltempname").val();

		var xz = $("#hdntemplist").val();

		$.each(jQuery.parseJSON(xz), function() {  

			if(this['secid']==selctdoption && this['template_id']==selctdtempid){

				InsTotable(this['itemid'],this['itemdesc'],this['itemunit'],this['itemsort']);

			}
		});
	}

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
				$("<td>").html("<input type='text' class=\"form-control input-xs text-center\" name=\"txtcrems\" id=\"txtcrems"+sornum+"\" value=\"\">"),
				$("<td align=\"center\">").html("<button type=\"button\" class=\"btn btn-danger btn-xs\" id=\"btnDel\" id=\"btnDel"+sornum+"\"><i class=\"fa fa-times\"></i></button>")
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

				$(this).find('input:hidden[name="txtitmcode"]').attr('id','txtitmcode'+$newval);
				$(this).find('input:hidden[name="txtitmdesc"]').attr('id','txtitmdesc'+$newval);
				$(this).find('input:hidden[name="txtcunit"]').attr('id','txtcunit'+$newval); 
				$(this).find('input[name="txtnqty"]').attr('id','txtnqty'+$newval);  
				$(this).find('input[name="txtcrems"]').attr('id','txtcrems'+$newval); 
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

			var whstat = "True";
			if($("#selwhfrom").val()==$("#selwhto").val()){
				whstat = "False";
			}

			if(qty=="False"){
				$("#AlertMsg").html("Blank quantity is not allowed!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
			}else if(whstat=="False"){
				$("#AlertMsg").html("Same From and To Section is not allowed!");
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


</script>
