<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvCnt_new.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];

	$arrdefitems = array();
	$sqllabelnme = mysqli_query($con,"select A.*, B.citemdesc from mrp_bom A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.cmainitemno='".$_REQUEST['itm']."' order by A.nitemsort");
	$rowlabelname = $sqllabelnme->fetch_all(MYSQLI_ASSOC);
	foreach($rowlabelname as $rs3){
		$arrdefitems[] = $rs3;
	}

	$itmname = "";
	$itmuom = "";
	$sqllabelnme = mysqli_query($con,"select * from items where compcode='$company' and cpartno='".$_REQUEST['itm']."'");
	$rowlabelname = $sqllabelnme->fetch_all(MYSQLI_ASSOC);
	foreach($rowlabelname as $rs4){
		$itmname = $rs4['citemdesc'];
		$itmuom = $rs4['cunit'];
	}


	$arrbomlabel = array();
	$sqllabelnme = mysqli_query($con,"select * from mrp_bom_label where compcode='$company' and citemno='".$_REQUEST['itm']."'");

	$rowcount=mysqli_num_rows($sqllabelnme);
	$rowlabelname = $sqllabelnme->fetch_all(MYSQLI_ASSOC);

	$totdcount = 1;
	if($rowcount>1){
		$totdcount = $rowcount;
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

	<style>
		.bg-level1 {
			background-color: #CFE2F3;
		}

		.bg-level2 {
			background-color: #F4CCCC;
		}

		.bg-level3 {
			background-color: #FFE599;
		}

		.bg-level4 {
			background-color: #CFE2F3;
		}

		.bg-level5 {
			background-color: #FCE5CD;
		}

		hr.here {

			height: 2px !important;

			background-color: "DodgerBlue" !important;

			margin-top: 20px !important;

			margin-bottom: 20px !important;

			width: 100% !important;

		}
	</style>

</head>

<body style="padding: 10px !important">

<input type="hidden" id="hdndefbom" value='<?=json_encode($arrdefitems)?>'>
<input type="hidden" id="hdncount" value='<?=$totdcount?>'>

	<form id="frmCount" name="frmCount" method="post" action="">
		<fieldset>
			<legend><?=$_REQUEST['itm']?> <?=$itmname?></legend>
		</fieldset>	

		<div class="col-xs-12 nopadwtop2x">	
			<div class="col-xs-9 nopadwdown">	
				<input type="text" class="form-control input-sm" id="txtscan" value="" placeholder="Search Item Name...">
			</div>
			<div class="col-xs-1 nopadwleft">	
				<button type="button" class="btn btn-sm btn-warning btn-block" name="btnaddversion" id="btnaddversion"><i class="fa fa-plus" aria-hidden="true"></i>
 &nbsp;Add Version</button>
			</div>

			<div class="col-xs-1 nopadwleft">	
				<button type="button" class="btn btn-sm btn-success btn-block" name="btnaddversion" id="btnaddversion"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
&nbsp;Upload Excel</button>
			</div>

			<div class="col-xs-1 nopadwleft">	
				<button type="button" class="btn btn-sm btn-info btn-block" name="btnaddversion" id="btnaddversion"><i class="fa fa-download" aria-hidden="true"></i>
&nbsp;DL Template</button>
			</div>

			<input type="hidden" name="rowcnt" id="rowcnt" value="">
		</div>

		<hr class="here">
                       
                <table name='MyTbl' id='MyTbl' class="table table-scroll table-striped table-condensed">
                  <thead>
                    <tr>
											<th width="50">&nbsp;</th>
                      <th width="150">Item Code</th>
                      <th>Item Description</th>
                      <th width="70" class="text-center">Unit</th>
											<th width="70" class="text-center">Level</th>
											<?php
											if($totdcount>1){
												foreach($rowlabelname as $rowx){
											?>
											<th width="70" class="text-center"><?=$rowx['cdesc']?>
												<br>
												<input type="radio" name='radversion' value='<?=$rowx['nversion']?>' <?=($rowx['ldefault']==1) ? "checked" : ""?>>
											</th>
											<?php
												}
											}else{
											?>
												<th width="70" class="text-center">Default
													<br>
													<input type="radio" name='radversion' checked>
												</th>
											<?php
											}
											?>
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

		$("#btnaddversion").on("click", function(){
			let version = prompt("Please enter version description");
			if (version != null) {
				$.ajax({
					url: "addver.php",
					dataType: "text",
					data: { ver: version, x: "<?=$_REQUEST['itm']?>" },
					success: function (data) {
						if(data.trim()=="True"){
							window.location.href = "Items.php?itm=<?=$_REQUEST['itm']?>";
						}

					}
				});
			}
		});
	
		$('#txtscan').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
					dataType: "json",
					data: { query: $("#txtscan").val() },
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

				var rowCount = $('#MyTbl tbody > tr').length;

				rowCount = rowCount + 1;
				InsTotable(item.id,item.desc,item.cunit,rowCount);
					
				$('#txtscan').val("").change();

				$("#MyTbl:not(thead)").tableDnDUpdate();
																		
			}
		
		});

	});

	function loadItms(){
		var selctdoption = $("#selwhfrom").val(); 
		var selctdtempid = $("#seltempname").val();

		var xz = $("#hdndefbom").val();

		$.each(jQuery.parseJSON(xz), function() {  


				itmid = this['citemno'];
				itmdesc = this['citemdesc'];
				itmunit = this['cunit'];
				sornum = this['nitemsort'];

				var $tdrows = "";

				var isselected = "";
				var optz = "";
				if(this['nlevel']){
					for (i = 2; i <= 5; i++) {
						if(parseInt(this['nlevel'])==i){
							optz = optz + "<option value='"+i+"' selected>"+i+"</option>";
						}else{
							optz = optz + "<option value='"+i+"'>"+i+"</option>";
						}
						
					}
				}

				var GENxyz = parseInt(this['nlevel'])-1;
						
				var GENxyz0 = 0;
				if(GENxyz>1){
					GENxyz0 = (5 * GENxyz) + (GENxyz * 2);
				}

				$tdrows = "<tr class=\"bg-level"+this['nlevel']+"\"><td><input type='text' class=\"form-control input-xs text-center\" value='"+sornum+"' name=\"txtsortnum\" id=\"txtsortnum"+sornum+"\" readonly></td><td><input type='hidden' value='"+itmid+"' name=\"txtitmcode\" id=\"txtitmcode"+sornum+"\">"+itmid+"</td><td><input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc\" id=\"txtitmdesc"+sornum+"\"><div style='text-indent:"+GENxyz0+"px'>"+itmdesc+"</div></td><td><input type='hidden' value='"+itmunit+"' name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+itmunit+"</td><td><select class=\"form-control input-xs text-center\" name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+optz+"</select></td>";


				getcnt = parseInt($("#hdncount").val());
				for (i = 1; i <= getcnt; i++) {

					$tdrows = $tdrows + "<td><input type='text' class=\"form-control input-xs text-center\" value='"+this['nqty'+i]+"' name=\"txtnqty"+i+"\" id=\"txtnqty"+sornum+"\"></td>";

				}

				$tdrows = $tdrows + "<td class=\"text-center\"><button class=\"btn btn-danger btn-xs\" id=\"btnDel"+sornum+"\"><i class=\"fa fa-times\"></i></button></td>";
				
				$row = "<tr>"+$tdrows+"</tr>";
				$("#MyTbl tbody").append($row);


		});

		$("#MyTbl:not(thead)").tableDnD({
        onDrop: function(table, row) {
          $.tableDnD.serialize();
        }
    });
	} 

	function InsTotable(itmid,itmdesc,itmunit,sornum,v1,v2,v3,v4,v5){

		//loop check if item exist
			var $tdrows = "";

			$tdrows = "<td><input type='text' class=\"form-control input-xs text-center\" value='"+sornum+"' name=\"txtsortnum\" id=\"txtsortnum"+sornum+"\" readonly></td><td><input type='hidden' value='"+itmid+"' name=\"txtitmcode\" id=\"txtitmcode"+sornum+"\">"+itmid+"</td><td><input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc\" id=\"txtitmdesc"+sornum+"\">"+itmdesc+"</td><td><input type='hidden' value='"+itmunit+"' name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+itmunit+"</td><td><select class=\"form-control input-xs text-center\" name=\"txtcunit\" id=\"txtcunit"+sornum+"\"><option value='2'>2</option><option value='3'>3</option><options value='4'>4</option><option value='5'>5</option></select></td>";


			getcnt = parseInt($("#hdncount").val());
			for (i = 1; i <= getcnt; i++) {

				$tdrows = $tdrows + "<td><input type='text' class=\"form-control input-xs text-center\" value='1' name=\"txtnqty"+i+"\" id=\"txtnqty"+sornum+"\"></td>";

			}

			$tdrows = $tdrows + "<td class=\"text-center\"><button class=\"btn btn-danger btn-xs\" id=\"btnDel"+sornum+"\"><i class=\"fa fa-times\"></i></button></td>";
			
			$row = "<tr>"+$tdrows+"</tr>";
			$("#MyTbl tbody").append($row);
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
