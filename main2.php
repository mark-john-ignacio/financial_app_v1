<?php 
	if(!isset($_SESSION)){
		session_start();
	}

	include('Connection/connection_string.php');
	include('include/denied.php');
	$company = $_SESSION['companyid'];   
	$employeeid = $_SESSION['employeeid'];

	$pages = [];
	$sql = "SELECT pageid FROM users_access WHERE userid = '$employeeid'";
	$query = mysqli_query($con, $sql);
	while($list = $query -> fetch_assoc()) {
		array_push($pages, $list["pageid"]);
	}

	$navmenu = array();
	$navmain = array();

	$sql = "SELECT * FROM nav_menu WHERE deleted = 0 Order by menu_order";
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
                                            
                $imgsrc =  str_replace("../","",$imgsrc);
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
											<a href="javascript:;" onClick="setpage('<?=$rs3['url']?>');">
												<i class="<?=$rs3['icon']?>"></i> <?=$rs3['title']?>
											</a>
										</li>
										<?php
											}
										}
									?>
								</ul>
								<?php
							}else if($rs2['main_id']==$rs1['id'] && $rs2['main']==2 && $rs2['report_list']==1){
								echo "<li><a href=\"javascript:;\" onClick=\"setpage('".$rs2['url']."');\"><i class=\"".$rs2['icon']."\"></i>".$rs2['title']."</a></li>";
							}else if($rs2['main_id']==$rs1['id'] && $rs2['main']==0){
								?>
									<li>
										<a href="javascript:;" onClick="setpage('<?=$rs2['url']?>');">
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
<!-- END CORE PLUGINS -->
<script src="global/scripts/metronic.js" type="text/javascript"></script>
<script src="admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>

<script>
	$(document).ready(function() { 
	setpage("Dashboard/index.php")
		Metronic.init(); // init metronic core components
		Layout.init(); // init current layout
		QuickSidebar.init(); // init quick sidebar
			
		loadxtrasession();
			loaddashboard();   
	});
		
	function loadxtrasession(){
		$.ajax ({
			url: "include/th_xtrasessions.php",
			async: false,
			success: function( data ) {
				console.log(data);
			}
		});
	}
	  
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
		let pages = <?= json_encode($pages) ?>;

		if (pages.includes("DashboardSales.php") || pages.includes("DashboardPurchase.php")) {
			setpage("./Dashboard/index.php")
		} else {
			setpage('MAIN/index.html')
		}

	  }
	  
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>