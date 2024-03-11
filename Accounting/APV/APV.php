<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "APV.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');


	$company = $_SESSION['companyid'];

	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'APV_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'APV_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}


	$unpoststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'APV_unpost.php'");
	if(mysqli_num_rows($sql) == 0){
		$unpoststat = "False";
	}

	$accts = array();
	$sql = mysqli_query($con,"select * from accounts where compcode = '$company' and cstatus = 'ACTIVE' and ctype='Details'");
    while($row = $sql -> fetch_assoc()){
        $accts[] = $row;
    }

	@$arrtaxlist = array();
	$gettaxcd = mysqli_query($con,"SELECT * FROM `vatcode` where compcode='$company' and ctype = 'Purchase' and cstatus='ACTIVE' order By cvatdesc"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrtaxlist[] = array('ctaxcode' => $row['cvatcode'], 'ctaxdesc' => $row['cvatdesc'], 'nrate' => number_format($row['nrate'])); 
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?=time()?>"> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">  

	<link rel="stylesheet" type="text/css" href="../../global/plugins/bootstrap-fileinput/bootstrap-fileinput.css?x=<?=time()?>"/>

	<link rel="stylesheet" type="text/css" href="../../global/plugins/bootstrap-select/bootstrap-select.min.css"/>
	<link rel="stylesheet" type="text/css" href="../../global/plugins/select2/select2.css"/>

	<link href="../../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="../../global/css/plugins.css" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

	<script type="text/javascript" src="../../global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
	<script type="text/javascript" src="../../global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
	<script type="text/javascript" src="../../global/plugins/select2/select2.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>

</head>

<body style="padding:5px">
	<section>
        <div class="row nopadding">
        	<div class="col-xs-6 nopadding" style="float:left; width:50%">
				<font size="+2"><u>AP Voucher</u></font>	
          </div>
        </div>
			
		<div class="row nopadwdown">
			<div class="col-xs-4 nopadding">
				<button type="button" class="btn btn-primary btn-sm"  onClick="location.href='APV_new.php'" id="btnNew" name="btnNew"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

				<?php
					if($unpoststat=="True"){
				?>
					<button type="button" class="btn btn-danger btn-sm" onClick="location.href='APV_void.php'"><span class="fa fa-times"></span>&nbsp;Void Transaction</button>
				<?php
					}
				?>

				<button type="button" class="btn btn-warning btn-sm" id="btnUpload" name="btnUpload" title="Upload Liquidations"><span class="fa fa-upload"></span></button>

			</div>
			<div class="col-xs-3 nopadwtop text-right" style="height:30px !important; padding-right: 10px !important">
				<b> Search Supplier / AP No / Ref No.: </b>
			</div>
			<div class="col-xs-3 text-right nopadding">
				<input type="text" name="searchByName" id="searchByName" value="<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : ""?>" class="form-control input-sm" placeholder="Search Supplier, AP No, Reference...">
			</div>
			<div class="col-xs-2 text-right nopadwleft">
				<select  class="form-control input-sm" name="selstats" id="selstats">
					<option value=""> All Transactions</option>
					<option value="post"> Posted </option>
					<option value="cancel"> Cancelled </option>
					<option value="void"> Voided </option>
					<option value="pending"> Pending </option>
				</select>
			</div>
		</div>

        
		<table id="example" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>AP No</th>
					<th>Reference</th>
					<th>Paid To</th>
					<th>AP Type</th>        
					<th>Gross</th>
					<th>AP Date</th>
					<th>Status</th>
				</tr>
			</thead>
		</table>

	</section>
	  
	<form name="frmedit" id="frmedit" method="post" action="APV_edit.php">
		<input type="hidden" name="txtctranno" id="txtctranno" />
		<input type="hidden" name="hdnsrchval" id="hdnsrchval" />
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
							<button type="button" class="btnmodz btn btn-primary btn-sm" id="OK">Ok</button>
							<button type="button" class="btnmodz btn btn-danger btn-sm" id="Cancel">Cancel</button>
							
							
							<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
							
							<input type="hidden" id="typ" name="typ" value = "">
							<input type="hidden" id="aptyp" name="aptyp" value = "">
							<input type="hidden" id="modzx" name="modzx" value = "">
						</center>
					</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal PICK VERSIONS -->
	<div class="modal fade" id="myAPVUp" data-keyboard="false" data-backdrop="static" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form action="CheckAPV.php" method="POST" enctype="multipart/form-data">

				<div class="modal-header">
					<h5 class="modal-title" id="myModalLabel"><b>Upload APV Others</b></h5>        
				</div>

				<div class="modal-body" style="height: 25vh;">

					

						<div class='row nopadwtop'>
							<div class="col-md-12 nopadwtop" style="text-align: center">
								<a href="templates/APV_Others.xlsx" download="APV_Others.xlsx" class="btn btn-info btn-sm" id="download" >Download Template</a>
								<br><br>
							</div>
						</div>
						<div class='row nopadwtop'>
							<div class="col-md-4 nopadwtop"> Select a Credit Account </div>
							<div class="col-md-8 nopadwleft">
								<select name="accts" id="accts" class='form-control input-sm select2'>
									<option value="0">From Supplier's Entry</option>
									<?php foreach($accts as $list): ?>
										<option value="<?= $list['cacctid'] ?>"><?= $list['cacctdesc'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>	
						
						<div class="row nopadwtop">
							<div class="col-xs-4 nopadwtop"> Select Excel File </div>
							<div class="col-xs-8 nopadwleft">
								<div class="form-group">                           
									<div class="col-xs-12 nopadding">
										<div class="fileinput fileinput-new" data-provides="fileinput">
											<div class="input-group input-medium">
												
												<span class="input-group-addon btn btn-success default btn-file">
													<span class="fileinput-new">Browse file </span>
													<span class="fileinput-exists">Change </span>
													<input type="file" type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls" required> 
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
							</div>	
						</div>
					

				</div>
				<div class="modal-footer">
						<input type="submit" id='submit' value="Submit" class='btn btn-success btn-sm' >
						<input type="button" id='cancel' value="Cancel" data-dismiss="modal" class='btn btn-danger btn-sm' >
						
				</div>

				</form>	
			</div>
		</div>
	</div>
	<!-- Modal -->
	
</body>
</html>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

	<script type="text/javascript">
		$(document).keydown(function(e) {	 
		if(e.keyCode == 112) { //F1
			e.preventDefault();
				window.location = "APV_new.php";
		}
		});

		$(document).ready(function() {
			
			fill_datatable("<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : "";?>");	

			$("#searchByName").keyup(function(){
				var searchByName = $('#searchByName').val();
				var searchBystat = $('#selstats').val(); 

				$('#example').DataTable().destroy();
				fill_datatable(searchByName, searchBystat);
			});

			$("#selstats").change(function(){
				var searchByName = $('#searchByName').val(); 
				var searchBystat = $('#selstats').val(); 

				$('#example').DataTable().destroy();
				fill_datatable(searchByName, searchBystat);
			});

			var x = "";
			var num = "";
			
			$(".btnmodz").on("click", function (){
				if($('#AlertModal').hasClass('in')==true){
					var idz = $(this).attr('id');
					
					if(idz=="OK"){
						var x = $("#typ").val();
						var num = $("#modzx").val();
						var apx = $("#aptyp").val();
						
						if(x=="POST"){
							var msg = "POSTED";
							
							//generate Account Entries
							if(apx=="Purchases" || apx=="PurchAdv"){
								$.ajax ({
									url: "th_acctentry.php",
									data: { tran: num },
									async: false,
									dataType: "text",
									success: function( data ) {
										//alert(data);
									}
								});
							}
							
							
							
						}
						else if(x=="CANCEL"){
							var msg = "CANCELLED";
						}


							$.ajax ({
								url: "APV_Tran.php",
								data: { x: num, typ: x },
								async: false,
								dataType: "json",
								beforeSend: function(){
									$("#AlertMsg").html("&nbsp;&nbsp;<b>Processing " + num + ": </b> Please wait a moment...");
									$("#alertbtnOK").hide();
									$("#OK").hide();
									$("#Cancel").hide();
									$("#AlertModal").modal('show');
								},
								success: function( data ) {
									console.log(data);
									$.each(data,function(index,item){
										
										itmstat = item.stat;
										
										if(itmstat!="False"){
											$("#msg"+num).html(item.stat);
											
												$("#AlertMsg").html("");
												
												$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+msg+"...");
												$("#alertbtnOK").show();
												$("#OK").hide();
												$("#Cancel").hide();
												$("#AlertModal").modal('show');
						
										}
										else{
											$("#AlertMsg").html("");
											
											$("#AlertMsg").html(item.ms);
											$("#alertbtnOK").show();
											$("#OK").hide();
											$("#Cancel").hide();
											$("#AlertModal").modal('show');
						
										}
									});
								}
							});


					}
					else if(idz=="Cancel"){
						
						$("#AlertMsg").html("");
						$("#AlertModal").modal('hide');
						
					}




				}
			});

			$("#btnUpload").on("click", function(){
				$("#myAPVUp").modal("show");
			});

			$("#accts").select2({
				placeholder: "Select Account Code...",
				allowClear: true
			});
				
		});

		function editfrm(x){
			$('#txtctranno').val(x); 
			$('#hdnsrchval').val($('#searchByName').val()); 
			document.getElementById("frmedit").submit();
		}

		function trans(x,num,aptyp){
			
			$("#typ").val(x);
			$("#aptyp").val(aptyp);
			$("#modzx").val(num);

			$("#AlertMsg").html("");
									
			$("#AlertMsg").html("Are you sure you want to "+x+" APV No.: "+num);
			$("#alertbtnOK").hide();
			$("#OK").show();
			$("#Cancel").show();
			$("#AlertModal").modal('show');

		}

		function fill_datatable(searchByName = '', searchBystat = '')
		{
			var dataTable = $('#example').DataTable({
					stateSave: true,
				"processing" : true,
				"serverSide" : true,
				"lengthChange": true,
				"order" : [],
				"searching" : false,
				"ajax" : {
						url:"th_datatable.php",
						type:"POST",
						data:{
							searchByName: searchByName, searchBystat: searchBystat
						}
				},
				"columns": [
						{ "data": null,
							"render": function (data, type, full, row) {
								var sts = "";
								if (full[8] == 1 || full[9] == 1) {
									sts="class='text-danger'";
								}
								return "<a "+sts+" href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"');\">"+full[0]+"</a>";							
							}
								
						},
						{ "data": 1 },
						{ "data": null,
							"render": function (data, type, full, row) {
								return full[2]+" - "+full[3];								
							}							
						},
						{ "data": 4 },
						{ "data": 5 },
						{ "data": 6 },
						{ "data": null,		
									"render": function (data, type, full, row) {

										if (full[7] == 1) {
											if(full[9] == 1){
												return '<b>Voided</b>';
											}else{										
												return 'Posted';
											}
											
										}
											
										else if (full[8] == 1) {
											
											return '<b>Cancelled</b>';
											
										}
											
										else{

											return 	"<div id=\"msg"+full[0]+"\"> <a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"','"+full[4]+"')\" class=\"btn btn-xs btn-default<?=($poststat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-up\" style=\"font-size:20px;color:Green ;\" title=\"Approve transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('CANCEL','"+full[0]+"','"+full[4]+"')\" class=\"btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></a> </div>";

										}
									}
						},
			
			],
					"columnDefs": [
						{
							"targets": 4,
							"className": "text-right"
						},
						{
							"targets": [5,6],
							"className": "text-center dt-body-nowrap"
						}
					],
					"createdRow": function( row, data, dataIndex ) {
							// Set the data-status attribute, and add a class
							if(data[8]==1  || data[9] == 1){
								$(row).addClass('text-danger');
							}
							
					}
			});
		}

	</script>