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


	<form id="frmBOM" name="frmBOM" method="post" action="item_save.php">
		<input type="hidden" name="cmainitemno" value='<?=$_REQUEST['itm']?>'>
		<input type="hidden" id="hdncount" name="hdncount" value='<?=$totdcount?>'>

		<fieldset>
			<legend><?=$_REQUEST['itm']?> <?=$itmname?></legend>
		</fieldset>	

		<div class="col-xs-12 nopadwtop2x">	 
			<div class="col-xs-9 nopadwdown">	
				<input type="text" class="form-control input-sm" id="txtscan" value="" placeholder="Level 2 - Search Item Name...">
			</div>
			<div class="col-xs-1 nopadwleft">	
				<button type="button" class="btn btn-sm btn-warning btn-block" name="btnaddversion" id="btnaddversion"><i class="fa fa-plus" aria-hidden="true"></i>
 &nbsp;Add Version</button>
			</div>

			<div class="col-xs-1 nopadwleft">	
				<button type="button" class="btn btn-sm btn-success btn-block" name="btnuploadexcel" id="btnuploadexcel"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
&nbsp;Upload Excel</button> 
			</div>

			<div class="col-xs-1 nopadwleft">	
				<a class="btn btn-sm btn-info btn-block" name="btndltemplate" id="btndltemplate" href="../bom_template.xlsx"><i class="fa fa-download" aria-hidden="true"></i>
&nbsp;DL Template</a>
			</div>

			<input type="hidden" name="rowcnt" id="rowcnt" value=""> 
		</div>

		<hr class="here">
                       
                <table name='MyTbl' id='MyTbl' class="table table-scroll table-condensed">
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
											<th width="50" class="text-center"><b>Sub</b></td>
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

					<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="window.location.href='Items.php?itm=<?=$_REQUEST['itm']?>'" id="btnUndo" name="btnUndo">
						Undo Edit<br>(CTRL+Z)
					</button>

					<button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
						Edit<br> (CTRL+E)
					</button>

					<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="chkform();" id="btnSave" name="btnSave">
						Save<br> (CTRL+S)
					</button>
			
			</td>

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

				<!-- 1) Add Sub Item Modal -->
					<div class="modal fade" id="modaddsub" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
						<div class="modal-dialog  modal-lg">
							<div class="modal-content">

								<div class="modal-header">
									Add Sub Level
								</div>
								<div class="modal-body" style="height:20vh">
											<center>
												<input type="text" class="form-control input-sm" id="txtscan2" value="" placeholder="Sub - Search Item Name...">
												<input type="hidden" id="levelsub" value="">
												<input type="hidden" id="levelindex" value="">
											</center>
								</div>

								<div class="modal-footer">
										<button type="button" class="btn btn-danger" data-dismiss="modal">Done</button>
								</div>

							</div>
						</div>
					</div>

					<!-- Upload Excel -->

					<div class="modal fade" id="moduploadexcel" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
						<div class="modal-dialog  modal-lg">
							<div class="modal-content">

							<form action="upload.php" method="post" enctype="multipart/form-data">
								<div class="modal-header">
									Upload Excel
								</div>
								<div class="modal-body" style="height:20vh">

										<fieldset>

											<div class="row">
												<div class="col-xs-3">&nbsp;</div>
												<div class="col-xs-6 text-center">

													<h4>Select bom template to upload:</h4>
													<br>
													<input type="file" name="file" id="file" class="form-control">
													<input type="hidden" name="xcitemno" id="xcitemno" value="<?=$_REQUEST['itm']?>">
													<br>
												</div>

												<div class="col-xs-3">&nbsp;</div>

											</div>

											

										</fieldset>									
								</div>

								<div class="modal-footer">
										
										<button type="submit" class="btn btn-success">Upload</button>

										<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
								</div>

							</form>

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
		else if(e.keyCode == 69 && e.ctrlKey){//CTRL E
		if($("#btnEdit").is(":disabled")==false){
			e.preventDefault();
			enabled();
		}
	  }
		else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
		if($("#btnUndo").is(":disabled")==false){
			e.preventDefault();
			chkSIEnter(13,'frmpos');
		}
	  }

	});


	$(document).ready(function() {

		loadItms();

		disabled();

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

		$("#btnuploadexcel").on("click", function(){
			$("#moduploadexcel").modal("show");
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

				rowC = rowCount + 1;

				InsTotable(item.id,item.desc,item.cunit,rowC,2,rowCount);
					
				$('#txtscan').val("").change();

				//$("#MyTbl:not(thead)").tableDnDUpdate();
																		
			}
		
		});

		$('#txtscan2').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
					dataType: "json",
					data: { query: $("#txtscan2").val() },
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

				InsTotable(item.id,item.desc,item.cunit,rowCount,$("#levelsub").val(),$("#levelindex").val());
					
				$('#txtscan2').val("").change();

				//$("#MyTbl:not(thead)").tableDnDUpdate();
																		
			}
		
		});

		$("input[name='radversion']").click(function(){
      var radioValue = $("input[name='radversion']:checked").val();

      if(radioValue){
				$.ajax({
					url: "set_default.php",
					dataType: "text",
					data: { ver: radioValue, x: "<?=$_REQUEST['itm']?>" },
					success: function (data) {
						if(data.trim()=="True"){
							window.location.href = "Items.php?itm=<?=$_REQUEST['itm']?>";
						}

					}
				});
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

				var GENxyz = parseInt(this['nlevel'])-1;
						
				var GENxyz0 = 0;
				if(GENxyz>1){
					GENxyz0 = (5 * GENxyz) + (GENxyz * 2);
				}

				$tdrows = "<td><input type='text' class=\"form-control input-xs text-center\" value='"+sornum+"' name=\"txtsortnum\" id=\"txtsortnum"+sornum+"\" readonly></td><td><input type='hidden' value='"+itmid+"' name=\"txtitmcode\" id=\"txtitmcode"+sornum+"\">"+itmid+"</td><td><input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc\" id=\"txtitmdesc"+sornum+"\"><div style='text-indent:"+GENxyz0+"px'>"+itmdesc+"</div></td><td><input type='hidden' value='"+itmunit+"' name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+itmunit+"</td><td><input type=\"text\" class=\"form-control input-xs text-center\" name=\"txtlvl\" id=\"txtlvl"+sornum+"\" value=\""+this['nlevel']+"\" readonly></td>";


				getcnt = parseInt($("#hdncount").val());
				for (i = 1; i <= getcnt; i++) {

					$tdrows = $tdrows + "<td><input type='text' class=\"form-control input-xs text-center\" value='"+this['nqty'+i]+"' name=\"txtnqty"+i+"\" id=\"txtnqty"+sornum+"\"></td>";

				}

				$tdrows = $tdrows + "<td class=\"text-center\"><button class=\"btn btn-danger btn-xs\" id=\"btnDel"+sornum+"\"><i class=\"fa fa-times\"></i></button></td>";

				$tdrows = $tdrows + "<td class=\"text-center\"><button type='button' class=\"btn btn-success btn-xs\" name=\"btnAdd\" id=\"btnAdd"+sornum+"\"><i class=\"fa fa-arrow-circle-down\"></i></button></td>";
				
				$row = "<tr id='tr"+sornum+"' class=\"bg-level"+this['nlevel']+"\">"+$tdrows+"</tr>";
				$("#MyTbl tbody").append($row);

				$("#btnDel"+sornum).on('click', function() { 
					$(this).closest('tr').remove();
					recomdel();
				});

				$("#btnAdd"+sornum).on('click', function() { 
					addsub(this);
				});


		});
	} 

	function InsTotable(itmid,itmdesc,itmunit,sornum,lvl,indx){

		//loop check if item exist


				var GENxyz = parseInt(lvl)-1;
						
				var GENxyz0 = 0;
				if(GENxyz>1){
					GENxyz0 = (5 * GENxyz) + (GENxyz * 2);
				}

			var $tdrows = "";

			$tdrows = "<td><input type='text' class=\"form-control input-xs text-center\" value='"+sornum+"' name=\"txtsortnum\" id=\"txtsortnum"+sornum+"\" readonly></td><td><input type='hidden' value='"+itmid+"' name=\"txtitmcode\" id=\"txtitmcode"+sornum+"\">"+itmid+"</td><td><input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc\" id=\"txtitmdesc"+sornum+"\"><div style='text-indent:"+GENxyz0+"px'>"+itmdesc+"</div></td><td><input type='hidden' value='"+itmunit+"' name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+itmunit+"</td><td><input type=\"text\" class=\"form-control input-xs text-center\" name=\"txtlvl\" id=\"txtlvl"+sornum+"\" value=\""+lvl+"\" readonly></td>";


			getcnt = parseInt($("#hdncount").val());
			for (i = 1; i <= getcnt; i++) {

				$tdrows = $tdrows + "<td><input type='text' class=\"form-control input-xs text-center\" value='1' name=\"txtnqty"+i+"\" id=\"txtnqty"+i+sornum+"\"></td>";

			}

			$tdrows = $tdrows + "<td class=\"text-center\"><button type='button' class=\"btn btn-danger btn-xs\" name=\"btnDel\" id=\"btnDel"+sornum+"\"><i class=\"fa fa-times\"></i></button></td>";

			$tdrows = $tdrows + "<td class=\"text-center\"><button type='button' class=\"btn btn-success btn-xs\" name=\"btnAdd\" id=\"btnAdd"+sornum+"\"><i class=\"fa fa-arrow-circle-down\"></i></button></td>";
			
			$row = "<tr id='tr"+sornum+"' class=\"bg-level"+lvl+"\">"+$tdrows+"</tr>";
			//$("#MyTbl tbody").append($row);
			if(indx==0){
				$("#MyTbl tbody").append($row);
			}else{
				$('#tr'+indx).after($row);
				reindextbl();
			}	

			$("#btnDel"+sornum).on('click', function() { 
				recomdel(this);

				$(this).closest('tr').remove();
				reindextbl();
			});

			$("#btnAdd"+sornum).on('click', function() { 
				addsub(this);
			});

	}

	function addsub(xc){
		getid = xc.id;

		rowindx = xc.parentNode.parentNode.rowIndex;

		getid = getid.replace("btnAdd","");

		var getsub = $("#txtlvl"+getid).val();
		getsub = parseInt(getsub) + 1;

		$("#txtscan2").attr("placeholder", "Level "+getsub+" - Search Item Name...");
		$("#levelsub").val(getsub); 
		$("#levelindex").val(rowindx);

		$("#modaddsub").modal("show");
	}

	function reindextbl(){
		var tx = 0;
		$("#MyTbl > tbody > tr").each(function(index) {
			tx = index + 1;

			//alert(tx);
			$(this).attr("id", "tr"+tx);
			$(this).find('input[name="txtsortnum"]').val(tx);
			$(this).find('input[name="txtsortnum"]').attr("id","txtsortnum"+tx);

			$(this).find('input[type=hidden][name="txtitmcode"]').attr("id","txtitmcode"+tx);
			$(this).find('input[type=hidden][name="txtitmdesc"]').attr("id","txtitmdesc"+tx);
			$(this).find('input[type=hidden][name="txtcunit"]').attr("id","txtcunit"+tx);
			$(this).find('input[name="txtlvl"]').attr("id","txtlvl"+tx);

			getcnt = parseInt($("#hdncount").val());
			for (i = 1; i <= getcnt; i++) {

				$(this).find('input[name="txtnqty'+i+'"]').attr("id","txtnqty"+i+tx);

			}

			$(this).find('button[name="btnDel"]').attr("id","btnDel"+tx);
			$(this).find('button[name="btnAdd"]').attr("id","btnAdd"+tx);

			$("#btnAdd"+tx).attr("onclick","addsub(this)");

		});
	}

	function recomdel(xc){
		rowindx = xc.parentNode.parentNode.rowIndex;
		getid = xc.id;
		getid = getid.replace("btnDel","");

		var getsub = $("#txtlvl"+getid).val();

		$("#MyTbl > tbody > tr").each(function(index) {
			tx = index + 1;

			disid = $(this).attr("id");
			disid = disid.replace("tr","");

			foundlvl = $(this).find('input[name="txtlvl"]').val();
			if(parseInt(disid) > parseInt(getid) && parseInt(foundlvl) > parseInt(getsub)){

				$(this).closest('tr').remove();

			}else if(parseInt(disid) > parseInt(getid) && parseInt(foundlvl) <= parseInt(getsub)){
				return false;
			}

		});
	}

	function chkform(){
		var qty = "False";

		var tbl1 = document.getElementById('MyTbl').getElementsByTagName('tr');
		var lastRow1 = tbl1.length-1;

		if(lastRow1!=0){
			//re intialize

			$("#MyTbl > tbody > tr").each(function(index) {
				$tx = index+1;

				$(this).find('input[name="txtsortnum"]').val($tx);
				$(this).find('input[name="txtsortnum"]').attr('name','txtsortnum'+$tx);
				$(this).find('input[type=hidden][name="txtitmcode"]').attr("name","txtitmcode"+$tx);
				$(this).find('input[type=hidden][name="txtitmdesc"]').attr("name","txtitmdesc"+$tx);
				$(this).find('input[type=hidden][name="txtcunit"]').attr("name","txtcunit"+$tx);
				$(this).find('input[name="txtlvl"]').attr("name","txtlvl"+$tx);

				getcnt = parseInt($("#hdncount").val());
				for (i = 1; i <= getcnt; i++) {

					$(this).find('input[name="txtnqty'+i+'"]').attr("name","txtnqty"+i+$tx);

				}

			});

			$("#rowcnt").val(lastRow1);
		
			$("#frmBOM").submit();

		}else{
			$("#AlertMsg").html("No details to save!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');
		}
	}


	function disabled(){

		$("#frmBOM :input").attr("disabled", true);   

		$("#btnMain").attr("disabled", false);
		$("#btnaddversion").attr("disabled", false);
		$("#btnuploadexcel").attr("disabled", false); 
		$("#btnEdit").attr("disabled", false);
		$("#btndltemplate").attr("disabled", false);

		$('input[name="radversion"]').attr("disabled",false);

	}

	function enabled(){

		$("#frmBOM :input").attr("disabled", false);   

		$("#btnMain").attr("disabled", true);
		$("#btnaddversion").attr("disabled", true);
		$("#btnuploadexcel").attr("disabled", true); 
		$("#btnEdit").attr("disabled", true);
		$("#btndltemplate").attr("disabled", true);

		$('input[name="radversion"]').attr("disabled",true);

	}


</script>
