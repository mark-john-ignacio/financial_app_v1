<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Currency.php";

	include('../../Connection/connection_string.php');
	include('../../include/accessinner.php');

	$company = $_SESSION['companyid'];
				
	$sql = "select A.cvalue, B.symbol, B.unit, B.country, B.id from parameters A left join currency_rate B on A.compcode=B.compcode and A.cvalue=B.symbol where A.compcode='$company' and A.ccode='DEF_CURRENCY'";
				
	$result=mysqli_query($con,$sql);
	$resdefcurr = mysqli_fetch_all($result, MYSQLI_ASSOC);
						
?>
	<!DOCTYPE html>
	<html>
	<head>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css"> 
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../include/autoNumeric.js"></script>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>MYX Financials</title>

</head>

<body style="padding:5px">

	<div>
		<section>
      <div>
        <div style="float:left; width:50%">
					<font size="+2"><u>Currency Exchange Rates</u></font>	
        </div>    
				        
      </div>

			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-5 nopadding">
					<button type="button" data-toggle="modal" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"><i class="fa fa-file-text-o" aria-hidden="true"></i> &nbsp; Create New </button>
					<a href="currency_xls.php" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> &nbsp; Export To Excel</a>
				</div>

				<div class="col-xs-1 nopadwtop" style="height:30px !important;">
					<b> Search: </b>
				</div>
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="" class="form-control input-sm" placeholder="Country, Symbol, Unit, Rate">
				</div>

				<div class="col-xs-3 text-right nopadwleft">
					<select id="seltypesearch" name="seltypesearch" class="form-control input-sm selectpicker"  tabindex="4">
						<option value="">ALL</option>
						<option value="ACTIVE">ACTIVE</option>
						<option value="INACTIVE">INACTIVE</option>								   
					</select>
				</div>

			</div>
            
      <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th style="text-align: center">Code</th>
						<th width="60%">Country</th>
						<th>Name</th>						
						<th style="text-align: right">Rate</th>
            <th style="text-align: center">Status</th>
					</tr>
				</thead>				
			</table>

		</section>
	</div>		


	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="myModalLabel"><b>Add New Currency</b></h5>        
				</div>

				<div class="modal-body" style="height: 25vh">   
					<div class="row nopadding">
						<div class="col-xs-2 nopadding" style="padding-top: 4px !important; padding-bottom: 2px !important">
							<b>Currency Code</b>
						</div>
								
						<div class="col-xs-3 nopadding">
							<input type="hidden" id="txtid" name="txtid" required>
							<input type="text" class="form-control input-xs" id="txtsymbol" name="txtsymbol" required>
						</div>
						
					</div>   

					<div class="row nopadwtop">
						<div class="col-xs-2 nopadding" style="padding-top: 4px !important; padding-bottom: 2px !important">
							<b>Country Name</b>
						</div>
							
						<div class="col-xs-9 nopadding">
							<input type="text" class="form-control input-xs" id="txtcountry" name="txtcountry" required>
						</div>

						
					</div>  
					
					<div class="row nopadwtop">
						<div class="col-xs-2 nopadding" style="padding-top: 4px !important; padding-bottom: 2px !important">
							<b>Currency Name</b>
						</div>
								
						<div class="col-xs-3 nopadwright">
							<input type="text" class="form-control input-xs" id="txtunit" name="txtunit" required>
						</div>

						<div class="col-xs-2 nopadwleft2x text-right" style="padding-top: 4px !important; padding-bottom: 2px !important">
							<b>Rate</b>
						</div>
								
						<div class="col-xs-4 nopadding">
							<input type="text" class="form-control input-xs" id="txtrate" name="txtrate" required value="0">
						</div>

					</div>
					
					<div class="row nopadwtop2x"><div class="col-xs-12 nopadding"><div class="alert alert-danger" id="add_err"></div></div></div>       

				</div>
			
				<div class="modal-footer">
					<button type="button" id="btnSave" name="Save" class="btn btn-primary btn-sm">Add Detail</button>
					<button type="button" id="btnUpdate" name="Update" class="btn btn-success btn-sm">Update Detail</button>
					<button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Cancel</button>
				</div>
			
			</div>
		</div>
	</div>
	<!-- Modal -->		

	<!-- 1) Alert Modal -->
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
      <div class="modal-dialog vertical-align-center">
        <div class="modal-content">
          <div class="alert-modal-danger">
            <p id="AlertMsg"></p>
						<p>
							<center>
								<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Ok</button>
							</center>
          	</p>
          </div>
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
		
		$("#txtrate").autoNumeric('init',{mDec:4});
		$("#txtrate").on("click", function () {
			$(this).select();
		});
		$("#add_err").hide();

		fill_datatable("", "");

			$("#searchByName").keyup(function(){
				var searchByName = $('#searchByName').val();
				var searchBystat = $('#selstats').val();

				$('#example').DataTable().destroy();
				fill_datatable(searchByName,searchBystat);

			});

			$("#selstats").change(function(){
				var searchByName = $('#searchByName').val(); 
				var searchBystat = $('#selstats').val(); 

				$('#example').DataTable().destroy();
				fill_datatable(searchByName,searchBystat);

			});

		// Adding new user
		$("#btnadd").on("click", function() {
		 var x = chkAccess('Currency_New.php');
		 
		 if(x.trim()=="True"){
			$("#btnSave").show();
			$("#btnUpdate").hide();

			$("#txtsymbol").attr('readonly',false);
			
			$("#txtid").val("new");
			$("#txtsymbol").val("");
			$("#txtcountry").val(""); 
			$("#txtunit").val("");
			
			$("#txtrate").autoNumeric('destroy');
			$("#txtrate").val(1);
			$("#txtrate").autoNumeric('init',{mDec:4});	

			$('#myModalLabel').html("<b>Add New Currency</b>");
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			 $("#AlertModal").modal('show');

		 }
		});
		
		$("#txtsymbol").on("keyup", function() {

			var valz = $(this).val();
			
			$.ajax ({
				url: "th_chkuomcode.php",
				data: { code: valz },
				async: false,
				success: function( data ) {
					if(data.trim()!="False"){
						$("#add_err").html("<b>ERROR: </b>"+data);
						$("#add_err").show();
					}
					else{
						
						$("#add_err").html("");
						$("#add_err").hide();
						
					}
				}
			
			});
		});

		$("#txtsymbol").on("blur", function() {

			var valz = $(this).val();
			
			$.ajax ({
				url: "th_chkuomcode.php",
				data: { code: valz },
				async: false,
				success: function( data ) {
					if(data.trim()!="False"){
						$("#txtsymbol").val("").change();
						$("#txtsymbol").focus();
					}
					else{						
						$("#add_err").html("");
						$("#add_err").hide();
					}
				}
			
			});
		});
		
		$("#btnSave, #btnUpdate").on("click", function() {
			var xid = $("#txtid").val();
			var xsymbol = $("#txtsymbol").val();
			var xcountry = $("#txtcountry").val(); 
			var xunit = $("#txtunit").val();			
			var xrate = $("#txtrate").val();

			if(xsymbol=="" || xcountry==""  || xunit==""  || xrate==""){
				$("#add_err").html("<center><b>Complete the needed data!</b></center>");
			 	$("#add_err").show();
			}else{
				if(xrate <=0 ){
					$("#add_err").html("<center><b>Minimum rate value is 1</b></center>");
			 		$("#add_err").show();
				}else{
					$.ajax ({
						url: "th_save.php",
						data: { id: xid, symbol: xsymbol, country: xcountry, unit: xunit, rate: xrate },
						async: false,
						success: function( data ) {
							if(data.trim()=="True"){
								
								$('#myModal').modal('hide');
								location.reload();
						
							}
							else {
								$("#add_err").html("<b>ERROR: </b>"+data);
								$("#add_err").show();
							}
						}			
					});
				}
			}
			

		})
		
	});

	function editgrp(id,unit,symbol,cntry,nrate){
		 var x = chkAccess('Currency_Edit.php');
		 
		 if(x.trim()=="True"){
			$("#btnSave").hide();
			$("#btnUpdate").show();
			
			$("#txtsymbol").attr('readonly',true);
			
			$("#txtid").val(id);
			$("#txtsymbol").val(symbol);
			$("#txtcountry").val(cntry); 
			$("#txtunit").val(unit);	
			
			$("#txtrate").autoNumeric('destroy');
			$("#txtrate").val(nrate);	
			$("#txtrate").autoNumeric('init',{mDec:4});
			
			$('#myModalLabel').html("<b>Update Currency Detail</b>");
			$('#myModal').modal('show');
		 } else {
			 $("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			 $("#AlertModal").modal('show');

		 }
	}
	
	function setStat(code, stat){
		$.ajax ({
			url: "th_itmsetstat.php",
			data: { code: code,  stat: stat, typz: 'ITEMTYP' },
			async: false,
			success: function( data ) {
				if(data.trim()!="True"){
					$("#itm"+code).html("<b>Error: </b>"+ data);
					$("#itm"+code).attr("class", "itmalert alert alert-danger nopadding")
					$("#itm"+code).show();
				}
				else{
					if(stat=="ACTIVE"){
						$("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
					}else{
						$("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
					}
						
					$("#itm"+code).html("<b>SUCCESS: </b> Status changed to "+stat);
					$("#itm"+code).attr("class", "itmalert alert alert-success nopadding")
					$("#itm"+code).show();
				}
			}		
		});
	}

	function chkAccess(id){
		var result;
			
		$.ajax ({
			url: "chkAccess.php",
			data: { id: id },
			async: false,
			success: function( data ) {
				result = data;
			}
		});
			
		return result;
	}
	
	function fill_datatable(searchByName = '',  searchBystat = ''){
			var Datatable = $('#example').DataTable({
				stateSave: false,
		    "processing" : true,
		    "serverSide" : true,
		    "lengthChange": true,
		    "order" : [],
		    "searching" : false,
		    "ajax" : {
					url:"th_datatable.php",
					type:"POST",
					data:{ searchByName: searchByName, searchBystat: searchBystat }
		    },
				"columns": [
					{ "data": null,
						"render": function (data, type, full, row) {

								if (full[1] == "<?=$resdefcurr[0]['symbol']?>") {
									return full[1];
								}else{
									return "<a href=\"javascript:;\" onClick=\"editgrp('"+full[0]+"','"+full[3]+"','"+full[1]+"','"+full[2]+"','"+full[4]+"')\">"+full[1]+"<divclass=\"itmalert alert alert-danger nopadding\" id=\"itm"+full[0]+"\" style=\"display: none\";></div></a>";
								}										
							}
					
					},
					{ "data": null,
							"render": function (data, type, full, row) {

									if (full[1] == "<?=$resdefcurr[0]['symbol']?>") {
										return full[2] + " - <font color='red'>Main Unit</font>";
									}else{
										return full[2];
									}										
								}
					},
					{ "data": 3 },
					{ "data": 4 },
					{ "data": null,
							"render": function (data, type, full, row) {
								if (full[5] == "ACTIVE") {
									return "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+full[0]+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
								}else{
									return "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+full[0]+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
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
						"targets": [0,4],
						"className": "text-center dt-body-nowrap"
					}
				]
			});
	}
	</script>
