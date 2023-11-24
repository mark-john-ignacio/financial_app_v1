<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Deposit.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access.php');

$company = $_SESSION['companyid'];


$sqlchk = mysqli_query($con,"Select a.cvalue,b.cacctdesc From parameters a left join accounts b on a.compcode=b.compcode and a.cvalue=b.cacctno where a.compcode='$company' and a.ccode='DEPDEBIT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
			$nDebitDesc = $row['cacctdesc'];
		}
	}else{
		$nDebitDef = "";
		$nDebitDesc =  "";
	}


	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Deposit_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Deposit_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}

	//CANCEL
	$voidstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Deposit_void'");
	if(mysqli_num_rows($sql) == 0){
		$voidstat = "False";
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

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>


</head>

<body style="padding:5px; height:750px">
		<div>
			<section>
				<div class="row nopadding">
					<div style="float:left; width:50%">
								<font size="+2"><u>Bank Deposit</u></font>	
					</div>
				</div>
			
				<div class="col-xs-12 nopadwdown">
					<div class="col-xs-4 nopadding">
						<button type="button" class="btn btn-primary btn-sm"  onClick="location.href='Deposit_new.php'" id="btnNew" name="btnNew"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

						<?php
							if($voidstat=="True"){
						?>
							<button type="button" class="btn btn-danger btn-sm" onClick="location.href='Deposit_void.php'"><span class="fa fa-times"></span>&nbsp;Void Transaction</button>
						<?php
							}
						?>
					</div>
					<div class="col-xs-3 nopadwtop text-right" style="height:30px !important; padding-right: 10px !important">
						<b> Search Transaction No </b>
					</div>
					<div class="col-xs-3 text-right nopadding">
						<input type="text" name="searchByName" id="searchByName" value="<?=(isset($_REQUEST['ix'])) ? $_REQUEST['ix'] : ""?>" class="form-control input-sm" placeholder="Enter Transaction No...">
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
						<th>Trans No.</th>
            <th>Deposit Acct</th>
						<th>Remarks</th>
						<th class="text-right">Total Deposited</th>
						<th class="text-center">Date</th>
						<th class="text-center">Status</th>
					</tr>
				</thead>

			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="Deposit_edit.php">
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
                        <input type="hidden" id="modzx" name="modzx" value = "">
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>


<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
<script type="text/javascript">
	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F2
	    e.preventDefault();
		window.location = "Deposit_new.php";
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
			var itmstat = "";	
			
			if($('#AlertModal').hasClass('in')==true){
				var idz = $(this).attr('id');
				
				if(idz=="OK"){
					var x = $("#typ").val();
					var num = $("#modzx").val();
					
					if(x=="POST"){
						var msg = "POSTED";
						
						//generate GL ENtry muna
						$.ajax ({
							dataType: "text",
							url: "../../include/th_toAcc.php",
							data: { tran: num, type: "BD" },
							async: false,
							success: function( data ) {
								//alert(data.trim());
								if(data.trim()=="True"){
									itmstat = "OK";								
								}
								else{
									itmstat = data.trim();	
								}
							}
						});
						
					}
					else if(x=="CANCEL"){
						var msg = "CANCELLED";
						itmstat = "OK";
					}


					if(itmstat=="OK"){
						$.ajax ({
							url: "Deposit_Tran.php",
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


				}
				else if(idz=="Cancel"){
					
					$("#AlertMsg").html("");
					$("#AlertModal").modal('hide');
					
				}




			}
		});
		
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
								
			$("#AlertMsg").html("Are you sure you want to "+x+" Deposit No.: "+num);
			$("#alertbtnOK").hide();
			$("#OK").show();
			$("#Cancel").show();
			$("#AlertModal").modal('show');
		

	}

	function fill_datatable(searchByName = '', searchBystat = ''){
		var dataTable = $('#example').DataTable( {
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
								if (full[7] == 1 || full[8] == 1) {
									sts="class='text-danger'";
								}
								return "<a "+sts+" href=\"javascript:;\" onclick=\"editfrm('"+full[0]+"')\">"+full[0]+"</a>";
						}								
				},
				{ "data": null,
						"render": function (data, type, full, row) {

							return full[1]+" - "+full[2];
								
						}
							
				},
				{ "data": 3 },	
				{ "data": 4 },
				{ "data": 5 },
				{ "data": null,
						"render": function (data, type, full, row) {
		
							if (full[6] == 1) {

								if(full[8] == 1){
									return '<b>Voided</b>';
								}else{									
									return 'Posted';
								}	

							}
								
							else if (full[7] == 1) {
								
								return '<b>Cancelled</b>';
								
							}
								
							else{

								return 	"<div id=\"msg"+full[0]+"\"> <a href=\"javascript:;\" onClick=\"trans('POST','"+full[0]+"')\" class=\"btn btn-xs btn-default<?=($poststat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-up\" style=\"font-size:20px;color:Green ;\" title=\"Approve transaction\"></i></a> <a href=\"javascript:;\" onClick=\"trans('CANCEL','"+full[0]+"')\" class=\"btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>\"><i class=\"fa fa-thumbs-down\" style=\"font-size:20px;color:Red ;\" title=\"Cancel transaction\"></i></a> </div>";

							}
						}
					}
			],
			"columnDefs": [ 
				{
					"targets": [3],
					"className": "text-right"
				},
				{
					"targets": [4,5],
					"className": "text-center dt-body-nowrap"
				}
			],
			"createdRow": function( row, data, dataIndex ) {
				// Set the data-status attribute, and add a class
				if(data[7]==1 || data[8] == 1){
					$(row).addClass('text-danger');
				}
						
			}
		});
	}

</script>

</body>
</html>