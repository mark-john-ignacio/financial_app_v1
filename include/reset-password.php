<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

function better_crypt($input, $rounds = 10) { 

	$crypt_options = array( 'cost' => $rounds ); 
	return password_hash($input, PASSWORD_BCRYPT, $crypt_options); 

}

?>


<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MYX Financials</title>
<link href="../Bootstrap/css/NFont.css" rel="stylesheet">
<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">

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

<?php
$error = "";
if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) 
&& ($_GET["action"]=="reset") && !isset($_POST["action"])){
  $key = $_GET["key"];
  $email = $_GET["email"];
  $curDate = date("Y-m-d H:i:s");
  $query = mysqli_query($con,
  "SELECT * FROM `password_reset_temp` WHERE `key`='".$key."' and `email`='".$email."';"
  );
  $row = mysqli_num_rows($query);
  if ($row==""){
?>
		<div id="myModal">
			<div class="modal-dialog modal-login">
				<div class="modal-content">
					<div class="modal-header">
		                <div class="logo">			
											<img src="../images/LogoNew.png" width="90" height="120">	
		                </div>

					</div>
					<div class="modal-body text-center">


					  <h2>Invalid Link</h2>
						<p>The link is invalid/expired. Either you did not copy the correct link
						from the email, or you have already used the key in which case it is 
						deactivated.</p>
						<p><a href="https://<?=$_SERVER['HTTP_HOST']?>/forgotpassword.php">
						Click here</a> to reset password.</p>

					</div>
				</div>
			</div>
		</div>
<?php
 }else{
  $row = mysqli_fetch_assoc($query);
  $expDate = $row['expDate'];
  if ($expDate >= $curDate){
  ?>
<div id="myModal">
	<div class="modal-dialog modal-login">
		<div class="modal-content">
			<div class="modal-header">
                <div class="logo">			
					<img src="../images/LogoNew.png" width="90" height="120">	
                </div>

			</div>
			<div class="modal-body">


			  <form method="post" action="" name="update">
				  <input type="hidden" name="action" value="update" />
					<div class="form-group">
				  	<input type="password" class="form-control form-control-sm" name="pass1" maxlength="15" required placeholder="Enter New Password..."/>
				  </div>
					<div class="form-group">
					<input type="password" class="form-control form-control-sm" name="pass2" maxlength="15" required placeholder="Re-Enter New Password..."/>
					</div>
				  <br /><br />
				  <input type="hidden" name="email" value="<?php echo $email;?>"/>
				  <input type="submit" value="Reset Password" class="btn btn-warning btn-lg btn-block login-btn" />
			  </form>

			</div>
		</div>
	</div>
</div>
<?php
}else{
?>

	<div id="myModal">
			<div class="modal-dialog modal-login">
				<div class="modal-content">
					<div class="modal-header">
		                <div class="logo">			
											<img src="../images/LogoNew.png" width="90" height="120">	
		                </div>

					</div>
					<div class="modal-body text-center">


					  <h2>Link Expired</h2>
<p>The link is expired. You are trying to use the expired link which 
as valid only 24 hours (1 day after request).<br /><br /></p>

					</div>
				</div>
			</div>
		</div>

<?php

            }
      }
} // isset email key validate end
 
 
if(isset($_POST["email"]) && isset($_POST["action"]) && ($_POST["action"]=="update")){
	$error="";
	$pass1 = mysqli_real_escape_string($con,$_POST["pass1"]);
	$pass2 = mysqli_real_escape_string($con,$_POST["pass2"]);
	$email = $_POST["email"];
	$curDate = date("Y-m-d H:i:s");
	if ($pass1!=$pass2){
		$error = "<p>Password do not match, both password should be same.<br /><br /></p>";
	}
  if($error!=""){
		//echo "<div class='error'>".$error."</div><br />";
	}
	else{
		$cPass_hash = better_crypt($pass1);

		mysqli_query($con,"UPDATE `users` SET `password`='".$cPass_hash."' WHERE `cemailadd`='".$email."'");

		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `module`, `cevent`, `cmachine`, `cremarks`) 
			values('001','$email','',NOW(),'PASSWORD','RECOVER','$compname','Recover Password')");		 

		mysqli_query($con,"DELETE FROM `password_reset_temp` WHERE `email`='".$email."'");
		 
		$error = '<div class="error"><p>Congratulations! Your password has been updated successfully.</p>
		<p><a href="https://'.$_SERVER['HTTP_HOST'].'">
		Click here</a> to Login.</p></div><br />';
   } 
?>

		<div id="myModal">
			<div class="modal-dialog modal-login">
				<div class="modal-content">
					<div class="modal-header">
		                <div class="logo">			
											<img src="../images/LogoNew.png" width="90" height="120">	
		                </div>

					</div>
					<div class="modal-body text-center">


					  <?php echo $error; ?>

					</div>
				</div>
			</div>
		</div>

<?php
}
?>

		

</body>
</html>