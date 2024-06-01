<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../Connection/connection_string.php";
	require_once('../Model/helper.php');

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
<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="../global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="../global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
<link href="../admin/pages/css/login-soft.css?x=<?=time()?>" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="../global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="../admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css"/>
<link href="../admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
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
			<img src="../images/LogoNew.png" width="120">	
		</a>
	</div>
	<!-- END LOGO -->

	<?php
	$error = "";
	if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) && ($_GET["action"]=="reset") && !isset($_POST["action"])){
		$key = $_GET["key"];
		$email = $_GET["email"];
		$curDate = date("Y-m-d H:i:s");

		$stmt = $con->prepare("SELECT * FROM `password_reset_temp` WHERE `key` = ? and `email`= ?");
        $stmt->bind_param("ss", $key, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $xp = ($row['email']) ?? "";

		if ($xp==""){
	?>
			<center><h3 class="form-title">Invalid Link</h3></center>
			<div class="form-group">
				<h4>Forgot your password ?</h4>
				<p>The link is invalid/expired. Either you did not copy the correct link
					from the email, or you have already used the key in which case it is 
					deactivated.
				</p>
				<p>Go <a href="<?=$UrlBase?>">
				back</a> to login page and click "here" to reset your password.</p>
			</div>
	<?php
		}else{
			$expDate = $row['expDate'];
			if ($expDate >= $curDate){
	?>

				
			<form method="post" action="reset-password.php" name="frmupdatepass" id="frmupdatepass">
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="email" value="<?=$_GET["email"]?>" />
				<div class="alert alert-danger display-hide">
					<button class="close" data-close="alert"></button>
					<span> Enter password and confirm password. </span>
				</div>
				<div class="form-group">
					<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
					<label class="control-label visible-ie8 visible-ie9">New Password</label>
					<div class="input-icon">
						<i class="fa fa-user"></i>
						<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="New Password" name="pass1" id="pass1" value=""/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label visible-ie8 visible-ie9">Confirm Password</label>
					<div class="input-icon">
						<i class="fa fa-lock"></i>
						<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Confirm Password" name="pass2" id="pass2" value=""/>
					</div>
				</div>

				<div class="form-group" id="warning" style="display: none">
					<div id="alphabettxt"><span id="alphabet"></span> Must have an Alphabetical character! </div>
					<div id="numerictxt"><span id="numeric"></span> Must have a Numeric character!</div>
					<div id="stringlentxt"><span id="stringlen"></span> Minimum of 8 characters! </div>
					<div id="passmatchtxt"><span id="passmatch"></span> Password Match! </div>					
				</div>

				<div class="form-actions">				
					<button type="button" id="btnAdd" name="btnAdd" class="btn blue pull-right"> Reset Password <i class="m-icon-swapright m-icon-white"></i> </button>
				</div>

				<p> &nbsp; </p>
			</form>

						
	<?php
		}else{

	?>

			<center><h3 class="form-title">Link Expired</h3></center>
			<div class="form-group">
				<p>The link is expired. You are trying to use the expired link which 
				is valid only 24 hours (1 day after request).<br /><br /></p>

				<p>Go <a href="<?=$UrlBase?>">
				back</a> to login page and click "here" to reset your password.</p>
			</div>

<?php

    	}
	}
} // isset email key validate end
 
if(isset($_POST["action"]) && ($_POST["action"]=="update")){
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

		$stmtlog = $con->prepare("UPDATE users set `password` = ?, `modify` = current_date(), `cstatus` = 'Active' WHERE `cemailadd` = ?");
		$stmtlog->bind_param("ss", $cPass_hash, $email);
		$stmtlog->execute();
		$stmtlog->close();

		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

		$stmtlog = $con->prepare("INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `module`, `cevent`, `cmachine`, `cremarks`) 
		values('001',?,'',NOW(),'PASSWORD','RECOVER',?,'Recover Password')");
		$stmtlog->bind_param("ss", $email, $compname);
		$stmtlog->execute();
		$stmtlog->close(); 

		$stmtlog = $con->prepare("DELETE FROM `password_reset_temp` WHERE `email` = ?");
		$stmtlog->bind_param("s", $email);
		$stmtlog->execute();
		$stmtlog->close(); 	 
   } 
?>

		<center><h3 class="form-title">Reset Password</h3></center>
		<div class="form-group">
			<h4>Congratulations!</h4>
			<p>Your password has been updated successfully.
			</p>
			<p><a href="<?=$UrlBase?>">Click here</a> to Login.</p>
		</div>

<?php
}
?>

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
<script src="../global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="../global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../global/plugins/select2/select2.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../global/scripts/metronic.js" type="text/javascript"></script>
<script src="../admin/layout/scripts/layout.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
	var warnings = { alpha: false, numeric: false, stringlen: false };
	jQuery(document).ready(function() {     
		Metronic.init(); // init metronic core components
		Layout.init(); // init current layout

		// init background slide images
		$.backstretch([
			"../admin/pages/media/bg/1.jpg?x=<?=time()?>",
			"../admin/pages/media/bg/2.jpg?x=<?=time()?>",
			"../admin/pages/media/bg/3.jpg?x=<?=time()?>",
			"../admin/pages/media/bg/4.jpg?x=<?=time()?>"
			], {
				fade: 1000,
				duration: 8000
			}
		);

		$('#btnAdd').on('click', function(){
			const newpassword = $('#pass1').val();
			const confirmpassword = $('#pass2').val();
			
			const confirmNewPassword = PasswordValidation( newpassword );
			const confirmPassword = PasswordValidation( confirmpassword );

			const chGkGo = PassMatch();
			

			if( confirmNewPassword && confirmPassword && chGkGo){
				
				$("#frmupdatepass").submit();

			} else {
				$('#warning').css('display', 'block')
				$('#alphabet').html("<i " + (!warnings.alpha ?  "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i> ");
				$('#alphabettxt').css('color', ( !warnings.alpha ? '#FF0000' : '#000000' ))

				$('#numeric').html("<i " + ( !warnings.numeric ? "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i> ");
				$('#numerictxt').css('color', ( !warnings.numeric ? '#FF0000' : '#000000' ))

				$('#stringlen').html("<i " + ( !warnings.stringlen ? "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i>");
				$('#stringlentxt').css('color', ( !warnings.stringlen ?  '#FF0000' : '#000000' ))

				$('#passmatch').html("<i " + ( !chGkGo ? "class='fa fa-exclamation' style='color: #FF0000;'" : "class='fa fa-check' style='color: #008000;' ") + "></i>");
				$('#passmatchtxt').css('color', ( !chGkGo ?  '#FF0000' : '#000000' ))
			}
		
		})
	});
	
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

	function PassMatch(){
		const newpassword = $('#pass1').val();
		const confirmpassword = $('#pass2').val();

		if(newpassword!="" && confirmpassword!=""){
			if(newpassword!=confirmpassword){
				return false;
			}else{
				return true;
			}
		}

	}

	function PasswordValidation(inputs){
		warnings['alpha'] = AlphabetFilter(inputs)
		warnings['numeric'] = NumericFilter(inputs)
		warnings['stringlen'] = PasswordLimit(inputs)

		return warnings['alpha'] && warnings['numeric'] && warnings['stringlen'];

	}

</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>

