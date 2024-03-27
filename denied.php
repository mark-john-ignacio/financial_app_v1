<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Myx Financials</title>
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
		opacity: 0.5;
    	filter: alpha(opacity=50); /* For IE8 and earlier */
	}
	.modal-login:hover {		
		opacity: 1;
    	filter: alpha(opacity=1); /* For IE8 and earlier */
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
				<form action="index.php" method="post">
					<div class="form-group">
						<center><font size="+3">
                        	Your Session has expired!
                            
                        </font></center>
                        
					</div>  
                     
                   <div class="form-group" id="add_err">

				   </div>

                         
					<div class="form-group">
						<button type="button" class="btn btn-primary btn-lg btn-block login-btn" id="btnLogout" name="btnLogout">Login</button>
                        
					</div>
				</form>
			</div>
		</div>
	</div>
</div>     
</body>
</html>

<script>
$(function(){
	$("#btnLogout").on("click", function() {
		window.top.location.href = "index.php"; 
	});
});
</script>
