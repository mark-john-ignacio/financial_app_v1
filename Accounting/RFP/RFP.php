<?php

	if(!isset($_SESSION)){
	session_start();
	}
	$_SESSION['pageid'] = "RFP.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

	//get users, post cancel and send access
	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'RFP_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'RFP_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}

	$unpststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'RFP_unpost'");
	if(mysqli_num_rows($sql) == 0){
		$unpststat = "False";
	}

	$chkapprovals = array();
	$sqlappx = mysqli_query($con,"Select * from rfp_trans_approvals where compcode='$company' and lapproved=0 and lreject=0 and userid = '$employeeid' Group BY crfpno HAVING nlevel = MIN(nlevel) Order By crfpno, nlevel");
	if (mysqli_num_rows($sqlappx)!=0) {
		while($rows = mysqli_fetch_array($sqlappx, MYSQLI_ASSOC)){
			@$chkapprovals[] = $rows['crfpno']; 
		}
	}


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
	<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>"/>
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?=time()?>"> 
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
    
       
  <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
  <script src="../../js/bootstrap3-typeahead.min.js"></script>
    
  <script src="../../Bootstrap/js/bootstrap.js"></script>

</head>

<body style="padding:5px; height:900px">
	<div>
		<section>
    	<div>
        <div style="float:left; width:50%">
					<font size="+2"><u>Request For Payment</u></font>	
        </div>
      </div>
			
				<div class="col-xs-12 nopadding">
					<div class="col-xs-4 nopadding">
						<button type="button" class="btn btn-primary btn-sm"  onClick="location.href='RFP_new.php'" id="btnNew" name="btnNew"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

						<?php
							if($unpststat=="True"){
						?>
							<button type="button" class="btn btn-danger btn-sm" onClick="location.href='RFP_void.php'"><span class="fa fa-times"></span>&nbsp;Void Transaction</button>
						<?php
							}
						?>
					</div>
					<div class="col-xs-3 nopadwtop text-right" style="height:30px !important; padding-right: 10px !important">
						<b> Search Supplier / Trans. No / Ref No.: </b>
					</div>
					<div class="col-xs-3 text-right nopadding">
						<input type="text" name="searchByName" id="searchByName" value="<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : ""?>" class="form-control input-sm" placeholder="Search Supplier, Trans No, Reference...">
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

      <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th class="text-center">Trans No</th>
						<th class="text-center">Reference</th>
            <th class="text-center">Paid To</th>
						<th class="text-center">Amount</th>
						<th class="text-center">Date</th>
						<th class="text-center">Status</th>
						<th class="text-center">Actions</th>
					</tr>
				</thead>
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="RFP_edit.php">
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
                        <button type="button" class="btn btn-primary btn-sm" id="OK" onclick="trans_send('OK')">Ok</button>
                        <button type="button" class="btn btn-danger btn-sm" id="Cancel" onclick="trans_send('Cancel')">Cancel</button>
                        
                        
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                        
                        <input type="hidden" id="typ" name="typ" value = "">
                        <input type="hidden" id="modzx" name="modzx" value = "">
                    </center>
                </p>
               </div> 
            </div>
        </div>
    </div>
</div>

<!-- 1) TRACKER Modal -->
<div class="modal fade" id="TrackMod" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="InvListHdr">RFP Approval Status</h3>
      </div>
            
      <div class="modal-body pre-scrollable" id="divtracker" style="height: 45vh">
				
			</div>

		</div>
	</div>
</div>


<?php
mysqli_close($con);

?>
</body>
</html>


<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
<script>
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

	});

	$(document).keydown(function(e) {	 
		if(e.keyCode == 112) { //F2
			e.preventDefault();
			window.location = "RFP_new.php";
		}
	});


	function editfrm(x){
		$('#txtctranno').val(x); 
		$('#hdnsrchval').val($('#searchByName').val()); 
		document.getElementById("frmedit").submit();
	}

		function trans(x,num){
			
			$("#typ").val(x);
			$("#modzx").val(num);


				$("#AlertMsg").html("");

				if(x=="CANCEL1"){
					x = "CANCEL";
				}
									
				$("#AlertMsg").html("Are you sure you want to "+x+" Payment No.: "+num);
				$("#alertbtnOK").hide();
				$("#OK").show();
				$("#Cancel").show();
				$("#AlertModal").modal('show');
			

		}

	function track(xno){

		$.ajax({
			type: "POST",
			url: "th_getapprovers.php",
			data: 'x='+xno,
			//contentType: "application/json; charset=utf-8",
			success: function(result) {
					$("#divtracker").html(result);
			}
		});

		$("#InvListHdr").text("RFP Approval Status: "+xno);


		$("#TrackMod").modal("show");
	}

	function trans_send(idz){

		var itmstat = "";
		var x = "";
		var num = "";
		var msg = "";

		if(idz=="OK"){
			var x = $("#typ").val();
			var num = $("#modzx").val();
			
			if(x=="POST"){
				var msg = "POSTED";
			}
			else if(x=="CANCEL" || x=="CANCEL1"){
				var msg = "CANCELLED";
			}
			else if(x=="SEND"){
				var msg = "SENT";
			}

				$.ajax ({
					url: "RFP_Tran.php",
					data: { x: num, typ: x },
					dataType: "json",
					beforeSend: function() {
						$("#AlertMsg").html("&nbsp;&nbsp;<b>Processing " + num + ": </b> Please wait a moment...");
						$("#alertbtnOK").css("display", "none");
						$("#OK").css("display", "none");
						$("#Cancel").css("display", "none");
					},
					success: function( data ) {
						console.log(data);
						setmsg(data,num);
					}
				});
			

		}
		else if(idz=="Cancel"){
			
			$("#AlertMsg").html("");
			$("#AlertModal").modal('hide');
			
		}

	}

	function setmsg(data,num){
		$.each(data,function(key,value){
													
			if(value.stat!="False"){
				$("#msg"+num).html(value.stat);
																
				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+value.stat+"...");
				$("#alertbtnOK").show();
				$("#OK").hide();
				$("#Cancel").hide();
				$("#AlertModal").modal('show');
			
			}
			else{
								
				$("#AlertMsg").html("");
								
				$("#AlertMsg").html(value.ms);
				$("#alertbtnOK").show();
				$("#OK").hide();
				$("#Cancel").hide();
				$("#AlertModal").modal('show');
											
			}
		});
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
							if (full[8] == 1 || full[10] == 1) {
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
					{ "data": 5 },
					{ "data": 6 },
					{ "data": null,
							"render": function (data, type, full, row) {
	
								if(full[9] == 0 && full[8]==0){
									return "For Sending";
								}else{
									if (full[7] == 0 && (full[8] == 0)) {
										return "For Approval";
									}else{
										if (full[7] == 1) {		
											if(full[10] == 1){
												return '<b>Voided</b>';
											}else{
												return 'Posted';
											}			
																					
										}else if (full[8] == 1) { //12 sent 13 void 4 apprve 5 cancel
											return '<b>Cancelled</b>';
										}else{
											return 'Pending';
										}
									}
								}
								
							}
						},
						{ "data": null,		
								"render": function (data, type, full, row) {

									$msgx = "";
									if(full[9] == 0 && full[8]==0){

										$msgx = "<a href=\"javascript:;\" onClick=\"trans('SEND','"+full[0]+"')\" class=\"btn btn-xs btn-default\"> <i class=\"fa fa-share\" style=\"font-size:20px;color: #ffb533;\" title=\"Send transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('CANCEL1','"+full[0]+"')\" class=\"btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></a>";

									}else{

										if(full[7] == 0 && full[8]==0){

											var chkrejstat1 = "disabled";
											var chkrejstat2 = "disabled";
											var xcz = '<?=json_encode(@$chkapprovals)?>';
											if(xcz!=""){
												$.each( JSON.parse(xcz), function( key, val ) {
													if(val==full[0]){
														chkrejstat1 = "";
														chkrejstat2 = "";
													}
													//console.log(key,val);
												});
											}

											if(chkrejstat1==""){
												chkrejstat1 = "<?=($poststat!="True") ? " disabled" : ""?>";
											}

											if(chkrejstat2==""){
												chkrejstat2 = "<?=($cancstat!="True") ? " disabled" : ""?>";
											}
											
											mgsx = "<button type=\"button\" onClick=\"trans('POST','"+full[0]+"')\" class=\"btn btn-xs btn-default\" "+chkrejstat1+"><i class=\"fa fa-thumbs-up\" style=\"font-size:20px;color:Green ;\" title=\"Approve transaction\"></i></button> <button type=\"button\" onClick=\"trans('CANCEL','"+full[0]+"')\" class=\"btn btn-xs btn-default\" "+chkrejstat2+"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></button>";
											
										}

									}

									if(full[9] == 1 && full[10]==0) {
										return "<div id=\"msg"+full[0]+"\"> "+ $msgx +" <button type=\"button\" onClick=\"track('"+full[0]+"')\" class=\"btn btn-xs btn-default\"> <i class=\"fa fa-file-text-o\" style=\"font-size:20px;color: #3374ff;\" title=\"Track transaction\"></i></button> </div>"
									}else{
										return "<div id=\"msg"+full[0]+"\"> "+ $msgx +" </div>";
									}

								}
						},
		
        ],
				"columnDefs": [
					{
						"targets": 3,
						"className": "text-right"
					},
					{
						"targets": [4,5,6],
						"className": "text-center dt-body-nowrap"
					}
				],
				"createdRow": function( row, data, dataIndex ) {
						// Set the data-status attribute, and add a class
						if(data[8]==1  || data[10] == 1){
							$(row).addClass('text-danger');
						}
						
				}
		  });
	}
</script>
