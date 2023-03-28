<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Accounts.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');


	$company = $_SESSION['companyid'];
	$result = mysqli_query ($con, "select cacctno,cacctid,cacctdesc,mainacct,ccategory,nlevel,ctype from accounts WHERE compcode = '".$company."'"); 
	$row = $result->fetch_all(MYSQLI_ASSOC);

	$cats = [];


	// store the categories in a 2-dim array with arrays of cats belonging to each parent 
	foreach ($row as $r) {
		if (!isset($cats[$r['mainacct']])) {
				$cats[$r['mainacct']] = [];
		}
		$cats[$r['mainacct']][] = [ 'id' => $r['cacctid'], 'name' => $r['cacctdesc'], 'typ' => $r['ccategory'] ];
	}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
<link href="../../Bootstrap/css/jquery.bootstrap.treeselect.css" rel="stylesheet">


<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>



</head>

<body style="padding:5px">
<input type="hidden" value='<?=json_encode($row)?>' id="hdnaccts">


	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Chart of Accounts</u></font>	
            </div>
        </div>
			<br><br>
           			 

				<div class="col-xs-12 nopadding">
					<div class="col-xs-2 nopadding">
						<button type="button" data-toggle="modal" class="btn btn-primary btn-sm" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
					</div>
					<div class="col-xs-3 nopadding">
						<div class="itmalert alert alert-danger" id="itmerr" style="display: none;"></div> <br><br>
					</div>

					<div class="col-xs-1 nopadwtop" style="height:30px !important;">
						<b> Search Item: </b>
					</div>
					<div class="col-xs-3 text-right nopadding">
						<input type="text" name="searchByName" id="searchByName" value="" class="form-control input-sm" placeholder="Enter Code or Desc...">
					</div>

					<div class="col-xs-3 text-right nopadwleft">
						<select id="seltypesearch" name="seltypesearch" class="form-control input-sm selectpicker"  tabindex="4">
								<option value="">ALL</option>
								<option value="ASSETS">ASSETS</option>
								<option value="LIABILITIES">LIABILITIES</option>
								<option value="EQUITY">EQUITY</option>
								<option value="INCOME">INCOME</option>
								<option value="EXPENSES">EXPENSES</option>											   
							</select>
					</div>

				</div>

			<br><br>			
			<table class="table table-hover" role="grid" id="MyTable">
				<thead>
					<tr>
						<th>Acct No</th>
						<th>Description</th>
						<th>Category</th>
						<th>Type</th>
					</tr>
				</thead>
			</table>

		</section>
	</div>		

	<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						
						<h5 class="modal-title" id="myModalLabel"><b>New Account</b></h5>
						
					</div>

					<form action="accounts_add.php" method="POST" name="frmnew" id="frmnew">

						<div class="modal-body">
							<div class="err" id="add_err"></div>

							<div class="col-sm-12 nopadding">
								<div class="col-sm-6 nopadding">
									<label class="radio-inline">
										<input type="radio" name="radtype" id="radtypegen" value="General"> Title
									</label>
								</div>
								<div class="col-sm-6 nopadding">
									<label class="radio-inline">
										<input type="radio" name="radtype" id="radtypedet" value="Details" checked> Detail Account
									</label>
								</div>
							</div>

							<div class="col-sm-12 nopadwtop2x">
								<div class="col-sm-4 nopadding">
									<b>Account Code:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<input type='text' class="form-control input-sm" id="cacctid" name="cacctid" value=""/>
								</div>
							</div>

							<div class="col-sm-12 nopadwtop">
								<div class="col-sm-4 nopadding">
									<b>Account Description:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<input type='text' class="form-control input-sm" id="cacctdesc" name="cacctdesc" value=""/>
								</div>
							</div>

							<div class="col-sm-12 nopadwtop">
								<div class="col-sm-4 nopadding">
									<b>Account Category:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<select name="selcat" id="selcat" class="form-control input-sm">
										<option value="ASSETS">ASSETS</option>
										<option value="LIABILITIES">LIABILITIES</option>
										<option value="EQUITY">EQUITY</option>
										<option value="INCOME">INCOME</option>
										<option value="EXPENSES">EXPENSES</option>
									</select>
								</div>
							</div>

							<div class="col-sm-12 nopadwtop">
								<div class="col-sm-4 nopadding">
									<b>Level:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<div class="col-sm-12 nopadding">
										<div class="col-sm-4 nopadding">
											<select name="selvl" id="selvl" class="form-control input-sm"> 
												<option value="1">1</option> 
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
											</select>
										</div>
										<div class="col-sm-8 text-right nopadding">
											<label class="checkbox-inline">
												<input type="checkbox" name="chkcontra" id="chkcontra" value="1"> Contra Account
											</label>
										</div>
									</div>
								</div>
							</div>

							<div class="col-sm-12 nopadwtop">
								<div class="col-sm-4 nopadding">
									<b>Header Account:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<select name="selhdr" id="selhdr" class="form-control input-sm disabled" disabled="disabled">
										<option value="">Select Header</option>
									</select>
								</div>
							</div>

						</div>
						<div class="modal-footer">
							<button type="submit" id="btnSave" name="btnSave" class="btn btn-primary">Save</button>
						</div>

					</form>
				</div>
			</div>
		</div>
	<!-- MODAL -->


		<!-- Update Modal -->
		<div class="modal fade" id="myUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						
						<h5 class="modal-title" id="myModalLabel"><b>Update Account</b></h5>
						
					</div>

					<form action="Accounts_update.php" method="POST" name="frmupdate" id="frmupdate">

						<div class="modal-body">

							<div class="col-sm-12 nopadding">
								<div class="col-sm-6 nopadding">
									<label class="radio-inline">
										<input type="radio" name="radtype2" id="radtypegen" value="General"> Title
									</label>
								</div>
								<div class="col-sm-6 nopadding">
									<label class="radio-inline">
										<input type="radio" name="radtype2" id="radtypedet" value="Details" checked> Detail Account
									</label> 
								</div>
							</div>

							<div class="col-sm-12 nopadwtop2x">
								<div class="col-sm-4 nopadding">
									<b>Account Code:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<input type="hidden" name="acctidcode" id="acctidcode" value="">
            			<input type="text" class="form-control input-sm" placeholder="Account No..." name="acctid2" id="acctid2" readonly>
								</div>
							</div>

							<div class="col-sm-12 nopadwtop">
								<div class="col-sm-4 nopadding">
									<b>Account Description:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<input type="text" class="form-control input-sm" placeholder="Decsription..." name="cdesc2" id="cdesc2" required>
								</div>
							</div>

							<div class="col-sm-12 nopadwtop">
								<div class="col-sm-4 nopadding">
									<b>Account Category:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<select name="selcat2" id="selcat2" class="form-control input-sm">
										<option value="ASSETS">ASSETS</option>
										<option value="LIABILITIES">LIABILITIES</option>
										<option value="EQUITY">EQUITY</option>
										<option value="INCOME">INCOME</option>
										<option value="EXPENSES">EXPENSES</option>
									</select>
								</div>
							</div>

							<div class="col-sm-12 nopadwtop">
								<div class="col-sm-4 nopadding">
									<b>Level:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<div class="col-sm-12 nopadding">
										<div class="col-sm-4 nopadding">
											<select name="selvl2" id="selvl2" class="form-control input-sm"> 
												<option value="1">1</option> 
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
											</select>
										</div>
										<div class="col-sm-8 text-right nopadding">
											<label class="checkbox-inline">
												<input type="checkbox" name="chkcontra2" id="chkcontra2" value="1"> Contra Account
											</label>
										</div>
									</div>
								</div>
							</div>

							<div class="col-sm-12 nopadwtop">
								<div class="col-sm-4 nopadding">
									<b>Header Account:</b>
								</div>
								<div class="col-sm-6 nopadding">
									<select name="selhdr2" id="selhdr2" class="form-control input-sm disabled" disabled="disabled">
										<option value="">Select Header</option>
									</select>
								</div>
							</div>

						</div>
						<div class="modal-footer">
							<button type="submit" id="btnUpdate" name="btnUpdate" class="btn btn-primary">Update</button>
						</div>

					</form>
				</div>
			</div>
		</div>
	<!-- MODAL -->


<?php

mysqli_close($con);
?>
</body>
</html>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	
	$(document).ready(function() {

		fill_datatable();	
		$("#searchByName").keyup(function(){
		   var searchByName = $(this).val();
			 var searchByType = $('#seltypesearch').val();
		  // if(searchByName != '')
		  // {
		    $('#MyTable').DataTable().destroy();
		    fill_datatable(searchByName,searchByType);
		 //  }
		});

		$("#seltypesearch").on("change", function(){
			var searchByName = $('#searchByName').val();
			var searchByType = $(this).val();

		    $('#MyTable').DataTable().destroy();
		    fill_datatable(searchByName,searchByType);
		});

		//Check new user id
		$("#cacctid").on("blur", function () {
			
			var x = $(this).val();
			
			if(x != "") {
				$.ajax({
				type:'post',
					url:'Accounts_chkID.php',// put your real file name 
					data:{id: x},
					success:function(msg){
						if(msg.trim()!=""){
							$("#add_err").html(msg.trim());
							//$("#add_err").css('display', 'none', 'important'); // your message will come here. 
							$("#cacctid").val("").change();
							$("#cacctid").focus(); 
						}else{
							$("#add_err").html("");
						}
					}
				});
			}
		});

		// Adding new account
		$("#btnadd").on("click", function() {
			$("#divmainacc").html("");

			$('#frmnew').trigger("reset");


			$('#myModal').modal('show');
		});

		$("#selvl").on("change", function() {

			var html = [];


			if($(this).val()>1){
				$("#selhdr").attr("disabled", false); 

				var lvl = parseInt($(this).val()) - 1;
				var hdrmain = $("#selcat").val();

				var obj = $("#hdnaccts").val();

				$.each(jQuery.parseJSON(obj), function() {
					if(lvl==this['nlevel']  && hdrmain==this['ccategory'] && this['ctype']=="General"){
						html.push('<option value="' +this['cacctid'] + '">' + this['cacctdesc'] + '</option>');
					}
				}); 
			}

			$('#selhdr').html(html.join(''));

		});

		$("#selvl2").on("change", function() {

			var html = [];


			if($(this).val()>1){
				$("#selhdr2").attr("disabled", false); 

				var lvl = parseInt($(this).val()) - 1;
				var hdrmain = $("#selcat2").val();

				var obj = $("#hdnaccts").val();

				$.each(jQuery.parseJSON(obj), function() {
					if(lvl==this['nlevel']  && hdrmain==this['ccategory'] && this['ctype']=="General"){
						html.push('<option value="' +this['cacctid'] + '">' + this['cacctdesc'] + '</option>');
					}
				}); 
			}

			$('#selhdr2').html(html.join(''));

		});


		$("#frmnew").on('submit', function (e) {
			e.preventDefault();

			var form = $("#frmnew");
			var formdata = form.serialize();
				$.ajax({
				url: 'Accounts_add.php',
				type: 'POST',
				async: false,
				data: formdata,
				success: function(data) {
					if(data.trim()!="False"){
						$('#myModal').modal('hide');

						alert(data);
						location.reload();
					}else{
						alert("Error saving new account!");	
					}
				}
	    });							

		});


		$("#frmupdate").on('submit', function (e) {
			e.preventDefault();

			var form = $("#frmupdate");
			var formdata = form.serialize();

				$.ajax({
				url: 'Accounts_update.php',
				type: 'POST',
				async: false,
				data: formdata,
				success: function(data) {
					if(data.trim()!="False"){
						$('#myModal').modal('hide');

						alert(data);
						location.reload();
					}else{
						alert("Error saving new account!");	
					}
				}
	    });							

		});



	});


	function fill_datatable(searchByName = '', searchByType = '')
	{
		var GENxyz
		var GENxyz0

		var table = $('#MyTable').DataTable( {
			"searching": true,
      "paging": true,
			"serverSide": true,
			"ordering": false,
			"lengthChange": false,
			"searching" : false,
			"ajax" : {
		     url:"serverside.php",
		     type:"POST",
		     data:{
		      searchByName:searchByName, searchByType:searchByType
		     }
		  },
			"columns": [
				{ "data": null,
					"render": function (data, type, full, row) {
							
						var GENxyz = parseInt(full[8]);
						
						var GENxyz0 = 0;
						if(GENxyz>1){
							GENxyz0 = (5 * GENxyz) + (GENxyz * 2);
						}

							if(full[4]==null){
								return full[9];
							}else{
								//editacct(id,codeno,name,typ,cat,mId,nlvl,lcon,conid)
								return "<div style='text-indent:"+GENxyz0+"px'> <a href=\"javascript:;\" onClick=\"editacct('"+full[1]+"','"+full[0]+"','"+full[2]+"','"+full[3]+"','"+full[4]+"','"+full[5]+"','"+full[8]+"')\">"+full[1]+"</a> </div>";
							}
					}
						
				},
				{ "data": null,
					"render": function (data, type, full, row) {
						var GENxyz = parseInt(full[8]);
						
						var GENxyz0 = 0;
						if(GENxyz>1){
							GENxyz0 = (5 * GENxyz) + (GENxyz * 2);
						}

						var symxcol = "";
						if(GENxyz0==14){
							symxcol = "&#8226; ";
						}else if(GENxyz0==21){
							symxcol = "&#10022; ";
						}else if(GENxyz0==28){
							symxcol = "&#10070; ";
						}else if(GENxyz0==35){
							symxcol = "&#10148; ";
						}
						
						return "<div style='text-indent:"+GENxyz0+"px'>  "+symxcol+full[2]+"</div>";
					}
				},
				{ "data": 4 },
				{ "data": 3 }		
      ],
			"columnDefs": [
				{ "targets": 3, "className": "text-center" } 
			],
		} );
			
	
	}
	
	
	
	function editacct(id,codeno,name,typ,cat,mId,nlvl,lcon,conid){
			$("#divmainacc2").html("");
			
			$("#acctidcode").val(codeno);
			$("#acctid2").val(id);
			$("#cdesc2").val(name);
			$("#selcat2").val(cat);
			var $radios = $('input:radio[name=radtype2]');
			$radios.filter('[value='+typ+']').prop('checked', true);
			
				if(parseInt(lcon)==1){
					$('#chkcontra2').prop('checked', true);
				}

			$("#selvl2").val(nlvl);

			$("#selvl2").trigger("change");

			$('#myUpdate').modal('show');

	}

	</script>
