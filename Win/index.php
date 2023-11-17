<?php

	if(!isset($_SESSION)){
		session_start();
	}
	
	include('../Connection/connection_string.php');

	$errmsg = "";

	if(isset($_POST['inputPassword'])){
		$password = $_POST['inputPassword'];
		$sql = mysqli_query($con,"select * from users where userid = 'Admin' LIMIT 1");
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC))
		{
			$employee = array('password' => $row['password']);
		}

		if(password_verify($password, $employee['password'])){

			$_SESSION['timestamp']=time();
			
			header('Location: main.php');
    	die();
		}else{
			$errmsg = "<div class='alert alert-danger' role='alert'>ACCESS DENIED!</div>";
		}
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
	<link href="../Bootstrap/css/NFont.css" rel="stylesheet">
	<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>" rel="stylesheet" type="text/css">

	<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../Bootstrap/js/bootstrap.js"></script>

	<style type="text/css">
		body {
			font-family: 'Varela Round', sans-serif;
		}
		.othfont {
			font-family: 'Aquawax', sans-serif;
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
			<div class="modal-header">
				<center>
					<font size="+3" class="othfont" style="font-weight: 600"><font color="Red">M</font><font color="green">Y</font><font color="orange">X</font></font> <font size="+3" style="font-family: Arial, sans-serif">Financials</font>
					<br>
					<font size="+1" style="font-family: Arial, sans-serif">System Management</font>
				</center>
			</div>
			<div class="modal-body" style="height: 30vh">
				<form action="index.php" method="post">
                    
					<div class="form-group">
						<input type="password" class="form-control" name="inputPassword" id="inputPassword" placeholder="Password" required  value=""  autocomplete="off">	
					</div>
                     
                     
          <div class="form-group">
							<?=$errmsg?>   
          </div>

                         
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-lg btn-block login-btn" id="btnLogin" name="btnLogin">Login</button>                        
					</div>
				</form>
			</div>

		</div>
	</div>
</div>     

</body>
</html>