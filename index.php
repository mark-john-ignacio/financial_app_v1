<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<META NAME="robots" CONTENT="noindex,nofollow">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MYX Financials</title>
	<link href="Bootstrap/css/NFont.css" rel="stylesheet">
	<link href="global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="Bootstrap/css/bootstrap.css?t=<?php echo time();?>">

	<script src="Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="Bootstrap/js/bootstrap.js"></script>

	<style type="text/css">
		body {
			font-family: 'Varela Round', sans-serif;
		}
		.modal-login {		
			color: #636363;
			width: 350px;
		}
		.modal-login .modal-content {
			padding: 20px;
			border-radius: 5px;
			border: none;
		}
		.modal-login .modal-header {
		border-bottom: none;   
		position: relative;
		justify-content: center;
		}
		.modal-login .form-control:focus {
			border-color: #70c5c0;
		}
		.modal-login .form-control, .modal-login .btn {
			min-height: 40px;
			border-radius: 3px; 
		}
		.modal-login .modal-footer {
			text-align: center;
		}	
		.modal-login.modal-dialog {
			margin-top: 80px;
		}
		.modal-login .btn {
					color: #fff;
					border-radius: 4px;
			background: #0089cb;
			text-decoration: none;
			transition: all 0.4s;
					line-height: normal;
					border: none;
			}
		.modal-login .btn:hover, .modal-login .btn:focus {
			background: #0373a9;
			outline: none;
		}
		.trigger-btn {
			display: inline-block;
			margin: 100px auto;
		}


	</style>
</head>
<body>
<!-- Modal HTML -->
<div id="myModal">
	<div class="modal-dialog modal-login">
		<div class="modal-content" >
			<div class="modal-header"><center>
        <div class="logo">			
					<img src="images/LogoNew.png" width="90" height="120">	
        </div></center>
			</div>
			<div class="modal-body" style="height: 40vh">
				<form action="index.php" method="post">
					<div class="form-group">
						<?php
							include('Connection/connection_string.php');
							$sqlhead = mysqli_query($con,"select * From company");

						?>
						<select class="form-control" name="selcompany" id="selcompany">
							<?php
								while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
							?>
								<option value="<?php echo $row['compcode'];?>"><?php echo $row['compname'];?></option>
							<?php
								}
							?>
						</select>

					</div>

					<div class="form-group">
						<input type="text" class="form-control" name="employeeid" id="employeeid" placeholder="Username" required value="" autocomplete="off">		
					</div>
                    
					<div class="form-group">
						<input type="password" class="form-control" name="inputPassword" id="inputPassword" placeholder="Password" required  value=""  autocomplete="off">	
					</div>
                     
                     
                   <div class="form-group" id="add_err">
						
				   
                   </div>

                         
					<div class="form-group">
						<button type="button" class="btn btn-primary btn-lg btn-block login-btn" id="btnLogin" name="btnLogin">Login</button>
                        
                        <input type="hidden" id="selcat" name="selcat" value="admin">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<a href="forgotpassword.php">Forgot Password</a>
			</div>
		</div>
	</div>
</div>     
</body>
</html>

<script type="text/javascript">

$(document).ready(function(){
    
	$("#add_err").css('display', 'none', 'important'); 
	//$("#userpic").css('display', 'none', 'important'); 
	
    $("#btnLogin").click(function(){  

			if(document.getElementById("employeeid").value == "" || document.getElementById("inputPassword").value == ""){
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Complete the form</div>");
			}else{


			  employeeid=$("#employeeid").val();
			  password=$("#inputPassword").val();
			  selcat = $("#selcompany").val();
				
			  $.ajax({
			   type: "POST",
			   url: "include/employeelogin.php?",
			   data: "employeeid="+employeeid+"&password="+password+"&selcompany="+selcat,
			   success: function(html){   
			   //alert(html); 
				if(html==1)    {
				 
					 window.location="main.php";

				}
				else    {
				 $("#add_err").css('display', 'inline', 'important');
				 $("#add_err").html("<div class='alert alert-danger' role='alert'>"+html+"</div>");
				}
			   },
			   beforeSend:function()
			   {
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<center><img src='images/loader.gif' width='50' height='50' /></center>")
			   }
			  });
			return false;
		
		}
    });
	
	$("#employeeid").on('blur', function() {
		
		if($(this).val() != "") {
			$.ajax({
			   type: "POST",
			   url: "include/checkuser.php?",
			   data: "id="+$(this).val()+"&xpass="+$("#inputPassword").val(),
			   success: function(html){ 

			   	if(html == 1){
				}
				else{
					$("#add_err").css('display', 'inline', 'important');
					$("#add_err").html("<div class='alert alert-danger' role='alert'>"+html+"</div>");
					
					$("#employeeid").val("");
					$("#employeeid").focus();
				}
				
			   }
			});
		}

	});
		
});

</script>
