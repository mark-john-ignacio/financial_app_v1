<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvTrans.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];
	$employeeid = $_SESSION['employeeid'];

	$arrseclist = array();
	$sqlempsec = mysqli_query($con,"select A.section_nid as nid, B.cdesc from users_sections A left join locations B on A.section_nid=B.nid where A.UserID='$employeeid' and B.cstatus='ACTIVE' Order By B.cdesc");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrseclist[] = $row0['nid'];
	}

	if(isset($_REQUEST['cwh'])){
		$arrseclist = array();
		$arrseclist[0] = $_REQUEST['cwh'];
	}else{
		if(count($arrseclist)==0){
			$arrseclist[] = 0;
		}
	}


	$arrinvsecs = array();
	$arrinvitms = array();
	$sqlcp = "select * from items_invlvl where compcode='$company'";
	$resultcp = mysqli_query ($con, $sqlcp); 
	if(mysqli_num_rows($resultcp)!=0){
		while($rowcp = mysqli_fetch_array($resultcp, MYSQLI_ASSOC)){
			
			$arrinvsecs[] = $rowcp['section_nid'];
			$arrinvitms[] = $rowcp['cpartno'];
			
		}
	}

	$arrdetails = array();
	$sqlcp = "select * from invtransfer_t where compcode='$company'";
	$resultcp = mysqli_query ($con, $sqlcp); 
	if(mysqli_num_rows($resultcp)!=0){
		while($rowcp = mysqli_fetch_array($resultcp, MYSQLI_ASSOC)){
			
			$arrdetails[] = array('citemno' => $rowcp['citemno'], 'ctranno' => $rowcp['ctranno']);
			
		}
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
				<font size="+2"><u>Inventory Transfer</u></font>	
            </div>
        </div>
			<br><br>

			<div style="float:left; width:50%">
				<button type="button" class="btn btn-primary btn-sm" onClick="javascript:;" id="btnSet">
					<span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)
				</button>

				<button type="button" class="btn btn-warning btn-sm" name="btnTemplate" id="btnTemplate">
					<span class="glyphicon glyphicon-cog"></span> Template
				</button>
			</div>
			<div style="float:right; width:20%">
				<select class="form-control input-sm" name="selwhfrom" id="selwhfrom">
					<?php
						$issel = 0;
							foreach($rowdetloc as $localocs){
								if(isset($_REQUEST['cwh'])){
									if($_REQUEST['cwh']==$localocs['nid']){
										$issel++;
									}else{
										$issel = 0;
									}
								}else{
									$issel++;
								}
								
					?>
								<option value="<?php echo $localocs['nid'];?>" <?=($issel==1) ? "selected" : ""?>><?php echo $localocs['cdesc'];?></option>										
					<?php	
							}						
					?>
				</select>
			</div>
			<br><br><br>

			<ul class="nav nav-tabs">
				<li class="active"><a href="#home">Transaction List</a></li>
				<li><a href="#menu1">From Other Sections</a></li>
			</ul>

			<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;text-align: left;overflow: auto">
				<div class="tab-content">  

					<div id="home" class="tab-pane fade in active" style="padding-left:5px; padding-top:15px;">

						<table id="MyTable1" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th>Transaction Code</th>
									<th>Type</th>
									<th>Section To</th>
									<th>Prepared By</th>
									<th>Trans Date</th>
									<th class="text-center">Status From</th>
									<th class="text-center">Status To</th>
								</tr>
							</thead>

							<tbody>
								<?php
									$arrsecs = array();
									$reslocs=mysqli_query($con,"Select * From locations where compcode='$company'");
									while($row = mysqli_fetch_array($reslocs, MYSQLI_ASSOC)){
										$arrsecs[$row['nid']] = $row['cdesc'];
									}

									$sql = "SELECT A.*, B.cdesc, C.Fname, C.Lname FROM invtransfer A left join locations B on A.compcode=B.compcode and A.csection2=B.nid left join users C on A.cpreparedby=C.Userid where A.compcode='$company' and A.csection1 = ".$arrseclist[0]." order by A.ddatetime DESC";

									$result=mysqli_query($con,$sql);
									
										if (!mysqli_query($con, $sql)) {
											printf("Errormessage: %s\n", mysqli_error($con));
										} 
										
									while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
									{

										$arrmyitems = array();
										foreach($arrdetails as $chkdet){
											if($chkdet['ctranno']==$row['ctranno']){
												$arrmyitems[] = $chkdet['citemno'];
											}
										}

										$cwhse = "";
										if($row['ctrantype']!=="request"){
											$cwhse = $row['csection1'];
										}
										
										$tocheck = 0;
										if(count(array_intersect($arrinvitms, $arrmyitems)) > 0 && in_array($cwhse, $arrinvsecs)){
											$tocheck = 1;
										}
					
								?>
									<tr <?=(intval($row['lcancelled1'])==intval(1) || intval($row['lcancelled2'])==intval(1)) ? "class='text-danger'" : "";?>>
										<td><a  <?=(intval($row['lcancelled1'])==intval(1) || intval($row['lcancelled2'])==intval(1)) ? "class='text-danger'" : "";?> href="javascript:;" onClick="editfrm('<?=$row['ctranno'];?>','InvTrans_Edit.php');"><?=$row['ctranno'];?></a></td>
										<td> <?=ucwords(str_replace("_"," ",$row['ctrantype']));?> </td>
										<td><?=$row['cdesc'];?></td>
										<td><?=$row['Fname']." ".$row['Lname'];?></td>
										<td><?=$row['dcutdate'];?></td>
										<td align="center">
											<div id="msg1<?=$row['ctranno'];?>">
												<?php 
													if(intval($row['lcancelled1'])==intval(0) && intval($row['lapproved1'])==intval(0)){
												?>
													<a href="javascript:;" onClick="trans('POST1','<?=$row['ctranno'];?>', 'msg1', '<?=$tocheck?>')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL1','<?=$row['ctranno'];?>', 'msg1', 0)">CANCEL</a>
												<?php
													}
													else{
														if(intval($row['lcancelled1'])==intval(1)){
															echo "<b>Cancelled</b>";
														}
														if(intval($row['lapproved1'])==intval(1)){
															echo "Posted";
														}
													}							
												?>
											</div>
										</td>

										<td align="center">
											<div id="msg2<?=$row['ctranno'];?>">
												<?php 
													if(intval($row['lcancelled2'])==intval(0) && intval($row['lapproved2'])==intval(0) && intval($row['lcancelled1'])==intval(0) && intval($row['lapproved1'])==intval(0)){
														echo "Waiting";
													}elseif(intval($row['lcancelled2'])==intval(0) && intval($row['lapproved2'])==intval(0) && (intval($row['lcancelled1'])==intval(1) || intval($row['lapproved1'])==intval(1))){
														if(intval($row['lcancelled1'])==intval(1) && intval($row['lapproved1'])==intval(0)){
															echo "-";
														}elseif(intval($row['lcancelled1'])==intval(0) && intval($row['lapproved1'])==intval(1)){
															echo "Pending";
														}
														
													}
													else{
														if(intval($row['lcancelled2'])==intval(1)){
															echo "<b>Cancelled</b>";
														}
														if(intval($row['lapproved2'])==intval(1)){
															echo "Posted";
														}
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

					</div>

					<div id="menu1" class="tab-pane fade" style="padding-left:5px; padding-top:15px;">

						<table id="MyTable2" class="display" cellspacing="0" width="100%">
							<thead>
							<tr>
									<th>Transaction Code</th>
									<th>Type</th>
									<th>Section From</th>
									<th>Prepared By</th>
									<th>Trans Date</th>
									<th class="text-center">Status From</th>
									<th class="text-center">Status To</th>
								</tr>
							</thead>

							<tbody>
								<?php
									$arrsecs = array();
									$reslocs=mysqli_query($con,"Select * From locations where compcode='$company'");
									while($row = mysqli_fetch_array($reslocs, MYSQLI_ASSOC)){
										$arrsecs[$row['nid']] = $row['cdesc'];
									}

									$sql = "SELECT A.*, B.cdesc, C.Fname, C.Lname FROM invtransfer A left join locations B on A.compcode=B.compcode and A.csection1=B.nid left join users C on A.cpreparedby=C.Userid where A.compcode='$company' and A.csection2 = ".$arrseclist[0]." and A.lapproved1=1 order by A.ddatetime DESC";

									$result=mysqli_query($con,$sql);
									
										if (!mysqli_query($con, $sql)) {
											printf("Errormessage: %s\n", mysqli_error($con));
										} 
										
									while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
									{
								
										$arrmyitems = array();
										foreach($arrdetails as $chkdet){
											if($chkdet['ctranno']==$row['ctranno']){
												$arrmyitems[] = $chkdet['citemno'];
											}
										}

										$cwhse = "";
										if($row['ctrantype']=="request"){
											$cwhse = $row['csection2'];
										}
										
										$tocheck = 0;
										if(count(array_intersect($arrinvitms, $arrmyitems)) > 0 && in_array($cwhse, $arrinvsecs)){
											$tocheck = 1;
										}
					
								?>
									<tr <?=(intval($row['lcancelled1'])==intval(1) || intval($row['lcancelled2'])==intval(1)) ? "class='text-danger'" : "";?>>
										<td><a  <?=(intval($row['lcancelled1'])==intval(1) || intval($row['lcancelled2'])==intval(1)) ? "class='text-danger'" : "";?> href="javascript:;" onClick="editfrm('<?=$row['ctranno'];?>','InvTrans_EditTo.php');"><?=$row['ctranno'];?></a></td>
										<td> <?=ucwords(str_replace("_"," ",$row['ctrantype']));?> </td>
										<td><?=$row['cdesc'];?></td>
										<td><?=$row['Fname']." ".$row['Lname'];?></td>
										<td><?=$row['dcutdate'];?></td>
										<td align="center">
											<div id="msgto1<?=$row['ctranno'];?>">
												<?php 
														if(intval($row['lapproved1'])==intval(1)){
															echo "Posted";
														}					
												?>
											</div>
										</td>

										<td align="center">
											<div id="msgto2<?=$row['ctranno'];?>">
												<?php 
													if(intval($row['lcancelled2'])==intval(0) && intval($row['lapproved2'])==intval(0)){
												?>
													<a href="javascript:;" onClick="trans('POST2','<?=$row['ctranno'];?>', 'msgto2', '<?=$tocheck?>')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL2','<?=$row['ctranno'];?>', 'msgto2', 0)">CANCEL</a>
												<?php
													}
													else{
														if(intval($row['lcancelled2'])==intval(1)){
															echo "<b>Cancelled</b>";
														}
														if(intval($row['lapproved2'])==intval(1)){
															echo "Posted";
														}
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

					</div>
				</div>									
			</div>

		</section>
	</div>		
     
<form name="frmNew" id="frmNew" method="get" action="InvTrans_New.php">
</form>		

<form name="frmEdit" id="frmEdit" method="get" action="InvTrans_Edit.php">
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
												<input type="hidden" id="modmsg" name="modmsg" value = "">

                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>


	<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"> 
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="InvListHdr">Inventory Level Check</h3>
        </div>           
        <div class="modal-body pre-scrollable" style="height:30vh" id="bdycheck">         

				</div>			
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
	</div>

</body>
</html>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
		$(document).ready(function(e) {	
		
			$("#divimgload").hide();
		
		});

		$(document).keydown(function(e) {	 
			if(e.keyCode == 112) { //F1
				e.preventDefault();
				window.location = "InvTrans_New.php";
			}
		});

		
		$(document).ready(function(e) {

			$(".nav-tabs a").click(function(){
					$(this).tab('show');
			});
			
			$('#MyTable2').DataTable({ 
				"lengthChange": false,
		    "searching" : false
			});	
			
			
			$('#MyTable1').DataTable({ 
				"lengthChange": false,
		    "searching" : false
			});		
		
			var x = "";
			var num = "";
		
			$("#btnSet").on('click', function() {
				$("#frmNew").submit();
			}); 

			$("#selwhfrom").on('change', function() {
				window.location="Inv.php?cwh="+$(this).val();
			});

			$("#btnTemplate").on('click', function() {
				window.location="InvTrans_Template.php";
			});

			$(".btnmodz").on("click", function (){
				var itmstat = "";	
				
				if($('#AlertModal').hasClass('in')==true){
					var idz = $(this).attr('id');
					
					if(idz=="OK"){
						x = $("#typ").val();
						num = $("#modzx").val();
						msgid = $("#modmsg").val();
						
						if(x=="POST1" || x=="POST2"){
							var msg = "POSTED";
							itmstat = "OK";	
							//insert to inventory

							if(x=="POST2"){
								//alert("../../include/th_toInv.php?tran="+num+"&type=INVTRANS");
								$.ajax ({
									dataType: "text",
									url: "../../include/th_toInv.php",
									data: { tran: num, type: "INVTRANS" },
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
						else if(x=="CANCEL1" || x=="CANCEL2"){
							var msg = "CANCELLED";
							itmstat = "OK";	
						}

						if(itmstat=="OK"){

								$.ajax ({
									dataType: "text",
									url: "InvTrans_Tran.php",
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

												$("#"+msgid+num).html(item.stat);

												$("#AlertMsg").html("");
													
												$("#AlertMsg").html("&nbsp;&nbsp;<b>" + num + ": </b> Successfully "+msg+"...");
												$("#alertbtnOK").show();
												$("#OK").hide();
												$("#Cancel").hide();
												$("#AlertModal").modal('show');		
												
												if(item.check==1){
													checklevel(num);
												}
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

		function trans(x,num,msg,tocheck){

			$("#typ").val(x);
			$("#modzx").val(num);
			$("#modmsg").val(msg);

			if(tocheck==1){
				checklevel(num);
				$("#mySIRef").modal('show');
			}else{

				$("#AlertMsg").html("");
									
				$("#AlertMsg").html("Are you sure you want to "+x+" Inv Count No.: "+num);
				$("#alertbtnOK").hide();
				$("#OK").show();
				$("#Cancel").show();
				$("#AlertModal").modal('show');

			}
			

		}

		function checklevel(num){    

			$.ajax({
        type: "POST",
        url: "InvTrans_Check.php?x="+num,
        contentType: "application/json; charset=utf-8",
        success: function(result) {
          $("#bdycheck").html(result);
      	}
      });

		}

		function proceed(){
			$("#mySIRef").modal('hide');

			$("#AlertMsg").html("");
									
			$("#AlertMsg").html("Are you sure you want to "+$("#typ").val()+" Inv Count No.: "+$("#modzx").val());
			$("#alertbtnOK").hide();
			$("#OK").show();
			$("#Cancel").show();
			$("#AlertModal").modal('show');
		}

	</script>