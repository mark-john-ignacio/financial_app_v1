<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "users.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coop Financials</title>

    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css"> 
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>

   
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

  <body style="padding-top:5px">
    
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Users List</u></font>	
            </div>
        </div>
			<br><br>
                <?php

					//check user access level sa page
					$employeeid = $_SESSION['employeeid'];	
					$vrdiabled = "";
					$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'users_add.php'");
									
					if(mysqli_num_rows($sql) == 0){
						$vrdiabled = "disabled";
					}
				
				?>
           
           			 <button type="button" data-toggle="modal" class="btn btn-primary btn-md" id="btnadd" name="btnadd" <?php echo $vrdiabled; ?>><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
													

      <br><br>
                
              <table id="example" class="display">
              <thead>
              	<tr>
                    
                    <th>&nbsp;</th>
                	<th>UserID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
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
				
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
				

				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					
					if ($row['cuserpic']=="") {
						$imgsrc =  "../imgusers/emp.jpg";

						
					} else {
						$imgsrc = "../imgusers/".$row['cuserpic'];

					}

				?>
              	<tr>
                	<td align="center">
                    	<img alt="" src="<?php echo $imgsrc; ?>" width="30px" height="30px" />
                  </td>
                	<td><?php echo $row['Userid'];?></td>
                    <td><?php echo $row['Fname']." ".$row['Lname'];?></td>
                    <td><?php echo $row['cemailadd'];?></td>
                    <td><?php 
					if ($row['cstatus']=="Active"){
						echo "<span class='label label-success'>Active</span>";
					}
					elseif ($row['cstatus']=="Inactive"){
						echo "<span class='label label-danger'>Inactive</span>";
					}
					else{
						echo "<span class='label label-default'>Status error!</h1></span>";
					}
					
					
					?></td>
                     <td align="center" width="150px">
                    <a href="javascript:;" id="editusr" name="<?php echo $row['Userid'];?>" class="usredit">
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
				
				
				?>
              </tbody>
              </table>
              
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
      <div class="modal-body">
         <div class="err" id="add_err"></div>
      
      	<form method="post" name="frmAdd" id="frmAdd" enctype="multipart/form-data" action = "">


        <input type="hidden" name="passT" id="passT" value="Password">
        
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td rowspan="5" width="150px" valign="top" style="padding-top:10px; padding-left:5px">
            	            
            <div id="image_preview">
            	<img id="previewing" src="../imgusers/preview.jpg" width="100px" height="100px"/>
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
                <input type="text" class="form-control input-sm" placeholder="MI" name="Mname" id="Mname" required>
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




    <link rel="stylesheet" type="text/css" href="../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$('#example').DataTable();
	$("#add_err").css('display', 'none', 'important');
	
	
	$(function(){
		// Adding new user
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
			$('#previewing').attr('src','../imgusers/preview.jpg');
			
			
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
			
		 var numz = 0;
			$('input[type=text]').each(function(){
			   if (this.value == "") {
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
			else {
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Invalid email address!</div>");
			}
		}
			
		  
		  
		});
		
		
		//Edit user Detail
		$(".usredit").on("click", function() {
			//alert('users_getdetail.php?id='+$(this).attr('name'));
			$("#hdnmodtype").val("Edit");
			
				$.ajax({
					url: 'users_getdetail.php',
					data: 'id='+$(this).attr('name'),
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
							
							$('#previewing').attr('src',item.imgsrc);
							
					   });
					   $("#btnSave").hide();
					   $("#btnUpdate").show();
					   
					   $("#userid").attr('readonly',true);
					   
					   
					   $('#myModalLabel').html("<b>Update User Details</b>");
					   $('#myModal').modal('show');

					}
				});
				
				

		});	
		
		//Save UPDATE on users Details
		$("#btnUpdate").on("click", function() {
			 var numz = 0;
				$('input[type=text]').each(function(){

				   if (this.value == "") {
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
				$('#previewing').attr('src','../imgusers/preview.jpg');
				
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
