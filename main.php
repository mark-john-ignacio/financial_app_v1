<?php 
	if(!isset($_SESSION)){
		session_start();		
	}
	include('Connection/connection_string.php');

	//get the value of the employee id

	if(!isset($_SESSION['employeeid'])){
		echo "<script>top.location='".$UrlBase."denied.php'</script>";
	}
	
	$employeeid = isset($_SESSION['employeeid']) ? $_SESSION['employeeid'] : '';
	$session_id = isset($_SESSION['session_id']) ? $_SESSION['session_id'] : '';
	$company = isset($_SESSION['companyid']) ? $_SESSION['companyid'] : ''; // Retrieve companyid from session

	$pages = array();
	$mainidx = array();
	$sql = "SELECT pageid, main_id, menu_id FROM users_access WHERE userid = '$employeeid'";
	$query = mysqli_query($con, $sql);
	while($list = $query -> fetch_assoc()) {
		array_push($pages, $list["pageid"]);
		
		if(!in_array($list["main_id"], $mainidx)){
			if($list["main_id"]!="" && $list["main_id"]!=null){
				$mainidx[] = $list["main_id"];
			}
		}

		if(!in_array($list["menu_id"], $mainidx)){
			if($list["menu_id"]!="" && $list["menu_id"]!=null && $list["main_id"]!="" && $list["main_id"]!=null){
				$mainidx[] = $list["menu_id"];
			}
		}

	}	

	//get main id of access
	$getmains = array();
	$sql = "SELECT * FROM nav_menu WHERE cstatus ='ACTIVE' and id in (".implode(",",$mainidx).")";
	$query = mysqli_query($con, $sql);
	while($list = $query -> fetch_assoc()) {
		if($list["main_id"]!="" && $list["main_id"]!=null){
			$mainidx[] = $list["main_id"];
		}
	}

	$navmenu = array();
	$navmain = array();

	$sql = "SELECT * FROM nav_menu WHERE cstatus ='ACTIVE' and id in (".implode(",",$mainidx).") Order by menu_order";
	$query = mysqli_query($con, $sql);
	while($list = $query -> fetch_assoc()) {
		
		if($list["main"]==1){
			$navmain[] = $list;
		}else{
			$navmenu[] = $list;
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Myx Financials</title>
<META NAME="robots" CONTENT="noindex,nofollow">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="global/css/googleapis.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<!--<link href="global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>-->
<link href="Bootstrap/css/bootstrap.css?x=<?=time()?>" rel="stylesheet" type="text/css"/>
<link href="Bootstrap/css/shopfont/flaticon.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="global/layout.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="global/themes/blue.css?h=<?php echo time();?>" id="style_color" rel="stylesheet" type="text/css"/>
<link href="global/custom.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="Bootstrap/css/alert-modal.css">


<!-- END THEME STYLES -->

</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-quick-sidebar-over-content page-style-square"> 
	<?php
		//get user details
		$arrcompz = array();
		$cntzcompany = 0;
		
		$result=mysqli_query($con,"select compcode, compname, clogoname, lallownontrade, lmrpmodules, IFNULL(csubcode,'') as csubcode From company");		

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$cntzcompany++;
			$arrcompz[] = $row;
			if($row['compcode'] == $company){
				$compname =  $row['compname'];
				$logoname =  str_replace("../","",$row['clogoname']);
				$lallowNT =  $row['lallownontrade'];
				$lallowMRP = $row['lmrpmodules'];
				$durlSUB = $row['csubcode'];
			}
		}    
	?>

<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="main.php">
				<img src="images/LOGOTOP.png" alt="logo" class="logo-default" width="150" height="48" />
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>

		<div class="dropdown page-comname">
			<div style="display: table-cell; vertical-align: middle;">

					<?php
						if($cntzcompany==1){
							echo "<font size='3' style='color:#fff'>".$compname."</font>";
						}else{
					?>
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
							<font size="3" style="color:#fff"><?=$compname?></font>
							<i class="fa fa-angle-down" style="color:white"></i>
						</a>

						<ul class="dropdown-menu" style="margin-left: 20px">
						<?php
							foreach($arrcompz as $rs1){
								if($rs1['compcode'] !== $company){
						?>
							<li>
								<a href="javascript:;" onClick="setpage('MasterFiles/ChangeCompany.php?x=<?=$rs1['compcode']?>');" >
								<?=$rs1['compname']?> </a>
							</li>
						<?php
								}
							}
						?>
						</ul>
					<?php
						}
					?>
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<!-- BEGIN NOTIFICATION DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-user">

						<?php                    
             
              $id = $_SESSION['employeeid'];
                        
              $sql = "select * From users where Userid='$id'";
              $result=mysqli_query($con,$sql);
                                        
              if (!mysqli_query($con, $sql)) {
                printf("Errormessage: %s\n", mysqli_error($con));
              } 
              
							$cfname = "";                                       
                        
              while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
              {
                $cfname =  $row['Fname'];
                $imgsrc =  $row['cuserpic'];
                                            
				if($row['cuserpic']!=null){
                	$imgsrc =  str_replace("../","",$imgsrc);
				}else{
					$imgsrc = "imgusers/emp.jpg";	
				}
              }
                                        
              if($imgsrc == ""){
                $imgsrc = "imgusers/emp.jpg";	
              }
                        
            ?>

					
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<img alt="" class="img-circle" src="<?php echo $imgsrc; ?>" />
						<span class="username username-hide-on-mobile"> <?php echo $cfname; ?> </span>
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="javascript:;" onClick="setpage('MasterFiles/ChangePass.php');" >
							<i class="icon-user"></i> Change Password </a>
						</li>
						<li>	<!--adding history logs href-->
							<a href="javascript:;" onClick="setpage('historylog.php');" >
							<i class="icon-user"></i> History Log </a>
						</li>
						<li>
							<a href="logout.php">
							<i class="icon-key"></i> Log Out </a>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
				<!-- BEGIN QUICK SIDEBAR TOGGLER -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <!--
				<li class="dropdown dropdown-quick-sidebar-toggler">
					<a href="javascript:;" class="dropdown-toggle">
					<i class="icon-logout"></i>
					</a>
				</li> -->
				<!-- END QUICK SIDEBAR TOGGLER -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
		<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
			<!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
			<!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
			<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
			<!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
			<!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
			<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<!-- END SIDEBAR TOGGLER BUTTON -->
				</li>
				<!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
				<?php
					foreach($navmain as $rs1){
						if($rs1['id']==1 || $rs1['id']==103){
							?>
								<li>		
									<a href="javascript:;" onClick="setpage('<?=$rs1['url']?>','<?=$rs1['id']?>');">
										<i class="<?=$rs1['icon']?>"></i> <?=$rs1['title']?>
									</a>
								</li>
							<?php
						}else{

							if($rs1['url']=="#"){
				?>
					<li>
						<a href="javascript:;">
							<i class="<?=$rs1['icon']?>"></i><span class="title"><?=$rs1['title']?></span><span class="arrow "></span>
						</a>
						<ul class="sub-menu"> 
					<?php
							foreach($navmenu as $rs2){
							if($rs2['main_id']==$rs1['id'] && $rs2['main']==2 && $rs2['report_list']==0){
								?>
							<li>
								<a href="javascript:;" class="nav-link nav-toggle">
									<i class="<?=$rs2['icon']?>"></i> <?=$rs2['title']?> <span class="arrow"></span>
								</a>
								
								<ul class="sub-menu">
									<?php
										foreach($navmenu as $rs3){
											if($rs3['main_id']==$rs2['id'] && $rs3['main']==0){
										?>
										<li>
											<a href="javascript:;" onClick="setpage('<?=$rs3['url']?>','<?=$rs3['id']?>');">
												<i class="<?=$rs3['icon']?>"></i> <?=$rs3['title']?>
											</a>
										</li>
										<?php
											}
										}
									?>
								</ul>
								<?php
							}else if($rs2['main_id']==$rs1['id'] && $rs2['main']==0 && $rs2['report_list']==1){
								echo "<li><a href=\"javascript:;\" onClick=\"setpage('".$rs2['url']."','".$rs2['id']."');\"><i class=\"".$rs2['icon']."\"></i>".$rs2['title']."</a></li>";
							}else if($rs2['main_id']==$rs1['id'] && $rs2['main']==0){
								?>
									<li>
										<a href="javascript:;" onClick="setpage('<?=$rs2['url']?>','<?=$rs2['id']?>');">
											<i class="<?=$rs2['icon']?>"></i> <?=$rs2['title']?>
										</a>
									</li>
								<?php
							}
					?>

					<?php
							}
					?>
						</ul>
					</li>
				<?php
							}else{
								echo "<li><a href=\"javascript:;\" onClick=\"setpage('".$rs1['url']."');\"><i class=\"".$rs1['icon']."\"></i>".$rs1['title']."</a></li>";
							}
						}

					}
				?>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->

	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row"> 
				<div class="col-xs-12 nopadding">																
         			<iframe id="myframe" name="myframe" scrolling="no" style="width:100%; display:block; margin:0px; padding:0px; border:0px" src=""></iframe>
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 2022 &copy; MYXFinancials by Sert Technology Inc. / HRWeb PH
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) --><!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="global/plugins/respond.min.js"></script>
<script src="global/plugins/excanvas.min.js"></script> 
<![endif]-->


	<div class="modal fade" id="changePassword" role="dialog" aria-labelledby="changeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					
					
					<h5 class="modal-title" id="myModalLabel"><b>Change Password</b>: <br> <i> 30 days have pass since your last password Change. Insert a new password. </i></h5>
					
				</div>
				<div class="modal-body" style="height: 35vh">
					<form method="post" name="frmpos" id="frmpos" >
						<div class='form-group'>
							<label for='uid' >Current Password: </label>
								<input type='password' class='form-control' name='password' id='password' placeholder="New Password" autocomplete="off"/>
						</div>
						<div class='form-group'>
							<label for='uid' >New Password: </label>
								<input type='password' class='form-control' name='newpassword' id='newpassword' placeholder="New Password" autocomplete="off"/>
						</div>
						<div class='form-group'>
								<label for='uid' >Confirm Password: </label>
								<input type='password' class='form-control' name='confirmPassword' id='confirmPassword' placeholder="Confirm Password" autocomplete="off"/> 
						</div>
						<div class='form-group'>
							<div class="col-xs-12 " id="warning" style="display: none">
								<div id="alphabettxt"><span id="alphabet"></span> Must have a Alphabetical characters! </div>
								<div id="numerictxt"><span id="numeric"></span> Must have a Numeric characters!</div>
								<div id="stringlentxt"><span id="stringlen"></span> Minimum of 8 characters! </div>
							</div>
						</div>
					</form>
				</div>
				
				<div class="modal-footer">
					<input type="hidden" name="hdnmodtype" id="hdnmodtype" value="" />
					<button type="button" id="updatepass" name="updatepass" class="btn btn-primary">Change Password</button>
				</div>
			</div>
		</div>
	</div>

	<!-- 1) Alert Modal -->
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog vertical-align-top">
				<div class="modal-content">
				<div class="alert-modal-danger">
					<p id="AlertMsg"></p>
					<p>
						<center>
							<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
						</center>
					</p>
				</div>
				</div>
			</div>
		</div>
	</div>

<script src="global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>

<script src="global/plugins/jquery-idle-timeout/jquery.idletimeout.js" type="text/javascript"></script>
<script src="global/plugins/jquery-idle-timeout/jquery.idletimer.js" type="text/javascript"></script>

<!-- END CORE PLUGINS -->
<script src="global/scripts/metronic.js" type="text/javascript"></script>
<script src="admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="global/scripts/ui-idletimeout.js?h=<?php echo time();?>"></script>

<script>
	var warnings = { alpha: false, numeric: false, stringlen: false };

	$(document).ready(function() { 
		setpage("Dashboard/dashboard.php")
		Metronic.init(); // init metronic core components
		Layout.init(); // init current layout
		QuickSidebar.init(); // init quick sidebar
		//UIIdleTimeout.init();

		let currentPage = "dashboard.php";
			
		loaddashboard();   
		login(); // call login function


		<?php
			if(intval($_SESSION['modify_pass'])>=30){
			?>
				$("#changePassword").modal("show");


				$('#updatepass').on('click', function(){

					var newpass = $('#newpassword').val();
					var confirm = $('#confirmPassword').val(); 


					const validateNew = PasswordValidation(newpass)
					const validateConfirm = PasswordValidation(confirm)
					if(validateNew && validateConfirm){
						$.ajax({
							url: 'MasterFiles/user_change_pass.php',
							type:'post',
							data: {
								id: "<?=$_SESSION['employeeid']?>",
								password: $('#password').val(),
								newpassword: newpass, 
								confirmPassword: confirm
							},
							dataType: 'json',
							async: false,
							success: function(res){
								if(res.valid){
									//alert(res.msg)
									$("#changePassword").modal("hide");

									$("#AlertMsg").html(res.msg);
									$("#alertbtnOK").show();
									$("#AlertModal").modal('show')
								} else {
									$("#AlertMsg").html(res.errMsg);
									$("#alertbtnOK").show();
									$("#AlertModal").modal('show')
								}
							}
						});
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
				});
			<?php
			}
		?>
	});
	  
	function setpage(valz){
		
		if(valz=="POS"){
			top.location.href="Sales/Win/index.php";
		}
		else{

			$("#myframe").attr('src',valz);

			setInterval(function(){
					document.getElementById("myframe").style.height = document.getElementById("myframe").contentWindow.document.body.scrollHeight + 'px';
			},1000);
			
			/*
			var iframe = $("#myframe")[0];
			
			iframe.contentWindow.focus();
			
			
			var iFrameID = document.getElementById('myframe');
				
				if(iFrameID) {
		
					iFrameID.height = "";
					x = parseInt(iFrameID.contentWindow.document.body.scrollHeight) + 300;
					
					iFrameID.height = x + "px";
					
				} 

					*/
					
			}
				
	  }
	  function loaddashboard(){
		let pages = <?= json_encode($mainidx) ?>;

		if (pages.includes("1")) {
			setpage("./Dashboard/dashboard2/index.php")
		} else {
			setpage('MAIN/index.html')
		}

	  }
	  //for autologout detect inactivity
	  function login() {
		
		// Simulate successful login
		lastActivityTime = Date.now();
		console.log("User logged in at: " + formatTime(lastActivityTime));
		
		// Start monitoring for activity
		document.addEventListener("mousemove", updateActivityTime);
		document.addEventListener("keypress", updateActivityTime);
		document.addEventListener("input", updateActivityTime);
		document.addEventListener("click", updateActivityTime);
		
		// Start the auto-logout timer
		startLogoutTimer();
	}

	function formatTime(milliseconds) {
		let totalSeconds = Math.floor(milliseconds / 1000);
		let hours = Math.floor(totalSeconds / 3600); // Calculate total hours
		let remainingSeconds = totalSeconds % 3600; // Remaining seconds after calculating hours
		let minutes = Math.floor(remainingSeconds / 60); // Calculate minutes from remaining seconds
		let seconds = remainingSeconds % 60; // Calculate remaining seconds after calculating minutes
		
		return `${hours}h ${minutes}m ${seconds}s`;
	}

	//update the activity time (main is the parent)
	function updateActivityTime() {
		parent.lastActivityTime = Date.now();
		console.log("Last activity time: " + formatTime(parent.lastActivityTime));
	}


	function startLogoutTimer() {
		logoutTimer = setInterval(function() {
			checkLogoutTime();
		}, 10000); // Check every 10 seconds
	}

	//compare the time from last activity to time now
	function checkLogoutTime(loginTime) {
		let currentTime = Date.now();
		const secondsInADay = 24 * 60 * 60; // 24 hours in seconds
		const timeDifferenceInSeconds = (currentTime - loginTime) / 1000; // Calculate time difference in seconds
		console.log(timeDifferenceInSeconds);
		if (timeDifferenceInSeconds >= secondsInADay) { // Check if 24 hours have passed
			logout(); // Assuming logout() is defined somewhere else
		}
	}


	function logout() {
		clearInterval(logoutTimer);
		// Send message to the dashboard frame that there are logout
		window.parent.postMessage({ logoutInitiated: true }, '*');
		alert("Auto logout due to inactivity." );
		//adding inactivity for reason to determine the status of logout
		window.location.href = "logout.php?logout_reason=inactivity";
	}


	<?php
		if(intval($_SESSION['modify_pass'])>=30){
	?>

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
	<?php
		}
	?>

</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>