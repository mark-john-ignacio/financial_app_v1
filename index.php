<?php

	if(!isset($_SESSION)){
		session_start();
	}
	include('Connection/connection_string.php');
  	require_once('Model/helper.php');

	if(isset($_SESSION['login'])){
		header("Location: //".$_SERVER['HTTP_HOST']."/myxfin_st/main.php");
	}

?>

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
			margin-top: 60px;
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
			<div class="modal-body" style="height: 50vh">
				<form action="index.php" method="post">
					<div class="form-group">
						<?php
							$sqlhead = mysqli_query($con,"select * From company");

						?>
						<select class="form-control" name="selcompany" id="selcompany">
							<?php
								while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
							?>
								<option value="<?php echo $row['compcode'];?>" selected><?php echo $row['compname'];?> </option>
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

<div class="modal fade" id="changeModal" tabindex="-1" role="dialog" aria-labelledby="changeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">

					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					
					<h5 class="modal-title" id="myModalLabel"><b>Change Password</b>: <br> <i> 30 days have pass since your last password Change. Insert a new password. </i></h5>
					
				</div>
				<div class="modal-body" style="height: 30vh">
					<form method="post" name="frmpos" id="frmpos" >

						<div class='form-group'>
							<label for='uid' >New Password: </label>
								<input type='password' class='form-control' name='changePass' id='changePass' placeholder="New Password" autocomplete="off"/>
						</div>
						<div class='form-group'>
								<label for='uid' >Confirm Password: </label>
								<input type='password' class='form-control' name='confirmChange' id='confirmChange' placeholder="Confirm Password" autocomplete="off"/>
						</div>
						<div class='form-group'>
							<div class="col-xs-12 " id="warning" style="display: none">
								<div id="alphabettxt"><span id="alphabet"></span> Must have a Alphabetical characters! </div>
								<div id="numerictxt"><span id="numeric"></span> Must have a Numberic characters!</div>
								<div id="stringlentxt"><span id="stringlen"></span> Minimum of 8 characters! </div>
							</div>
						</div>
					</form>
				</div>
				
				<div class="modal-footer">
							<input type="hidden" name="hdnmodtype" id="hdnmodtype" value="" />
							<button type="button" id="update" name="update" class="btn btn-primary">Change Password</button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

<script type="text/javascript">
var warnings = { alpha: false, numeric: false, stringlen: false };
var attempts = 1;
$(document).ready(function(){
    
	
	$("#add_err").css('display', 'none', 'important'); 
	//$("#userpic").css('display', 'none', 'important'); 
	$('#view').on('click', function(){
		$('#changeModal').modal('show');
	})

	$('#update').on('click', function(){

		var newpass = $('#changePass').val();
		var confirm = $('#confirmChange').val();
		

		const validateNew = PasswordValidation(newpass)
		const validateConfirm = PasswordValidation(newpass)
		if(validateNew && validateConfirm){
			$.ajax({
				url: 'MasterFiles/user_change_pass.php',
				type:'post',
				data: {
					id: $('#employeeid').val(),
					password: $('#inputPassword').val(),
					newpassword: newpass, 
					confirmPassword: confirm
				},
				dataType: 'json',
				async: false,
				success: function(res){
					if(res.valid){
						alert('<strong>'+ res.msg +'</strong>')
						switch(res.usertype){
							case "ADMIN":
								window.location="main.php";
								break;
							case "CASHIER":
								window.location="POS/index.php";
								break;
							default: 
								window.location="main.php";
								break;
						}
					} else {
						alert("<strong>"+res.errCode+": </strong>" + res.errMsg)
					}
				}
			})
		} else {
			$('#warning').css('display', 'block')
			$('#alphabet').html("<i " + (!warnings.alpha ?  "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i> ");
			$('#alphabettxt').css('color', ( !warnings.alpha ? '#FF0000' : '#000000' ))

			$('#numeric').html("<i " + ( !warnings.numeric ? "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i> ");
			$('#numerictxt').css('color', ( !warnings.numeric ? '#FF0000' : '#000000' ))

			$('#stringlen').html("<i " + ( !warnings.stringlen ? "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i>");
			$('#stringlentxt').css('color', ( !warnings.stringlen ?  '#FF0000' : '#000000' ))
		}
	})
	// $.ajax({
	// 	url: 'th_checkUser.php',
	// 	async: false,
	// 	success: function(data){
	// 		if(data){
	// 			location.replace('main.php')
	// 		}
	// 	}
	// })

	
    $("#btnLogin").click(function(){  

			if(document.getElementById("employeeid").value == "" || document.getElementById("inputPassword").value == ""){
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Complete the form</div>");
			}else{


			  employeeid=$("#employeeid").val();
			  password=$("#inputPassword").val();
			  selcat = $("#selcompany").val();
			  const login = {
				id: $("#employeeid").val(),
				password: $("#inputPassword").val(),
				company: $("#selcompany").val()
			  }
				
			  $.ajax({
			   type: "POST",
			   url: "include/employeelogin.php?",
			   data: {
				employeeid: login.id,
				password: login.password,
				selcompany: login.company,
				attempts: attempts
			   },
			   dataType: 'json',
			   beforeSend:function(){
					attempts += 1;
					$("#add_err").css('display', 'inline', 'important');
					$("#add_err").html("<center><img src='images/loader.gif' width='50' height='50' /></center>")
					console.log(attempts)
			   },
			   success: function(res){   
			   //alert(html); 
				if(res.valid)    {
					if(res.proceed){
						switch(res.usertype){
							case "ADMIN":
								window.location="main.php";
								break;
							case "CASHIER":
								window.location="POS/index.php";
								break;
							default: 
								window.location="main.php";
								break;
						}
						
					} else {
						$('#changeModal').modal('show');
					}
					 
				} else {
					$("#add_err").css('display', 'inline', 'important');
					$("#add_err").html("<div class='alert alert-danger' role='alert'> "+res.errMsg+"</div>");
				}
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

function attempts({id, password, company}){
	$.ajax({
		url: "include/user_restriction.php",
		type: "post",
		data: {
			id: id, 
			password: password,
			company: company
		},
		success: function(data){
			$("#add_err").css('display', 'inline', 'important');
			$("#add_err").html("<div class='alert alert-danger' role='alert'>"+data+"</div>");
		},
		error: function(data){
			$("#add_err").css('display', 'inline', 'important');
			$("#add_err").html("<div class='alert alert-danger' role='alert'>"+data+"</div>");
		}
	})
}

/**
 * Validation For Password
 */
function AlphabetFilter(password){
	var filter = /^(?=.*[a-zA-Z])/;
	return filter.test(password)
}
function NumericFilter(password){
	var filter = /(?=.*[0-9])/;
	return filter.test(password);
	}

function PasswordLimit(inputs){
	return inputs.length >= 8;
}

function PasswordValidation(inputs){
	warnings['alpha'] = AlphabetFilter(inputs)
	warnings['numeric'] = NumericFilter(inputs)
	warnings['stringlen'] = PasswordLimit(inputs)

	return warnings['alpha'] && warnings['numeric'] && warnings['stringlen'];
}	


</script>
