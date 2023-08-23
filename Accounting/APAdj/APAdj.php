<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "APAdj.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	
	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'APAdj_unpost.php'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
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
  <script src="../../Bootstrap/js/bootstrap.js"></script>

	<script type="text/javascript">
		$(document).keydown(function(e) {	 
			if(e.keyCode == 112) { //F1
				e.preventDefault();
			window.location = "APAdj_new.php";
			}
		});

		function editfrm(x){
			document.getElementById("txtctranno").value = x;
			document.getElementById("frmedit").submit();
		}

		function trans(x,num){
			
			$("#typ").val(x);
			$("#modzx").val(num); 


				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Are you sure you want to "+x+" Adjustment No.: "+num);
				$("#alertbtnOK").hide();
				$("#OK").show();
				$("#Cancel").show();
				$("#AlertModal").modal('show');
			

		}

		$(function(){
			var x = "";
			var num = "";
			
			$(".btnmodz").on("click", function (){
			var itmstat = "";	
				
				if($('#AlertModal').hasClass('in')==true){
					var idz = $(this).attr('id');
					
					if(idz=="OK"){
						var x = $("#typ").val();
						var num = $("#modzx").val();

							$.ajax ({
								url: "APAdj_Tran.php",
								data: { x: num, typ: x},
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
												
												$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+item.stat+"...");
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
			
		});

	</script>
</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>AP Adjustment</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary btn-sm" onClick="location.href='APAdj_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

			<?php
				if($poststat=="True"){
			?>
				<button type="button" class="btn btn-warning btn-sm" onClick="location.href='APAdj_unpost.php'"><span class="fa fa-refresh"></span>&nbsp;Un-Post Transaction</button>
			<?php
				}
			?>

      <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Transaction No</th>
						<th>Type</th>
            <th>Date</th>
						<th>Customer</th>
						<th>Amount</th>
						<th>Status</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$company = $_SESSION['companyid'];
					
					$sql = "select A.*, B.cname from apadjustment A left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode where A.compcode='$company' order by A.ddate DESC";
					$result=mysqli_query($con,$sql);
					
						if (!mysqli_query($con, $sql)) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
						
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
					?>
						<tr>
							<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
							<td><?php echo $row['ctype'];?></td>
							<td><?php echo $row['dcutdate'];?></td>
							<td><?php echo $row['cname'];?></td>
							<td><?php echo number_format($row['ngross'],2);?></td>
							<td align="center">
								<div id="msg<?php echo $row['ctranno'];?>">
								<?php 
									if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
								?>
									<a href="javascript:;" onClick="trans('POST','<?php echo $row['ctranno'];?>')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL','<?php echo $row['ctranno'];?>')">CANCEL</a>
								<?php
									}else{
										if(intval($row['lcancelled'])==intval(1)){
											echo "Cancelled";
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
    
<form name="frmedit" id="frmedit" method="post" action="APAdj_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
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
												<input type="hidden" id="isret" name="isret" value = "">
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
	$('#example').DataTable({bSort:false});

	</script>

</body>
</html>