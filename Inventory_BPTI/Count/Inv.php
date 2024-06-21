<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvCnt";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];

	//POST
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'InvCnt_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	//CANCEL
	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'InvCnt_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}

	$employeeid = $_SESSION['employeeid'];

//get sections access
	$arraccess = array();
	$sql = mysqli_query($con,"select * from users_sections where UserID = '$employeeid'");

	$arraccess[] = 0;
	while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
		$arraccess[] = $row['section_nid'];
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?x=<?=time()?>">
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../js/bootstrap3-typeahead.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>

</head>

<body style="padding:5px; height:750px">
	<div>
		<section>
         <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Inventory Count</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary btn-sm" onClick="javascript:;" id="btnSet">
				<span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)
			</button>

			<button type="button" class="btn btn-warning btn-sm" name="btnTemplate" id="btnTemplate">
				<span class="glyphicon glyphicon-cog"></span> Template
			</button>

			<br><br>

			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Transaction Code</th>
						<th>Type</th>
						<th>Section</th>
						<th>Prepared By</th>
						<th>Inventory Date</th>
						<th style="text-align:center">Status</th>
					</tr>
				</thead>

				<tbody>
         			<?php
						$arrsecs = array();
						$reslocs=mysqli_query($con,"Select * From locations where compcode='$company'");
						while($row = mysqli_fetch_array($reslocs, MYSQLI_ASSOC)){
							$arrsecs[$row['nid']] = $row['cdesc'];
						}

						$sql = "SELECT * FROM invcount where compcode='$company' and section_nid in (".implode(",", $arraccess).") order by ddate DESC";
						$result=mysqli_query($con,$sql);
						
						if (!mysqli_query($con, $sql)) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
							
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
					
							$varbtnDis = "";
							if(intval($row['lapproved'])!=intval(1)){
								$varbtnDis = "disabled";
							}
		
					?>
						<tr <?=(intval($row['lcancelled'])==intval(1)) ? "class='text-danger'" : "";?>>
							<td><a  <?=(intval($row['lcancelled'])==intval(1)) ? "class='text-danger'" : "";?> href="javascript:;" onClick="editfrm('<?=$row['ctranno'];?>','InvCnt_Edit.php');"><?=$row['ctranno'];?></a></td>
							<td><?=($row['ctype']=="output") ? "Production Output" : "Inventory Ending";?> </td>
							<td><?=$arrsecs[$row['section_nid']];?></td>
							<td><?=$row['cpreparedby'];?></td>
							<td><?=$row['dcutdate'];?></td>
							<td align="center">
								<div id="msg<?=$row['ctranno'];?>">
									<?php 
										if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
									?>

											<a href="javascript:;" onClick="trans('POST','<?=$row['ctranno'];?>','<?=$row['ctype'];?>')" class="btn btn-xs btn-default<?=($poststat!="True") ? " disabled" : ""?>"><i class="fa fa-thumbs-up" style="font-size:20px;color:Green ;" title="Approve transaction"></i></a> 
											<a href="javascript:;" onClick="trans('CANCEL','<?=$row['ctranno'];?>','')" class="btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>"><i class="fa fa-thumbs-down" style="font-size:20px;color:Red ;" title="Cancel transaction"></i></a>

									<?php
										}
										else{
											if(intval($row['lcancelled'])==intval(1)){
												echo "<b>Cancelled</b>";
											}
											if(intval($row['lapproved'])==intval(1)){
												echo "Posted";
											}
										}							
									?>
								</div>
							</td>
						</tr>
          			<?php 
							}				
							mysqli_close($con);				
					?>
               
				</tbody>
			</table>

		</section>
	</div>		
     
	<form name="frmNew" id="frmNew" method="get" action="InvCnt_New.php">
	</form>		

	<form name="frmEdit" id="frmEdit" method="get" action="InvCnt_Edit.php">
		<input type="hidden" name="id" id="id" />
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
													<input type="hidden" id="modztyp" name="modztyp" value = "">
						</center>
					</p>
				</div>
				</div>
			</div>
		</div>
	</div>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
		$(document).ready(function(e) {	
		
			$("#divimgload").hide();
		
		});

		$(document).keydown(function(e) {	 
		if(e.keyCode == 112) { //F2
			e.preventDefault();
			window.location = "InvAdj.php";
		}
		});

	
		$(function(){
			
			$('#example').DataTable();		
			
			var x = "";
			var num = "";
			
			$("#btnSet").on('click', function() {
				$("#frmNew").submit();
			}); 

			$("#btnTemplate").on('click', function() {
				window.location="InvCnt_Template.php";
			});

			$(".btnmodz").on("click", function (){
				var itmstat = "";	
				
				if($('#AlertModal').hasClass('in')==true){
					var idz = $(this).attr('id');
					
					if(idz=="OK"){
						x = $("#typ").val();
						num = $("#modzx").val();
						
						if(x=="POST"){
							var msg = "POSTED";
							itmstat = "OK";	
							//insert to inventory
							///insert o inventory

							if($("#modztyp").val()=="output"){
								$.ajax ({
									dataType: "text",
									url: "../../include/th_toInv.php",
									data: { tran: num, type: "INVCNT" },
									async: false,
									success: function( data ) {
									//	alert(data.trim());
									if(data.trim()=="True"){
											itmstat = "OK";							
										}
										else{
											itmstat = data.trim();	
										}
									}
								});
							}
							
						}
						else if(x=="CANCEL"){
							var msg = "CANCELLED";
							itmstat = "OK";	
						}


						if(itmstat=="OK"){

								$.ajax ({
									dataType: "text",
									url: "InvCnt_Tran.php",
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

		function editfrm(x,url){
			document.getElementById("id").value = x;
			document.frmEdit.action = url;
			document.getElementById("frmEdit").submit();
		}

		function trans(x,num,tran_typ){
			
			$("#typ").val(x);
			$("#modzx").val(num);
			$("#modztyp").val(tran_typ);


				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Are you sure you want to "+x+" Inv Count No.: "+num);
				$("#alertbtnOK").hide();
				$("#OK").show();
				$("#Cancel").show();
				$("#AlertModal").modal('show');
			

		}

	</script>

</body>
</html>