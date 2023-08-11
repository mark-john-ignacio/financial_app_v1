<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvAdj_new.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];
	$EmpID = $_SESSION['employeeid'];

	$_SESSION['myxtoken'] = gen_token();

	$arrseclist = array();
	$sqlempsec = mysqli_query($con,"select nid, cdesc from locations where compcode='$company' and cstatus='ACTIVE' Order By cdesc");
	$arrseclist[] = 0;
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrseclist[] = $row0['nid'];
	}

	$sql = "select a.citemno, b.citemdesc, a.cmainunit, a.nsection_id, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
	From tblinventory a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' group by a.citemno, b.citemdesc, a.cmainunit, a.nsection_id";
	$result=mysqli_query($con,$sql);
	
	$arrinventories = array();
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$arrinventories[] = array('citemno' => $row['citemno'], 'cdesc' => str_replace("'","",$row['citemdesc']), 'cmainunit' => $row['cmainunit'], 'nsection_id'=>$row['nsection_id'], 'nqty'=>number_format($row['nqty'],2));
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
	<input type="hidden" value='<?=json_encode(@$arrinventories)?>' id="hdnivntrs">

	<form id="frmCount" name="frmCount" method="post" action="<?="https://".$_SERVER['SERVER_NAME']?>/Inventory/Adjustment/InvAdj_NewSave.php">

		<input type="hidden" name="hdnmyxfin" value="<?= $_SESSION['myxtoken'] ?? '' ?>">

		<fieldset>
			<legend>New Inventory Adjustment</legend>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-2 nopadding">
					<b>Section: </b>
				</div>
				<div class="col-xs-3 nopadding">
					
					<select class="form-control input-sm" name="selwhfrom" id="selwhfrom">
					<?php
						$issel = 0;
							foreach($rowdetloc as $localocs){
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
					<b>Adjustment Date: </b>
				</div>
				
				<div class="col-xs-2 nopadding">
					<input type="text" class="datepick form-control input-sm" name="txtdtrandate" id="txtdtrandate">
				</div>

			</div>

			<div class="col-xs-12 nopadwtop">
				<div class="col-xs-2 nopadding">
					<b>Adjustment Type: </b>
				</div>
				<div class="col-xs-3 nopadding">
					<select class="form-control input-sm" name="selcntyp" id="selcntyp">			
						<option value="manual">Manual</option>		
						<option value="theo">Theoretical</option>	
						<option value="ending">Count Ending</option>					
					</select>
				</div>

				<div class="col-xs-1 nopadwleft">
          <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnSISearch" onClick="InsertDet();" disabled><i class="fa fa-search"></i></button>
        </div>
			</div>

			<div class="col-xs-12 nopadwtop">
				<div class="col-xs-2 nopadding">
					<b>Remarks: </b>
				</div>
				<div class="col-xs-8 nopadding">
					<input type="text" class="form-control input-sm" name="txtccrems" id="txtccrems" value="" placeholder="Enter Remarks...">
				</div>
			</div>
	
		</fieldset>	

		<div class="col-xs-12 nopadwtop2x">			
			<input type="text" class="form-control input-lg" name="txtscan" id="txtscan" value="" placeholder="Search Item Name...">

			<input type="hidden" name="rowcnt" id="rowcnt" value="">
		</div>

            
		<div class="alt2" dir="ltr" style="
						margin: 0px;
						padding: 3px;
						border: 1px solid #919b9c;
						width: 100%;
						height: 250px;
						text-align: left;
						overflow: auto">

                <table name='MyTbl' id='MyTbl' class="table table-scroll table-striped table-condensed">
                  <thead>
                    <tr>
											<th width="50">&nbsp;</th>
                      <th width="150">Item Code</th>
                      <th>Item Description</th>
                      <th width="70">Unit</th>
											<th width="100" class="text-center">Theo End</th>
                      <th width="100" class="text-center">Actual Qty</th>
											<th width="100" class="text-center">Adjustment</th>
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

	<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title" id="InvListHdr">Inventory Count List</h3>
        </div>           
        <div class="modal-body pre-scrollable" style="height:25vh">            

							<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight small">
								<thead>
									<tr>
										<th>Trans No.</th>
										<th>Inventory Date</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
  	            
				</div>			
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
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
			$("#MyTbl tbody").empty();

			$("#selcntyp").change();
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

				//get inventory
				var xz = $("#hdnivntrs").val();
				theocnt = 0;
				$.each(jQuery.parseJSON(xz), function() { 
					
					//alert(item.id + "==" + this['citemno'] + " && " + this['nsection_id'] + "==" + $("#selwhfrom").val());

					if(item.id==this['citemno'] && this['nsection_id']==$("#selwhfrom").val()){ 
						theocnt = this['nqty'];
					}
				});

				var rowCount = $('#MyTbl tr').length;

				InsTotable(item.id,item.desc,item.cunit,rowCount,theocnt,0,"","");
					
				$('#txtscan').val("").change();
																		
			}
		
		});

		$("#selcntyp").on("change", function(){
			$("#MyTbl tbody").empty();

			if($(this).val()=="ending"){
				$("#btnSISearch").attr("disabled", false);
			}else{
				$("#btnSISearch").attr("disabled", true);
				if($(this).val()=="theo"){

					var xz = $("#hdnivntrs").val();
					theocnt = 0;

					$.each(jQuery.parseJSON(xz), function() { 

						if(this['nsection_id']==$("#selwhfrom").val()){ 

							var rowCount = $('#MyTbl tr').length;							
							theocnt = this['nqty'];
							InsTotable(this['citemno'],this['cdesc'],this['cmainunit'],rowCount,theocnt,0,"","");

						}
					});
				}

			}

		});
		

});

	function InsTotable(itmid,itmdesc,itmunit,sornum,theocount,actcnt,cref,creident){

		//loop check if item exist
		var isExist = "False";
		$("#MyTbl > tbody > tr").each(function(index) {	
			citmno = $(this).find('input[type="hidden"][name="txtitmcode"]').val();

			if(citmno==itmid){
				isExist = "True";
			}
		});


		if(isExist=="False"){

			actcnt = (parseFloat(actcnt)>0) ? actcnt.replace(/,/g,'') : actcnt;
			theocount = (parseFloat(theocount)>0) ? theocount.replace(/,/g,'') : theocount;

			itmadj = parseFloat(actcnt) -  parseFloat(theocount);

			$("<tr>").append( 
				$("<td>").html(sornum), 
				$("<td>").html("<input type='hidden' value='"+cref+"' name=\"hdncreference\" id=\"hdncreference"+sornum+"\"><input type='hidden' value='"+creident+"' name=\"hdncrefident\" id=\"hdncrefident"+sornum+"\"><input type='hidden' value='"+itmid+"' name=\"txtitmcode\" id=\"txtitmcode"+sornum+"\">"+itmid),  
				$("<td>").html("<input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc\" id=\"txtitmdesc"+sornum+"\">"+itmdesc),
				$("<td>").html("<input type='hidden' value='"+itmunit+"' name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+itmunit),
				$("<td>").html("<input type='text' class=\"form-control input-xs text-center\" name=\"txtnqtytheo\" id=\"txtnqtytheo"+sornum+"\" value=\""+theocount+"\" readonly>"),
				$("<td>").html("<input type='text' class=\"numeric form-control input-xs text-center\" name=\"txtnqty\" id=\"txtnqty"+sornum+"\" value=\""+actcnt+"\">"),
				$("<td>").html("<input type='text' class=\"numeric form-control input-xs text-center\" value='"+itmadj+"' name=\"txtdiff"+sornum+"\" id=\"txtdiff"+sornum+"\" readonly>"),
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

			$("#txtnqty"+sornum).on('keyup', function() {
				computediff($(this).val(), $(this).attr("id"));
			});
			

		}
	}

	function computediff(valz,valid){
		var numberPattern = /\d+/g;
		var r = valid.match(numberPattern);

		if(valz==""){
			$("#txtnqty"+r).val(0); 
			valz = 0;
		}else{
			valz = valz.replace(/,/g,'');
		}
		
		var oldqty = $("#txtnqtytheo"+r).val().replace(/,/g,'');
				
		var diff = parseFloat(valz) - parseFloat(oldqty);

		if(diff != 0){
			$("#txtdiff"+r).val(diff);
		}
		else{
			$("#txtdiff"+r).val(0);
		}

		$("#txtdiff"+r).autoNumeric('destroy');
		$("#txtdiff"+r).autoNumeric('init',{mDec:2});
	}

	function Reinitialize(){
		$("#MyTbl > tbody > tr").each(function(index) {
				$newval = index+1;

				$(this).find('input:hidden[name="hdncreference"]').attr('id','hdncreference'+$newval);
				$(this).find('input:hidden[name="hdncrefident"]').attr('id','hdncrefident'+$newval);
				$(this).find('input:hidden[name="txtitmcode"]').attr('id','txtitmcode'+$newval);
				$(this).find('input:hidden[name="txtitmdesc"]').attr('id','txtitmdesc'+$newval);
				$(this).find('input:hidden[name="txtcunit"]').attr('id','txtcunit'+$newval); 
				$(this).find('input[name="txtnqtytheo"]').attr('id','txtnqtytheo'+$newval); 
				$(this).find('input[name="txtnqty"]').attr('id','txtnqty'+$newval); 
				$(this).find('input[name="txtdiff"]').attr('id','txtdiff'+$newval); 
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

					$(this).find('input:hidden[name="hdncreference"]').attr('name','hdncreference'+$newval);
					$(this).find('input:hidden[name="hdncrefident"]').attr('name','hdncrefident'+$newval);
					$(this).find('input:hidden[name="txtitmcode"]').attr('name','txtitmcode'+$newval);
					$(this).find('input:hidden[name="txtitmdesc"]').attr('name','txtitmdesc'+$newval);
					$(this).find('input:hidden[name="txtcunit"]').attr('name','txtcunit'+$newval); 
					$(this).find('input[name="txtnqtytheo"]').attr('name','txtnqtytheo'+$newval); 
					$(this).find('input[name="txtnqty"]').attr('name','txtnqty'+$newval); 
					$(this).find('input[name="txtdiff"]').attr('name','txtdiff'+$newval);
					
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


	function InsertDet(){

		var cwhse = $("#selwhfrom").val();						
		var xstat = "YES";
				
		$.ajax({
			url: 'th_countlist.php',
			data: 'x='+cwhse,
			dataType: 'json',
			method: 'post',
			success: function (data) {

				console.log(data);
				$.each(data,function(index,item){
							
					if(item.cpono=="NONE"){
						$("#AlertMsg").html("No Inventory Count Available");
						$("#alertbtnOK").show();
						$("#OK").hide();
						$("#Cancel").hide();
						$("#AlertModal").modal('show');

						xstat = "NO";

					}
					else{
						$("<tr>").append(
						$("<td id='td"+item.ctranno+"'>").text(item.ctranno),
						$("<td>").text(item.dcutdate)
						).appendTo("#MyInvTbl tbody");
													
						$("#td"+item.ctranno).on("click", function(){
							opengetdet($(this).text());
						});
								
						$("#td"+item.ctranno).on("mouseover", function(){
							$(this).css('cursor','pointer');
						});
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
	}

	function opengetdet(valz){

		$.ajax({
			url: 'th_countlistdet.php',
			data: 'x='+valz,
			dataType: 'json',
			method: 'post',
			success: function (data) {
						
				console.log(data);
				$.each(data,function(index,item){

					//get inventory
					var xz = $("#hdnivntrs").val();
					theocnt = 0;
					$.each(jQuery.parseJSON(xz), function() { 
						
						//alert(item.id + "==" + this['citemno'] + " && " + this['nsection_id'] + "==" + $("#selwhfrom").val());

						if(item.citemno==this['citemno'] && this['nsection_id']==$("#selwhfrom").val()){ 
							theocnt = this['nqty'];
						}
					});

					var rowCount = $('#MyTbl tr').length;

					InsTotable(item.citemno,item.citemdesc,item.cunit,rowCount,theocnt,item.nqty,item.ctranno,item.nidentity);
				});
			},
			complete: function(){
				$('#mySIRef').modal("hide");
			},
			error: function (req, status, err) {
				console.log('Something went wrong', status, err);
				$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
			}
		});

	}


</script>
