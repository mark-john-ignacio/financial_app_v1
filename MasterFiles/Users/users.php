<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "users.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

@$lvlcntA = 0;
@$lvlcntI = 0;

$sqlhead = mysqli_query($con,"Select * from users where Userid<>'Admin'");
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		if($row['cstatus']=="Active"){
			@$lvlcntA++;
		}
		
		if($row['cstatus']=="Inactive"){
			@$lvlcntI++;
		}
	}
}

$sqlhead = mysqli_query($con,"Select code from company where compcode='".$_SESSION['companyid']."'");
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		@$keycode = $row['code']; // 1 = both; 0 = active only
	}
}

@$lvlcnt = 0;
$sqlhead = mysqli_query($con,"Select * from users_license where compcode='".$_SESSION['companyid']."'");
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		@$lvlcnt = $row['value'];
		@$lvlcompute = $row['ccompute']; // 1 = both; 0 = active only
	}
}

@$licval = 0;
$c=base64_decode(@$lvlcnt);
$ivlen=openssl_cipher_iv_length($cipher="AES-128-CBC");
$iv=substr($c,0,$ivlen);
$hmac=substr($c,$ivlen,$sha2len=32);
$ciphertext_raw=substr($c,$ivlen+$sha2len);
$original_plaintext=openssl_decrypt($ciphertext_raw,$cipher,@$keycode,$options=OPENSSL_RAW_DATA,$iv);
$calcmac=hash_hmac('sha256',$ciphertext_raw,@$keycode,$as_binary=true);
if(hash_equals($hmac,$calcmac))// timing attack safe comparison
{
  @$licval = $original_plaintext."\n";
}

if(@$lvlcompute==1){
	@$remain = intval(@$licval) - (intval(@$lvlcntA)+intval(@$lvlcntI));
}else{
	@$remain = intval(@$licval) - intval(@$lvlcntA);
}



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Myx Financials</title>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css"> 
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>

   
  <script type="text/javascript">	
		function resetpass(x){
			document.getElementById("empid").value = x;
			document.getElementById("frmreset").submit();
		}
		
		function sendedit(x){
			document.getElementById("empedit").value = x;
			document.getElementById("frmedit").submit();
		}

		function setstat(x,y){
			document.getElementById("emp").value = x;
			document.getElementById("xz").value = y;
			document.getElementById("frmstat").submit();

		}	
  </script>
  </head>

  <body style="padding:5px">

	<div>
		<section>


		<table border="0" width="100%">
      <tr>                 
        <td width="20%"><font size="+2"><u>Users List</u></font></td>
				<td rowspan="2" align="right" class="text-danger">

				[Users License:<b><?=@$licval?></b> , Total Employees (Active [<b><?=@$lvlcntA?></b>] + Inactive [<b><?=@$lvlcntI?></b>]) = <b><?=intval(@$lvlcntA)+intval(@$lvlcntI)?></b> , Remaining License : <b><?=@$remain?></b> ]

				</td>
			</tr>
			<tr>                 
        <td>
					<?php

						//check user access level sa page
						$employeeid = $_SESSION['employeeid'];	
						$vrdiabled = "";
						$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'users_add.php'");
										
						if(mysqli_num_rows($sql) == 0){
							$vrdiabled = "disabled";
						}

						if(intval(@$remain) <= 0){
							$vrdiabled = "disabled";
						}
					?>
					<button type="button" data-toggle="modal" class="btn btn-primary btn-sm" id="btnadd" name="btnadd" <?=$vrdiabled?>><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>

				</td>
			</tr>
		</table>												

      <br>
                
              <table id="example" class="display">
								<thead>
									<tr>
											
											<th>&nbsp;</th>
										<th>UserID</th>
											<th>Name</th>
											<th>Email</th>
											<th style="text-align: center">Status</th>
											<th style="text-align: center">Actions</th>
											<!--<th>Password</th>-->
									</tr>
								</thead>
								<tbody>
									<?php
										if($_REQUEST['f'] == "search"){
											
											$sql = "select * from users where (Userid like '%$_POST[search]%' or Fname like '%$_POST[search]%' or Lname like '%$_POST[search]%') order by Userid";
										}else{
											$sql = "select * from users order by Userid";
										}
										
										@$allemailadd = array();
										$result=mysqli_query($con,$sql);
										
											if (!mysqli_query($con, $sql)) {
												printf("Errormessage: %s\n", mysqli_error($con));
											} 
										

										while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
										{
											$allowx = "Yes";
											
											if ($row['cuserpic']=="") {
												$imgsrc =  "../../imgusers/emp.jpg";

												
											} else {
												$imgsrc = $row['cuserpic'];

											}

												if($row['Userid']=="Admin" && $_SESSION['employeeid']!="Admin") {
													$allowx = "No";
												}

												if($allowx == "Yes"){

										?>
													<tr>
														<td align="center">
																<img alt="" src="<?php echo $imgsrc; ?>" width="30px" height="30px" />
														</td>
														<td><?php echo $row['Userid'];?></td>
														<td><?php echo $row['Fname']." ".$row['Lname'];?></td>
														<td><?php echo $row['cemailadd'];?></td>
														<td align="center">
															<?php 
																@$allemailadd[] = array('cemailadd' => $row['cemailadd'], 'Userid' => $row['Userid']);

																if ($row['cstatus']=="Active"){
																	echo "<span class='label label-success'>Active</span>";
																}
																elseif ($row['cstatus']=="Inactive"){
																	echo "<span class='label label-danger'>Inactive</span>";
																}
																elseif ($row['cstatus']=="Deactivate"){
																	echo "<span class='label label-danger'>Blocked</span>";
																}
																else{
																	echo "<span class='label label-default'>Status error!</h1></span>";
																}
																
																
															?>
														</td>
														<td align="center" width="150px">
															<a href="javascript:;" id="editusr" name="<?php echo $row['Userid'];?>" onClick="editsrc('<?php echo $row['Userid'];?>')">
																<i class="fa fa-user" style="font-size:20px;color:SlateGrey ;" title="Edit user's details"></i>
															</a>
																&nbsp;
															<a href="javascript:;" onClick="sendedit('<?php echo $row['Userid'];?>')">
																<i class="fa fa-edit" style="font-size:20px;color:SteelBlue ;" title="Edit user's access"></i>
															</a>
																&nbsp;
															<a href="javascript:;" onClick="resetpass('<?php echo $row['Userid'];?>')" class=info>
																<i class="fa fa-refresh" style="font-size:20px;color:green;" title="Reset user password"></i>
															</a>
																&nbsp;
															<?php
																if ($row['cstatus']=="Active"){
															?>
															
															<a href="javascript:;" onClick="setstat('<?php echo $row['Userid'];?>', 'Inactive')" class=info>
																<i class="fa fa-times-circle" style="font-size:20px;color:red;" title="Inactive user access"></i>
															</a>

															<?php
																}
																elseif ($row['cstatus']=="Inactive"){
															?>
															
															<a href="javascript:;" onClick="setstat('<?php echo $row['Userid'];?>', 'Active')" class=info>
																<i class="fa fa-check-circle" style="font-size:20px;color:GoldenRod ;" title="Activate user access"></i>
															</a>
										
															<?php
															}
															?>

														</td>
														<!--<td><a href="" title="<?php //echo base64_decode($row['password']);?>"><?php //echo $row['password'];?></a></td>-->
													</tr>
									<?php 
												}
									}								
									?>
								</tbody>
              </table>

							<input type="hidden" value='<?=json_encode(@$allemailadd)?>' id="hdnemails">
              
    </div> <!-- /container -->

	


	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					
					<h5 class="modal-title" id="myModalLabel"><b>Add New User</b>:  <i>Default password for new user: <b>Password</b></i></h5>
					
				</div>
				<div class="modal-body" style="height: 50vh">
					<div class="err" id="add_err"></div>
				
					<form method="post" name="frmAdd" id="frmAdd" enctype="multipart/form-data" action = "">


						<input type="hidden" name="passT" id="passT" value="Password123">
					
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td rowspan="9" width="150px" valign="top" style="padding-top:10px; padding-left:5px">
															
								<div id="image_preview">
									<img id="previewing" src="../../imgusers/preview.jpg" width="100px" height="100px" style="border-radius: 50%; border: 1px solid black"/>
								</div>
								
								<div class="col-xs-6 nopadwtop2x">
								<label class="btn btn-warning btn-sm">
										Browse&hellip; <input type="file" name="file" id="file" style="display: none;">
								</label>
								</div>
								</td>
								<td><b>UserID: </b></td>
								<td style="padding:2px">
								<div class="col-xs-12">
									<input type="text" class="form-control input-sm" placeholder="User ID..." name="userid" id="userid" required autofocus>
								</div>
								</td>
							</tr>
							<tr>
								<td><b>First Name: </b></td>
								<td style="padding:2px">
								<div class="col-xs-12">
									<input type="text" class="form-control input-sm" placeholder="First Name" name="Fname" id="Fname" required>
								</div>
								</td>
							</tr>
							<tr>
								<td><b>Middle Intial: </b></td>
								<td style="padding:2px">
								<div class="col-xs-5">
										<input type="text" class="form-control input-sm" placeholder="MI" name="Mname" id="Mname">
								</div>
								</td>
							</tr>

							<tr>
								<td><b>Last Name: </b></td>
								<td style="padding:2px">
								<div class="col-xs-12">
										<input type="text" class="form-control input-sm" placeholder="Last Name" name="Lname" id="Lname" required>
								</div>

								</td>
							</tr>

							<tr>
								<td><b>Email Address: </b></td>
								<td style="padding:2px">
								<div class="col-xs-12">
									<input type="text" class="form-control input-sm" placeholder="Email Address..." name="emailadd" id="emailadd" required >
								</div>

								</td>
							</tr>

							<tr>
								<td><b>Department: </b></td>
								<td style="padding:2px">
								<div class="col-xs-12">
									<input type="text" class="form-control input-sm" placeholder="Department..." name="cdept" id="cdept" required >
								</div>

								</td>
							</tr>

							<tr>
								<td><b>Designation: </b></td>
								<td style="padding:2px">
								<div class="col-xs-12">
									<input type="text" class="form-control input-sm" placeholder="Designation..." name="cdesig" id="cdesig" required >
								</div>

								</td>
							</tr>
							<tr>
								<td><b>User Type: </b></td>
								<td style="padding:2px">
								<div class="col-xs-12">
									<select name="usertype" id="usertype" class="form-control input-sm">
										<option value="ADMIN">ADMIN</option>
										<option value="CASHIER">CASHIER</option>
									</select>
								</div>
								</td>
							</tr>
							<tr>
								<td><b>Signature: </b></td>
								<td style="padding:2px">
								<div class="col-xs-12">
									<input type="file" name="filsign" id="filsign" value="">
								</div>
								<div class="col-xs-12" id="imgsignuser">
									
								</div>
								</td>
							</tr>
					</table>
				

					</form>
					
				</div>
				
				<div class="modal-footer">
							<input type="hidden" name="hdnmodtype" id="hdnmodtype" value="" />
									
									<button type="button" id="btnSave" name="Save" class="btn btn-primary">Add User</button>
									<button type="button" id="btnUpdate" name="Update" class="btn btn-success">Update</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->		




<?php
	
				mysqli_close($con);
?>




<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
<script>
	$('#example').DataTable();
	$("#add_err").css('display', 'none', 'important');
	
	
	$(function(){
		// Adding new user
		$('#editpass').on('click', function (){
			
		});


		$("#btnadd").on("click", function() {
			$("#hdnmodtype").val("Add");
			$("#btnSave").show();
			$("#btnUpdate").hide();

			$("#userid").attr('readonly',false);
						
			$("#userid").val("");
			$("#Fname").val("");
			$("#Mname").val("");
			$("#Lname").val("");
			$("#emailadd").val("");
			$("#file").val("");
			$('#previewing').attr('src','../../imgusers/preview.jpg');
			
			
			$('#myModalLabel').html("<b>Add New User</b>:  <i>Default password for new user: <b>Password</b></i>");
			$('#myModal').modal('show');
		});
		
		//Check UserID while typing
		$("#userid").on("keyup", function() {
			var x = $(this).val();
			
			if(x != "") {
				$.ajax({
				type:'post',
					url:'users_chkID.php',// put your real file name 
					data:{id: x},
					success:function(msg){
						if(msg !=""){
							$("#add_err").css('display', 'inline', 'important');
							$("#add_err").html("<div class='alert alert-danger' role='alert'>&nbsp;&nbsp;" + msg + "</div>");
						}
						else{
							$("#add_err").css('display', 'none', 'important');
						}
					}
				});
			}
			
		});
		
		//Check new user id
		$("#userid").on("blur", function () {
			
			var x = $(this).val();

			if(x != "" && $("#hdnmodtype").val()=="Add") {
				$.ajax({
				type:'post',
					url:'users_chkID.php',// put your real file name 
					data:{id: x},
					success:function(msg){
						if(msg.trim()!=""){
							$("#add_err").css('display', 'none', 'important'); // your message will come here. 
							$("#userid").val("").change();
							$("#userid").focus(); 
						}
					}
				});
			}
		});
				
		//Save NEW User
		$("#btnSave").on("click", function(e) {
			
			if (validateEmail($("#emailadd").val())) {

				xvar = $("#emailadd").val();
				var xz = $("#hdnemails").val();
				var validemail = "";
				$.each(jQuery.parseJSON(xz), function() { 
					if(xvar==this['cemailadd'] && this['Userid']!==$("#userid").val()){
						$("#add_err").css('display', 'inline', 'important');
						$("#add_err").html("<div class='alert alert-danger' role='alert'>&nbsp;&nbsp;Email Address already exist for "+this['Userid']+"</div>");

						$("#emailadd").val("").change();
						$("#emailadd").focus(); 

						validemail = "No";
						return false;
					}
				});


				if(validemail==""){
			
					var fd = document.getElementById("frmAdd");
					var formData = new FormData(fd);
										
					$.ajax({
						type: 'post',
						url: 'users_add.php',
						data: formData,
						contentType: false,
						processData: false,
						async:false,
						success: function (data) {
							alert(data);
								
							$('#myModal').modal('hide');
							location.reload();
						}
					});
				}
			}
			else {
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Invalid email address!</div>");
			}	  
		  
		});
		
		
		//Edit user Detail

		//Save UPDATE on users Details
		$("#btnUpdate").on("click", function() {
			 var numz = 0;
				$('input[type=text]').each(function(){

				   if (this.value == "" && this.id!=="Mname") {
					   numz = numz + 1;
						$("#add_err").css('display', 'inline', 'important');
						$("#add_err").html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Complete the form</div>");
				   } 
				})
		
	
			if(parseInt(numz)==0){
				if (validateEmail($("#emailadd").val())) {


				var fd = document.getElementById("frmAdd");
				var formData = new FormData(fd);

					$.ajax({
						type: 'post',
						url: 'users_update.php',
						data: formData,
   					    contentType: false,
						processData: false,
						async:false,
						success: function (data) {
						  alert(data);
						  
						  $('#myModal').modal('hide');
						  location.reload();
						}
					});

					
				}
				else {
					$("#add_err").css('display', 'inline', 'important');
					$("#add_err").html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Invalid email address!</div>");
				}
			}
			
		});
		
		//Checking of uploaded file.. must be image
		$("#file").change(function() {
			$("#add_err").empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
				$('#previewing').attr('src','../../imgusers/preview.jpg');
				
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<div class='alert alert-danger' role='alert'>Please Select A valid Image File. <b>Note: </b>Only jpeg, jpg and png Images type allowed</div>");
				return false;
			}
			else
			{
				var reader = new FileReader();
				reader.onload = imageIsLoaded;
				reader.readAsDataURL(this.files[0]);
			}
		});

			
	});
	
	$(document).keydown(function(e) {	
		 
		 if(e.keyCode == 112) { //F1
			 e.preventDefault();
			 $("#btnadd").click();
		 }
	 });
	 
	//preview of image
	function imageIsLoaded(e) {
		$("#file").css("color","green");
		$('#image_preview').css("display", "block");
		$('#previewing').attr('src', e.target.result);
		$('#previewing').attr('width', '100px');
		$('#previewing').attr('height', '100px');
	};


	//check if valid email
	function validateEmail(sEmail) {
		var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
		if (filter.test(sEmail)) {
		return true;
		}
		else {
		return false;
		}
	}

	

	function editsrc($xcv){
		$("#hdnmodtype").val("Edit");
			
			$.ajax({
				url: 'users_getdetail.php',
				data: 'id='+$xcv,
				dataType: 'json',
				method: 'post',
				async:false,
				success: function (data) {
					 console.log(data);
										 $.each(data,function(index,item){
					
						$("#userid").val(item.id);
						$("#Fname").val(item.fname);
						$("#Mname").val(item.mname);
						$("#Lname").val(item.lname);
						$("#emailadd").val(item.emailadd);
						$("#cdept").val(item.cdepartment);
						$("#cdesig").val(item.cdesignation);
						$("#usertype option").each(function(){	
							if ($(this).val() === item.usertype) {
								return $(this).attr("selected", "selected"); 
							} 
						});
						
						$('#previewing').attr('src',item.imgsrc);

						if(item.signsrc!=="" && item.signsrc!==null){
							$('#imgsignuser').html("<a href='javascript:;' title='Click to remove image' onclick='potransset('sign','"+item.id+"')'><img src = '"+item.signsrc+"' height='50px' alt='Click to remove image'></a>");
						}else{
							$('#imgsignuser').html("");
						}
						
					 });
					 $("#btnSave").hide();
					 $("#btnUpdate").show();
					 
					 $("#userid").attr('readonly',true);
					 
					 
					 $('#myModalLabel').html("<b>Update User Details</b>");
					 $('#myModal').modal('show');

				}
			});
	}

</script>
    
<form method="post" action="users_access.php" name="frmedit" id="frmedit"> 
	<input type="hidden" name="empedit" id="empedit" value="">
</form>

<form method="post" action="users_reset.php" name="frmreset" id="frmreset">
	<input type="hidden" name="empid" id="empid" value="">
    <input type="hidden" name="x" id="x" value="">
</form>

<form method="post" action="users_status.php" name="frmstat" id="frmstat">
	<input type="hidden" name="emp" id="emp" value="">
    <input type="hidden" name="xz" id="xz" value="">
</form>

  </body>
</html>
