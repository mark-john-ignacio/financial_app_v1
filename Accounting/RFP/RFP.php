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

	<script type="text/javascript">


		$(document).keydown(function(e) {	 
			if(e.keyCode == 112) { //F2
				e.preventDefault();
			window.location = "PayBill_new.php";
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
									
				$("#AlertMsg").html("Are you sure you want to "+x+" Payment No.: "+num);
				$("#alertbtnOK").hide();
				$("#OK").show();
				$("#Cancel").show();
				$("#AlertModal").modal('show');
			

		}

		function set(){
						var left = (screen.width/2)-(500/2);
						var top = (screen.height/2)-(400/2);
						var sFeatures="dialogHeight: 400px; dialogWidth: 500px; dialogTop: " + top + "px; dialogLeft: " + left + "px;";
						
						var url = "PayBill_set.php?"
						
						window.showModalDialog(url, "", sFeatures)

		}
	</script>
</head>

<body style="padding:5px; height:900px">
	<div>
		<section>
    	<div>
        <div style="float:left; width:50%">
					<font size="+2"><u>Request For Payment</u></font>	
        </div>
      </div>
			
			<br><br>
			
			<button type="button" class="btn btn-primary" onClick="location.href='RFP_new.php'">
				<span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)
			</button>
      <!--
			<button type="button" class="btn btn-warning btn-md" name="btnSet" id="btnSet">
				<span class="glyphicon glyphicon-cog"></span> Settings
			</button>
			-->

      <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th class="text-center">Trans No</th>
						<th class="text-center">Reference</th>
            <th class="text-center">Paid To</th>
						<th class="text-center">Bank</th>
						<th class="text-center">Gross</th>
						<th class="text-center">Status</th>
						<th class="text-center">Actions</th>
					</tr>
				</thead>

				<tbody>
          <?php
						$sql = "select a.ctranno, a.lsent, a.lapproved, a.lcancelled, a.ccode, a.ngross, b.cname, e.cname as bankname, GROUP_CONCAT(c.capvno) as capvno
						from rfp a 
						left join rfp_t c on a.compcode=c.compcode and a.ctranno=c.ctranno 
						left join bank e on a.compcode=e.compcode and a.cbankcode=e.ccode 
						left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode 
						where a.compcode='$company' Group By a.ctranno, a.lsent, a.lapproved, a.lcancelled, a.ccode, a.ngross, b.cname, e.cname order by a.ddate DESC";
						$result=mysqli_query($con,$sql);
				
						if (!mysqli_query($con, $sql)) {
							printf("Errormessage: %s\n", mysqli_error($con));
						} 
					
						while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
					?>
						<tr <?=(intval($row['lcancelled'])==intval(1)) ? "class='text-danger'" : "";?>>
							<td><a <?=(intval($row['lcancelled'])==intval(1)) ? "class='text-danger'" : "";?> href="javascript:;" onClick="editfrm('<?=$row['ctranno'];?>');"><?=$row['ctranno'];?></a></td>
							<td><?=$row['capvno'];?></td>
							<td><?=$row['ccode'];?> - <?=$row['cname']?> </td>
							<td><?=$row['bankname'];?></td>
							<td align="right"><?=number_format($row['ngross'],2);?></td>
							<td align="center">
								<?php
									if(intval($row['lsent'])==intval(0)){
										echo "For Sending";
									}else{
										if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
											echo "For Approval";
										}else{
											if(intval($row['lapproved'])==intval(1)){
												echo "Posted";
											}elseif(intval($row['lcancelled'])==intval(1)){
												echo "<b>Cancelled</b>";
											}else{
												echo "Pending";
											}
										}
									}
								?>
							</td>
							<td align="center">
								<div id="msg<?php echo $row['ctranno'];?>">
									<?php 
										if(intval($row['lsent'])!==intval(1)){	
									?>

										<a href="javascript:;" onClick="trans('SEND','<?php echo $row['ctranno'];?>')" class="btn btn-xs btn-default"> 
											<i class="fa fa-share" style="font-size:20px;color: #ffb533;" title="Send transaction"></i>
										</a>

									<?php
										}else{

										if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0) && intval($row['lsent'])==intval(1)){

									?>
											<a href="javascript:;" onClick="trans('POST','<?php echo $row['ctranno'];?>')" class="btn btn-xs btn-default<?=($poststat!="True") ? " disabled" : ""?>">
												<i class="fa fa-thumbs-up" style="font-size:20px;color:Green ;" title="Approve transaction"></i>
											</a>

											<a href="javascript:;" onClick="trans('CANCEL','<?php echo $row['ctranno'];?>')" class="btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>">
												<i class="fa fa-thumbs-down" style="font-size:20px;color:Red ;" title="Cancel transaction"></i>
											</a>

									<?php
										}				
									?>
										
											<a href="javascript:;" onClick="track('<?php echo $row['ctranno'];?>')" class="btn btn-xs btn-default"> 
												<i class="fa fa-file-text-o" style="font-size:20px;color: #3374ff;" title="Track transaction"></i>
											</a>

									<?php
										}							
									?>
								</div>
							</td>
						</tr>
          <?php 
						}			
					?>
               
				</tbody>
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="RFP_edit.php">
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
		
		$('#example').DataTable({bSort:false});

	});

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
			else if(x=="CANCEL"){
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
</script>
