<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "JobOrders_new";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$_SESSION['myxtoken'] = gen_token();


	$arrallsec = array();
	$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc From locations A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");

	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrallsec[] = array('nid' => $row0['nid'], 'cdesc' => $row0['cdesc']);				
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?php echo time();?>">
  <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>

	<script src="../../Bootstrap/js/jquery-3.6.0.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">

	<form action="JO_newsave.php" name="frmpos" id="frmpos" method="post" enctype="multipart/form-data">
		<fieldset>
				<legend>Job Order</legend>
				
					<ul class="nav nav-tabs">
						<li class="active"><a href="#apv">JO Details</a></li>
						<li><a href="#attc">Attachments</a></li>
					</ul>

					<div class="tab-content" style="overflow: inherit !important">  

						<div id="apv" class="tab-pane fade in active" style="padding-left:5px; padding-top:10px; padding-right:5px; overflow: inherit !important">

							<table width="100%" border="0" cellspacing="0" cellpadding="2"  style="margin-bottom: 25px">
								<tr>
									<td><span style="padding:2px"><b>Customer:</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-4 nopadding ">
													<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm required" required placeholder="Supplier Code..." readonly>
											</div>
											<div class="col-xs-8 nopadwleft">
													<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" placeholder="Search Supplier Name..." required autocomplete="off" tabindex="4">
											</div>
										</div>
									</td>
									<td><span style="padding:2px" id="chkdate"><b>Target Date:</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<div class='col-xs-8 nopadding'>
													<input type='text' class="datepick form-control input-sm" placeholder="Pick a Date" name="txtTargetDate" id="txtTargetDate" value="<?php echo date("m/d/Y"); ?>" />
											</div>
										</div>
									</td>
								</tr>
							
								<tr>
									<td width="150"><span style="padding:2px" id="paymntdesc"><b>Reference SO</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-3 nopadding">
												<input type="text" class="form-control input-sm required" id="crefSO" name="crefSO" value="" placeholder="Reference SO No." readonly required>
											</div>
											<div class="col-xs-1 nopadwleft">
												<button type="button" class="btn btn-block btn-primary btn-sm" name="btnsearchSO" id="btnsearchSO"><i class="fa fa-search"></i></button>
											</div>		
											
											<div class="col-xs-8 nopadwright">
												<div class="form-check" style="padding-top: 3px; padding-left: 10px">
													<input class="form-check-input" type="checkbox" value="1" id="isWRef" name="isWRef"/>
													<label class="form-check-label" for="flexCheckChecked">No Reference</label>
												</div>
											</div>
										</div>

									</td>

									<td width="150"><span style="padding:2px"><b>Priority</b></span></td>
									<td>
										<div class="col-xs-12" style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-8 nopadding">
												<select id="selpriority" name="selpriority" class="form-control input-sm selectpicker">
													<option value="Low">Low</option>
													<option value="Normal" selected>Normal</option>
													<option value="High">High</option>
												</select>
											</div>
									</td>		
														
								</tr>

								<tr>									
									<td valign="top" style="padding-top:8px;"><span style="padding:2px;"><b>Remarks</b></span></td>
									<td>
										<div class="col-xs-12"  style="padding-left:2px; padding-bottom:2px">
											<textarea class="form-control input-sm" id="txtcremarks" name="txtcremarks" rows="3"></textarea>
										</div>
									</td>
									<td valign="top" style="padding-top:8px;"><span style="padding:2px"><b>Department:</b></span></td>
									<td valign="top">
										<div class="col-xs-12" style="padding-left:2px; padding-bottom:2px">
											<div class="col-xs-8 nopadding">
												<select id="seldept" name="seldept" class="form-control input-sm selectpicker">
													<?php
														foreach($arrallsec as $localocs){
													?>
														<option value="<?php echo $localocs['nid'];?>"><?php echo $localocs['cdesc'];?></option>										
													<?php	
														}						
													?>
												</select>
											</div>
									</td>											
								</tr>

							</table>

							<hr>
							<div class="col-xs-12 nopadwdown"><b>Item Details</b></div>

							<div class="col-xs-12 nopadwtop">
								<div class="col-xs-6 nopadwleft"><b>Item</b></div>
								<div class="col-xs-1 nopadwleft"><b>UOM</b></div>
								<div class="col-xs-1 nopadwleft"><b>JO Qty</b></div>
								<div class="col-xs-1 nopadwleft"><b>Working Hours</b></div>
								<div class="col-xs-1 nopadwleft"><b>Setup Time</b></div>
								<div class="col-xs-1 nopadwleft"><b>Cycle Time</b></div>
								<div class="col-xs-1 nopadwleft"><b>Total Time</b></div>
							</div>

							<div class="col-xs-12  nopadwtop">
								<div class="col-xs-6 nopadwleft"><input type="text" id="citemdesc" name="citemdesc" class="form-control input-sm required" required placeholder="Item Description..." readonly> <input type="hidden" id="citemno" name="citemno" value=""> <input type="hidden" id="nrefident" name="nrefident" value=""></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtcunit" name="txtcunit" class="form-control input-sm required" required placeholder="UOM..." readonly></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtjoqty" name="txtjoqty" class="form-control input-sm required text-right numeric" required placeholder="0.00"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtworkinghrs" name="txtworkinghrs" class="form-control input-sm required text-right numeric" required placeholder="0.00"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtsetuptime" name="txtsetuptime" class="form-control input-sm required text-right numeric" required placeholder="0.00"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtcycletime" name="txtcycletime" class="form-control input-sm required text-right numeric" required placeholder="0.00"></div>
								<div class="col-xs-1 nopadwleft"><input type="text" id="txtntotal" name="txtntotal" class="form-control input-sm required text-right numeric" required placeholder="0.00" readonly></div>
							</div>
							
						</div>	

						<div id="attc" class="tab-pane fade in" style="padding-left:5px; padding-top:10px;">

							<div class="col-xs-12 nopadwdown"><b>Attachments:</b></div>
							<div class="col-sm-12 nopadwdown"><i>Can attach a file according to the ff: file type: (jpg,png,gif,jpeg,pdf,txt,csv,xls,xlsx,doc,docx,ppt,pptx)</i></div> <br><br><br>
							<input type="file" name="upload[]" id="file-0" multiple />

						</div>
					</div>

					
							
						
					<br><br><br><br><br>
					<table width="100%" border="0" cellpadding="3">
						<tr>
							<td width="60%" rowspan="2"><input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">																
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='RFP.php';" id="btnMain" name="btnMain">
									Back to Main<br>(ESC)
								</button>																																		
								<button type="submit" class="btn btn-success btn-sm" tabindex="6">
									Generate JO<br> (CTRL+S)
								</button>														
							</td>
						</tr>									
					</table>


			</fieldset>

	</form>

			<!-- DETAILS ONLY -->
			<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title" id="InvListHdr">SO List</h3>
							</div>
									
							<div class="modal-body" style="height:40vh">

								<div class="col-xs-12 nopadding">
									<div class="form-group">
										<div class="col-xs-4 nopadding pre-scrollable" style="height:37vh">
											<table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
												<thead>
													<tr>
														<th>SO No</th>
														<th>Delivery Date</th>
													</tr>
												</thead>
												<tbody>
												</tbody>
											</table>
										</div>

										<div class="col-xs-8 nopadwleft pre-scrollable" style="height:37vh">
											<table name='MyInvDetList' id='MyInvDetList' class="table table-small">
												<thead>
													<tr>
														<th>Item No</th>
														<th>Description</th>
														<th>UOM</th>
														<th>Qty</th>
													</tr>
												</thead>
												<tbody>
																		
												</tbody>
											</table>
										</div>
									</div>
							</div>														
						</div>
						
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<!-- End Bootstrap modal -->

			<!-- 1) Alert Modal -->
				<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
					<div class="vertical-alignment-helper">
						<div class="modal-dialog vertical-align-top">
							<div class="modal-content">
								<div class="alert-modal-danger">
									<p id="AlertMsg"></p>
									<p><center>
										<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
									</center></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			<!-- End Alert modal -->

</body>
</html>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>

<script type="text/javascript">

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		  return chkform();
	  }
	  else if(e.keyCode == 27){ //ESC
		 e.preventDefault();
		 window.location.replace("JO.php");
	  }
	});

	$(document).ready(function() {

		$('#txtTargetDate').datetimepicker({
      format: 'MM/DD/YYYY',
    });

		$(".nav-tabs a").click(function(){
    	$(this).tab('show');
		});

		$("input.numeric").autoNumeric('init',{mDec:2}); 
		$("input.numeric").on("click", function () {
			$(this).select();
		});
									
		$("input.numeric").on("keyup", function () {
			computeTot();
		}); 

		//Search Cust name
		$('#txtcust').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "../../Sales/th_customer.php",
					dataType: "json",
					data: {
						query: $("#txtcust").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 					
							
				$('#txtcust').val(item.value).change(); 
				$("#txtcustid").val(item.id);
			
			}
		
		});

		$('#btnsearchSO').on("click", function(){
			getSO();
		});

		$('#isWRef').change(function() {

			if(this.checked) {
				$('#btnsearchSO').attr("disabled", true); 
				$('#citemdesc').attr("readonly", false);
			}else{
				$('#btnsearchSO').attr("disabled", false);
				$('#citemdesc').attr("readonly", true);
			}

		});

		$('#citemdesc').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
					dataType: "json",
					data: { query: $("#citemdesc").val() },
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.id+": "+item.desc+'</span</div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 					
							
				$('#citemdesc').val(item.desc).change(); 
				
				$("#citemdesc").val(item.desc); 
				$("#citemno").val(item.id);
				$("#txtcunit").val(item.cunit);
				$("#nrefident").val(item.nident);
				$("#txtjoqty").val(item.nqty);
				$("#txtworkinghrs").val(item.nworkhrs);
				$("#txtsetuptime").val(item.nsetuptime);
				$("#txtcycletime").val(item.ncycletime);

				computeTot();
				
				
			}
		
		});

		$("#file-0").fileinput({
			showUpload: false,
			showClose: false,
			allowedFileExtensions: ['jpg', 'png', 'gif', 'jpeg', 'pdf', 'txt', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx'],
			overwriteInitial: false,
			maxFileSize:100000,
			maxFileCount: 5,
			browseOnZoneClick: true,
			fileActionSettings: { showUpload: false, showDrag: false,}
		});

	});

	function getSO(){
		$xcus = $('#txtcustid').val();

		if($xcus == ""){
			alert("Please pick a valid customer!");
		}
		else{

			//clear table body if may laman
			$('#MyInvTbl tbody').empty(); 
			$('#MyInvDetList tbody').empty();

			xstat = "YES";

			$.ajax({
        url: 'th_solist.php',
				data: 'x='+$xcus,
        dataType: 'json',
        method: 'post',
        success: function (data) {
					   
          console.log(data);
          $.each(data,function(index,item){
								
						if(item.cpono=="NONE"){
							$("#AlertMsg").html("No Sales Order Available");
							$("#alertbtnOK").show();
							$("#AlertModal").modal('show');

							xstat = "NO";

						}
						else{
							$("<tr>").append(
								$("<td id='td"+item.csono+"'>").text(item.csono),
								$("<td>").text(item.dcutdate)
							).appendTo("#MyInvTbl tbody");
														
							$("#td"+item.csono).on("click", function(){
								opengetdet($(this).text());
							});
							
							$("#td"+item.csono).on("mouseover", function(){
								$(this).css('cursor','pointer');
							});
					  }

          });
					   
					if(xstat=="YES"){
						$("#mySIRef").modal("show");
					}

        },
        error: function (req, status, err) {
						console.log('Something went wrong', status, err);
						$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');
				}
      });

		}

		
	}

	function opengetdet(valz){
		var drno = valz;

		$("#txtrefSI").val(drno);

		$('#InvListHdr').html("SO List: " + $('#txtcust').val() + " | SO Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
		
		$('#MyInvDetList tbody').empty();
			
		$('#loadimg').show();
		
			var salesnos = "";
			var cnt = 0;

			$.ajax({
        url: 'th_solistdet.php',
				data: 'x='+drno,
        dataType: 'json',
        method: 'post',
        success: function (data) {
					   
          console.log(data);
					$.each(data,function(index,item){
											  
						if (item.nqty>=1){
							$("<tr>").append(
								$("<td>").html("<a href='javascript:;' onclick='savedet(this);' data-itemno='"+item.citemno+"' data-desc='"+item.cdesc+"' data-ident='"+item.nident+"' data-unit='"+item.cunit+"' data-qty='"+item.nqty+"' data-workhrs='"+item.nworkhrs+"' data-setup='"+item.nsetuptime+"' data-cycle='"+item.ncycletime+"' data-csono='"+drno+"'>"+item.citemno+"</a>"),
								$("<td>").text(item.cdesc),
								$("<td>").text(item.cunit),
								$("<td>").text(item.nqty)
							).appendTo("#MyInvDetList tbody");
					 	}
					 });
        },
				complete: function(){
					$('#loadimg').hide();
				},
        error: function (req, status, err) {
					console.log('Something went wrong', status, err);
 					$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
					$("#alertbtnOK").show();
					$("#AlertModal").modal('show');
         }
      });
	}

	function savedet(xz){

		$("#crefSO").val(xz.dataset.csono);
		$("#citemdesc").val(xz.dataset.desc); 
		$("#citemno").val(xz.dataset.itemno);
		$("#txtcunit").val(xz.dataset.unit);
		$("#nrefident").val(xz.dataset.ident);
		$("#txtjoqty").val(xz.dataset.qty);
		$("#txtworkinghrs").val(xz.dataset.workhrs);
		$("#txtsetuptime").val(xz.dataset.setup);
		$("#txtcycletime").val(xz.dataset.cycle);

		computeTot();

		$("#mySIRef").modal("hide");

	}	

	function computeTot(){

		$nqty = parseFloat($("#txtjoqty").val().replace(/,/g,''));
		$ncycle = parseFloat($("#txtcycletime").val().replace(/,/g,''));
		$nsetup = parseFloat($("#txtsetuptime").val().replace(/,/g,''));

		$xtot = ($nqty*$ncycle) + $nsetup;
		$("#txtntotal").val($xtot);

		//$("#txtjoqty").autoNumeric('destroy');
		//$("#txtjoqty").autoNumeric('init',{mDec:2});
		//$("#txtworkinghrs").autoNumeric('destroy');
		//$("#txtworkinghrs").autoNumeric('init',{mDec:2});
		//$("#txtsetuptime").autoNumeric('destroy');
		//$("#txtsetuptime").autoNumeric('init',{mDec:2});
		//$("#txtcycletime").autoNumeric('destroy');
		//$("#txtcycletime").autoNumeric('init',{mDec:2});
		$("#txtntotal").autoNumeric('destroy');
		$("#txtntotal").autoNumeric('init',{mDec:2});

	}

</script>
