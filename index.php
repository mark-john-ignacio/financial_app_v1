<?php

	if(!isset($_SESSION)){
		session_start();
	}
	
	include('Connection/connection_string.php');
  	require_once('Model/helper.php');

	 if(isset($_SESSION['employeeid']) && isset($_SESSION['session_id'])) {		
		//passing the value when the login button is click
		if($_SESSION['employeeid'] !="" && $_SESSION['session_id'] !=0) {		
			$employeeid = isset($_SESSION['employeeid']) ? $_SESSION['employeeid'] : '';
			$session_id = isset($_SESSION['session_id']) ? $_SESSION['session_id'] : '';
		//exit();

			header("Location: ./main.php");
		}
	}

	$defusnmr = "";
	$defpsswd = "";
	if(!isset($_SERVER['HTTP_REFERER'])){
		session_unset(); 
		session_destroy();

		
	}else{
		$defusnmr = isset($_SESSION['employeeid']) ? $_SESSION['employeeid'] : '';
		$defpsswd = "";
	}

	if(!isset($_SESSION['employeeid'])){
		$defusnmr = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
		$defpsswd = isset($_COOKIE['password']) ? $_COOKIE['password'] : '';
	}
?>
  

  <!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>MYX Financials</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
<link href="admin/pages/css/login-soft.css?x=<?=time()?>" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css"/>
<link href="admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">

<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<!-- BEGIN LOGO -->
	<div class="logo">
		<a href="index.html">
			<img src="images/LogoNew.png" width="120">	
		</a>
	</div>
	<!-- END LOGO -->

	<form class="login-form" action="authenticate.php" method="post">
		<center><h3 class="form-title">Login to your account</h3></center>
		<?php
			$dxmsgs = "Enter username and password.";
			$dxstat = " display-hide";
			if(isset($_SESSION['xmessage'])){
				if($_SESSION['xmessage']!=""){
					$dxstat = "";
					$dxmsgs = $_SESSION['xmessage'];
				}
			}
		?>
		<div class="alert alert-danger<?=$dxstat?>">
			<button class="close" data-close="alert"></button>
			<span> <?=$dxmsgs?> </span>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">Username</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" value="<?=$defusnmr?>"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" value="<?=$defpsswd?>"/>
			</div>
		</div>
		<div class="form-actions">
			<label class="checkbox">
			<input type="checkbox" name="remember" value="1"/> Remember me </label>
			<button type="submit" name="login" class="btn blue pull-right"> Login <i class="m-icon-swapright m-icon-white"></i> </button>
		</div>
		
		<div class="forget-password">
			<h4>Forgot your password ?</h4>
			<p>
				 no worries, click <a href="javascript:;" id="forget-password">
				here </a>
				to reset your password.
			</p>
		</div>
		
	</form>
	<!-- END LOGIN FORM -->
	<!-- BEGIN FORGOT PASSWORD FORM -->
	<form class="forget-form" action="forgotpass.php" method="post">
		<h3>Forget Password ?</h3>
		<p>
			 Enter your e-mail address below to reset your password.
		</p>
		<div class="form-group">
			<div class="input-icon">
				<i class="fa fa-envelope"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"/>
			</div>
		</div>
		<div class="form-actions">
			<button type="button" id="back-btn" class="btn">
			<i class="m-icon-swapleft"></i> Back </button>
			<button type="submit" name="forgets" class="btn blue pull-right">
			Submit <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
	</form>
	<!-- END FORGOT PASSWORD FORM -->
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright"> 2022 &copy; MYXFinancials by Sert Technology Inc. / HRWeb PH </div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="global/plugins/respond.min.js"></script>
<script src="global/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<script type="text/javascript" src="global/plugins/select2/select2.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="global/scripts/metronic.js" type="text/javascript"></script>
<script src="admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="admin/pages/scripts/login-soft.js?x=<?=time()?>" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {     
  	Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
  	Login.init();

       // init background slide images
       $.backstretch([
        "admin/pages/media/bg/1.jpg?x=<?=time()?>",
        "admin/pages/media/bg/2.jpg?x=<?=time()?>",
        "admin/pages/media/bg/3.jpg?x=<?=time()?>",
        "admin/pages/media/bg/4.jpg?x=<?=time()?>"
        ], {
          fade: 1000,
          duration: 8000
    }
    );
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>