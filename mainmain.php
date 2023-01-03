<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Coop Financials</title>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="global/css/googleapis.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="global/plugins/simple-line-icons/simple-line-icons.min.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="Bootstrap/css/bootstrap.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="Bootstrap/css/shopfont/flaticon.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="global/plugins/uniform/css/uniform.default.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="global/plugins/bootstrap-switch/css/bootstrap-switch.min.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="global/plugins/bootstrap-datepicker/css/datepicker.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="global/css/components.css?h=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>
<link href="global/css/plugins.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="global/layout.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link id="style_color" href="global/themes/blue.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<link href="global/custom.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
</head>
<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="setpage('MAIN/index.html');">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="index.html">
			<img src="images/LOGOTOP.png" alt="logo" class="logo-default" width="150" height="48" />
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
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
                        if(!isset($_SESSION)){
session_start();
}
                        
                        //get user details
                        
                        include('Connection/connection_string.php');
                        include('include/denied.php');
                        
                       // $company = $_SESSION['companyid'];
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
							<a href="javascript:;" onClick="setpage('Maintenance/ChangePass.php');" >
							<i class="icon-user"></i> Change Password </a>
						</li>
						<li class="divider">
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
				<li class="start ">
					<a href="javascript:;">
					<i class="icon-settings"></i>
					<span class="title">Master Data Files</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu">
						<li>
							<a href="javascript:;" onClick="setpage('Maintenance/Accounts.php?f=');">
                            <i class="fa fa-bars"></i>
							Chart of Accounts</a>
						</li>
						<li>
							<a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-handbag"></i> Items <span class="arrow"></span> </a>
                             
                                        <ul class="sub-menu">
                                        	<li>
                                            	<a href="javascript:;" onClick="setpage('Maintenance/Items.php?f=');"> <i class="fa fa-list-ul "></i> Master List </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Items/UOM.php');"> <i class="fa fa-angle-double-right"></i> Unit of Measure </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Items/TYPE.php?f=');"> <i class="fa fa-angle-double-right"></i> Types </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Items/CLASS.php?f=');"> <i class="fa fa-angle-double-right"></i> Classification </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Items/Groupings.php');"> <i class="fa fa-angle-double-right"></i> Groupings </a>
                                            </li>
                                        </ul>
						</li>
						<li>
							<a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-rub"></i> Price List <span class="arrow"></span> </a>
                             
                                        <ul class="sub-menu">
                                        	<li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Items/PM.php');"> <i class="fa fa-angle-double-right"></i> Sale Pricelist </a>
                                            </li>
                                        	<li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Items/PP.php');"> <i class="fa fa-angle-double-right"></i> Purchase Pricelist </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Items/DISC.php');"> <i class="fa fa-angle-double-right"></i> Discounts List </a>
                                            </li>

										</ul>
                         </li>
						<li>
							<a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-basket-loaded"></i>
							Customers<span class="arrow"></span></a>
                                        <ul class="sub-menu">
                                        	<li>
                                            	<a href="javascript:;" onClick="setpage('Maintenance/Customers.php?f=');"> <i class="fa fa-list-ul "></i> Master List </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Custs/TYPE.php');"> <i class="fa fa-angle-double-right"></i> Types </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Custs/CLASS.php');"> <i class="fa fa-angle-double-right"></i> Classification </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Custs/Groupings.php');"> <i class="fa fa-angle-double-right"></i> Groupings </a>
                                            </li>
                                        </ul>
						</li>
						<li>
							<a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-truck"></i>
							Suppliers<span class="arrow"></span></a>
                                        <ul class="sub-menu">
                                        	<li>
                                            	<a href="javascript:;" onClick="setpage('Maintenance/Suppliers.php?f=');"> <i class="fa fa-list-ul "></i> Master List </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Supp/TYPE.php');"> <i class="fa fa-angle-double-right"></i> Types </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Maintenance/Supp/CLASS.php');"> <i class="fa fa-angle-double-right"></i> Classification </a>
                                            </li>
                                        </ul>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Maintenance/Bank.php');">
                            <i class="fa fa-bank"></i>
							Banks</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Maintenance/users.php?f=');">
                            <i class="fa fa-users"></i>
							System Users</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('System/');">
                            <i class="fa fa-gears"></i>
							System Setup</a>
						</li>
					</ul>
				</li>
				<li>
					<a href="javascript:;">
					<i class="fa fa-tags"></i>
					<span class="title">Sales &amp; Delivery</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu"> 
						<li>
							<a href="javascript:;" onClick="setpage('Sales/Quote/Quote.php');">
                            <i class="fgly flaticon-020-receipt"></i>
							Quotation</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Sales/SO/SO.php');">
                            <i class="fgly-sm flaticon-003-shopping-list"></i>
							Sales Order</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Sales/DR/DR.php');">
                            <i class="fgly-sm flaticon-035-invoice"></i>
							Delivery Receipt</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Sales/Sales/SI.php');">
                            <i class="fgly-sm flaticon-065-bill"></i> 
							Sales Invoice</a>
						</li>
                        
                        <li>
							<a href="javascript:;" onClick="setpage('POS');">
                            <i class="fgly flaticon-060-cash-register"></i>
							Point of Sale</a>
						</li>
						<!--
                        <li>
							<a href="javascript:;" onClick="setpage('Sales/Return/SR.php');">
                            <i class="icon-action-undo"></i>
							Sales Return</a>
						</li>
                        -->
                        
					</ul>
				</li>
				<li>
					<a href="javascript:;">
					<i class="fa fa-shopping-cart"></i>
					<span class="title">Purchases</span>
					<span class="arrow"></span>
					</a>
					<ul class="sub-menu">
						<li>
							<a href="javascript:;" onClick="setpage('Purchases/PO/Purch.php');">
                            <i class="glyphicon glyphicon-list"> </i>
							Purchase Order</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Purchases/RR/RR.php');">
                            <i class="fa fa-download"> </i>
							Receiving</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Purchases/PRet/PurchRet.php');">
                            <i class="fa fa-upload"> </i>
							Purchase Return</a>
						</li>

					</ul>
				</li>
				<li>
					<a href="javascript:;">
					<i class="icon-book-open "></i>
					<span class="title">Accounting</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu">
						<li>
							<a href="javascript:;" onClick="setpage('Accounting/Journal.php');">
                            <i class="fa fa-book"> </i>
							Journal Entry</a>
						</li>
						<li>
							<a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-credit-card"></i>
							Accounts Payable<span class="arrow"></span></a>
                                        <ul class="sub-menu">
                                        	<li>
                                            	<a href="javascript:;" onClick="setpage('Accounting/APInv/APV.php');"> <i class="fa fa-angle-double-right"></i> AP Voucher </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Accounting/Pay/PayBill.php');"> <i class="fa fa-angle-double-right"></i> Check Issuance </a>
                                            </li>
                                        </ul>
						</li>


						<li>
							<a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-money"></i>
							Accounts Receivable<span class="arrow"></span></a>
                                        <ul class="sub-menu">
                                        	<li>
                                            	<a href="javascript:;" onClick="setpage('Accounting/CM/SR.php');"> <i class="fa fa-angle-double-right"></i> Credit Memo </a>
                                            </li>
                                        	<li>
                                            	<a href="javascript:;" onClick="setpage('Accounting/DM/SR.php');"> <i class="fa fa-angle-double-right"></i> Debit Memo </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onClick="setpage('Accounting/OR/OR.php');"> <i class="fa fa-angle-double-right"></i> AR Payments </a>
                                            </li>
                                        </ul>
						</li>

						<li>
							<a href="javascript:;" onClick="setpage('Accounting/Deposit.php');">
                            <i class="fa fa-credit-card-alt"> </i>
							Prepare Bank Deposit</a>
						</li>
                        
                        <li>
							<a href="javascript:;" onClick="openclosing();">
                            <i class="fa fa-window-close-o"> </i>
							Monthly Closing</a>
						</li>

					</ul>
				</li>
				<li>
					<a href="javascript:;">
					<i class="icon-puzzle"></i>
					<span class="title">Inventory</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu">
						
                        <li>
							<a href="javascript:;" onClick="setpage('InvCnt/Inv.php');"> 
                            <i class="fa fa-calendar"> </i>
							Inventory Count</a>
						</li>
                       
						<li>
							<a href="javascript:;" onClick="setpage('InvAdj/Inv.php');">
							 <i class="fa fa-tasks"> </i>
                            Inventory Adjustment</a>
						</li>
	
    					<li>
							<a href="javascript:;" onClick="setpage('InvRec/Inv.php');">
							 <i class="fa fa-barcode"> </i>
                            Inventory Receiving</a>
						</li>

    				</ul>
				</li>
				<li>
					<a href="javascript:;">
					<i class="fgly-md flaticon-010-discount-1"></i>
					<span class="title">Loan Mgr</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu">
						
                        <li>
							<a href="javascript:;" onClick="setpage('Loans/App/App.php');"> 
                            <i class="glyphicon glyphicon-file"> </i>
							Loan Application</a>
						</li>
                        
						<li>
							<a href="javascript:;" onClick="setpage('Loans/Processing.php');">
							 <i class="fgly-sm flaticon-025-box-2"> </i>
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
							Finance</a>
						</li>
						<li>
							<a href="javascript:;" onClick="setpage('Reports/rptmenu.php?id=inv');">
                            <i class="glyphicon glyphicon-file "> </i>
							Inventory</a>
						</li>
                        <li>
							<a href="javascript:;" onClick="setpage('Reports/btchP.php');">
                            <i class="glyphicon glyphicon-print "> </i>
							Batch Printing</a>
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


<!-- 1) Alert Modal -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true"> 
       <div class="modal-dialog2 modal-sm">
            <div class="modal-content">
            
            	<div class="modal-header">
                	<b>Monthly Closing</b>
                </div>

				<div class="modal-body" style="height:30vh">
                	
                     <div id="divprocessingdate">
                    <fieldset>
                    	<legend><font size="-1">Date Range</font></legend>
                        
                              <div class="col-xs-12 nopadding">
        						<div class="col-xs-3 nopadding">From: </div>
                                <div class="col-xs-9 nopadding"> <input type='text' class="form-control input-sm" id="closedate1" name="closedate1" value="<?php echo date("m/d/Y"); ?>" /> </div>
                             </div>

                              <div class="col-xs-12 nopadwtop2x">
        						<div class="col-xs-3 nopadding">To: </div>
                                <div class="col-xs-9 nopadding"> <input type='text' class="form-control input-sm" id="closedate2" name="closedate2" value="<?php echo date("m/d/Y"); ?>" /> </div>
                             </div>
                             
                             <div class="col-xs-12 nopadwtop2x text-center" id="statmsg">
                             	
                             </div>

                    </fieldset>
                    </div>
                    
                    <div id="divprocessing">
                    	<img src="images/PGIFT.gif">
                    </div>
                    
                </div>

            	<div class="modal-footer">
					<div class="col-xs-12 nopadding">
                    	<div class="col-xs-6 nopadding"><button type="button" class="btn btn-danger btn-sm btn-block" data-dismiss="modal" id="btnmonthcloseX" name="btnmonthcloseX">CLOSE</button></div>
                        <div class="col-xs-6 nopadding"><button type="button" class="btn btn-success btn-sm btn-block" id="btnmonthclose" name="btnmonthclose">SUBMIT</button></div>
                    </div>
                        

                </div>
            </div>
        </div>
</div>



<script src="global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="bootstrap/js/bootstrap.js" type="text/javascript"></script>
<script src="global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script> 
<script src="global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script> 
<script src="global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<script src="global/scripts/metronic.js" type="text/javascript"></script>
<script src="admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="admin/layout/scripts/demo.js" type="text/javascript"></script>

<script>
      jQuery(document).ready(function() {    
        Metronic.init(); // init metronic core components
		Layout.init(); // init current layout
		QuickSidebar.init(); // init quick sidebar
		Demo.init(); // init demo features
		
		loadxtrasession();
		
      });
	  
	  $(function(){
		  $("#closedate1, #closedate2").datepicker({
			  autoclose: true,
              format: 'mm/dd/yyyy',
				// onChangeDateTime:changelimits,
				 //minDate: new Date(),
        	});
		
		  $("#btnmonthclose").on("click", function() {			
				
				$("#divprocessingdate").hide();
				$("#divprocessing").show();
				$("#btnmonthclose").attr("disabled", true);
				$("#btnmonthcloseX").attr("disabled", true); 
				$("#closedate1").attr("disabled", true);
				$("#closedate2").attr("disabled", true);
				
							$.ajax({
							  url: "Accounting/ProcessClosing.php",
							  data: { dte1: $("#closedate1").val(), dte2: $("#closedate2").val() },
							  dataType: "text",
							  cache: false,
							  success: function(data){
									if(data.trim()=="True"){
										alert("Please wait while I'm closing your transactions! DONT RELOAD THIS PAGE!");
													$.ajax({
													  url: "Accounting/Closing/POS_Del.php",
													  async: false,
													  cache: false,
													  success: function(data){
														alert(data.trim());
															if(data.trim()=="True"){
																alert("Done Processing");
															}
													  },
													  complete: function(){
															$("#divprocessingdate").show();
															$("#divprocessing").hide();
															
															
													  }
													});
									}
							  },
							});

				
		  });
	  
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
	  
	  
	  function openclosing(){
		  $("#divprocessingdate").show();
		  $("#divprocessing").hide();
		  $("#AlertModal").modal('show');
	  }
	  
	   
    

	  
   </script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>