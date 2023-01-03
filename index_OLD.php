<?php
include('Connection/connection_string.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>POS ACCOUNTING</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="global/css/googleapis.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->

<link href="global/css/login2.css?t=<?php echo time();?>" rel="stylesheet" type="text/css"/>


<!-- BEGIN THEME STYLES -->
<link href="global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="global/layout.css" rel="stylesheet" type="text/css"/>
<link id="style_color" href="global/themes/default.css" rel="stylesheet" type="text/css"/>
<link href="global/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>

<script src="Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="Bootstrap/js/noright.js"></script>

</head>

<title>MyPOS</title>
<body class="login">

<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<div class="logo">
	<a href="index.html">
	<img src="images/MyxFin.png" alt="" width="300" height="50"/>
	</a>
</div>
<div id="userpic">
	
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN

<div class="mainbox"> -->
<div class = "content">

	<form class="login-form" action="index.html" method="post" autocomplete="off">
		<div class="form-title">
			<span class="form-title">Welcome.</span>
			<span class="form-subtitle">Please login.</span>
		</div>
		<div id="add_err">

		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">Username</label>            
            <div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="employeeid" id="employeeid" />
			</div>

		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
			<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="inputPassword" id="inputPassword" />
            </div>
		</div>
        <div class="form-group">
        <input type="hidden" id="selcat" name="selcat" value="admin">
       <!--
       <select id="selcat" name="selcat" class="form-control input-sm">
        	  <option value="admin" selected>Admin</option>
              <option value="win">Walk-In</option>
              <option value="pos">POS Full</option>
        	  <!--<option value="display2">Display w/ Approval</option>
        	  
              <option value="display">Display</option>
              <option value="user">POS User</option>
        </select>-->

        </div>
		<div class="form-actions">
			<button type="button" class="btn btn-primary btn-block uppercase" name="btnLogin" id="btnLogin">Login</button>
		</div>
</form>
</div>

<!--</div>-->

</body>
</html>
<script type="text/javascript">

$(document).ready(function(){
    
	$("#add_err").css('display', 'none', 'important'); 
	$("#userpic").css('display', 'none', 'important'); 
	
    $("#btnLogin").click(function(){  

		if(document.getElementById("employeeid").value == "" || document.getElementById("inputPassword").value == ""){
			$("#add_err").css('display', 'inline', 'important');
            $("#add_err").html("<div class='alert alert-danger' role='alert'><strong>ERROR!</strong> Complete the form</div>");
		}else{


			  employeeid=$("#employeeid").val();
			  password=$("#inputPassword").val();
			  selcat = $("#selcat").val();
				
			  $.ajax({
			   type: "POST",
			   url: "include/employeelogin.php?",
			   data: "employeeid="+employeeid+"&password="+password,
			   success: function(html){   
			   //alert(html); 
				if(html==1)    {
				 //$("#add_err").html("right username or password");
				 if(selcat=='user'){
					 window.location="SYS/";
				 }
				 else if(selcat=='display'){
					 window.location="DIS/";
				 }
				 else if(selcat=='display2'){
					 window.location="DisApp/";
				 }
				 else if(selcat=='pos'){
					 window.location="SysFull/";
				 }
				 else if(selcat=='win'){
					 window.location="Win/";
				 }
				 else{
					 window.location="main.php";
				 }

				}
				else    {
				 $("#add_err").css('display', 'inline', 'important');
				 $("#add_err").html("<div class='alert alert-danger' role='alert'>"+html+"</div>");
				}
			   },
			   beforeSend:function()
			   {
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<center><img src='images/loader.gif' widt='50' height='50' /></center>")
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


