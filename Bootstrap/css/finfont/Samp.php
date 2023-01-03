<!DOCTYPE html>
<html>

<head>
    <title>Flaticon WebFont</title>

<link href="../../../global/css/googleapis.css" rel="stylesheet" type="text/css"/>
<link href="../../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="flaticon.css">
<link href="../../../global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="../../../global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="../../../global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../../global/layout.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link id="style_color" href="../../../global/themes/blue.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="../../../global/custom.css" rel="stylesheet" type="text/css"/>

    <meta charset="UTF-8">
</head>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="setpage('MAIN/index.html');">
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
				<li>
					<a href="javascript:;">
					<i class="fgly-md flaticon-011-dollar"></i>
                    <i class="fgly-sm flaticon-011-dollar"></i>
					<span class="title">Loan Mgr</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu">
						
                        <li>
							<a href="javascript:;" onClick="setpage('RECOM/');"> 
                            <i class="glyphicon glyphicon-file"> </i>
							Loan Application</a>
						</li>
                        
						<li>
							<a href="javascript:;" onClick="setpage('InvAdj/Inv.php');">
							 <i class="glyphicon glyphicon-file"> </i>
                            Loan Processing</a>
						</li>
					</ul>
				</li>

				<li>
					<a href="javascript:;">
					<i class="fa  fa-bar-chart-o "></i>
					<span class="title">Reports</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu">
						<li>
							<a href="javascript:;" onClick="setpage('Reports/rptmenu.php?id=sales');">
                            <i class="glyphicon glyphicon-file "> </i>
							Sales</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Reports/rptmenu.php?id=purch');">
                            <i class="glyphicon glyphicon-file "> </i>
							Purchases</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Reports/rptmenu.php?id=acc');">
                            <i class="glyphicon glyphicon-file "> </i>
							Accounting</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Reports/rptmenu.php?id=inv');">
                            <i class="glyphicon glyphicon-file "> </i>
							Inventory</a>
						</li>
					</ul>
				</li>
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
				<div class="col-md-12 nopadding">
					 
                         <iframe id="myframe" name="myframe" scrolling="no" style="width:100%; display:block; margin:0px; padding:0px; border:0px" src=""></iframe>

				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) --><!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="global/plugins/respond.min.js"></script>
<script src="global/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../../global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../../global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../../global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="../../global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../../global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<script src="../../global/scripts/metronic.js" type="text/javascript"></script>
<script src="../../admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../../admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="../../admin/layout/scripts/demo.js" type="text/javascript"></script>
<script>
      jQuery(document).ready(function() {    
        Metronic.init(); // init metronic core components
		Layout.init(); // init current layout
		QuickSidebar.init(); // init quick sidebar
		Demo.init(); // init demo features
		
		loadxtrasession();
		
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
	  
	  var iframe = $("#myframe")[0];
	  
	  iframe.contentWindow.focus();
	  
	  
	  var iFrameID = document.getElementById('myframe');
		  
		  if(iFrameID) {
	
				iFrameID.height = "";
				x = parseInt(iFrameID.contentWindow.document.body.scrollHeight) + 300;
				
				iFrameID.height = x + "px";
				
		  } 
		  
	}
	  
	  }
	  
   </script>
<!-- END JAVASCRIPTS -->
</body>
</html>