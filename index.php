<?php

	if(!isset($_SESSION)){
		session_start();
	}
	
	include('Connection/connection_string.php');
  	require_once('Model/helper.php');

	 if(isset($_SESSION['login']) || isset($_SESSION["id"])) {
		header("Location: ./main.php");
		//passing the value when the login button is click
		$employeeid = isset($_SESSION['employeeid']) ? $_SESSION['employeeid'] : '';
		$session_id = isset($_SESSION['session_id']) ? $_SESSION['session_id'] : '';
		$companyid = isset($_SESSION['companyid']) ? $_SESSION['companyid'] : $selcompany;
		exit();
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
						<!-- add validate password input function for password requirements and length is 15 --> 
						<input type="password" class="form-control" name="inputPassword" id="inputPassword" placeholder="Password" required  value=""  autocomplete="off" maxlength="15">	
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
	
    
	//showing modal for changing password in 30 days
	$('#view').on('click', function(){
		$('#changeModal').modal('show');
	})

	
	$("#add_err").css('display', 'none', 'important'); 
	//$("#userpic").css('display', 'none', 'important'); 


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
						alert(res.msg)
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
						alert(res.errMsg)
						//ADDING SHOW MODAL AGAIN BECAUSE OF BUGS WHEN SHOWING ERROR
						$('#changeModal').modal('show');
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
			//ADD SHOW MODAL
			$('#changeModal').modal('show');
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
					$("#add_err").html("<center><img src='images/loader.gif' width='50' height='50' margin-bottom='30%' /></center>")
				},
				success: function(res){   
				//alert(html);
				console.log(res) 
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

function validatePassword() {
    var password = document.getElementById("inputPassword").value;
	var changepassword = document.getElementById("changePass").value;
	var confirmpassword = document.getElementById("confirmChange").value;
	
    var regex = /^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]{8,15}$/;
    var errorMessage = $("#add_err");

    if (password === "") {
        errorMessage.css('display', 'inline', 'important');
        errorMessage.html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Password is required</div>");
    } else if (!regex.test(password)) {
        errorMessage.css('display', 'inline', 'important');
        errorMessage.html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Password must contain a combination of alphabetic and numeric characters and be 8-15 characters long.</div>");
    } else {
        errorMessage.hide(); // Hide any previous error messages
    }
}

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

//creating a window on load function to call the code
$(window).on('load', function() {

	//getting cookiename to decode and set the variable
    var employeeid_cookie = getCookie('employeeid');
    var session_id_cookie = getCookie('session_id');
    var companyid_cookie = getCookie('companyid');
	
    function getCookie(cookieName) {
        var name = cookieName + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var cookieArray = decodedCookie.split(';');
        for (var i = 0; i < cookieArray.length; i++) {
            var cookie = cookieArray[i];
            while (cookie.charAt(0) == ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(name) == 0) {
                return cookie.substring(name.length, cookie.length);
            }
        }
        return "";
    }
	//if cookies is not null perform an ajax
    if (employeeid_cookie !== "" && session_id_cookie !== "" && companyid_cookie !== "") {
        console.log("Cookies are set. Proceeding with AJAX request...");
        console.log("Employee ID: " + employeeid_cookie);
        console.log("Session ID: " + session_id_cookie);
        console.log("Company ID: " + companyid_cookie);
        $.ajax({
            type: "post",
            url: "include/employeelogin.php",
            data: {
                from_window_load: true,
                employeeid: employeeid_cookie,
                session_id: session_id_cookie,
                companyid: companyid_cookie
            },
            dataType: 'json',
            success: function(response) {
				console.log(response);
                try {
                    if (response && response.success) {
                        console.log("Login successful. Redirecting...");
                        window.location.href = response.redirect_url;
                    } else {
                        console.error("Login failed. Error message:", response.message);
                        // Display error message to the user
                    }
                } catch (error) {
                    console.error("Error parsing JSON response:", error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error occurred during AJAX request:", error);
                console.log("XHR object:", xhr);
                console.log("Status:", status);
            }
        });
    } else {
        console.error("Required cookies are not set.");
    }
});


</script>