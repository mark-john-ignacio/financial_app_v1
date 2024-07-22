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

	$arrseclist = array();
	$sqlloc = mysqli_query($con,"select A.section_nid as nid, B.cdesc from users_sections A left join locations B on A.section_nid=B.nid where A.UserID='$EmpID' Order By B.cdesc");
	$rowdetloc = $sqlloc->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrseclist[] = $row0['nid'];
	}

	$arrtemplates= array();
	$sqltemplate = mysqli_query($con,"select A.section_nid as nid, A.citemno, B.citemdesc, B.cunit, A.sortnum, A.template_id from invcount_template A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.section_nid in (".implode(",",$arrseclist).") Order By A.section_nid, A.sortnum");
	$rowTemplate = $sqltemplate->fetch_all(MYSQLI_ASSOC);
	foreach($rowTemplate as $row0){
		$arrtemplates[] = array('itemid' => $row0['citemno'], 'itemdesc' => $row0['citemdesc'], 'itemunit' => $row0['cunit'], 'itemsort' => $row0['sortnum'], 'secid' => $row0['nid'], 'template_id' => $row0['template_id']);
	}

	$arrtempsname[] = array();
	$sqltempname = mysqli_query($con,"select * from invcount_template_names where compcode='$company'");
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
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
	<script src="../../include/tableDnd/js/jquery.tablednd.js" type="text/javascript"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body>
	<input type="hidden" id="hdntemplist" value='<?=json_encode($arrtemplates)?>'>
	<input type="hidden" id="hdntempsname" value='<?=json_encode($arrtempsname)?>'>

	<form id="frmCount" name="frmCount" method="post" action="InvCnt_TemplateSave.php">
		<fieldset>
			<legend>Inventory Count Template</legend>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-1 nopadding">
					<b>Section: </b>
				</div>
				<div class="col-xs-3 nopadwleft">
					
					<select class="form-control input-sm" name="selwhfrom" id="selwhfrom">
						<?php
						$issel = 0;
							foreach($rowdetloc as $localocs){
								$issel++;

								if($issel==1){
									$dfirstsec = $localocs['nid'];
								}
						?>
							<option value="<?php echo $localocs['nid'];?>" <?=($issel==1) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
						<?php	
							}						
						?>
					</select>
				</div>

				<div class="col-xs-3 nopadwleft">
					<select class="form-control input-sm" name="seltempname" id="seltempname">
						<option value="">NEW TEMPLATE</option>
					</select>
				</div>

				<div class="col-xs-3 nopadwleft"> 
					<input type='text' class="form-control input-sm" name="newtemptxt" id="newtemptxt" placeholder="Enter name for new template...">
				</div> 
			</div>

		</fieldset>	

		<div class="col-xs-12 nopadwtop2x">	
			<input type="text" class="form-control input-md" id="txtscan" value="" placeholder="Search Item Name...">

			<input type="hidden" name="rowcnt" id="rowcnt" value="">
		</div>

                       
                <table name='MyTbl' id='MyTbl' class="table table-scroll table-striped table-condensed">
                  <thead>
                    <tr>
											<th width="50">&nbsp;</th>
                      <th width="150">Item Code</th>
                      <th>Item Description</th>
                      <th width="70">Unit</th>
                      <th width="50" class="text-center"><b>Del</b></td>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
				 				</table>


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


	$(document).ready(function() {

		loadItms();

		loadtepnames();

		$("#selwhfrom").on("change", function(){
			//$("#MyTbl tbody").empty();
			loadtepnames();
		});

		$("#seltempname").on("change", function(){
			$("#MyTbl tbody").empty();
			
			if($(this).val()==""){
				$("#newtemptxt").attr("readonly",false);
			}else{
				$("#newtemptxt").attr("readonly",true);
			}

			loadItms();
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

				$("#MyTbl:not(thead)").tableDnDUpdate();
																		
			}
		
		});

	});

	function loadItms(){
		var selctdoption = $("#selwhfrom").val(); 
		var selctdtempid = $("#seltempname").val();

		var xz = $("#hdntemplist").val();

		$.each(jQuery.parseJSON(xz), function() {  

			if(this['secid']==selctdoption && this['template_id']==selctdtempid){

				InsTotable(this['itemid'],this['itemdesc'],this['itemunit'],this['itemsort']);

			}
		});

		$("#MyTbl:not(thead)").tableDnD({
        onDrop: function(table, row) {
          $.tableDnD.serialize();
        }
    });
	} 

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

		loadItms();
	
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
				$("<td>").html("<input type='text' class=\"form-control input-xs text-center\" value='"+sornum+"' name=\"txtsortnum\" id=\"txtsortnum"+sornum+"\" readonly>"), 
				$("<td>").html("<input type='hidden' value='"+itmid+"' name=\"txtitmcode\" id=\"txtitmcode"+sornum+"\">"+itmid),  
				$("<td>").html("<input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc\" id=\"txtitmdesc"+sornum+"\">"+itmdesc),
				$("<td>").html("<input type='hidden' value='"+itmunit+"' name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+itmunit),
				$("<td align=\"center\">").html("<button class=\"btn btn-danger btn-xs\" id=\"btnDel"+sornum+"\"><i class=\"fa fa-times\"></i></button>")
			).appendTo("#MyTbl tbody");
			

		}
	}

	function chkform(){
		var qty = "False";

		var tbl1 = document.getElementById('MyTbl').getElementsByTagName('tr');
		var lastRow1 = tbl1.length-1;

		if(lastRow1!=0){
			//re intialize

			$("#MyTbl > tbody > tr").each(function(index) {
				$newval = index+1;

				$(this).find('input[name="txtsortnum"]').val($newval);
				$(this).find('input[name="txtsortnum"]').attr('name','txtsortnum'+$newval);
				$(this).find('input:hidden[name="txtitmcode"]').attr('name','txtitmcode'+$newval);
				$(this).find('input:hidden[name="txtitmdesc"]').attr('name','txtitmdesc'+$newval);
				$(this).find('input:hidden[name="txtcunit"]').attr('name','txtcunit'+$newval);
			});
		
			$("#rowcnt").val(lastRow1);

			if($("#seltempname").val()=="" && $("#newtemptxt").val()==""){
				$("#AlertMsg").html("Template Name required!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
			}else{
				$("#frmCount").submit();
			}

		}else{
			$("#AlertMsg").html("No details to save!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');
		}
	}


</script>
