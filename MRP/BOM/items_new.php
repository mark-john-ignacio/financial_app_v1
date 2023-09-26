<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "MaterialBOM_new";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];

	$arrprocess = array();
  $sqlprocess = mysqli_query($con,"SELECT * FROM `mrp_process` WHERE compcode='$company' and cstatus='ACTIVE'"); 
  if (mysqli_num_rows($sqlprocess)!=0) {
    while($row = mysqli_fetch_array($sqlprocess, MYSQLI_ASSOC)){
      $arrprocess[] = $row;
  	}
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
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css">  
    
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<script src="../../include/tableDnd/js/jquery.tablednd.js" type="text/javascript"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

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
			margin-top: 50px !important;
			margin-bottom: 3px !important;
			width: 100% !important;
		}
	</style>

</head>

<body style="padding: 10px !important">
<input type="hidden" value='<?=json_encode(@$arrprocess)?>' id="hdnprocess">  
<input type="hidden" id="hdndefbom" value='<?=json_encode($arrdefitems)?>'>


	<form id="frmBOM" name="frmBOM" method="post" action="item_save.php">
		<input type="hidden" id="hdncount" name="hdncount" value='1'>

		<fieldset>
			<legend>Bill of Materials</legend>
		</fieldset>	

		<div class="col-xs-12 nopadwtop"> 
			<div class="col-xs-2 nopadding"> <b>Product No.: </b> </div> 
			<div class="col-xs-2 nopadding"> <input type="text" class="form-control input-xs" name="cmainitemno"  id="cmainitemno" value="" readonly> </div>
			<div class="col-xs-1 nopadwleft"> <button type="button" id="btnsrchprod" class="btn btn-xs btn-success" data-toggle="modal" data-target="#moditm"><i class="fa fa-search" aria-hidden="true"></i></button> </div>
			<div class="col-xs-1 nopadding"> <b>Unit: </b> </div>
			<div class="col-xs-2 nopadding"> <input type="text" class="form-control input-xs" name="cunit"  id="cunit" value="" readonly> </div>
		</div>

		<div class="col-xs-12 nopadwtop">
			<div class="col-xs-2 nopadding"> <b>Product Description: </b> </div>
			<div class="col-xs-6 nopadding"> <input type="text" class="form-control input-xs" name="citemdesc"  id="citemdesc" value="" readonly> </div>
		</div>

		<div class="col-xs-12 nopadwtop2x">&nbsp;</div>

			<ul class="nav nav-tabs">
				<li class="active"><a href="#comp">Components</a></li>
				<li><a href="#para">Parameters</a></li>
			</ul>

			<div class="tab-content">  

				<div id="comp" class="tab-pane fade in active" style="padding-left:5px;">

					<div class="col-xs-12 nopadwtop2x">	 
						<div class="col-xs-10 nopadwdown">	
							<input type="text" class="form-control input-sm" id="txtscan" value="" placeholder="Level 2 - Search Item Name...">
						</div>
						<!--<div class="col-xs-2 nopadwleft">	
							<button type="button" class="btn btn-sm btn-warning btn-block" name="btnaddversion" id="btnaddversion"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Version</button>
						</div>-->

						<div class="col-xs-2 nopadwleft">	
							<button type="button" class="btn btn-sm btn-success btn-block" name="btnuploadexcel" id="btnuploadexcel" disabled><i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;Upload Excel</button> 
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
								<th width="70" class="text-center">Default
									<br>
									<input type="radio" name='radversion' checked>
								</th>
								<th width="50" class="text-center"><b>Del</b></td>
								<th width="50" class="text-center"><b>Sub</b></td>
							</tr>
						</thead>
						<tbody>											
						</tbody>
					</table>
				</div>
				<div id="para" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">
					<div class="col-xs-12 nopadwtop">
						<div class="col-xs-2 nopadding"> <b>Working Hours.: </b> </div>
						<div class="col-xs-2 nopadding"> <input type="text" class="numeric form-control input-xs" name="nworkinghrs"  id="nworkinghrs" value="0" > </div>
					</div>

					<div class="col-xs-12 nopadwtop">
						<div class="col-xs-2 nopadding"> <b>Setup Time.: </b> </div>
						<div class="col-xs-2 nopadding"> <input type="text" class="numeric form-control input-xs" name="nsetuptime"  id="nsetuptime" value="0"> </div>
					</div>

					<div class="col-xs-12 nopadwtop">
						<div class="col-xs-2 nopadding"> <b>Cycle Time.: </b> </div>
						<div class="col-xs-2 nopadding"> <input type="text" class="numeric form-control input-xs" name="ncycletime"  id="ncycletime" value="0"> </div>
					</div>

					&nbsp;
					<hr style="border: 1px solid DodgerBlue;"><h4>Process List</h4>
          	<input type="button" value="Add Process" name="btnaddprocess" id="btnaddprocess" class="btn btn-primary btn-xs" onClick="addprocess();">                                
            <input name="hdnprocesslist" id="hdnprocesslist" type="hidden" value="0">

            <table width="50%" border="0" cellpadding="2" id="myProcessTable" style="margin-top: 10px;">
              <tr>
                <th scope="col">PROCESS</th>
                <th scope="col" width="80">STATUS</th>
              </tr>
            </table>
				</div>

			</div>


		<br>
		<table width="100%" border="0" cellpadding="3">
			<tr>
				<td>
					<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='items_list.php';" id="btnMain" name="btnMain">
						Back to Main<br>(ESC)
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
													<input type="hidden" name="xcitemno" id="xcitemno" value="">
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

					<!-- Search Item -->
					<div class="modal fade" id="moditm" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
						<div class="modal-dialog  modal-lg">
							<div class="modal-content">

								<div class="modal-header">
									List of Items
								</div>
								<div class="modal-body" style="height:70vh" style="overflow: auto">
											<input type="text" class="form-control input-sm" id="txtsrchitm" value="" placeholder="Search Item Name...">

											<table id="tblitms" class="table table-sm table-striped" style="width:100%; padding-top: 5px">
												<thead>
													<tr>
														<th>Item Code</th>
														<th>Description</th>
														<th>Unit</th>
													</tr>
												</thead>
												<tbody>

												</tbody>
											</table>
								</div>
							</div>
						</div>
					</div>

</body>

</html>

<script type="text/javascript">

	$("#txtscan").focus();

	fillitmtable();

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

		$("input.numeric").autoNumeric('init',{mDec:2});
		$("input.numeric").on("click", function () {
			$(this).select();
		});

		$(".nav-tabs a").click(function(){
    	$(this).tab('show');
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

		$('#txtsrchitm').on("keyup", function (){
			$('#tblitms').DataTable().destroy();
			fillitmtable($(this).val());
		});
		

	});

	function InsTotable(itmid,itmdesc,itmunit,sornum,lvl,indx){

		//loop check if item exist


				var GENxyz = parseInt(lvl)-1;
						
				var GENxyz0 = 0;
				if(GENxyz>1){
					GENxyz0 = (5 * GENxyz) + (GENxyz * 2);
				}

			var $tdrows = "";

			$tdrows = "<td><input type='text' class=\"form-control input-xs text-center\" value='"+sornum+"' name=\"txtsortnum\" id=\"txtsortnum"+sornum+"\" readonly></td><td><input type='hidden' value='"+itmid+"' name=\"txtitmcode\" id=\"txtitmcode"+sornum+"\">"+itmid+"</td><td><input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc\" id=\"txtitmdesc"+sornum+"\"><div style='text-indent:"+GENxyz0+"px'>"+itmdesc+"</div></td><td><input type='hidden' value='"+itmunit+"' name=\"txtcunit\" id=\"txtcunit"+sornum+"\">"+itmunit+"</td><td><input type=\"text\" class=\"form-control input-xs text-center\" name=\"txtlvl\" id=\"txtlvl"+sornum+"\" value=\""+lvl+"\" readonly></td>";

			$tdrows = $tdrows + "<td><input type='text' class=\"form-control input-xs text-center\" value='1' name=\"txtnqty\" id=\"txtnqty1"+sornum+"\"></td>";

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

	function addprocess(){
    var tbl = document.getElementById('myProcessTable').getElementsByTagName('tr');
    var lastRow = tbl.length;

    var a=document.getElementById('myProcessTable').insertRow(-1);
    var u=a.insertCell(0);
		u.style.paddingTop = "1px"; 
    var y=a.insertCell(1);
		y.style.paddingTop = "1px"; 

    var xz = $("#hdnprocess").val();
		prooptions = "";
		$.each(jQuery.parseJSON(xz), function() { 
			prooptions = prooptions + "<option value='"+this['nid']+"'>"+this['cdesc']+"</option>";
		});
        
    u.innerHTML = "<div id='divselproc"+lastRow+"' class=\"col-xs-12 nopadwright\"><select name='selproc"+lastRow+"' id='selproc"+lastRow+"' class='form-control input-sm selectpicker'>"+prooptions+"</select></div>";
    y.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='Delete' onClick=\"delProcRow(this);\"/>";

  }

  function delProcRow(r) {
    var tbl = document.getElementById('myProcessTable').getElementsByTagName('tr');
    var lastRow = tbl.length;
    var i=r.parentNode.parentNode.rowIndex;
    document.getElementById('myProcessTable').deleteRow(i);
    var lastRow = tbl.length;
    var z; //for loop counter changing textboxes ID;
        
    for (z=i+1; z<=lastRow; z++){
      var tempcitemno = document.getElementById('selproc' + z);
                
      var x = z-1;
      tempcitemno.id = "selproc" + x;
      tempcitemno.name = "selproc" + x;

    }
  }


	function chkform(){
		var qty = "False";

		var tbl1 = document.getElementById('MyTbl').getElementsByTagName('tr');
		var lastRow1 = tbl1.length-1;
		$("#rowcnt").val(lastRow1);

		var tbl = document.getElementById('myProcessTable').getElementsByTagName('tr');
    var lastRow = tbl.length-1;                                               
    $("#hdnprocesslist").val(lastRow);

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

					$(this).find('input[name="txtnqty"]').attr("name","txtnqty"+i+$tx);

				}

			});
		
			$("#frmBOM").submit();

		}else{
			$("#AlertMsg").html("No details to save!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');
		}
	}

	function fillitmtable(srchitm = "")
	{
		var dataTable = $('#tblitms').DataTable({
		  "processing" : true,
		  "serverSide" : true,
		  "lengthChange": false,
		  "searching" : false,
		  "ajax" : {
		    url:"th_itemsdata.php",
		    type:"POST",
		    data:{
		      searchByName:srchitm
		    }
		  },
		  "columns": [
				{ "data": null,
					"render": function (data, type, full, row) {							
						return "<a href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"','"+full[1]+"','"+full[2]+"');\">"+full[0]+"</a>";							
					}						
				},
				{ "data": 1 },
				{ "data": 2 }
      ],
		});
	}

	function editfrm($xid,$xdesc,$xunit){
		$("#cmainitemno").val($xid);
		$("#citemdesc").val($xdesc);
		$("#cunit").val($xunit);   

		$("#xcitemno").val($xid);
		$("#btnuploadexcel").attr("disabled", false); 

		$("#moditm").modal("hide");
	}

</script>
