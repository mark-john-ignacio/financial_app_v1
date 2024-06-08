<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "MaterialBOM";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'MaterialBOM_edit'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}


	$itm = "";
	if(isset($_REQUEST['itm'])){
		$itm = $_REQUEST['itm'];
	}

	$arrdefitems = array();
	$sqllabelnme = mysqli_query($con,"select A.*, B.citemdesc from mrp_bom A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.cmainitemno='".$itm ."' order by A.nversion, A.nitemsort");
	$rowlabelname = $sqllabelnme->fetch_all(MYSQLI_ASSOC);
	foreach($rowlabelname as $rs3){
		$arrdefitems[] = $rs3;
	}

	$itmname = "";
	$itmuom = "";
	$sqllabelnme = mysqli_query($con,"select * from items where compcode='$company' and cpartno='".$itm ."'");
	$rowlabelname = $sqllabelnme->fetch_all(MYSQLI_ASSOC);
	foreach($rowlabelname as $rs4){
		$itmname = $rs4['citemdesc'];
		$itmuom = $rs4['cunit'];
	}


	$arrbomlabel = array();
	$sqllabelnme = mysqli_query($con,"select * from mrp_bom_label where compcode='$company' and citemno='".$itm ."'");

	$rowcount=mysqli_num_rows($sqllabelnme);
	$rowlabelname = $sqllabelnme->fetch_all(MYSQLI_ASSOC);

	$totdcount = 1;
	if($rowcount>1){
		$totdcount = $rowcount;
	}

	$arrprocess = array();
  $sqlprocess = mysqli_query($con,"SELECT * FROM `mrp_process` WHERE compcode='$company' and cstatus='ACTIVE'"); 
  if (mysqli_num_rows($sqlprocess)!=0) {
    while($row = mysqli_fetch_array($sqlprocess, MYSQLI_ASSOC)){
      $arrprocess[] = $row;
  	}
  }

	$arrparams = array();
  $sqlprocess = mysqli_query($con,"SELECT * FROM `mrp_items_parameters` WHERE compcode='$company' and citemno='".$itm ."'"); 
  if (mysqli_num_rows($sqlprocess)!=0) {
    while($row = mysqli_fetch_array($sqlprocess, MYSQLI_ASSOC)){
      $arrparams[] = $row;
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
    
	<link rel="stylesheet" type="text/css" href="../../global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
    <link href="../../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript" ></script>

	<script src="../../include/autoNumeric.js"></script>

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
			margin-top: 50px !important;
			margin-bottom: 3px !important;
			width: 100% !important;
		}

		.reset {
    	all: revert;
		}
	</style>

</head>

<body style="padding: 10px !important">
<input type="hidden" value='<?=json_encode(@$arrprocess)?>' id="hdnprocess">  
<input type="hidden" id="hdndefbom" value='<?=json_encode($arrdefitems)?>'>


	<form id="frmBOM" name="frmBOM" method="post" action="item_save.php">
		<input type="hidden" id="hdncount" name="hdncount" value='<?=$totdcount?>'>


		<div class="col-xs-12 nopadding" style="border-bottom: 2px solid #cce">	 
			<div class="col-xs-8 nopadding" >	
				<font size="+1"><b>Bill of Materials</b></font>
			</div>

			<div class="col-xs-2 nopadwdown">	
				<button type="button" class="btn btn-sm btn-warning btn-block" name="btnaddversion" id="btnaddversion"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Version</button>
			</div>

			<div class="col-xs-2 nopadwleft">	
				<button type="button" class="btn btn-sm btn-success btn-block" name="btnuploadexcel" id="btnuploadexcel"><i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;Upload Excel</button> 
			</div>
		</div>

		<div class="col-xs-12 nopadwtop">&nbsp;</div>

		<div class="col-xs-12 nopadwtop">
			<div class="col-xs-1" style="padding-top: 6px !important; padding-left: 0 !important"> <b>Customer: </b> </div>
			<div class="col-xs-3 nopadding"> <input type="text" class="form-control input-sm" name="citemcustomer"  id="citemcustomer" value="<?=$arrparams[0]['ccustomer']?>" placeholder="Customer Name"> </div>

			<div class="col-xs-1 text-right" style="padding-top: 6px !important; padding-left: 0 !important"> <b>Project: </b> </div>
			<div class="col-xs-3 nopadwleft"> <input type="text" class="form-control input-sm" name="citemproj"  id="citemproj" value="<?=$arrparams[0]['cproject']?>" placeholder="Project"> </div>

			<div class="col-xs-1 text-right" style="padding-top: 6px !important; padding-left: 0 !important"> <b>Title: </b> </div>
			<div class="col-xs-3 nopadwleft"> <input type="text" class="form-control input-sm" name="citemtitl"  id="citemtitl" value="<?=$arrparams[0]['ctitle']?>" placeholder="Title"> </div>
		</div>

		<div class="col-xs-12 nopadwtop"> 
			<div class="col-xs-1" style="padding-top: 6px !important; padding-left: 0 !important"> <b>Product: </b> </div> 
			<div class="col-xs-2 nopadding">
				<div class="input-group">
					
					<span class="input-group-btn">
						<button class="btn btn-primary btn-sm" id="btnsrchprod" type="button" data-toggle="modal" data-target="#moditm"><span class="glyphicon glyphicon-search" aria-hidden="true">
						</span> Search!</button>
					</span>
					<input type="text" class="form-control input-sm" name="cmainitemno"  id="cmainitemno" value="<?=$itm?>" readonly placeholder="Code">
				</div>
			</div>
			
			<div class="col-xs-6 nopadwleft"> <input type="text" class="form-control input-sm" name="citemdesc"  id="citemdesc" value="<?=$itmname?>" readonly placeholder="Description"> </div>
			<div class="col-xs-1 nopadwleft"> <input type="text" class="form-control input-sm" name="cunit"  id="cunit" value="<?=$itmuom?>" readonly placeholder="UOM"> </div>
		</div>

		<div class="col-xs-12 nopadwtop2x">&nbsp;</div>

			<ul class="nav nav-pills">
				<li class="active"><a data-toggle="pill" href="#comp">Components</a></li>
				<li><a data-toggle="pill" href="#para">Parameters</a></li>
			</ul>

			<div class="tab-content">  

				<div id="comp" class="tab-pane fade in active" style="padding-left:5px;">

					<ul class="nav nav-tabs" style="margin-top: 5px">
						<?php
							if($totdcount>1){
								$xc=0;
								$xcstat = "";
								foreach($rowlabelname as $rowx){
									$xc++;
									if($xc==1){
										$xcstat = "class='active'";
									}else{
										$xcstat = "";
									}
						?>
						<li <?=$xcstat?>><a href="#V<?=$rowx['nversion']?>" class="bg-danger"><?=$rowx['cdesc']?></a></li>
						<?php
								}
							}else{
						?>
						<li class="active"><a href="#V1">Default</a></li>
						<?php
							}
						?>
					</ul>

					<div class="tab-content">
						<?php
							$xc=0;
							$xcstat = "";
							foreach($rowlabelname as $rowx){
								$xc++;
								if($xc==1){
									$xcstat = "active";
								}else{
									$xcstat = "";
								}
						?>
							<div id="V<?=$rowx['nversion']?>" class="tab-pane fade in <?=$xcstat?>" >
									
								<div class="col-xs-12 nopadwtop2x">	 
									<div class="col-xs-10 nopadwdown">	
										<input type="text" class="txtscan form-control input-sm" id="txtscan<?=$rowx['nversion']?>" value="" placeholder="Level 2 - Search Item Name..." data-id="<?=$rowx['nversion']?>">
									</div>
									<?php
										if($rowx['ldefault']==0){
									?>
										<div class="col-xs-2 nopadwleft">	
											<button type="button" class="btnact btn btn-sm btn-info btn-block" name="btnact<?=$rowx['nversion']?>" id="btnact<?=$rowx['nversion']?>" data-id="<?=$rowx['nversion']?>"><i class="fa fa-check-circle-o" aria-hidden="true"></i>&nbsp;Set as Active</button>
										</div>
									<?php
										}
									?>

									
									<input type="hidden" name="rowcnt<?=$rowx['nversion']?>" id="rowcnt<?=$rowx['nversion']?>" value=""> 
								</div>

								<hr class="here">

								<fieldset class="">
									<legend class=""><b>ECO List Details</b></legend>

									<div class="col-xs-12 nopadwtop">
										<div class="col-xs-1" style="padding-top: 6px !important; padding-left: 0 !important"> <b>S/N: </b> </div>
										<div class="col-xs-1 nopadding"> <input type="text" class="form-control input-sm" name="bomecosn<?=$rowx['nversion']?>" id="bomecosn<?=$rowx['nversion']?>" value="<?=$rowx['ecoSN']?>" placeholder="S/N"> </div>

										<div class="col-xs-1 text-right" style="padding-top: 6px !important; padding-left: 0 !important"> <b>Revision: </b> </div>
										<div class="col-xs-1 nopadwleft"> <input type="text" class="form-control input-sm" name="bomecorev<?=$rowx['nversion']?>" id="bomecorev<?=$rowx['nversion']?>" value="<?=$rowx['ecoRev']?>" placeholder="Rev."> </div>

										<div class="col-xs-2 text-right" style="padding-top: 6px !important; padding-left: 0 !important"> <b>Prepared By: </b> </div>
										<div class="col-xs-3 nopadwleft"> <input type="text" class="form-control input-sm" name="bomecoprep<?=$rowx['nversion']?>" id="bomecoprep<?=$rowx['nversion']?>" value="<?=$rowx['ecoPrepared']?>" placeholder="Prepared By"> </div>

										<div class="col-xs-1 text-right" style="padding-top: 6px !important; padding-left: 0 !important"> <b>Date: </b> </div>
										<div class="col-xs-2 nopadwleft"> <input type="date" class="form-control input-sm" name="bomecodate<?=$rowx['nversion']?>" id="bomecodate<?=$rowx['nversion']?>" value="<?=$rowx['ecoDate']?>" placeholder="Date"> </div>

									</div>

									<div class="col-xs-12 nopadwtop">
										<div class="col-xs-1" style="padding-top: 6px !important; padding-left: 0 !important"> <b>Description: </b> </div>
										<div class="col-xs-11 nopadding"> <textarea class="form-control input-sm" name="bomecodesc<?=$rowx['nversion']?>"><?=$rowx['ecoDesc']?></textarea> </div>
									</div>
								</fieldset>
										
								<hr>

								<table name='MyTbl<?=$rowx['nversion']?>' id='MyTbl<?=$rowx['nversion']?>' class="table table-scroll table-condensed">
									<thead>
										<tr>
											<th width="50">&nbsp;</th>
											<th width="150">Item Code</th>
											<th>Item Description</th>
											<th width="70" class="text-center">Unit</th>
											<th width="70" class="text-center">Level</th>
											<th width="70" class="text-center">Qty</th>											
											<th width="80" class="text-center"><b>Type</b></td>
											<th width="50" class="text-center"><b>Del</b></td>
										</tr>
									</thead>
									<tbody>											
									</tbody>
								</table>

							</div>
						<?php
							}
						?>
					</div>

				
				</div>
				<div id="para" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">
					<div class="col-xs-12 nopadwtop">
						<div class="col-xs-2 nopadding"> <b>Working Hours.: </b> </div>
						<div class="col-xs-2 nopadding"> <input type="text" class="numeric form-control input-xs" name="nworkinghrs"  id="nworkinghrs" value="<?=isset($arrparams[0]['nworkhrs']) ? $arrparams[0]['nworkhrs'] : 0;?>" > </div>
					</div>

					<div class="col-xs-12 nopadwtop">
						<div class="col-xs-2 nopadding"> <b>Setup Time.: </b> </div>
						<div class="col-xs-2 nopadding"> <input type="text" class="numeric form-control input-xs" name="nsetuptime"  id="nsetuptime" value="<?=isset($arrparams[0]['nsetuptime']) ? $arrparams[0]['nsetuptime'] : 0?>"> </div>
					</div>

					<div class="col-xs-12 nopadwtop">
						<div class="col-xs-2 nopadding"> <b>Cycle Time.: </b> </div>
						<div class="col-xs-2 nopadding"> <input type="text" class="numeric form-control input-xs" name="ncycletime"  id="ncycletime" value="<?=isset($arrparams[0]['ncycletime']) ? $arrparams[0]['ncycletime'] : 0?>"> </div>
					</div>

					&nbsp;
					<hr style="border: 1px solid DodgerBlue;"><h4>Process List</h4>
          		<input type="button" value="Add Process" name="btnaddprocess" id="btnaddprocess" class="btn btn-primary btn-xs" onClick="addprocess();">                                
            	<input name="hdnprocesslist" id="hdnprocesslist" type="hidden" value="0">

				<table width="50%" border="0" cellpadding="2" id="myProcessTable" style="margin-top: 10px;">
				<tr>
									<th scope="col" width="50px">&nbsp;</th>
					<th scope="col">PROCESS</th>
					<th scope="col" width="80">&nbsp;</th>
				</tr>

				<?php
					$cbtr = 0;
					$sqlprocess = mysqli_query($con,"SELECT * FROM `mrp_process_t` WHERE compcode='$company' and citemno='".$itm."' Order by nid"); 
					if (mysqli_num_rows($sqlprocess)!=0) {
					while($row = mysqli_fetch_array($sqlprocess, MYSQLI_ASSOC)){
						$cbtr++;
				?>

				<tr>
									<td>
										<div class="nopadwright"><input type="text" readonly class="form-control input-sm text-center" id="nitemsort<?=$cbtr?>" value="<?=$cbtr?>"></div>
									</td>
					<td style="padding-top:1px">
					<div id='divselproc<?=$cbtr?>' class="col-xs-12 nopadwright">
											<select name='selproc<?=$cbtr?>' id='selproc<?=$cbtr?>' class='form-control input-sm selectpicker'>
												<?php
													foreach(@$arrprocess as $xcv){
														$xselec = "";
														if($xcv['nid']==$row['items_process_id']){
															$xselec = " selected";
														}

														echo "<option value='".$xcv['nid']."'".$xselec."> ".$xcv['cdesc']." </option>";
													}
												?>
											</select>
					</div>  
					</td>
					<td style="padding-top:1px">
										<button class='btn btn-danger btn-xs' type='button' id='row_<?=$cbtr?>_delete' class='delete' onClick="delProcRow(this);"> <i class="fa fa-trash"></i></button>
					</td>
				</tr>

				<?php
					}
					}
				?>
				</table>
			</div>

		</div>

		
		<?php
			if($poststat=="True"){
		?>
		<br>
		<table width="100%" border="0" cellpadding="3">
			<tr>
				<td>
					<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='items_list.php';" id="btnMain" name="btnMain">
						Back to Main<br>(ESC)
					</button>

					<button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='items_new.php';" id="btn_New" name="btn_New">
						New<br> (F1)
					</button>

					<button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="window.location.href='Items.php?itm=<?=(isset($_REQUEST['itm'])) ? $_REQUEST['itm'] : ""?>'" id="btnUndo" name="btnUndo">
						Undo Edit<br>(CTRL+Z)
					</button>

					<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $itm;?>');" id="btnPrint" name="btnPrint">
						Print<br>(CTRL+P)
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
		<?php
			}
		?>

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

	<!-- 1) Add Sub Item Modal
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
		</div> -->

					<!-- Upload Excel -->

					<div class="modal fade" id="moduploadexcel" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
						<div class="modal-dialog  modal-lg">
							<div class="modal-content">

							<form action="upload.php" method="post" enctype="multipart/form-data">
								<div class="modal-header">
									Upload Excel
								</div>
								<div class="modal-body" style="height:30vh">

										<fieldset>

											<div class="row">
												<div class="col-xs-3">&nbsp;</div>
												<div class="col-xs-6 text-center">
													<h4>Select bom template to upload:</h4>
													<br>
												</div>

												<div class="col-xs-3">&nbsp;</div>

											</div>

											<div class="row">
												<div class="col-xs-4 text-right"><b>Select Version: </b></div>
												<div class="col-xs-5"> 
													<select name="selver" id="selver" class="form-control" required>
														<option value="0">All Version</option>
														<?php
															foreach($rowlabelname as $rowx){
																echo "<option value=\"".$rowx['nversion']."\">".$rowx['cdesc']."</option>";
															}
														?>
													</select>
												</div>
											</div>

											<div class="row" style="padding-top: 5px !important">
												<div class="col-xs-4 nopadwtop2x text-right"><b>Select To Import File: </b></div>
												<div class="col-xs-8">
													<div class="form-group">
														<div class="col-md-12 nopadding">
															<div class="fileinput fileinput-new" data-provides="fileinput">
																<div class="input-group">
																	
																	<span class="input-group-addon btn btn-success default btn-file">
																	<span class="fileinput-new">
																	Select file </span>
																	<span class="fileinput-exists">
																	Change </span>
																	<input type="file" type="file" name="file" id="file" accept=".xlsx, .xls" required> 
																	</span>
																	<a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
																	Remove </a>
																	<div class="form-control uneditable-input" data-trigger="fileinput">
																		<i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
																		</span>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<input type="hidden" name="xcitemno" id="xcitemno" value="<?=$_REQUEST['itm']?>">
												</div>
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

					<form action="" method="post" name="frmQPrint" id="frmQprint" target="_blank">
						<input type="hidden" name="hdntransid" id="hdntransid" value="">
					</form>
</body>

</html>

<script type="text/javascript">

	$("#txtscan").focus();

	<?php
		if($poststat=="True"){
	?>

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){
			e.preventDefault();
			return chkform();
		}
	  }
		else if(e.keyCode == 112) { //F1
		if($("#btn_New").is(":disabled")==false){
			e.preventDefault();
			window.location.href='items_new.php';
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			window.location.href='items_list.php';
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

	<?php
		}
	?>


	$(document).ready(function() {

		loadItms();

		disabled();

		$("input.numeric").autoNumeric('init',{mDec:2});
		$("input.numeric").on("click", function () {
			$(this).select();
		});

		$(".nav-tabs a").click(function(){
    	$(this).tab('show');
		});

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
	
		$('.txtscan').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
					dataType: "json",
					data: { query: request },
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

				$id = this.$element.data("id");
			
				console.log('#MyTbl'+$id);

				var rowCount = $('#MyTbl'+$id+' tbody > tr').length;

				rowC = rowCount + 1;

				InsTotable(item.id,item.desc,item.cunit,rowC,2,rowCount,$id);
					
				this.$element.val("").change();

				//$("#MyTbl:not(thead)").tableDnDUpdate();
																		
			}
		
		});

		/*$('#txtscan2').typeahead({
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
		
		});*/

		$(".btnact").on("click", function(){
			var radioValue = $(this).data("id");

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

		
		var totvalver = $("#hdncount").val();

		for (let i = 1; i <= totvalver; i++){

			var xz = $("#hdndefbom").val();
			$.each(jQuery.parseJSON(xz), function() {  
				
				console.log(this);
				if(this['nversion']==i){
					
					itmid = this['citemno'];
					itmdesc = this['citemdesc'];
					itmunit = this['cunit'];
					sornum = this['nitemsort'];
					ctype = this['ctype'];

					var $tdrows = "";

					var GENxyz = parseInt(this['nlevel'])-1;
							
					var GENxyz0 = 0;
					if(GENxyz>1){
						GENxyz0 = (5 * GENxyz) + (GENxyz * 2);
					}

					$tdrows = "<td><input type='text' class=\"form-control input-xs text-center\" value='"+sornum+"' name=\"txtsortnum"+i+"\" id=\"txtsortnum"+i+""+sornum+"\" readonly></td><td><input type='hidden' value='"+itmid+"' name=\"txtitmcode"+i+"\" id=\"txtitmcode"+i+""+sornum+"\">"+itmid+"</td><td><input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc"+i+"\" id=\"txtitmdesc"+i+""+sornum+"\"><div style='text-indent:"+GENxyz0+"px'>"+itmdesc+"</div></td><td><input type='hidden' value='"+itmunit+"' name=\"txtcunit"+i+"\" id=\"txtcunit"+i+""+sornum+"\">"+itmunit+"</td><td><input type=\"text\" class=\"form-control input-xs text-center\" name=\"txtlvl"+i+"\" id=\"txtlvl"+i+""+sornum+"\" value=\""+this['nlevel']+"\" readonly></td>";

					$tdrows = $tdrows + "<td><input type='text' class=\"form-control input-xs text-center\" value='"+this['nqty1']+"' name=\"txtnqty"+i+"\" id=\"txtnqty"+i+""+sornum+"\"></td>";

					if(ctype=="MAKE"){
						var xmake = " selected";
						var xbuys = "";
					}else{
						var xmake = "";
						var xbuys = " selected";
					}
					
					$tdrows = $tdrows + "<td class=\"text-center\"><select class=\"form-control input-xs text-center\" name=\"selType"+i+"\" id=\"selType"+i+""+sornum+"\"><option value='MAKE'"+xmake+">MAKE</option><option value='BUY'"+xbuys+">BUY</option></select></td>";

					$tdrows = $tdrows + "<td class=\"text-center\"><button class=\"btn btn-danger btn-xs\" id=\"btnDel"+i+""+sornum+"\"><i class=\"fa fa-times\"></i></button></td>";

					//	$tdrows = $tdrows + "<td class=\"text-center\"><button type='button' class=\"btn btn-success btn-xs\" name=\"btnAdd\" id=\"btnAdd"+sornum+"\"><i class=\"fa fa-arrow-circle-down\"></i></button></td>";
					
					$row = "<tr id='tr"+i+""+sornum+"' class=\"bg-level"+this['nlevel']+"\">"+$tdrows+"</tr>";
					$("#MyTbl"+i+" tbody").append($row);

					$("#btnDel"+i+sornum).on('click', function() { 
						$(this).closest('tr').remove();
						reindextbl(i);
					});

					//$("#btnAdd"+sornum).on('click', function() { 
					//	addsub(this);
					//});

				}
			});
			

		}
	} 

	function InsTotable(itmid,itmdesc,itmunit,sornum,lvl,indx,$xid){

		//loop check if item exist

				var GENxyz = parseInt(lvl)-1;
						
				var GENxyz0 = 0;
				if(GENxyz>1){
					GENxyz0 = (5 * GENxyz) + (GENxyz * 2);
				}

			var $tdrows = "";

			$tdrows = "<td><input type='text' class=\"form-control input-xs text-center\" value='"+sornum+"' name=\"txtsortnum"+$xid+"\" id=\"txtsortnum"+$xid+""+sornum+"\" readonly></td><td><input type='hidden' value='"+itmid+"' name=\"txtitmcode"+$xid+"\" id=\"txtitmcode"+$xid+""+sornum+"\">"+itmid+"</td><td><input type='hidden' value='"+itmdesc+"' name=\"txtitmdesc"+$xid+"\" id=\"txtitmdesc"+$xid+""+sornum+"\"><div style='text-indent:"+GENxyz0+"px'>"+itmdesc+"</div></td><td><input type='hidden' value='"+itmunit+"' name=\"txtcunit"+$xid+"\" id=\"txtcunit"+$xid+""+sornum+"\">"+itmunit+"</td><td><input type=\"text\" class=\"form-control input-xs text-center\" name=\"txtlvl"+$xid+"\" id=\"txtlvl"+$xid+""+sornum+"\" value=\""+lvl+"\" readonly></td>";


			$tdrows = $tdrows + "<td><input type='text' class=\"form-control input-xs text-center\" value='1' name=\"txtnqty"+$xid+"\" id=\"txtnqty"+$xid+""+sornum+"\"></td>";

			$tdrows = $tdrows + "<td class=\"text-center\"><select class=\"form-control input-xs text-center\" name=\"selType"+$xid+"\" id=\"selType"+$xid+""+sornum+"\"><option value='MAKE'>MAKE</option><option value='BUY'>BUY</option></select></td>";

			$tdrows = $tdrows + "<td class=\"text-center\"><button type='button' class=\"btn btn-danger btn-xs\" name=\"btnDel"+$xid+"\" id=\"btnDel"+$xid+""+sornum+"\"><i class=\"fa fa-times\"></i></button></td>";

		//	$tdrows = $tdrows + "<td class=\"text-center\"><button type='button' class=\"btn btn-success btn-xs\" name=\"btnAdd\" id=\"btnAdd"+sornum+"\"><i class=\"fa fa-arrow-circle-down\"></i></button></td>";
			
			$row = "<tr id='tr"+$xid+sornum+"' class=\"bg-level"+lvl+"\">"+$tdrows+"</tr>";
			//$("#MyTbl tbody").append($row);

			//console.log("#MyTbl"+$xid);
			if(indx==0){
				$("#MyTbl"+$xid+" tbody").append($row);
			}else{
				$('#tr'+$xid+indx).after($row);
				reindextbl();
			}	

			$("#btnDel"+$xid+sornum).on('click', function() { 
				//recomdel(this);

				$(this).closest('tr').remove();
				reindextbl($xid);
			});

			//$("#btnAdd"+sornum).on('click', function() { 
				//addsub(this);
			//});

	}

	/*function addsub(xc){
		getid = xc.id;

		rowindx = xc.parentNode.parentNode.rowIndex;

		getid = getid.replace("btnAdd","");

		var getsub = $("#txtlvl"+getid).val();
		getsub = parseInt(getsub) + 1;

		$("#txtscan2").attr("placeholder", "Level "+getsub+" - Search Item Name...");
		$("#levelsub").val(getsub); 
		$("#levelindex").val(rowindx);

		$("#modaddsub").modal("show");
	}*/

	function reindextbl($xid){
		var tx = 0;
		$("#MyTbl"+$xid+" > tbody > tr").each(function(index) {
			tx = index + 1;

			//alert(tx);
			$(this).attr("id", "tr"+$xid+tx);
			$(this).find('input[name="txtsortnum'+$xid+'"]').val(tx);
			$(this).find('input[name="txtsortnum'+$xid+'"]').attr("id","txtsortnum"+tx);

			$(this).find('input[type=hidden][name="txtitmcode'+$xid+'"]').attr("id","txtitmcode"+tx);
			$(this).find('input[type=hidden][name="txtitmdesc'+$xid+'"]').attr("id","txtitmdesc"+tx);
			$(this).find('input[type=hidden][name="txtcunit'+$xid+'"]').attr("id","txtcunit"+tx);
			$(this).find('input[name="txtlvl'+$xid+'"]').attr("id","txtlvl"+tx);
			$(this).find('select[name="selType'+$xid+'"]').attr("name","selType"+tx);

			$(this).find('input[name="txtnqty'+$xid+'"]').attr("id","txtnqty"+$xid+tx);

			$(this).find('button[name="btnDel'+$xid+'"]').attr("id","btnDel"+tx);
			$(this).find('button[name="btnAdd'+$xid+'"]').attr("id","btnAdd"+tx);

			//$("#btnAdd"+tx).attr("onclick","addsub(this)");

		});
	}

	/*function recomdel(xc){
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
	}*/

	function addprocess(){
		var tbl = document.getElementById('myProcessTable').getElementsByTagName('tr');
    var lastRow = tbl.length;

    var a=document.getElementById('myProcessTable').insertRow(-1);

    var u=a.insertCell(0);
		u.style.paddingTop = "1px"; 
    var y=a.insertCell(1);
		y.style.paddingTop = "1px"; 
		var z=a.insertCell(2);
		z.style.paddingTop = "1px";

    var xz = $("#hdnprocess").val();
		prooptions = "";
		$.each(jQuery.parseJSON(xz), function() { 
			prooptions = prooptions + "<option value='"+this['nid']+"'>"+this['cdesc']+"</option>";
		});

		u.innerHTML = "<div class=\"nopadwright\"><input type=\"text\" readonly class=\"form-control input-sm text-center\" id=\"nitemsort"+lastRow+"\" value=\""+lastRow+"\"></div>";
    y.innerHTML = "<div id='divselproc"+lastRow+"' class=\"col-xs-12 nopadwright\"><select name='selproc"+lastRow+"' id='selproc"+lastRow+"' class='form-control input-sm selectpicker'>"+prooptions+"</select></div>";
    z.innerHTML = "<button class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' onClick=\"delProcRow(this);\"/> <i class=\"fa fa-trash\"></i></button>";

  }

  function delProcRow(r) {
    var tbl = document.getElementById('myProcessTable').getElementsByTagName('tr');
    var lastRow = tbl.length;
    var i=r.parentNode.parentNode.rowIndex;
    document.getElementById('myProcessTable').deleteRow(i);
    var lastRow = tbl.length;
    var z; //for loop counter changing textboxes ID;
        
    for (z=i+1; z<=lastRow; z++){
			var tempcitemnosort = document.getElementById('nitemsort' + z);
      var tempcitemno = document.getElementById('selproc' + z);
                
      var x = z-1;
			tempcitemnosort.id = "nitemsort" + x;
      tempcitemno.id = "selproc" + x;
      tempcitemno.name = "selproc" + x;

			$("#nitemsort" + x).val(x);

    }
  }


	function chkform(){
		var qty = "False";

		var tbl = document.getElementById('myProcessTable').getElementsByTagName('tr');
    var lastRow = tbl.length-1;                                               
    $("#hdnprocesslist").val(lastRow);

		if(lastRow1!=0){
			//re intialize
			var totvalver = $("#hdncount").val();

			for (let iX = 1; iX <= totvalver; iX++){

				var tbl1 = document.getElementById('MyTbl'+iX).getElementsByTagName('tr');
				var lastRow1 = tbl1.length-1;
				$("#rowcnt"+iX).val(lastRow1);


				$("#MyTbl"+iX+" > tbody > tr").each(function(index) {
					$tx = index+1;

					$(this).find('input[name="txtsortnum'+iX+'"]').val($tx);
					$(this).find('input[name="txtsortnum'+iX+'"]').attr('name','txtsortnum'+iX+$tx);
					$(this).find('input[type=hidden][name="txtitmcode'+iX+'"]').attr("name","txtitmcode"+iX+$tx);
					$(this).find('input[type=hidden][name="txtitmdesc'+iX+'"]').attr("name","txtitmdesc"+iX+$tx);
					$(this).find('input[type=hidden][name="txtcunit'+iX+'"]').attr("name","txtcunit"+iX+$tx);
					$(this).find('input[name="txtlvl'+iX+'"]').attr("name","txtlvl"+iX+$tx);
					$(this).find('select[name="selType'+iX+'"]').attr("name","selType"+iX+$tx);

					$(this).find('input[name="txtnqty'+iX+'"]').attr("name","txtnqty"+iX+$tx);

				});
			}
		
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
		$("#btn_New").attr("disabled", false);
		$("#btnaddversion").attr("disabled", false);
		$("#btnuploadexcel").attr("disabled", false); 
		$(".btnact").attr("disabled", false);
		$("#btnPrint").attr("disabled", false);
		$("#btnEdit").attr("disabled", false);
		$("#btndltemplate").attr("disabled", false);

		$('input[name="radversion"]').attr("disabled",false);

	}

	function enabled(){

		$("#frmBOM :input").attr("disabled", false);   

		$("#btnMain").attr("disabled", true); 
		$("#btn_New").attr("disabled", true);
		$("#btnaddversion").attr("disabled", true);
		$("#btnuploadexcel").attr("disabled", true);
		$(".btnact").attr("disabled", true); 
		$("#btnPrint").attr("disabled", true);
		$("#btnEdit").attr("disabled", true);
		$("#btndltemplate").attr("disabled", true);

		$('input[name="radversion"]').attr("disabled",true);

	}

	function printchk(x){

		$("#hdntransid").val(x);
		$("#frmQprint").attr("action","BOMPrint.php");

		$("#frmQprint").submit();

	}

</script>
