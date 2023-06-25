<!DOCTYPE html>
<?php

	require("vendor/phpmailer/phpmailer/src/PHPMailer.php");
	require("vendor/phpmailer/phpmailer/src/Exception.php");
  require("vendor/phpmailer/phpmailer/src/SMTP.php");

if(!isset($_SESSION)){
session_start();
}
require_once "Connection/connection_string.php";

?>

<html lang="en">
<head>
<meta charset="utf-8">
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
		background: #ecf0f1;
		border-color: #dee4e7;
		text-align: center;
        justify-content: center;
		margin: 0 -20px -20px;
		border-radius: 5px;
		font-size: 13px;
	}
	.modal-login .modal-footer a {
		color: #999;
	}		
	.modal-login .avatar {
		position: absolute;
		margin: 0 auto;
		left: 0;
		right: 0;
		top: -70px;
		width: 95px;
		height: 95px;
		border-radius: 50%;
		z-index: 9;
		background: #DDF3FF;
		padding: 15px;
		box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
	}
	.modal-login .logo {
		position: absolute;
		margin: 0 auto;
		left: 0;
		right: 0;
		top: -30px;
		width: 130px;
		height: 150px;
		z-index: 9;
		padding: 15px;
		border-radius: 50%;
		/*background: #FFFFFF;
		box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);*/
		
	}
	.modal-login .avatar img {
		width: 100%;
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
	.modal-body{
		top: 70px;
	}
</style>
</head>
<body>
<!-- Modal HTML -->
<div id="myModal">
	<div class="modal-dialog modal-login">
		<div class="modal-content">
			<div class="modal-header">
                <div class="logo">			
					<img src="images/LogoNew.png" width="90" height="120">	
                </div>

			</div>
			<div class="modal-body">
				
<?php
if(isset($_POST["email"]) && (!empty($_POST["email"]))){
$email = $_POST["email"];
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$email = filter_var($email, FILTER_VALIDATE_EMAIL);
$error = "";

	 if (!$email) {
   		$error .="<p>Invalid email address please type a valid email address!</p>";
   }else{
   		$sel_query = "SELECT * FROM `users` WHERE cemailadd='".$email."'";
  	 	$results = mysqli_query($con,$sel_query);
   		$row = mysqli_num_rows($results);
   if ($row==""){
   		$error .= "<p>No user is registered with this email address!</p>";
   }
  }
   if($error!=""){

?>
<form action="forgotpassword.php" method="post">
					<div class="form-group">
						<input type="text" class="form-control" name="email" id="email" placeholder="Email Address..." required value="" autocomplete="off">		
					</div> 
                     
                   <div class="form-group text-center" id="add_err">
						
				   						<?php echo $error; ?>
                   </div>

                         
					<div class="form-group">
						<button type="submit" class="btn btn-warning btn-lg btn-block login-btn" id="btnLogin" name="btnLogin">Reset Password</button>
                        
            <input type="hidden" id="selcat" name="selcat" value="admin">
					</div>
</form>
<?php
}else{
   $expFormat = mktime(date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y"));
   $expDate = date("Y-m-d H:i:s",$expFormat);
   $key = md5((2418*2).$email);
   $addKey = substr(md5(uniqid(rand(),1)),3,10);
   $key = $key . $addKey;
	// Insert Temp Table
		mysqli_query($con,"INSERT INTO `password_reset_temp` (`email`, `key`, `expDate`) VALUES ('".$email."', '".$key."', '".$expDate."');");
	 
	  $output='<p>Dear user,</p>';
	  $output.='<p>Please click on the following link to reset your password.</p>';
	  $output.='<p>-------------------------------------------------------------</p>';
	  $output.='<p><a href="https://'.$_SERVER['HTTP_HOST'].'/include/reset-password.php?key='.$key.'&email='.$email.'&action=reset" target="_blank">Reset Password</a></p>'; 
		$output.='<p>-------------------------------------------------------------</p>';
		$output.='<p>The link will expire after 1 day for security reason.</p>';
		$output.='<p>If you did not request this forgotten password email, no action 
		is needed, your password will not be reset. However, you may want to log into 
		your account and change your security password as someone may have guessed it.</p>';   
		$output.='<p>Thanks,</p>';
		$output.='<p>Myx Financials,</p>';
		$body = $output; 
		$subject = "Myx Financials - Password Recovery";
 
		$email_to = $email;
		$fromserver = "myxfin@serttech.com"; 

		//use PHPMailer\PHPMailer\PHPMailer; 	
		//use PHPMailer\PHPMailer\Exception;

		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPDebug = 2;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAutoTLS = false;
		$mail->Host = "ssl://mail.serttech.com"; // Enter your host here
		$mail->Port = 587;

		$mail->IsHTML(true);
		$mail->Username = "myxfin@serttech.com"; // Enter your email here
		$mail->Password = "Sert@2022"; //Enter your password here
		$mail->From = "noreply@serttech.com";
		$mail->FromName = "Myx Financials";
		$mail->Sender = "myxfin@serttech.com"; // indicates ReturnPath header
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AddAddress($email_to);
		if(!$mail->Send()){
			$error = 'Mail error: '.$mail->ErrorInfo;

			return false;
		}else{

?>	
			<form action="index.php" method="post">
					<div class="form-group">
						An email has been sent to you with instructions on how to reset your password.		
					</div> 
                         
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-lg btn-block login-btn" id="btnLogin" name="btnLogin">Go To Login</button>
					</div>
				</form>

<?php
 }
   }
}else{
?>

	
				<form action="forgotpassword.php" method="post">
					<div class="form-group">
						<input type="text" class="form-control" name="email" id="email" placeholder="Email Address..." required value="" autocomplete="off">		
					</div> 
                     
                   <div class="form-group" id="add_err">
						
				   
                   </div>

                         
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-lg btn-block login-btn" id="btnLogin" name="btnLogin">Reset Password</button>
                        
            <input type="hidden" id="selcat" name="selcat" value="admin">
					</div>
				</form>

<?php
					}
?>

			</div>

		</div>
	</div>
</div>     
</body>
</html>
